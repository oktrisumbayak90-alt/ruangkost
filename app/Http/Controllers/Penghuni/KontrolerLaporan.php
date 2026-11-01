<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Category;
use App\Models\Report;
use App\Models\ReportHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KontrolerLaporan extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Report::where('pengguna_id', $user->id)
            ->with(['category', 'room'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('nomor_laporan', 'like', "%{$search}%");
            });
        }

        $reports = $query->paginate(10)->withQueryString();

        return view('tenant.reports.index', compact('reports'));
    }

    public function create()
    {
        $user = auth()->user();

        // Cek apakah penghuni masih aktif dan memiliki stay aktif
        if (!$user->activeStay()->exists()) {
            return redirect()->route('tenant.dashboard')
                ->withErrors(['error' => 'Anda tidak memiliki kamar aktif. Silakan hubungi pemilik kos.']);
        }

        $categories = Category::active()->orderBy('urutan')->get();
        $activeStay = $user->activeStay()->with('room')->first();

        return view('tenant.reports.create', compact('categories', 'activeStay'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Cek akun aktif & punya stay aktif
        if (!$user->is_aktif || !$user->activeStay()->exists()) {
            abort(403, 'Anda tidak diizinkan membuat laporan.');
        }

        $request->validate([
            'title'          => 'nullable|string|max:200',
            'judul'          => 'nullable|string|max:200',
            'category_id'    => 'nullable|exists:kategori,id',
            'kategori_id'    => 'nullable|exists:kategori,id',
            'description'    => 'nullable|string|min:10|max:2000',
            'deskripsi'      => 'nullable|string|min:10|max:2000',
            'location'       => 'nullable|string|max:200',
            'lokasi'         => 'nullable|string|max:200',
            'incident_date'  => 'nullable|date|before_or_equal:today',
            'tanggal_kejadian'=> 'nullable|date|before_or_equal:today',
            'priority'       => 'nullable|in:low,medium,high,rendah,sedang,tinggi',
            'prioritas'      => 'nullable|in:low,medium,high,rendah,sedang,tinggi',
            'image'          => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'foto'           => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'incident_date.before_or_equal'   => 'Tanggal kejadian tidak boleh di masa depan.',
            'tanggal_kejadian.before_or_equal'=> 'Tanggal kejadian tidak boleh di masa depan.',
            'image.image'                     => 'File harus berupa gambar.',
            'image.mimes'                     => 'Format gambar harus JPEG, JPG, PNG, atau WebP.',
            'image.max'                       => 'Ukuran gambar maksimal 2MB.',
            'foto.image'                      => 'File harus berupa gambar.',
            'foto.mimes'                      => 'Format gambar harus JPEG, JPG, PNG, atau WebP.',
            'foto.max'                        => 'Ukuran gambar maksimal 2MB.',
        ]);

        $judul = $request->input('judul', $request->input('title'));
        $kategoriId = $request->input('kategori_id', $request->input('category_id'));
        $deskripsi = $request->input('deskripsi', $request->input('description'));
        $lokasi = $request->input('lokasi', $request->input('location'));
        $tanggalKejadian = $request->input('tanggal_kejadian', $request->input('incident_date', date('Y-m-d')));
        $prioritasRaw = $request->input('prioritas', $request->input('priority', 'medium'));

        if (empty($judul)) {
            return back()->withInput()->withErrors(['title' => 'Judul laporan wajib diisi.']);
        }
        if (empty($kategoriId)) {
            return back()->withInput()->withErrors(['category_id' => 'Kategori wajib dipilih.']);
        }
        if (empty($deskripsi) || strlen($deskripsi) < 10) {
            return back()->withInput()->withErrors(['description' => 'Deskripsi minimal 10 karakter.']);
        }

        // Map prioritas ke format baku database (rendah, sedang, tinggi)
        $prioritasMap = [
            'low'    => 'rendah',
            'medium' => 'sedang',
            'high'   => 'tinggi',
            'rendah' => 'rendah',
            'sedang' => 'sedang',
            'tinggi' => 'tinggi',
        ];
        $prioritas = $prioritasMap[strtolower((string)$prioritasRaw)] ?? 'sedang';

        $activeStay = $user->activeStay()->with('room')->first();
        $imagePath = null;

        if ($request->hasFile('foto')) {
            $imagePath = $request->file('foto')->store('reports', 'public');
        } elseif ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reports', 'public');
        }

        DB::transaction(function () use ($user, $activeStay, $imagePath, $judul, $kategoriId, $deskripsi, $lokasi, $tanggalKejadian, $prioritas) {
            $report = Report::create([
                'pengguna_id'        => $user->id,
                'kategori_id'        => $kategoriId,
                'kamar_id'           => $activeStay->kamar_id ?? $activeStay->room_id ?? null,
                'riwayat_tinggal_id' => $activeStay->id ?? null,
                'judul'              => $judul,
                'deskripsi'          => $deskripsi,
                'lokasi'             => $lokasi,
                'tanggal_kejadian'   => $tanggalKejadian,
                'prioritas'          => $prioritas,
                'status'             => 'menunggu',
                'jalur_foto'         => $imagePath,
            ]);

            // Riwayat awal
            ReportHistory::create([
                'laporan_id'  => $report->id,
                'pengguna_id' => $user->id,
                'status_lama' => null,
                'status_baru' => 'menunggu',
                'catatan'     => 'Laporan dibuat.',
            ]);

            // Notifikasi ke owner
            $owner = User::whereIn('peran', ['pemilik', 'owner'])->first();
            if ($owner) {
                AppNotification::create([
                    'pengguna_id' => $owner->id,
                    'tipe'        => 'new_report',
                    'judul'       => 'Laporan Baru Masuk',
                    'pesan'       => "{$user->nama} membuat laporan baru: \"{$report->judul}\".",
                    'url'         => route('owner.reports.show', $report),
                    'laporan_id'  => $report->id,
                    'is_dibaca'   => false,
                ]);
            }
        });

        return redirect()->route('tenant.reports.index')
            ->with('success', 'Laporan berhasil dikirim. Nomor laporan Anda akan segera muncul.');
    }

    public function show(Report $report)
    {
        $this->authorizeReport($report);
        $report->load(['category', 'room', 'responses.user', 'histories.user']);
        return view('tenant.reports.show', compact('report'));
    }

    public function edit(Report $report)
    {
        $this->authorizeReport($report);

        if (!$report->isEditable()) {
            return redirect()->route('tenant.reports.show', $report)
                ->withErrors(['error' => 'Laporan ini tidak dapat diedit karena sudah diproses.']);
        }

        $categories = Category::active()->orderBy('urutan')->get();
        return view('tenant.reports.edit', compact('report', 'categories'));
    }

    public function update(Request $request, Report $report)
    {
        $this->authorizeReport($report);

        if (!$report->isEditable()) {
            abort(403, 'Laporan ini tidak dapat diedit.');
        }

        $request->validate([
            'title'          => 'nullable|string|max:200',
            'judul'          => 'nullable|string|max:200',
            'category_id'    => 'nullable|exists:kategori,id',
            'kategori_id'    => 'nullable|exists:kategori,id',
            'description'    => 'nullable|string|min:10|max:2000',
            'deskripsi'      => 'nullable|string|min:10|max:2000',
            'location'       => 'nullable|string|max:200',
            'lokasi'         => 'nullable|string|max:200',
            'incident_date'  => 'nullable|date|before_or_equal:today',
            'tanggal_kejadian'=> 'nullable|date|before_or_equal:today',
            'priority'       => 'nullable|in:low,medium,high,rendah,sedang,tinggi',
            'prioritas'      => 'nullable|in:low,medium,high,rendah,sedang,tinggi',
            'image'          => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'foto'           => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $judul = $request->input('judul', $request->input('title', $report->judul));
        $kategoriId = $request->input('kategori_id', $request->input('category_id', $report->kategori_id));
        $deskripsi = $request->input('deskripsi', $request->input('description', $report->deskripsi));
        $lokasi = $request->input('lokasi', $request->input('location', $report->lokasi));
        $tanggalKejadian = $request->input('tanggal_kejadian', $request->input('incident_date', $report->tanggal_kejadian));
        $prioritasRaw = $request->input('prioritas', $request->input('priority', $report->prioritas));

        $prioritasMap = [
            'low'    => 'rendah',
            'medium' => 'sedang',
            'high'   => 'tinggi',
            'rendah' => 'rendah',
            'sedang' => 'sedang',
            'tinggi' => 'tinggi',
        ];
        $prioritas = $prioritasMap[strtolower((string)$prioritasRaw)] ?? $report->prioritas;

        $imagePath = $report->jalur_foto;

        if ($request->hasFile('foto')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('foto')->store('reports', 'public');
        } elseif ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('reports', 'public');
        }

        $report->update([
            'judul'            => $judul,
            'kategori_id'      => $kategoriId,
            'deskripsi'        => $deskripsi,
            'lokasi'           => $lokasi,
            'tanggal_kejadian' => $tanggalKejadian,
            'prioritas'        => $prioritas,
            'jalur_foto'       => $imagePath,
        ]);

        return redirect()->route('tenant.reports.show', $report)
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function cancel(Request $request, Report $report)
    {
        $this->authorizeReport($report);

        if (!$report->isCancellable()) {
            return back()->withErrors(['error' => 'Laporan ini tidak dapat dibatalkan.']);
        }

        DB::transaction(function () use ($request, $report) {
            $report->update(['status' => 'dibatalkan']);

            ReportHistory::create([
                'laporan_id'  => $report->id,
                'pengguna_id' => auth()->id(),
                'status_lama' => 'menunggu',
                'status_baru' => 'dibatalkan',
                'catatan'     => $request->input('reason', $request->input('catatan', 'Dibatalkan oleh penghuni.')),
            ]);
        });

        return redirect()->route('tenant.reports.index')
            ->with('success', 'Laporan berhasil dibatalkan.');
    }

    public function destroy(Report $report)
    {
        $this->authorizeReport($report);

        if (!in_array($report->status, ['pending', 'cancelled', 'menunggu', 'dibatalkan'])) {
            return back()->withErrors(['error' => 'Laporan yang sedang diproses atau sudah selesai tidak dapat dihapus.']);
        }

        $nomorLaporan = $report->report_number;

        DB::transaction(function () use ($report) {
            if ($report->jalur_foto && Storage::disk('public')->exists($report->jalur_foto)) {
                Storage::disk('public')->delete($report->jalur_foto);
            }

            $report->notifications()->delete();
            $report->delete();
        });

        return redirect()->route('tenant.reports.index')
            ->with('success', "Laporan #{$nomorLaporan} berhasil dihapus.");
    }

    private function authorizeReport(Report $report): void
    {
        if ($report->pengguna_id !== auth()->id() && $report->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }
    }
}

<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Report;
use App\Models\ReportHistory;
use App\Models\ReportResponse;
use App\Models\Room;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KontrolerLaporan extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['user', 'category', 'room'])->orderByDesc('created_at');

        // Filter kategori dari sidebar
        if ($request->filled('category_slug')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category_slug));
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter prioritas
        $priorityFilter = $request->input('prioritas', $request->input('priority'));
        if ($priorityFilter) {
            $query->where(function ($q) use ($priorityFilter) {
                $q->where('prioritas', $priorityFilter);
                if ($priorityFilter === 'low') $q->orWhere('prioritas', 'rendah');
                if ($priorityFilter === 'medium') $q->orWhere('prioritas', 'sedang');
                if ($priorityFilter === 'high') $q->orWhere('prioritas', 'tinggi');
            });
        }

        // Filter kategori by ID
        $catId = $request->input('kategori_id', $request->input('category_id'));
        if ($catId) {
            $query->where('kategori_id', $catId);
        }

        // Filter kamar
        $roomId = $request->input('kamar_id', $request->input('room_id'));
        if ($roomId) {
            $query->where('kamar_id', $roomId);
        }

        // Filter penghuni
        $userId = $request->input('pengguna_id', $request->input('user_id'));
        if ($userId) {
            $query->where('pengguna_id', $userId);
        }

        // Filter tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('nomor_laporan', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('nama', 'like', "%{$search}%"));
            });
        }

        $reports = $query->paginate(15)->withQueryString();
        $categories = Category::active()->orderBy('urutan')->get();
        $rooms = Room::orderBy('nomor_kamar')->get();
        $tenants = User::whereIn('peran', ['penghuni', 'tenant'])->orderBy('nama')->get();

        // Label kategori aktif untuk sidebar
        $activeCategory = null;
        if ($request->filled('category_slug')) {
            $activeCategory = Category::where('slug', $request->category_slug)->first();
        }

        return view('owner.reports.index', compact(
            'reports', 'categories', 'rooms', 'tenants', 'activeCategory'
        ));
    }

    public function show(Report $report)
    {
        $report->load(['user', 'category', 'room', 'responses.user', 'histories.user', 'tenantStay']);
        return view('owner.reports.show', compact('report'));
    }

    public function updateStatus(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled,menunggu,diproses,selesai,dibatalkan',
            'note'   => 'nullable|string|max:500',
            'catatan'=> 'nullable|string|max:500',
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in'       => 'Status tidak valid.',
        ]);

        $validTransitions = [
            'pending'     => ['in_progress', 'cancelled', 'diproses', 'dibatalkan'],
            'menunggu'    => ['in_progress', 'cancelled', 'diproses', 'dibatalkan'],
            'in_progress' => ['completed', 'cancelled', 'pending', 'selesai', 'dibatalkan', 'menunggu'],
            'diproses'    => ['completed', 'cancelled', 'pending', 'selesai', 'dibatalkan', 'menunggu'],
            'completed'   => [],
            'selesai'     => [],
            'cancelled'   => ['pending', 'menunggu'],
            'dibatalkan'  => ['pending', 'menunggu'],
        ];

        if (!in_array($request->status, $validTransitions[$report->status] ?? [])) {
            return back()->withErrors(['status' => 'Transisi status tidak valid.']);
        }

        DB::transaction(function () use ($request, $report) {
            $oldStatus = $report->status;
            $note = $request->input('catatan', $request->input('note'));

            $report->update(['status' => $request->status]);

            // Simpan riwayat
            ReportHistory::create([
                'laporan_id'  => $report->id,
                'pengguna_id' => auth()->id(),
                'status_lama' => $oldStatus,
                'status_baru' => $request->status,
                'catatan'     => $note,
            ]);

            // Kirim notifikasi ke tenant
            AppNotification::create([
                'pengguna_id' => $report->pengguna_id ?? $report->user_id,
                'tipe'        => 'status_changed',
                'judul'       => 'Status Laporan Diperbarui',
                'pesan'       => "Status laporan \"{$report->judul}\" telah diubah menjadi " . $report->fresh()->status_label . ".",
                'url'         => route('tenant.reports.show', $report),
                'laporan_id'  => $report->id,
                'is_dibaca'   => false,
            ]);
        });

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function addResponse(Request $request, Report $report)
    {
        $request->validate([
            'response'  => 'nullable|string|min:5|max:2000',
            'tanggapan' => 'nullable|string|min:5|max:2000',
        ]);

        $tanggapan = $request->input('tanggapan', $request->input('response'));
        if (empty($tanggapan) || strlen($tanggapan) < 5) {
            return back()->withErrors(['response' => 'Tanggapan minimal 5 karakter.']);
        }

        DB::transaction(function () use ($tanggapan, $report) {
            ReportResponse::create([
                'laporan_id'  => $report->id,
                'pengguna_id' => auth()->id(),
                'tanggapan'   => $tanggapan,
            ]);

            // Kirim notifikasi ke tenant
            AppNotification::create([
                'pengguna_id' => $report->pengguna_id ?? $report->user_id,
                'tipe'        => 'new_response',
                'judul'       => 'Tanggapan Baru dari Pemilik Kos',
                'pesan'       => "Pemilik kos telah memberikan tanggapan untuk laporan \"{$report->judul}\".",
                'url'         => route('tenant.reports.show', $report),
                'laporan_id'  => $report->id,
                'is_dibaca'   => false,
            ]);
        });

        return back()->with('success', 'Tanggapan berhasil dikirim.');
    }

    public function exportPdf(Request $request)
    {
        $query = Report::with(['user', 'category', 'room'])->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $catId = $request->input('kategori_id', $request->input('category_id'));
        if ($catId) {
            $query->where('kategori_id', $catId);
        }
        $priorityFilter = $request->input('prioritas', $request->input('priority'));
        if ($priorityFilter) {
            $query->where(function ($q) use ($priorityFilter) {
                $q->where('prioritas', $priorityFilter);
                if ($priorityFilter === 'low') $q->orWhere('prioritas', 'rendah');
                if ($priorityFilter === 'medium') $q->orWhere('prioritas', 'sedang');
                if ($priorityFilter === 'high') $q->orWhere('prioritas', 'tinggi');
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $reports = $query->get();
        $categories = Category::active()->get();

        $categoryName = null;
        if ($catId) {
            $categoryName = Category::find($catId)?->nama;
        }

        $filters = [
            'status'    => $request->status,
            'category'  => $categoryName,
            'priority'  => $priorityFilter,
            'date_from' => $request->date_from,
            'date_to'   => $request->date_to,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('owner.reports.pdf', compact('reports', 'filters'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-kos-' . now()->format('Y-m-d') . '.pdf');
    }

    public function destroy(Report $report)
    {
        $nomorLaporan = $report->report_number;

        DB::transaction(function () use ($report) {
            // Hapus file foto jika ada di disk
            if ($report->jalur_foto && Storage::disk('public')->exists($report->jalur_foto)) {
                Storage::disk('public')->delete($report->jalur_foto);
            }

            // Hapus notifikasi terkait laporan
            $report->notifications()->delete();

            // Hapus laporan
            $report->delete();
        });

        return redirect()->route('owner.reports.index')
            ->with('success', "Laporan #{$nomorLaporan} berhasil dihapus.");
    }
}

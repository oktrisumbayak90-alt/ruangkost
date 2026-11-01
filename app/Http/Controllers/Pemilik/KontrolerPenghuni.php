<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\TenantStay;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class KontrolerPenghuni extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn('peran', ['penghuni', 'tenant'])
            ->with(['activeStay.room'])
            ->orderBy('nama');

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhereHas('activeStay.room', fn($r) => $r->where('nomor_kamar', 'like', "%{$search}%"));
            });
        }

        // Filter status akun
        if ($request->filled('is_active') || $request->filled('is_aktif')) {
            $val = $request->input('is_aktif', $request->input('is_active'));
            $query->where('is_aktif', $val);
        }

        // Filter kamar
        if ($request->filled('room_id') || $request->filled('kamar_id')) {
            $kamarId = $request->input('kamar_id', $request->input('room_id'));
            $query->whereHas('activeStay', fn($q) => $q->where('kamar_id', $kamarId));
        }

        $tenants = $query->paginate(15)->withQueryString();
        $rooms = Room::orderBy('nomor_kamar')->get();

        return view('owner.tenants.index', compact('tenants', 'rooms'));
    }

    public function create()
    {
        $rooms = Room::where(function ($q) {
            $q->whereIn('status', ['tersedia', 'available'])
              ->orWhereIn('status', ['terisi', 'occupied']);
        })->orderBy('nomor_kamar')->get()
          ->filter(fn($r) => $r->isAvailable());

        return view('owner.tenants.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $nama               = $request->input('nama', $request->input('name'));
        $telepon            = $request->input('telepon', $request->input('phone'));
        $nomorIdentitas     = $request->input('nomor_identitas', $request->input('id_number'));
        $jenisKelamin       = $request->input('jenis_kelamin', $request->input('gender'));
        $alamat             = $request->input('alamat', $request->input('address'));
        $kamarId            = $request->input('kamar_id', $request->input('room_id'));
        $tanggalMasuk       = $request->input('tanggal_masuk', $request->input('move_in_date'));
        $jumlahPenghuni     = $request->input('jumlah_penghuni', $request->input('occupant_count', 1));
        $wajibGantiPassword = $request->boolean('wajib_ganti_password', $request->boolean('must_change_password', false));
        $kataSandi          = $request->input('kata_sandi', $request->input('password'));

        $request->merge([
            'nama'             => $nama,
            'telepon'          => $telepon,
            'nomor_identitas'  => $nomorIdentitas,
            'jenis_kelamin'    => $jenisKelamin,
            'alamat'           => $alamat,
            'kamar_id'         => $kamarId,
            'tanggal_masuk'    => $tanggalMasuk,
            'jumlah_penghuni'  => $jumlahPenghuni,
            'kata_sandi'       => $kataSandi,
        ]);

        $request->validate([
            'nama'            => 'required|string|max:100',
            'email'           => 'required|email|unique:pengguna,email',
            'username'        => 'nullable|string|max:50|unique:pengguna,username|alpha_num',
            'kata_sandi'      => ['required', Password::min(8)],
            'telepon'         => 'nullable|string|max:20',
            'nomor_identitas' => 'nullable|string|max:20',
            'jenis_kelamin'   => 'nullable|in:laki-laki,perempuan,male,female',
            'alamat'          => 'nullable|string|max:500',
            'kamar_id'        => 'required|exists:kamar,id',
            'tanggal_masuk'   => 'required|date',
            'jumlah_penghuni' => 'required|integer|min:1',
        ], [
            'nama.required'             => 'Nama lengkap wajib diisi.',
            'email.required'            => 'Email wajib diisi.',
            'email.email'               => 'Format email tidak valid.',
            'email.unique'              => 'Email sudah digunakan.',
            'username.unique'           => 'Username sudah digunakan.',
            'username.alpha_num'        => 'Username hanya boleh huruf dan angka.',
            'kata_sandi.required'       => 'Kata sandi wajib diisi.',
            'kamar_id.required'         => 'Kamar wajib dipilih.',
            'kamar_id.exists'           => 'Kamar tidak ditemukan.',
            'tanggal_masuk.required'    => 'Tanggal masuk wajib diisi.',
            'jumlah_penghuni.required'  => 'Jumlah penghuni wajib diisi.',
            'jumlah_penghuni.integer'   => 'Jumlah penghuni harus berupa angka.',
            'jumlah_penghuni.min'       => 'Jumlah penghuni minimal 1 orang.',
        ]);

        // Normalisasi jenis kelamin
        if ($jenisKelamin === 'male') $jenisKelamin = 'laki-laki';
        if ($jenisKelamin === 'female') $jenisKelamin = 'perempuan';

        // Cek ketersediaan kamar
        $room = Room::findOrFail($kamarId);
        if (!$room->isAvailable()) {
            return back()->withErrors(['kamar_id' => 'Kamar sudah penuh. Pilih kamar lain.'])->withInput();
        }

        // Validasi jumlah penghuni tidak melebihi sisa kapasitas kamar
        $sisaKapasitas = $room->kapasitas - $room->getActiveTenantsCount();
        if ($jumlahPenghuni > $sisaKapasitas) {
            return back()->withErrors(['jumlah_penghuni' => "Jumlah penghuni melebihi sisa kapasitas kamar. Sisa kapasitas: {$sisaKapasitas} orang."])->withInput();
        }

        DB::transaction(function () use ($request, $room, $nama, $telepon, $nomorIdentitas, $jenisKelamin, $alamat, $kamarId, $tanggalMasuk, $jumlahPenghuni, $wajibGantiPassword, $kataSandi) {
            // Buat user
            $user = User::create([
                'nama'                 => $nama,
                'email'                => $request->email,
                'username'             => $request->username ?: null,
                'password'             => Hash::make($kataSandi),
                'peran'                => 'penghuni',
                'telepon'              => $telepon,
                'nomor_identitas'      => $nomorIdentitas,
                'jenis_kelamin'        => $jenisKelamin,
                'alamat'               => $alamat,
                'is_aktif'             => true,
                'wajib_ganti_password' => $wajibGantiPassword,
            ]);

            // Buat riwayat tinggal
            TenantStay::create([
                'pengguna_id'     => $user->id,
                'kamar_id'        => $room->id,
                'tanggal_masuk'   => $tanggalMasuk,
                'jumlah_penghuni' => $jumlahPenghuni,
                'status'          => 'aktif',
            ]);

            // Update status kamar
            $room->updateStatus();
        });

        return redirect()->route('owner.tenants.index')
            ->with('success', 'Penghuni berhasil ditambahkan.');
    }

    public function show(User $tenant)
    {
        $this->authorizeTenant($tenant);
        $tenant->load(['tenantStays.room', 'reports.category', 'reports' => fn($q) => $q->orderByDesc('created_at')]);
        return view('owner.tenants.show', compact('tenant'));
    }

    public function edit(User $tenant)
    {
        $this->authorizeTenant($tenant);
        $tenant->load('activeStay.room');

        $rooms = Room::orderBy('nomor_kamar')->get();

        return view('owner.tenants.edit', compact('tenant', 'rooms'));
    }

    public function update(Request $request, User $tenant)
    {
        $this->authorizeTenant($tenant);

        $nama           = $request->input('nama', $request->input('name'));
        $telepon        = $request->input('telepon', $request->input('phone'));
        $nomorIdentitas = $request->input('nomor_identitas', $request->input('id_number'));
        $jenisKelamin   = $request->input('jenis_kelamin', $request->input('gender'));
        $alamat         = $request->input('alamat', $request->input('address'));
        $jumlahPenghuni = $request->input('jumlah_penghuni', $request->input('occupant_count'));

        $request->merge([
            'nama'            => $nama,
            'telepon'         => $telepon,
            'nomor_identitas' => $nomorIdentitas,
            'jenis_kelamin'   => $jenisKelamin,
            'alamat'          => $alamat,
        ]);

        $rules = [
            'nama'            => 'required|string|max:100',
            'email'           => 'required|email|unique:pengguna,email,' . $tenant->id,
            'username'        => 'nullable|string|max:50|unique:pengguna,username,' . $tenant->id . '|alpha_num',
            'telepon'         => 'nullable|string|max:20',
            'nomor_identitas' => 'nullable|string|max:20',
            'jenis_kelamin'   => 'nullable|in:laki-laki,perempuan,male,female',
            'alamat'          => 'nullable|string|max:500',
        ];

        $messages = [
            'nama.required'   => 'Nama lengkap wajib diisi.',
            'email.required'  => 'Email wajib diisi.',
            'email.unique'    => 'Email sudah digunakan.',
            'username.unique' => 'Username sudah digunakan.',
        ];

        // Tambahkan validasi jumlah_penghuni jika ada stay aktif
        $activeStay = $tenant->activeStay;
        if ($activeStay && $jumlahPenghuni !== null) {
            $rules['jumlah_penghuni'] = 'required|integer|min:1';
            $messages['jumlah_penghuni.required'] = 'Jumlah penghuni wajib diisi.';
            $messages['jumlah_penghuni.integer']  = 'Jumlah penghuni harus berupa angka.';
            $messages['jumlah_penghuni.min']      = 'Jumlah penghuni minimal 1 orang.';
        }

        $request->validate($rules, $messages);

        if ($jenisKelamin === 'male') $jenisKelamin = 'laki-laki';
        if ($jenisKelamin === 'female') $jenisKelamin = 'perempuan';

        $tenant->update([
            'nama'            => $nama,
            'email'           => $request->email,
            'username'        => $request->username ?: null,
            'telepon'         => $telepon,
            'nomor_identitas' => $nomorIdentitas,
            'jenis_kelamin'   => $jenisKelamin,
            'alamat'          => $alamat,
        ]);

        // Update jumlah penghuni di riwayat tinggal aktif
        if ($activeStay && $jumlahPenghuni !== null) {
            $room = $activeStay->room;
            if ($room) {
                $kapasitasLain = $room->getActiveTenantsCount() - ($activeStay->jumlah_penghuni ?? 1);
                $sisaKapasitas = $room->kapasitas - $kapasitasLain;
                if ($jumlahPenghuni > $sisaKapasitas) {
                    return back()->withErrors(['jumlah_penghuni' => "Jumlah penghuni melebihi sisa kapasitas kamar. Sisa kapasitas: {$sisaKapasitas} orang."])->withInput();
                }
            }
            $activeStay->update(['jumlah_penghuni' => $jumlahPenghuni]);
            if ($room) {
                $room->updateStatus();
            }
        }

        return redirect()->route('owner.tenants.show', $tenant)
            ->with('success', 'Data penghuni berhasil diperbarui.');
    }

    public function toggleActive(User $tenant)
    {
        $this->authorizeTenant($tenant);

        $tenant->update(['is_aktif' => !$tenant->is_aktif]);

        $status = $tenant->is_aktif ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun penghuni berhasil {$status}.");
    }

    public function evict(Request $request, User $tenant)
    {
        $this->authorizeTenant($tenant);

        $tanggalKeluar = $request->input('tanggal_keluar', $request->input('move_out_date'));
        $catatan       = $request->input('catatan', $request->input('notes'));

        $request->merge([
            'tanggal_keluar' => $tanggalKeluar,
            'catatan'        => $catatan,
        ]);

        $request->validate([
            'tanggal_keluar' => 'required|date',
            'catatan'        => 'nullable|string|max:500',
        ], [
            'tanggal_keluar.required' => 'Tanggal keluar wajib diisi.',
        ]);

        DB::transaction(function () use ($tanggalKeluar, $catatan, $tenant) {
            // Akhiri semua stay aktif
            $activeStays = TenantStay::where('pengguna_id', $tenant->id)
                ->whereIn('status', ['aktif', 'active'])
                ->with('room')
                ->get();

            foreach ($activeStays as $stay) {
                $stay->update([
                    'status'         => 'selesai',
                    'tanggal_keluar' => $tanggalKeluar,
                    'catatan'        => $catatan,
                ]);

                // Update status kamar
                $stay->room->updateStatus();
            }

            // Nonaktifkan akun
            $tenant->update(['is_aktif' => false]);
        });

        return redirect()->route('owner.tenants.index')
            ->with('success', "Penghuni {$tenant->nama} berhasil dikeluarkan dari kos.");
    }

    public function resetPassword(Request $request, User $tenant)
    {
        $this->authorizeTenant($tenant);

        $kataSandiBaru = $request->input('kata_sandi_baru', $request->input('new_password'));
        $request->merge(['kata_sandi_baru' => $kataSandiBaru]);

        $request->validate([
            'kata_sandi_baru' => ['required', Password::min(8)],
        ], [
            'kata_sandi_baru.required' => 'Kata sandi baru wajib diisi.',
        ]);

        $tenant->update([
            'password'             => Hash::make($kataSandiBaru),
            'wajib_ganti_password' => true,
        ]);

        return back()->with('success', 'Kata sandi penghuni berhasil direset. Penghuni harus mengganti kata sandi saat login berikutnya.');
    }

    public function history()
    {
        $stays = TenantStay::with(['user', 'room'])
            ->orderByDesc('tanggal_masuk')
            ->paginate(20);

        return view('owner.tenants.history', compact('stays'));
    }

    public function destroy(User $tenant)
    {
        $this->authorizeTenant($tenant);

        $nama = $tenant->nama;

        DB::transaction(function () use ($tenant) {
            // Akhiri semua stay aktif dan perbarui status kamar
            $activeStays = TenantStay::where('pengguna_id', $tenant->id)
                ->whereIn('status', ['aktif', 'active'])
                ->with('room')
                ->get();

            foreach ($activeStays as $stay) {
                $stay->update([
                    'status'         => 'selesai',
                    'tanggal_keluar' => now()->toDateString(),
                    'catatan'        => 'Akun dihapus oleh admin.',
                ]);

                $stay->room->updateStatus();
            }

            // Hapus akun penghuni beserta data terkait
            $tenant->delete();
        });

        return redirect()->route('owner.tenants.index')
            ->with('success', "Akun penghuni {$nama} berhasil dihapus secara permanen.");
    }

    private function authorizeTenant(User $tenant): void
    {
        if (!$tenant->isTenant()) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
}

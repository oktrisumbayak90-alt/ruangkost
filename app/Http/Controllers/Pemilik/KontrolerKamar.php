<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\TenantStay;
use Illuminate\Http\Request;

class KontrolerKamar extends Controller
{
    public function index(Request $request)
    {
        $query = Room::withCount(['activeStays'])->with('activeStays')->orderBy('nomor_kamar');

        if ($request->filled('status')) {
            $status = $request->status;
            if (in_array($status, ['tersedia', 'available', 'kosong'])) {
                $query->whereIn('status', ['tersedia', 'available']);
            } elseif (in_array($status, ['terisi', 'occupied', 'penuh', 'full'])) {
                $query->whereIn('status', ['terisi', 'occupied', 'penuh', 'full']);
            } elseif (in_array($status, ['pemeliharaan', 'maintenance', 'perbaikan'])) {
                $query->whereIn('status', ['pemeliharaan', 'maintenance']);
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_kamar', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $rooms = $query->paginate(15)->withQueryString();

        return view('owner.rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('owner.rooms.create');
    }

    public function store(Request $request)
    {
        $nomorKamar = $request->input('nomor_kamar', $request->input('room_number'));
        $lantai     = $request->input('lantai', $request->input('floor'));
        $kapasitas  = $request->input('kapasitas', $request->input('capacity'));
        $deskripsi  = $request->input('deskripsi', $request->input('description'));
        $status     = $request->input('status', 'tersedia');

        if (in_array($status, ['available', 'tersedia', 'kosong'])) $status = 'tersedia';
        if (in_array($status, ['occupied', 'terisi'])) $status = 'terisi';
        if (in_array($status, ['full', 'penuh'])) $status = 'penuh';
        if (in_array($status, ['maintenance', 'pemeliharaan', 'perbaikan'])) $status = 'pemeliharaan';

        $request->merge([
            'nomor_kamar' => $nomorKamar,
            'lantai'      => $lantai,
            'kapasitas'   => $kapasitas,
            'deskripsi'   => $deskripsi,
            'status'      => $status,
        ]);

        $request->validate([
            'nomor_kamar' => 'required|string|max:20|unique:kamar,nomor_kamar',
            'lantai'      => 'nullable|string|max:10',
            'kapasitas'   => 'required|integer|min:1|max:10',
            'deskripsi'   => 'nullable|string|max:500',
            'status'      => 'required|in:tersedia,pemeliharaan,available,maintenance',
        ], [
            'nomor_kamar.required' => 'Nomor kamar wajib diisi.',
            'nomor_kamar.unique'   => 'Nomor kamar sudah digunakan.',
            'kapasitas.required'   => 'Kapasitas wajib diisi.',
            'kapasitas.min'        => 'Kapasitas minimal 1.',
            'status.required'      => 'Status wajib dipilih.',
        ]);

        Room::create([
            'nomor_kamar' => $nomorKamar,
            'lantai'      => $lantai,
            'kapasitas'   => $kapasitas,
            'deskripsi'   => $deskripsi,
            'status'      => $status,
        ]);

        return redirect()->route('owner.rooms.index')
            ->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function show(Room $room)
    {
        $room->load(['tenantStays.user', 'activeStays.user']);
        return view('owner.rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        return view('owner.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $nomorKamar = $request->input('nomor_kamar', $request->input('room_number'));
        $lantai     = $request->input('lantai', $request->input('floor'));
        $kapasitas  = $request->input('kapasitas', $request->input('capacity'));
        $deskripsi  = $request->input('deskripsi', $request->input('description'));

        $request->merge([
            'nomor_kamar' => $nomorKamar,
            'lantai'      => $lantai,
            'kapasitas'   => $kapasitas,
            'deskripsi'   => $deskripsi,
        ]);

        $request->validate([
            'nomor_kamar' => 'required|string|max:20|unique:kamar,nomor_kamar,' . $room->id,
            'lantai'      => 'nullable|string|max:10',
            'kapasitas'   => 'required|integer|min:1|max:10',
            'deskripsi'   => 'nullable|string|max:500',
        ], [
            'nomor_kamar.required' => 'Nomor kamar wajib diisi.',
            'nomor_kamar.unique'   => 'Nomor kamar sudah digunakan.',
            'kapasitas.required'   => 'Kapasitas wajib diisi.',
        ]);

        $room->update([
            'nomor_kamar' => $nomorKamar,
            'lantai'      => $lantai,
            'kapasitas'   => $kapasitas,
            'deskripsi'   => $deskripsi,
        ]);

        // Sinkronkan status kamar
        $statusInput = $request->input('status');
        if (in_array($statusInput, ['pemeliharaan', 'maintenance', 'perbaikan'])) {
            $room->status = 'pemeliharaan';
            $room->save();
        } else {
            if (in_array($room->status, ['pemeliharaan', 'maintenance', 'perbaikan'])) {
                $room->status = 'tersedia';
            }
            $room->updateStatus();
        }

        return redirect()->route('owner.rooms.show', $room)
            ->with('success', 'Data kamar berhasil diperbarui.');
    }

    public function destroy(Room $room)
    {
        if ($room->activeStays()->exists()) {
            return back()->withErrors(['error' => 'Kamar tidak dapat dihapus karena masih ada penghuni aktif.']);
        }

        $room->delete();

        return redirect()->route('owner.rooms.index')
            ->with('success', 'Kamar berhasil dihapus.');
    }
}

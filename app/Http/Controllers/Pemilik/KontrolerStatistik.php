<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Report;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class KontrolerStatistik extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['user', 'category', 'room'])->orderByDesc('created_at');

        // Filter
        if ($request->filled('status')) {
            $status = $request->status;
            $query->where(function($q) use ($status) {
                $q->where('status', $status);
                if ($status === 'pending') $q->orWhere('status', 'menunggu');
                if ($status === 'in_progress') $q->orWhere('status', 'diproses');
                if ($status === 'completed') $q->orWhere('status', 'selesai');
                if ($status === 'cancelled') $q->orWhere('status', 'dibatalkan');
            });
        }

        if ($request->filled('category_id') || $request->filled('kategori_id')) {
            $catId = $request->input('kategori_id', $request->input('category_id'));
            $query->where('kategori_id', $catId);
        }

        if ($request->filled('priority') || $request->filled('prioritas')) {
            $prioritas = $request->input('prioritas', $request->input('priority'));
            $query->where(function($q) use ($prioritas) {
                $q->where('prioritas', $prioritas);
                if ($prioritas === 'high') $q->orWhere('prioritas', 'tinggi');
                if ($prioritas === 'medium') $q->orWhere('prioritas', 'sedang');
                if ($prioritas === 'low') $q->orWhere('prioritas', 'rendah');
            });
        }

        if ($request->filled('user_id') || $request->filled('pengguna_id')) {
            $userId = $request->input('pengguna_id', $request->input('user_id'));
            $query->where('pengguna_id', $userId);
        }

        if ($request->filled('room_id') || $request->filled('kamar_id')) {
            $kamarId = $request->input('kamar_id', $request->input('room_id'));
            $query->where('kamar_id', $kamarId);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $reports = $query->get();

        // Ringkasan
        $summary = [
            'total'       => $reports->count(),
            'pending'     => $reports->whereIn('status', ['menunggu', 'pending'])->count(),
            'in_progress' => $reports->whereIn('status', ['diproses', 'in_progress'])->count(),
            'completed'   => $reports->whereIn('status', ['selesai', 'completed'])->count(),
            'cancelled'   => $reports->whereIn('status', ['dibatalkan', 'cancelled'])->count(),
            'high'        => $reports->whereIn('prioritas', ['tinggi', 'high'])->count(),
            'medium'      => $reports->whereIn('prioritas', ['sedang', 'medium'])->count(),
            'low'         => $reports->whereIn('prioritas', ['rendah', 'low'])->count(),
        ];

        // Data grafik per kategori
        $byCategory = $reports->groupBy(fn($r) => $r->category?->nama ?? $r->category?->name ?? 'Lainnya')
            ->map(fn($g) => $g->count());

        // Data grafik per bulan
        $byMonth = $reports->groupBy(fn($r) => $r->created_at->format('Y-m'))
            ->map(fn($g) => $g->count())
            ->sortKeys();

        $categories = Category::active()->orderBy('urutan')->get();
        $rooms = Room::orderBy('nomor_kamar')->get();
        $tenants = User::whereIn('peran', ['penghuni', 'tenant'])->orderBy('nama')->get();

        return view('owner.statistics.index', compact(
            'reports', 'summary', 'byCategory', 'byMonth',
            'categories', 'rooms', 'tenants'
        ));
    }

    public function exportPdf(Request $request)
    {
        return app(KontrolerLaporan::class)->exportPdf($request);
    }
}

<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Report;

class KontrolerDasbor extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalReports      = Report::where('pengguna_id', $user->id)->count();
        $pendingReports    = Report::where('pengguna_id', $user->id)->whereIn('status', ['menunggu', 'pending'])->count();
        $inProgressReports = Report::where('pengguna_id', $user->id)->whereIn('status', ['diproses', 'in_progress'])->count();
        $completedReports  = Report::where('pengguna_id', $user->id)->whereIn('status', ['selesai', 'completed'])->count();

        $recentReports = Report::where('pengguna_id', $user->id)
            ->with(['category', 'responses' => fn($q) => $q->latest()->limit(1)])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Laporan dengan tanggapan terbaru
        $respondedReports = Report::where('pengguna_id', $user->id)
            ->whereHas('responses')
            ->with(['category', 'responses' => fn($q) => $q->latest()->limit(1)])
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get();

        $activeStay = $user->activeStay()->with('room')->first();

        $unreadNotifCount = AppNotification::where('pengguna_id', $user->id)
            ->where('is_dibaca', false)
            ->count();

        return view('tenant.dashboard', compact(
            'user', 'totalReports', 'pendingReports', 'inProgressReports', 'completedReports',
            'recentReports', 'respondedReports', 'activeStay', 'unreadNotifCount'
        ));
    }
}

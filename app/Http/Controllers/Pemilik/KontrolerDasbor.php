<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Category;
use App\Models\Report;
use App\Models\Room;
use App\Models\TenantStay;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class KontrolerDasbor extends Controller
{
    public function index()
    {
        // Statistik penghuni & kamar
        $totalActiveTenants = User::whereIn('peran', ['penghuni', 'tenant'])
            ->where('is_aktif', true)
            ->whereHas('activeStay')
            ->count();

        $totalRooms = Room::count();
        $availableRooms = Room::whereIn('status', ['tersedia', 'available'])->count();
        $occupiedRooms = Room::whereIn('status', ['terisi', 'occupied', 'penuh', 'full'])->count();
        $maintenanceRooms = Room::whereIn('status', ['pemeliharaan', 'maintenance'])->count();

        // Statistik laporan
        $totalReports    = Report::count();
        $pendingReports  = Report::whereIn('status', ['menunggu', 'pending'])->count();
        $inProgressReports = Report::whereIn('status', ['diproses', 'in_progress'])->count();
        $completedReports  = Report::whereIn('status', ['selesai', 'completed'])->count();
        $cancelledReports  = Report::whereIn('status', ['dibatalkan', 'cancelled'])->count();
        $highPriorityReports = Report::whereIn('prioritas', ['tinggi', 'high'])
            ->whereIn('status', ['menunggu', 'pending', 'diproses', 'in_progress'])
            ->count();

        // Laporan terbaru
        $recentReports = Report::with(['user', 'category', 'room'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Laporan prioritas tinggi
        $highPriorityList = Report::with(['user', 'category', 'room'])
            ->whereIn('prioritas', ['tinggi', 'high'])
            ->whereIn('status', ['menunggu', 'pending', 'diproses', 'in_progress'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Data grafik: laporan per kategori
        $reportsByCategory = Category::withCount('reports')
            ->orderByDesc('reports_count')
            ->get()
            ->map(fn($c) => ['name' => $c->nama ?? $c->name, 'count' => $c->reports_count]);

        // Data grafik: laporan per status
        $reportsByStatus = [
            ['name' => 'Menunggu',   'count' => $pendingReports,    'color' => '#f59e0b'],
            ['name' => 'Diproses',   'count' => $inProgressReports, 'color' => '#3b82f6'],
            ['name' => 'Selesai',    'count' => $completedReports,  'color' => '#10b981'],
            ['name' => 'Dibatalkan', 'count' => $cancelledReports,  'color' => '#6b7280'],
        ];

        // Data grafik: laporan per bulan (6 bulan terakhir)
        $reportsByMonth = Report::where('created_at', '>=', now()->subMonths(6))
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn($r) => $r->created_at->format('Y-m'))
            ->map(function ($group, $yearMonth) {
                [$year, $month] = explode('-', $yearMonth);
                return [
                    'label' => \Carbon\Carbon::createFromDate((int)$year, (int)$month, 1)->locale('id')->isoFormat('MMM YY'),
                    'count' => $group->count(),
                ];
            })
            ->values();

        // Notifikasi belum dibaca untuk owner
        $unreadNotifCount = AppNotification::where('pengguna_id', auth()->id())
            ->where('is_dibaca', false)
            ->count();

        return view('owner.dashboard', compact(
            'totalActiveTenants', 'totalRooms', 'availableRooms', 'occupiedRooms',
            'totalReports', 'pendingReports', 'inProgressReports', 'completedReports',
            'highPriorityReports', 'recentReports', 'highPriorityList',
            'reportsByCategory', 'reportsByStatus', 'reportsByMonth', 'unreadNotifCount'
        ));
    }
}

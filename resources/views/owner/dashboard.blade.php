@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Pemilik Kos')



@section('content')
<div class="page-header d-flex align-items-start justify-content-between">
    <div>
        <h1>Dashboard</h1>
        <p class="text-muted mb-0" style="font-size:13px;">Selamat datang, {{ auth()->user()->name }}! Berikut ringkasan sistem kos Anda.</p>
    </div>
    <div class="text-end d-none d-md-block">
        <div style="font-size:13px;color:#64748b;">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</div>
    </div>
</div>

<!-- Stat Cards Row 1: Penghuni & Kamar -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Penghuni Aktif</div>
                    <div class="stat-value" style="color:#2563EB;">{{ $totalActiveTenants }}</div>
                </div>
                <div class="stat-icon" style="background:#dbeafe;color:#2563EB;"><i class="bi bi-people-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Total Kamar</div>
                    <div class="stat-value" style="color:#0f172a;">{{ $totalRooms }}</div>
                </div>
                <div class="stat-icon" style="background:#f1f5f9;color:#64748b;"><i class="bi bi-door-open-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Kamar Terisi</div>
                    <div class="stat-value" style="color:#f59e0b;">{{ $occupiedRooms }}</div>
                </div>
                <div class="stat-icon" style="background:#fef3c7;color:#f59e0b;"><i class="bi bi-house-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Kamar Kosong</div>
                    <div class="stat-value" style="color:#10b981;">{{ $availableRooms }}</div>
                </div>
                <div class="stat-icon" style="background:#d1fae5;color:#10b981;"><i class="bi bi-door-closed-fill"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards Row 2: Laporan -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Total Laporan</div>
                    <div class="stat-value" style="color:#0f172a;">{{ $totalReports }}</div>
                </div>
                <div class="stat-icon" style="background:#f1f5f9;color:#64748b;"><i class="bi bi-file-earmark-text-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="cursor:pointer;" onclick="location.href='{{ route('owner.reports.index', ['status'=>'pending']) }}'">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Menunggu</div>
                    <div class="stat-value" style="color:#f59e0b;">{{ $pendingReports }}</div>
                </div>
                <div class="stat-icon" style="background:#fef3c7;color:#f59e0b;"><i class="bi bi-hourglass-split"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="cursor:pointer;" onclick="location.href='{{ route('owner.reports.index', ['status'=>'in_progress']) }}'">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Diproses</div>
                    <div class="stat-value" style="color:#3b82f6;">{{ $inProgressReports }}</div>
                </div>
                <div class="stat-icon" style="background:#dbeafe;color:#3b82f6;"><i class="bi bi-arrow-repeat"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="cursor:pointer;" onclick="location.href='{{ route('owner.reports.index', ['status'=>'completed']) }}'">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Selesai</div>
                    <div class="stat-value" style="color:#10b981;">{{ $completedReports }}</div>
                </div>
                <div class="stat-icon" style="background:#d1fae5;color:#10b981;"><i class="bi bi-check-circle-fill"></i></div>
            </div>
        </div>
    </div>
</div>

@if($highPriorityReports > 0)
<div class="alert alert-danger d-flex align-items-center gap-3 mb-4" style="border-radius:12px;border:none;background:#fef2f2;">
    <div style="width:40px;height:40px;background:#fee2e2;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:18px;"></i>
    </div>
    <div>
        <strong>Perhatian!</strong> Terdapat <strong>{{ $highPriorityReports }}</strong> laporan prioritas tinggi yang belum diselesaikan.
        <a href="{{ route('owner.reports.index', ['priority' => 'high', 'status' => 'pending']) }}" class="alert-link ms-1">Lihat sekarang →</a>
    </div>
</div>
@endif

<div class="row g-4 mb-4">
    <!-- Chart: Laporan per Kategori -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-pie-chart text-primary"></i>
                Laporan per Kategori
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="padding:24px;">
                <canvas id="categoryChart" style="max-height:220px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart: Status -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-donut text-primary"></i>
                Status Laporan
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="padding:24px;">
                <canvas id="statusChart" style="max-height:220px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart: Per Bulan -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-bar-chart-line text-primary"></i>
                Laporan 6 Bulan Terakhir
            </div>
            <div class="card-body" style="padding:24px;">
                <canvas id="monthChart" style="max-height:220px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Laporan Terbaru -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-clock-history me-2 text-primary"></i>Laporan Terbaru</span>
                <a href="{{ route('owner.reports.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @if($recentReports->isEmpty())
                <div class="empty-state py-5">
                    <i class="bi bi-inbox d-block text-center" style="font-size:48px;color:#cbd5e1;"></i>
                    <p class="text-center text-muted mt-3 mb-0">Belum ada laporan</p>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Laporan</th>
                                <th>Status</th>
                                <th>Prioritas</th>
                                <th class="pe-4">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentReports as $report)
                            <tr style="cursor:pointer;" onclick="location.href='{{ route('owner.reports.show', $report) }}'">
                                <td class="ps-4">
                                    <div style="font-weight:600;font-size:13px;">{{ Str::limit($report->title, 35) }}</div>
                                    <div style="font-size:11px;color:#64748b;">{{ $report->user?->name ?? '(Akun Dihapus)' }} · {{ $report->category?->name ?? '-' }}</div>
                                </td>
                                <td><span class="badge bg-{{ $report->status_badge }}">{{ $report->status_label }}</span></td>
                                <td><span class="badge bg-{{ $report->priority_badge }}">{{ $report->priority_label }}</span></td>
                                <td class="pe-4" style="font-size:12px;color:#64748b;white-space:nowrap;">{{ $report->created_at->locale('id')->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Prioritas Tinggi -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Prioritas Tinggi</span>
                <a href="{{ route('owner.reports.index', ['priority'=>'high']) }}" class="btn btn-sm btn-outline-danger">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @if($highPriorityList->isEmpty())
                <div class="empty-state py-5">
                    <i class="bi bi-check-circle d-block text-center" style="font-size:40px;color:#d1fae5;"></i>
                    <p class="text-center text-muted mt-3 mb-0" style="font-size:13px;">Tidak ada laporan prioritas tinggi yang aktif</p>
                </div>
                @else
                @foreach($highPriorityList as $report)
                <a href="{{ route('owner.reports.show', $report) }}" class="d-flex align-items-start gap-3 p-3 border-bottom text-decoration-none" style="color:inherit;transition:background .15s;" onmouseover="this.style.background='#fff8f8'" onmouseout="this.style.background=''">
                    <div style="width:36px;height:36px;background:#fee2e2;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-exclamation-circle-fill text-danger" style="font-size:16px;"></i>
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div style="font-weight:600;font-size:13px;">{{ Str::limit($report->title, 40) }}</div>
                        <div style="font-size:11px;color:#64748b;">{{ $report->user?->name ?? '(Akun Dihapus)' }} · Kamar {{ $report->room?->room_number ?? '-' }}</div>
                        <span class="badge bg-{{ $report->status_badge }} mt-1" style="font-size:10px;">{{ $report->status_label }}</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted" style="font-size:12px;flex-shrink:0;margin-top:2px;"></i>
                </a>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const chartDefaults = {
    font: { family: 'Inter', size: 12 },
    plugins: { legend: { labels: { font: { family: 'Inter', size: 12 }, padding: 12 } } }
};

// Chart Kategori
const catData = @json($reportsByCategory);
new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: catData.map(d => d.name),
        datasets: [{
            data: catData.map(d => d.count),
            backgroundColor: ['#2563EB','#10b981','#f59e0b','#ef4444','#8b5cf6','#6b7280'],
            borderWidth: 2, borderColor: '#fff'
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        cutout: '65%',
        plugins: { legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 11 }, padding: 10 } } }
    }
});

// Chart Status
const statusData = @json($reportsByStatus);
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusData.map(d => d.name),
        datasets: [{
            data: statusData.map(d => d.count),
            backgroundColor: statusData.map(d => d.color),
            borderWidth: 2, borderColor: '#fff'
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        cutout: '65%',
        plugins: { legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 11 }, padding: 10 } } }
    }
});

// Chart Bulan
const monthData = @json($reportsByMonth);
new Chart(document.getElementById('monthChart'), {
    type: 'bar',
    data: {
        labels: monthData.map(d => d.label),
        datasets: [{
            label: 'Jumlah Laporan',
            data: monthData.map(d => d.count),
            backgroundColor: 'rgba(37,99,235,0.15)',
            borderColor: '#2563EB',
            borderWidth: 2,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: 'Inter', size: 11 } }, grid: { color: '#f1f5f9' } },
            x: { ticks: { font: { family: 'Inter', size: 11 } }, grid: { display: false } }
        }
    }
});
</script>
@endpush

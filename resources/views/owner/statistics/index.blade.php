@extends('layouts.app')

@section('title', 'Statistik & Laporan Pengaduan')
@section('page-title', 'Statistik & Rekapitulasi')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>Statistik & Rekapitulasi Laporan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Statistik</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('owner.statistics.export-pdf', request()->query()) }}" class="btn btn-outline-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i> Unduh Rekap PDF
        </a>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('owner.statistics.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Diproses</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Prioritas</label>
                <select name="priority" class="form-select">
                    <option value="">Semua Prioritas</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Tinggi</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Sedang</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Rendah</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100" title="Filter"><i class="bi bi-filter"></i></button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value text-primary">{{ $summary['total'] }}</div>
                    <div class="stat-label">Total Laporan</div>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-journal-text"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value text-warning">{{ $summary['pending'] }}</div>
                    <div class="stat-label">Menunggu (Pending)</div>
                </div>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value text-info">{{ $summary['in_progress'] }}</div>
                    <div class="stat-label">Sedang Diproses</div>
                </div>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="bi bi-gear"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value text-success">{{ $summary['completed'] }}</div>
                    <div class="stat-label">Laporan Selesai</div>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-check2-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-pie-chart me-2"></i>Komposisi Kategori Masalah</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 280px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-bar-chart-line me-2"></i>Tren Laporan per Bulan</h5>
            </div>
            <div class="card-body" style="min-height: 280px;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Data Table Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="bi bi-table me-2"></i>Daftar Laporan Terfilter ({{ $reports->count() }})</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>No. Laporan</th>
                    <th>Pelapor</th>
                    <th>Kategori</th>
                    <th>Kamar</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $rep)
                <tr>
                    <td><span class="font-monospace fw-semibold">{{ $rep->report_number }}</span></td>
                    <td>{{ $rep->user?->name ?? '(Akun Dihapus)' }}</td>
                    <td>
                        <span class="badge" style="background-color: {{ $rep->category?->color ?? '#2563EB' }}15; color: {{ $rep->category?->color ?? '#2563EB' }}; border: 1px solid {{ $rep->category?->color ?? '#2563EB' }}40;">
                            {{ $rep->category?->name ?? '-' }}
                        </span>
                    </td>
                    <td>{{ $rep->room ? 'Kamar ' . $rep->room->room_number : '-' }}</td>
                    <td>
                        @if($rep->priority === 'high')
                            <span class="badge bg-danger">Tinggi</span>
                        @elseif($rep->priority === 'medium')
                            <span class="badge bg-warning text-dark">Sedang</span>
                        @else
                            <span class="badge bg-secondary">Rendah</span>
                        @endif
                    </td>
                    <td>
                        @if($rep->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($rep->status === 'in_progress')
                            <span class="badge bg-info text-dark">Diproses</span>
                        @elseif($rep->status === 'completed')
                            <span class="badge bg-success">Selesai</span>
                        @else
                            <span class="badge bg-danger">Dibatalkan</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $rep->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        Tidak ada data laporan pada filter ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Category Chart
        const catCtx = document.getElementById('categoryChart').getContext('2d');
        const catData = @json($byCategory);
        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(catData),
                datasets: [{
                    data: Object.values(catData),
                    backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Monthly Chart
        const monthCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthData = @json($byMonth);
        new Chart(monthCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(monthData),
                datasets: [{
                    label: 'Jumlah Laporan Masuk',
                    data: Object.values(monthData),
                    backgroundColor: '#2563eb',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    });
</script>
@endpush
@endsection

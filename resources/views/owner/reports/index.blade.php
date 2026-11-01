@extends('layouts.app')

@section('title', 'Semua Laporan')
@section('page-title', 'Daftar Laporan Masuk')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>Daftar Laporan & Pengaduan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Laporan</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('owner.reports.export-pdf', request()->query()) }}" class="btn btn-outline-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i> Ekspor PDF
        </a>
    </div>
</div>

@if($activeCategory)
<div class="alert alert-info d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-2">
        <i class="{{ $activeCategory->icon }} fs-5"></i>
        <span>Menampilkan laporan untuk kategori: <strong>{{ $activeCategory->name }}</strong></span>
    </div>
    <a href="{{ route('owner.reports.index') }}" class="btn btn-sm btn-outline-secondary">Hapus Filter Kategori</a>
</div>
@endif

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('owner.reports.index') }}" class="row g-3 align-items-end">
            @if(request('category_slug'))
                <input type="hidden" name="category_slug" value="{{ request('category_slug') }}">
            @endif
            
            <div class="col-md-3">
                <label class="form-label">Cari Laporan</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Judul, No. Laporan, Penghuni..." value="{{ request('search') }}">
                </div>
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
                <label class="form-label">Kamar</label>
                <select name="room_id" class="form-select">
                    <option value="">Semua Kamar</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                            Kamar {{ $room->room_number }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>
                <a href="{{ route('owner.reports.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Report List Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Total: {{ $reports->total() }} Laporan</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>No. Laporan</th>
                    <th>Judul & Penghuni</th>
                    <th>Kategori</th>
                    <th>Kamar</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                <tr>
                    <td>
                        <span class="badge bg-light text-dark border font-monospace">
                            {{ $report->report_number }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $report->title }}</div>
                        <div class="text-muted small">
                            <i class="bi bi-person me-1"></i>{{ $report->user?->name ?? '(Akun Dihapus)' }}
                        </div>
                    </td>
                    <td>
                        <span class="badge" style="background-color: {{ $report->category?->color ?? '#2563EB' }}15; color: {{ $report->category?->color ?? '#2563EB' }}; border: 1px solid {{ $report->category?->color ?? '#2563EB' }}40;">
                            <i class="{{ $report->category?->icon ?? 'bi bi-tag' }} me-1"></i>{{ $report->category?->name ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-secondary border">
                            {{ $report->room ? 'Kamar ' . $report->room->room_number : '-' }}
                        </span>
                    </td>
                    <td>
                        @if($report->priority === 'high')
                            <span class="badge bg-danger-subtle text-danger border border-danger">Tinggi</span>
                        @elseif($report->priority === 'medium')
                            <span class="badge bg-warning-subtle text-warning border border-warning">Sedang</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border">Rendah</span>
                        @endif
                    </td>
                    <td>
                        @if($report->status === 'pending')
                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                        @elseif($report->status === 'in_progress')
                            <span class="badge bg-info text-dark"><i class="bi bi-gear me-1"></i>Diproses</span>
                        @elseif($report->status === 'completed')
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                        @else
                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Dibatalkan</span>
                        @endif
                    </td>
                    <td class="text-muted small">
                        {{ $report->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="text-end">
                        <div class="d-inline-flex gap-1">
                            <a href="{{ route('owner.reports.show', $report) }}" class="btn btn-sm btn-outline-primary" title="Detail Laporan">
                                <i class="bi bi-eye me-1"></i> Detail
                            </a>
                            <form action="{{ route('owner.reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan {{ $report->report_number }}?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Laporan">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <h5>Tidak Ada Laporan Ditemukan</h5>
                            <p>Belum ada pengaduan yang sesuai dengan kriteria pencarian atau filter Anda.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reports->hasPages())
    <div class="card-footer bg-transparent py-3">
        {{ $reports->links() }}
    </div>
    @endif
</div>
@endsection

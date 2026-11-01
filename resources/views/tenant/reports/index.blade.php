@extends('layouts.app')

@section('title', 'Laporan Pengaduan Saya')
@section('page-title', 'Daftar Laporan Saya')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>Laporan Pengaduan Saya</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Laporan Saya</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('tenant.reports.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Buat Laporan Baru
        </a>
    </div>
</div>

<!-- Filter & Search Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('tenant.reports.index') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Cari Laporan</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan judul atau nomor laporan..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Diproses</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>
                <a href="{{ route('tenant.reports.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Reports Table Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Total: {{ $reports->total() }} Laporan</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>No. Laporan</th>
                    <th>Judul Masalah</th>
                    <th>Kategori</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Tanggal Dibuat</th>
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
                            <i class="bi bi-calendar-event me-1"></i>Kejadian: {{ \Carbon\Carbon::parse($report->incident_date)->format('d M Y') }}
                        </div>
                    </td>
                    <td>
                        <span class="badge" style="background-color: {{ $report->category->color ?? '#2563EB' }}15; color: {{ $report->category->color ?? '#2563EB' }}; border: 1px solid {{ $report->category->color ?? '#2563EB' }}40;">
                            <i class="{{ $report->category->icon }} me-1"></i>{{ $report->category->name }}
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
                            <a href="{{ route('tenant.reports.show', $report) }}" class="btn btn-sm btn-primary" title="Lihat Detail">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            @if($report->isEditable())
                            <a href="{{ route('tenant.reports.edit', $report) }}" class="btn btn-sm btn-outline-secondary" title="Edit Laporan">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endif
                            @if(in_array($report->status, ['pending', 'cancelled', 'menunggu', 'dibatalkan']))
                            <form action="{{ route('tenant.reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan {{ $report->report_number }}?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Laporan">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state py-5">
                            <i class="bi bi-inbox fs-1"></i>
                            <h5>Tidak Ada Laporan</h5>
                            <p>Anda belum pernah membuat laporan atau tidak ada laporan yang cocok dengan filter.</p>
                            <a href="{{ route('tenant.reports.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-lg me-1"></i> Buat Laporan Sekarang
                            </a>
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

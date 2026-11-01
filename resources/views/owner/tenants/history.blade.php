@extends('layouts.app')

@section('title', 'Riwayat Penghuni Kos')
@section('page-title', 'Riwayat Huni Kos')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>Riwayat Huni Penghuni Kos</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('owner.tenants.index') }}">Penghuni</a></li>
                <li class="breadcrumb-item active" aria-current="page">Riwayat</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('owner.tenants.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Data Penghuni Aktif
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Semua Catatan Riwayat Huni ({{ $stays->total() }})</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Penghuni</th>
                    <th>Kamar</th>
                    <th>Tanggal Masuk</th>
                    <th>Tanggal Keluar</th>
                    <th>Jml. Penghuni</th>
                    <th>Durasi Tinggal</th>
                    <th>Status Huni</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stays as $stay)
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">{{ $stay->user?->name ?? '(Akun Dihapus)' }}</div>
                        <small class="text-muted">{{ $stay->user?->phone ?? $stay->user?->email ?? '-' }}</small>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-door-open me-1"></i>Kamar {{ $stay->room->room_number ?? '-' }}
                        </span>
                        <div class="text-muted" style="font-size: 11px;">Lantai {{ $stay->room->floor ?? '1' }}</div>
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($stay->move_in_date)->format('d M Y') }}
                    </td>
                    <td>
                        @if($stay->move_out_date)
                            {{ \Carbon\Carbon::parse($stay->move_out_date)->format('d M Y') }}
                        @else
                            <span class="text-success fw-bold">Masih Menghuni</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-info-subtle text-info border border-info-subtle">
                            <i class="bi bi-people-fill me-1"></i>{{ $stay->jumlah_penghuni ?? 1 }} orang
                        </span>
                    </td>
                    <td>
                        <span class="small fw-semibold text-dark">{{ $stay->formatted_duration }}</span>
                    </td>
                    <td>
                        @if($stay->is_active)
                            <span class="badge bg-success-subtle text-success border border-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border">Selesai Huni</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-muted small">{{ $stay->notes ?? '-' }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="bi bi-clock-history"></i>
                            <h5>Belum Ada Riwayat Huni</h5>
                            <p>Catatan riwayat huni penghuni akan tercatat di sini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stays->hasPages())
    <div class="card-footer bg-transparent py-3">
        {{ $stays->links() }}
    </div>
    @endif
</div>
@endsection

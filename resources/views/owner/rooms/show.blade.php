@extends('layouts.app')

@section('title', 'Detail Kamar ' . $room->room_number)
@section('page-title', 'Detail Kamar Kos')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>Kamar {{ $room->room_number }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('owner.rooms.index') }}">Kamar</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $room->room_number }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('owner.rooms.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <a href="{{ route('owner.rooms.edit', $room) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i> Edit Kamar
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Room Overview -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-door-open me-2"></i>Informasi Kamar</h5>
            </div>
            <div class="card-body">
                <div class="text-center py-3">
                    <div class="display-4 fw-bold text-primary mb-1">{{ $room->room_number }}</div>
                    <div class="text-muted">Lantai {{ $room->floor ?? '1' }}</div>
                    <div class="mt-2">
                        @php $activeCount = $room->getActiveTenantsCount(); @endphp
                        @if(in_array($room->status, ['pemeliharaan', 'maintenance']))
                            <span class="badge bg-warning-subtle text-warning border border-warning fs-6">
                                <i class="bi bi-tools me-1"></i>Perbaikan
                            </span>
                        @elseif($activeCount === 0 || in_array($room->status, ['tersedia', 'available']))
                            <span class="badge bg-success-subtle text-success border border-success fs-6">
                                <i class="bi bi-check-circle me-1"></i>Kosong
                            </span>
                        @elseif($activeCount >= $room->capacity || in_array($room->status, ['penuh', 'full']))
                            <span class="badge bg-danger-subtle text-danger border border-danger fs-6">
                                <i class="bi bi-people-fill me-1"></i>Terisi Penuh
                            </span>
                        @else
                            <span class="badge bg-primary-subtle text-primary border border-primary fs-6">
                                <i class="bi bi-person-fill me-1"></i>Terisi
                            </span>
                        @endif
                    </div>
                </div>

                <ul class="list-group list-group-flush small border-top pt-2">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Kapasitas Maksimal</span>
                        <strong>{{ $room->capacity }} Orang</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Penghuni Saat Ini</span>
                        <strong>{{ $activeCount }} Orang</strong>
                    </li>
                    <li class="list-group-item px-0">
                        <span class="text-muted d-block mb-1">Fasilitas / Deskripsi:</span>
                        <p class="mb-0 text-dark">{{ $room->description ?: 'Tidak ada deskripsi tambahan.' }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Right Column: Active Occupants & Stay History -->
    <div class="col-lg-8">
        <!-- Active Occupants -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-people me-2"></i>Penghuni Aktif Saat Ini</h5>
                <span class="badge bg-primary">{{ $room->activeStays->count() }} / {{ $room->capacity }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Penghuni</th>
                            <th>Kontak</th>
                            <th>Tanggal Masuk</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($room->activeStays as $stay)
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark">{{ $stay->user?->name ?? '(Akun Dihapus)' }}</div>
                                <small class="text-muted">{{ $stay->user?->gender === 'male' ? 'Laki-laki' : ($stay->user?->gender === 'female' ? 'Perempuan' : '-') }}</small>
                            </td>
                            <td>
                                <div><i class="bi bi-envelope me-1 text-muted"></i>{{ $stay->user?->email ?? '-' }}</div>
                                <small class="text-muted"><i class="bi bi-whatsapp me-1"></i>{{ $stay->user?->phone ?? '-' }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($stay->move_in_date)->format('d M Y') }}</td>
                            <td class="text-end">
                                @if($stay->user)
                                <a href="{{ route('owner.tenants.show', $stay->user) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Detail Penghuni
                                </a>
                                @else
                                <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="bi bi-person-x fs-2 d-block mb-1"></i>
                                Kamar ini saat ini sedang kosong (tidak ada penghuni).
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Room Stay History -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i>Riwayat Semua Penghuni Kamar Ini</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Penghuni</th>
                            <th>Mulai Huni</th>
                            <th>Selesai Huni</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($room->tenantStays as $stay)
                        <tr>
                            <td>{{ $stay->user?->name ?? '(Akun Dihapus)' }}</td>
                            <td>{{ \Carbon\Carbon::parse($stay->move_in_date)->format('d M Y') }}</td>
                            <td>
                                {{ $stay->move_out_date ? \Carbon\Carbon::parse($stay->move_out_date)->format('d M Y') : 'Saat Ini' }}
                            </td>
                            <td>
                                @if($stay->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Selesai</span>
                                @endif
                            </td>
                            <td><span class="small text-muted">{{ $stay->notes ?? '-' }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">Belum ada riwayat penghuni tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

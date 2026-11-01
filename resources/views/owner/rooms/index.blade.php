@extends('layouts.app')

@section('title', 'Data Kamar Kos')
@section('page-title', 'Manajemen Kamar Kos')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>Data Kamar Kos</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kamar</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('owner.rooms.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kamar
        </a>
    </div>
</div>

<!-- Filter & Search Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('owner.rooms.index') }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Cari Nomor Kamar</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Contoh: 101, A1..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label">Status Kamar</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ in_array(request('status'), ['tersedia', 'available', 'kosong']) ? 'selected' : '' }}>Kosong</option>
                    <option value="terisi" {{ in_array(request('status'), ['terisi', 'occupied', 'penuh']) ? 'selected' : '' }}>Terisi</option>
                    <option value="pemeliharaan" {{ in_array(request('status'), ['pemeliharaan', 'maintenance', 'perbaikan']) ? 'selected' : '' }}>Perbaikan</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>
                <a href="{{ route('owner.rooms.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Rooms Grid / Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Total: {{ $rooms->total() }} Kamar</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>No. Kamar</th>
                    <th>Lantai</th>
                    <th>Kapasitas & Penghuni</th>
                    <th>Status</th>
                    <th>Deskripsi</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rooms as $room)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-light border p-2 text-primary fw-bold fs-5 text-center" style="width: 50px;">
                                {{ $room->room_number }}
                            </div>
                            <div>
                                <a href="{{ route('owner.rooms.show', $room) }}" class="fw-bold text-dark text-decoration-none">
                                    Kamar {{ $room->room_number }}
                                </a>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="text-secondary">Lantai {{ $room->floor ?? '1' }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 6px; width: 80px;">
                                @php
                                    $activeOccupants = $room->getActiveTenantsCount();
                                    $pct = $room->capacity > 0 ? ($activeOccupants / $room->capacity) * 100 : 0;
                                @endphp
                                <div class="progress-bar {{ $pct >= 100 ? 'bg-danger' : ($pct > 0 ? 'bg-primary' : 'bg-success') }}" 
                                     style="width: {{ $pct }}%;"></div>
                            </div>
                            <span class="small fw-semibold">{{ $activeOccupants }} / {{ $room->capacity }} orang</span>
                        </div>
                    </td>
                    <td>
                        @if(in_array($room->status, ['pemeliharaan', 'maintenance']))
                            <span class="badge bg-warning-subtle text-warning border border-warning">
                                <i class="bi bi-tools me-1"></i>Perbaikan
                            </span>
                        @elseif($activeOccupants === 0 || in_array($room->status, ['tersedia', 'available']))
                            <span class="badge bg-success-subtle text-success border border-success">
                                <i class="bi bi-check-circle me-1"></i>Kosong
                            </span>
                        @elseif($activeOccupants >= $room->capacity || in_array($room->status, ['penuh', 'full']))
                            <span class="badge bg-danger-subtle text-danger border border-danger">
                                <i class="bi bi-people-fill me-1"></i>Terisi Penuh
                            </span>
                        @else
                            <span class="badge bg-primary-subtle text-primary border border-primary">
                                <i class="bi bi-person-fill me-1"></i>Terisi
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="text-muted small">{{ Str::limit($room->description, 50) ?? '-' }}</span>
                    </td>
                    <td class="text-end">
                        <div class="d-inline-flex gap-1">
                            <a href="{{ route('owner.rooms.show', $room) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('owner.rooms.edit', $room) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($room->active_stays_count === 0)
                            <form action="{{ route('owner.rooms.destroy', $room) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kamar {{ $room->room_number }}?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="bi bi-door-closed"></i>
                            <h5>Tidak Ada Kamar Ditemukan</h5>
                            <p>Belum ada kamar kos yang didaftarkan dalam sistem.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($rooms->hasPages())
    <div class="card-footer bg-transparent py-3">
        {{ $rooms->links() }}
    </div>
    @endif
</div>
@endsection

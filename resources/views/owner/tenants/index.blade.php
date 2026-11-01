@extends('layouts.app')

@section('title', 'Data Penghuni Kos')
@section('page-title', 'Manajemen Penghuni Kos')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>Data Penghuni Kos</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Penghuni</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('owner.tenants.history') }}" class="btn btn-outline-secondary">
            <i class="bi bi-clock-history me-1"></i> Riwayat Penghuni
        </a>
        <a href="{{ route('owner.tenants.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Penghuni
        </a>
    </div>
</div>

<!-- Filter & Search Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('owner.tenants.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Cari Penghuni</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Nama, email, nomor HP, kamar..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-3">
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

            <div class="col-md-2">
                <label class="form-label">Status Akun</label>
                <select name="is_active" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>
                <a href="{{ route('owner.tenants.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Tenants Table Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Total: {{ $tenants->total() }} Penghuni Terdaftar</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Penghuni</th>
                    <th>Kontak</th>
                    <th>Kamar Saat Ini</th>
                    <th>Tanggal Masuk</th>
                    <th>Status Akun</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; font-size: 14px;">
                                {{ substr($tenant->name, 0, 1) }}
                            </div>
                            <div>
                                <a href="{{ route('owner.tenants.show', $tenant) }}" class="fw-semibold text-dark text-decoration-none">
                                    {{ $tenant->name }}
                                </a>
                                <div class="text-muted small">
                                    {{ $tenant->gender === 'male' ? 'Laki-laki' : ($tenant->gender === 'female' ? 'Perempuan' : '') }}
                                    @if($tenant->username) • <span class="font-monospace">{{ '@' . $tenant->username }}</span> @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div><i class="bi bi-envelope me-1 text-muted"></i>{{ $tenant->email }}</div>
                        <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $tenant->phone ?? '-' }}</div>
                    </td>
                    <td>
                        @if($tenant->activeStay && $tenant->activeStay->room)
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 py-1 px-2">
                                <i class="bi bi-door-open me-1"></i>Kamar {{ $tenant->activeStay->room->room_number }}
                            </span>
                            <div class="mt-1">
                                <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 11px;">
                                    <i class="bi bi-people-fill me-1"></i>{{ $tenant->activeStay->jumlah_penghuni ?? 1 }} orang
                                </span>
                            </div>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border">Tidak ada kamar aktif</span>
                        @endif
                    </td>
                    <td>
                        @if($tenant->activeStay)
                            <span class="small text-dark">{{ \Carbon\Carbon::parse($tenant->activeStay->move_in_date)->format('d M Y') }}</span>
                            <div class="text-muted" style="font-size: 11px;">
                                ({{ \Carbon\Carbon::parse($tenant->activeStay->move_in_date)->diffForHumans() }})
                            </div>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        @if($tenant->is_active)
                            <span class="badge bg-success-subtle text-success border border-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger"><i class="bi bi-slash-circle me-1"></i>Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Opsi
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('owner.tenants.show', $tenant) }}">
                                        <i class="bi bi-eye me-2 text-primary"></i> Detail Lengkap
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('owner.tenants.edit', $tenant) }}">
                                        <i class="bi bi-pencil me-2 text-secondary"></i> Edit Data
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                 <li>
                                    <form action="{{ route('owner.tenants.toggle-active', $tenant) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-warning">
                                            <i class="bi bi-power me-2"></i> {{ $tenant->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                                        </button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button type="button" class="dropdown-item text-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $tenant->id }}">
                                        <i class="bi bi-trash3 me-2"></i> Hapus Akun
                                    </button>
                                </li>
                            </ul>
                        </div>

                        {{-- Modal Hapus Akun (per penghuni) --}}
                        <div class="modal fade" id="deleteModal{{ $tenant->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('owner.tenants.destroy', $tenant) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-content border-danger">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title"><i class="bi bi-exclamation-octagon-fill me-2"></i>Hapus Akun Permanen</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-danger d-flex gap-2 align-items-start mb-3">
                                                <i class="bi bi-exclamation-triangle-fill fs-5 mt-1"></i>
                                                <div>Tindakan ini <strong>tidak dapat dibatalkan</strong>. Akun <strong>{{ $tenant->name }}</strong> akan dihapus permanen dari sistem.</div>
                                            </div>
                                            <label class="form-label fw-semibold">Ketik <span class="text-danger font-monospace">HAPUS</span> untuk konfirmasi:</label>
                                            <input type="text" class="form-control delete-confirm-input" data-target="deleteBtn{{ $tenant->id }}" placeholder="Ketik HAPUS" autocomplete="off">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" id="deleteBtn{{ $tenant->id }}" class="btn btn-danger" disabled>
                                                <i class="bi bi-trash3 me-1"></i> Ya, Hapus
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="bi bi-people"></i>
                            <h5>Tidak Ada Penghuni Ditemukan</h5>
                            <p>Belum ada penghuni kos yang sesuai dengan filter pencarian.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tenants->hasPages())
    <div class="card-footer bg-transparent py-3">
        {{ $tenants->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.querySelectorAll('.delete-confirm-input').forEach(function (input) {
        input.addEventListener('input', function () {
            const btn = document.getElementById(this.dataset.target);
            if (btn) btn.disabled = this.value.trim() !== 'HAPUS';
        });
    });
</script>
@endpush

@endsection

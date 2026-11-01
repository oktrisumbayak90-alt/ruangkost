@extends('layouts.app')

@section('title', 'Detail Penghuni - ' . $tenant->name)
@section('page-title', 'Detail Penghuni')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>{{ $tenant->name }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('owner.tenants.index') }}">Penghuni</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $tenant->name }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('owner.tenants.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <a href="{{ route('owner.tenants.edit', $tenant) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i> Edit Data
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Tenant Profile & Actions -->
    <div class="col-lg-4">
        <!-- Profile Card -->
        <div class="card mb-4 text-center">
            <div class="card-body">
                <div class="avatar bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold fs-2 mb-3 shadow" style="width: 72px; height: 72px;">
                    {{ substr($tenant->name, 0, 1) }}
                </div>
                <h4 class="fw-bold mb-1">{{ $tenant->name }}</h4>
                <div class="text-muted small mb-2">{{ '@' . ($tenant->username ?? 'user' . $tenant->id) }}</div>
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    @if($tenant->is_active)
                        <span class="badge bg-success-subtle text-success border border-success">Akun Aktif</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger">Akun Nonaktif</span>
                    @endif

                    @if($tenant->activeStay)
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                            Kamar {{ $tenant->activeStay->room->room_number ?? '-' }}
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border">Mantan Penghuni</span>
                    @endif
                </div>

                <div class="d-grid gap-2 border-top pt-3">
                    @if($tenant->phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tenant->phone) }}" target="_blank" class="btn btn-success btn-sm">
                        <i class="bi bi-whatsapp me-1"></i> Chat WhatsApp
                    </a>
                    @endif

                    <form action="{{ route('owner.tenants.toggle-active', $tenant) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                            <i class="bi bi-power me-1"></i> {{ $tenant->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                        </button>
                    </form>

                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                        <i class="bi bi-key me-1"></i> Reset Kata Sandi
                    </button>

                    @if($tenant->activeStay)
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#evictModal">
                        <i class="bi bi-box-arrow-right me-1"></i> Keluarkan dari Kos
                    </button>
                    @endif

                    <div class="border-top mt-2 pt-2">
                        <button type="button" class="btn btn-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <i class="bi bi-trash3 me-1"></i> Hapus Akun Permanen
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Info Details Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-card-text me-2"></i>Informasi Lengkap</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Email</span>
                        <span class="fw-semibold">{{ $tenant->email }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">No. Telepon / WA</span>
                        <span class="fw-semibold">{{ $tenant->phone ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">NIK / No. KTP</span>
                        <span class="fw-semibold">{{ $tenant->id_number ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Jenis Kelamin</span>
                        <span class="fw-semibold">
                            {{ $tenant->gender === 'male' ? 'Laki-laki' : ($tenant->gender === 'female' ? 'Perempuan' : '-') }}
                        </span>
                    </li>
                    <li class="list-group-item">
                        <span class="text-muted d-block mb-1">Alamat Asal:</span>
                        <span class="fw-semibold">{{ $tenant->address ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Terdaftar Sejak</span>
                        <span class="fw-semibold">{{ $tenant->created_at->format('d M Y') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Right Column: Stays & Report History -->
    <div class="col-lg-8">
        <!-- Current / Past Stays -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-door-closed me-2"></i>Riwayat Huni Kamar</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Kamar</th>
                            <th>Tanggal Masuk</th>
                            <th>Tanggal Keluar</th>
                            <th>Jml. Penghuni</th>
                            <th>Status Huni</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenant->tenantStays as $stay)
                        <tr>
                            <td>
                                <strong>Kamar {{ $stay->room->room_number ?? '-' }}</strong>
                                <div class="text-muted small">Lantai {{ $stay->room->floor ?? '-' }}</div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($stay->move_in_date)->format('d M Y') }}</td>
                            <td>
                                {{ $stay->move_out_date ? \Carbon\Carbon::parse($stay->move_out_date)->format('d M Y') : 'Saat ini' }}
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info border border-info-subtle">
                                    <i class="bi bi-people-fill me-1"></i>{{ $stay->jumlah_penghuni ?? 1 }} orang
                                </span>
                            </td>
                            <td>
                                @if($stay->is_active)
                                    <span class="badge bg-success">Aktif Menghuni</span>
                                @else
                                    <span class="badge bg-secondary">Selesai Huni</span>
                                @endif
                            </td>
                            <td>
                                <span class="small text-muted">{{ $stay->notes ?? '-' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted">Belum ada riwayat tinggal tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tenant Reports List -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-file-earmark-text me-2"></i>Laporan yang Diajukan ({{ $tenant->reports->count() }})</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No. Laporan</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenant->reports as $rep)
                        <tr>
                            <td><span class="font-monospace small">{{ $rep->report_number }}</span></td>
                            <td><strong>{{ $rep->title }}</strong></td>
                            <td>
                                <span class="badge" style="background-color: {{ $rep->category->color ?? '#2563EB' }}15; color: {{ $rep->category->color ?? '#2563EB' }}; border: 1px solid {{ $rep->category->color ?? '#2563EB' }}40;">
                                    {{ $rep->category->name }}
                                </span>
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
                            <td class="text-end">
                                <a href="{{ route('owner.reports.show', $rep) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Penghuni ini belum pernah mengajukan laporan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Evict Tenant -->
@if($tenant->activeStay)
<div class="modal fade" id="evictModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('owner.tenants.evict', $tenant) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Keluarkan Penghuni</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tindakan ini akan mengakhiri masa sewa kamar <strong>Kamar {{ $tenant->activeStay->room->room_number }}</strong> dan menonaktifkan akun login penghuni.</p>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Keluar <span class="text-danger">*</span></label>
                        <input type="date" name="move_out_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan / Alasan Keluar</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Selesai kontrak, pindah kota, dll..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Keluarkan Penghuni</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Modal Reset Password -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('owner.tenants.reset-password', $tenant) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-key me-2"></i>Reset Kata Sandi Penghuni</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Masukkan kata sandi baru untuk akun <strong>{{ $tenant->name }}</strong>. Penghuni akan diwajibkan mengganti kata sandi setelah login.</p>
                    <div class="mb-3">
                        <label class="form-label">Kata Sandi Baru <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" class="form-control" minlength="8" placeholder="Minimal 8 karakter" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kata Sandi Baru</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Modal Hapus Akun Permanen -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
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
                    <div class="alert alert-danger d-flex align-items-start gap-2 mb-3">
                        <i class="bi bi-exclamation-triangle-fill fs-5 mt-1"></i>
                        <div>
                            <strong>Peringatan!</strong> Tindakan ini <strong>tidak dapat dibatalkan</strong>.
                            Seluruh data akun akan dihapus secara permanen dari sistem.
                        </div>
                    </div>
                    <p>Anda akan menghapus akun penghuni:</p>
                    <ul class="list-unstyled ms-2">
                        <li><i class="bi bi-person-fill me-2 text-danger"></i><strong>{{ $tenant->name }}</strong></li>
                        <li><i class="bi bi-envelope-fill me-2 text-danger"></i>{{ $tenant->email }}</li>
                    </ul>
                    <p class="text-muted small mt-2">
                        Jika penghuni masih memiliki kamar aktif, kamar tersebut akan otomatis dikosongkan.
                        Riwayat laporan yang pernah diajukan juga akan ikut terhapus.
                    </p>
                    <div class="mb-0 mt-3">
                        <label class="form-label fw-semibold">Ketik <span class="text-danger font-monospace">HAPUS</span> untuk konfirmasi:</label>
                        <input type="text" id="deleteConfirmInput" class="form-control" placeholder="Ketik HAPUS" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="deleteConfirmBtn" class="btn btn-danger" disabled>
                        <i class="bi bi-trash3 me-1"></i> Ya, Hapus Permanen
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('deleteConfirmInput')?.addEventListener('input', function () {
        const btn = document.getElementById('deleteConfirmBtn');
        btn.disabled = this.value.trim() !== 'HAPUS';
    });
</script>
@endpush

@endsection

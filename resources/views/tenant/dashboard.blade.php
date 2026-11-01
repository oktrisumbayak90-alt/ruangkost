@extends('layouts.app')

@section('title', 'Dashboard Penghuni')
@section('page-title', 'Dashboard Penghuni')

@section('content')
<!-- Welcome Greeting Card -->
<div class="card mb-4" style="background: #ffffff; border-left: 4px solid var(--primary);">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary-subtle text-primary fw-semibold px-2.5 py-1">Penghuni Kos</span>
                    @if($activeStay && $activeStay->room)
                    <span class="badge bg-light text-secondary border">Kamar {{ $activeStay->room->room_number }}</span>
                    @endif
                </div>
                <h3 class="fw-bold mb-1" style="color: #0f172a; font-size: 20px;">Selamat Datang, {{ $user->name }}!</h3>
                <p class="mb-0 text-muted" style="font-size: 13.5px;">
                    @if($activeStay && $activeStay->room)
                        Anda saat ini terdaftar aktif di <strong>Kamar {{ $activeStay->room->room_number }}</strong> (Lantai {{ $activeStay->room->floor ?? '1' }}). Sampaikan kendala fasilitas atau keluhan kos dengan mudah dan transparan.
                    @else
                        Sampaikan kendala fasilitas, kebersihan, atau keluhan lingkungan kos Anda secara langsung melalui sistem ini.
                    @endif
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('tenant.reports.create') }}" class="btn btn-primary px-3 py-2 fw-medium shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Buat Laporan Baru
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value text-primary">{{ $totalReports }}</div>
                    <div class="stat-label">Total Laporan Saya</div>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value text-warning">{{ $pendingReports }}</div>
                    <div class="stat-label">Menunggu Tanggapan</div>
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
                    <div class="stat-value text-info">{{ $inProgressReports }}</div>
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
                    <div class="stat-value text-success">{{ $completedReports }}</div>
                    <div class="stat-label">Laporan Selesai</div>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Recent Reports -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i>Laporan Terbaru Saya</h5>
                <a href="{{ route('tenant.reports.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No. Laporan</th>
                            <th>Judul & Kategori</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentReports as $rep)
                        <tr>
                            <td><span class="font-monospace small">{{ $rep->report_number }}</span></td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $rep->title }}</div>
                                <span class="badge" style="background-color: {{ $rep->category->color ?? '#2563EB' }}15; color: {{ $rep->category->color ?? '#2563EB' }}; font-size: 11px;">
                                    <i class="{{ $rep->category->icon }} me-1"></i>{{ $rep->category->name }}
                                </span>
                            </td>
                            <td>
                                @if($rep->status === 'pending')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                @elseif($rep->status === 'in_progress')
                                    <span class="badge bg-info text-dark"><i class="bi bi-gear me-1"></i>Diproses</span>
                                @elseif($rep->status === 'completed')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Dibatalkan</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $rep->created_at->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('tenant.reports.show', $rep) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state py-4">
                                    <i class="bi bi-card-checklist fs-2"></i>
                                    <h5>Belum Ada Laporan</h5>
                                    <p>Anda belum pernah membuat laporan keluhan atau pengaduan.</p>
                                    <a href="{{ route('tenant.reports.create') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="bi bi-plus-lg me-1"></i> Buat Laporan Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Room Info & Recent Owner Responses -->
    <div class="col-lg-4">
        <!-- Room Info Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-house-door me-2"></i>Info Kamar Anda</h5>
            </div>
            <div class="card-body">
                @if($activeStay && $activeStay->room)
                    <div class="text-center py-2 mb-3">
                        <div class="display-5 fw-bold text-primary mb-1">
                            {{ $activeStay->room->room_number }}
                        </div>
                        <div class="badge bg-light text-dark border">Lantai {{ $activeStay->room->floor ?? '1' }}</div>
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Tanggal Mulai Huni</span>
                            <strong>{{ \Carbon\Carbon::parse($activeStay->move_in_date)->format('d M Y') }}</strong>
                        </li>
                        <li class="list-group-item px-0">
                            <span class="text-muted d-block mb-1">Fasilitas Kamar:</span>
                            <span class="text-dark">{{ $activeStay->room->description ?: 'Standar kos.' }}</span>
                        </li>
                    </ul>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-info-circle fs-3 d-block mb-1"></i>
                        Tidak ada penempatan kamar aktif. Hubungi pemilik kos untuk verifikasi.
                    </div>
                @endif
            </div>
        </div>

        <!-- Latest Responses from Owner -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-chat-quote me-2"></i>Tanggapan Pemilik Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <div class="p-3">
                    @forelse($respondedReports as $rep)
                    <div class="p-3 rounded bg-light border mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="small text-truncate" style="max-width: 170px;">{{ $rep->title }}</strong>
                            <span class="badge bg-secondary" style="font-size: 10px;">{{ $rep->report_number }}</span>
                        </div>
                        @if($rep->responses->isNotEmpty())
                        <p class="small text-muted mb-2 fst-italic">
                            "{{ Str::limit($rep->responses->first()->response, 70) }}"
                        </p>
                        @endif
                        <a href="{{ route('tenant.reports.show', $rep) }}" class="btn btn-sm btn-link p-0 text-decoration-none small">
                            Lihat Diskusi &rarr;
                        </a>
                    </div>
                    @empty
                    <div class="text-center py-3 text-muted small">
                        Belum ada tanggapan baru dari pemilik kos.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

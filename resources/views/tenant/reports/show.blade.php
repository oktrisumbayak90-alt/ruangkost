@extends('layouts.app')

@section('title', 'Detail Laporan #' . $report->report_number)
@section('page-title', 'Detail Laporan #' . $report->report_number)

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>Laporan #{{ $report->report_number }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tenant.reports.index') }}">Laporan Saya</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $report->report_number }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('tenant.reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        @if($report->isEditable())
        <a href="{{ route('tenant.reports.edit', $report) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i> Edit Laporan
        </a>
        @endif
        @if($report->isCancellable())
        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#cancelModal">
            <i class="bi bi-x-circle me-1"></i> Batalkan Laporan
        </button>
        @endif
        @if(in_array($report->status, ['pending', 'cancelled', 'menunggu', 'dibatalkan']))
        <form action="{{ route('tenant.reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan {{ $report->report_number }}?');" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash me-1"></i> Hapus Laporan
            </button>
        </form>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Details & Owner Responses -->
    <div class="col-lg-8">
        <!-- Main Report Content -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge" style="background-color: {{ $report->category->color ?? '#2563EB' }}15; color: {{ $report->category->color ?? '#2563EB' }}; border: 1px solid {{ $report->category->color ?? '#2563EB' }}40;">
                        <i class="{{ $report->category->icon }} me-1"></i>{{ $report->category->name }}
                    </span>
                    <span class="text-muted small">• Dibuat {{ $report->created_at->diffForHumans() }}</span>
                </div>
                <div>
                    @if($report->status === 'pending')
                        <span class="badge bg-warning text-dark fs-6"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                    @elseif($report->status === 'in_progress')
                        <span class="badge bg-info text-dark fs-6"><i class="bi bi-gear me-1"></i>Sedang Diproses</span>
                    @elseif($report->status === 'completed')
                        <span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                    @else
                        <span class="badge bg-danger fs-6"><i class="bi bi-x-circle me-1"></i>Dibatalkan</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <h3 class="fw-bold mb-3">{{ $report->title }}</h3>

                <div class="row g-3 p-3 bg-light rounded-3 mb-4">
                    <div class="col-sm-4">
                        <span class="text-muted small d-block">Nomor Kamar</span>
                        <strong>{{ $report->room ? 'Kamar ' . $report->room->room_number : 'Fasilitas Umum' }}</strong>
                    </div>
                    <div class="col-sm-4">
                        <span class="text-muted small d-block">Tanggal Kejadian</span>
                        <strong>{{ \Carbon\Carbon::parse($report->incident_date)->format('d M Y') }}</strong>
                    </div>
                    <div class="col-sm-4">
                        <span class="text-muted small d-block">Urgensi / Prioritas</span>
                        @if($report->priority === 'high')
                            <span class="badge bg-danger">Tinggi / Mendesak</span>
                        @elseif($report->priority === 'medium')
                            <span class="badge bg-warning text-dark">Sedang</span>
                        @else
                            <span class="badge bg-secondary">Rendah</span>
                        @endif
                    </div>
                    @if($report->location)
                    <div class="col-12 border-top pt-2 mt-2">
                        <span class="text-muted small d-block">Lokasi:</span>
                        <span><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $report->location }}</span>
                    </div>
                    @endif
                </div>

                <h5 class="fw-bold mb-2">Deskripsi Masalah</h5>
                <p class="text-secondary" style="white-space: pre-line; line-height: 1.6;">{{ $report->description }}</p>

                @if($report->image_path)
                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-bold mb-3"><i class="bi bi-image me-1"></i> Foto Bukti / Lampiran</h6>
                    <img src="{{ asset('storage/' . $report->image_path) }}" 
                         alt="Lampiran" 
                         class="img-fluid rounded border shadow-sm" 
                         style="max-height: 350px; cursor: pointer;"
                         data-bs-toggle="modal" data-bs-target="#imageModal">
                    <div class="small text-muted mt-1"><i class="bi bi-zoom-in me-1"></i>Klik gambar untuk memperbesar</div>

                    <!-- Modal Zoom -->
                    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0">
                                <div class="modal-header">
                                    <h5 class="modal-title">Lampiran Laporan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center p-0">
                                    <img src="{{ asset('storage/' . $report->image_path) }}" alt="Lampiran" class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Responses from Owner -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-chat-left-text me-2"></i>Tanggapan & Komunikasi Pemilik Kos</h5>
            </div>
            <div class="card-body">
                @forelse($report->responses as $resp)
                <div class="p-3 rounded-3 mb-3 {{ $resp->user->isOwner() ? 'bg-primary-subtle border border-primary-subtle' : 'bg-light border' }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-sm {{ $resp->user->isOwner() ? 'bg-primary text-white' : 'bg-secondary text-white' }} rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;font-size:12px;font-weight:700;">
                                {{ substr($resp->user->name, 0, 1) }}
                            </div>
                            <div>
                                <span class="fw-bold">{{ $resp->user->name }}</span>
                                <span class="badge {{ $resp->user->isOwner() ? 'bg-primary' : 'bg-secondary' }} ms-1" style="font-size: 10px;">
                                    {{ $resp->user->isOwner() ? 'Pemilik Kos' : 'Penghuni' }}
                                </span>
                            </div>
                        </div>
                        <span class="text-muted small">{{ $resp->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $resp->response }}</p>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-hourglass-top fs-2 d-block mb-2"></i>
                    Belum ada tanggapan dari pemilik kos. Pemilik akan segera meninjau dan memberi kabar di sini.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: Status Tracker & Timeline -->
    <div class="col-lg-4">
        <!-- Status Tracker Progress -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-check-all me-2"></i>Status Penanganan</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white" style="width: 32px; height: 32px;">
                            <i class="bi bi-check"></i>
                        </div>
                        <div>
                            <div class="fw-bold small">1. Laporan Dibuat</div>
                            <div class="text-muted" style="font-size: 11px;">{{ $report->created_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center {{ in_array($report->status, ['in_progress', 'completed']) ? 'bg-info text-white' : 'bg-light border text-muted' }}" style="width: 32px; height: 32px;">
                            <i class="bi bi-gear"></i>
                        </div>
                        <div>
                            <div class="fw-bold small">2. Peninjauan / Tindak Lanjut</div>
                            <div class="text-muted" style="font-size: 11px;">
                                {{ in_array($report->status, ['in_progress', 'completed']) ? 'Sedang dalam pengerjaan' : 'Menunggu' }}
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center {{ $report->status === 'completed' ? 'bg-success text-white' : ($report->status === 'cancelled' ? 'bg-danger text-white' : 'bg-light border text-muted') }}" style="width: 32px; height: 32px;">
                            <i class="bi bi-{{ $report->status === 'cancelled' ? 'x' : 'check2-all' }}"></i>
                        </div>
                        <div>
                            <div class="fw-bold small">3. {{ $report->status === 'cancelled' ? 'Dibatalkan' : 'Selesai' }}</div>
                            <div class="text-muted" style="font-size: 11px;">
                                {{ $report->status === 'completed' ? 'Kendala telah teratasi' : ($report->status === 'cancelled' ? 'Laporan dibatalkan' : 'Menunggu penyelesaian') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Timeline -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i>Riwayat Perubahan</h5>
            </div>
            <div class="card-body p-3">
                @forelse($report->histories as $history)
                <div class="d-flex gap-2 position-relative mb-3 {{ !$loop->last ? 'border-start ps-3 ms-2' : 'ps-3 ms-2' }}" style="border-color: #cbd5e1!important;">
                    <div class="position-absolute rounded-circle bg-primary" style="left: -5px; top: 5px; width: 9px; height: 9px;"></div>
                    <div>
                        <div class="small fw-semibold">Status: <span class="badge bg-secondary">{{ $history->new_status }}</span></div>
                        <div class="text-muted" style="font-size: 11px;">{{ $history->created_at->format('d M Y, H:i') }}</div>
                        @if($history->note)
                        <div class="small bg-light p-2 rounded mt-1 border text-secondary">
                            {{ $history->note }}
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-muted small">Belum ada riwayat tercatat.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
@if($report->isCancellable())
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('tenant.reports.cancel', $report) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-x-circle me-2"></i>Batalkan Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin membatalkan laporan ini? Laporan yang dibatalkan tidak akan diproses lebih lanjut.</p>
                    <div class="mb-3">
                        <label class="form-label">Alasan Pembatalan (Opsional)</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="Contoh: Masalah sudah bisa teratasi sendiri..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger">Ya, Batalkan Laporan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

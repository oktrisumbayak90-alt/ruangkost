@extends('layouts.app')

@section('title', 'Detail Laporan #' . $report->report_number)
@section('page-title', 'Detail Laporan #' . $report->report_number)

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>Detail Laporan #{{ $report->report_number }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('owner.reports.index') }}">Laporan</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $report->report_number }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('owner.reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <form action="{{ route('owner.reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan {{ $report->report_number }}?');" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash me-1"></i> Hapus Laporan
            </button>
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Report Details & Responses -->
    <div class="col-lg-8">
        <!-- Main Report Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge" style="background-color: {{ $report->category->color ?? '#2563EB' }}15; color: {{ $report->category->color ?? '#2563EB' }}; border: 1px solid {{ $report->category->color ?? '#2563EB' }}40;">
                        <i class="{{ $report->category->icon }} me-1"></i>{{ $report->category->name }}
                    </span>
                    <span class="text-muted small">• Dilaporkan {{ $report->created_at->diffForHumans() }}</span>
                </div>
                <div>
                    @if($report->status === 'pending')
                        <span class="badge bg-warning text-dark fs-6"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                    @elseif($report->status === 'in_progress')
                        <span class="badge bg-info text-dark fs-6"><i class="bi bi-gear me-1"></i>Diproses</span>
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
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted small d-block">Penghuni Pelapor</span>
                        <strong>{{ $report->user->name }}</strong>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted small d-block">Nomor Kamar</span>
                        <strong>{{ $report->room ? 'Kamar ' . $report->room->room_number : 'Umum' }}</strong>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted small d-block">Prioritas</span>
                        @if($report->priority === 'high')
                            <span class="badge bg-danger">Tinggi</span>
                        @elseif($report->priority === 'medium')
                            <span class="badge bg-warning text-dark">Sedang</span>
                        @else
                            <span class="badge bg-secondary">Rendah</span>
                        @endif
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted small d-block">Tanggal Kejadian</span>
                        <strong>{{ \Carbon\Carbon::parse($report->incident_date)->format('d M Y') }}</strong>
                    </div>
                    @if($report->location)
                    <div class="col-12 border-top pt-2 mt-2">
                        <span class="text-muted small d-block">Lokasi Spesifik:</span>
                        <span><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $report->location }}</span>
                    </div>
                    @endif
                </div>

                <h5 class="fw-bold mb-2">Deskripsi Masalah</h5>
                <p class="text-secondary" style="white-space: pre-line; line-height: 1.6;">{{ $report->description }}</p>

                @if($report->image_path)
                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-bold mb-3"><i class="bi bi-image me-1"></i> Foto Bukti / Lampiran</h6>
                    <div class="position-relative d-inline-block">
                        <img src="{{ asset('storage/' . $report->image_path) }}" 
                             alt="Lampiran Laporan" 
                             class="img-fluid rounded border shadow-sm" 
                             style="max-height: 380px; object-fit: cover; cursor: pointer;"
                             data-bs-toggle="modal" data-bs-target="#imageModal">
                        <div class="small text-muted mt-1"><i class="bi bi-zoom-in me-1"></i>Klik gambar untuk memperbesar</div>
                    </div>

                    <!-- Image Zoom Modal -->
                    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0">
                                <div class="modal-header">
                                    <h5 class="modal-title">Lampiran Laporan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

        <!-- Tanggapan & Komunikasi -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-chat-left-dots me-2"></i>Tanggapan & Diskusi ({{ $report->responses->count() }})</h5>
            </div>
            <div class="card-body">
                @if($report->responses->count() > 0)
                <div class="d-flex flex-column gap-3 mb-4">
                    @foreach($report->responses as $resp)
                    <div class="p-3 rounded-3 {{ $resp->user->isOwner() ? 'bg-primary-subtle border border-primary-subtle ms-4' : 'bg-light border me-4' }}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-sm {{ $resp->user->isOwner() ? 'bg-primary text-white' : 'bg-secondary text-white' }} rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;font-size:12px;font-weight:700;">
                                    {{ substr($resp->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <span class="fw-bold">{{ $resp->user->name }}</span>
                                    <span class="badge {{ $resp->user->isOwner() ? 'bg-primary' : 'bg-secondary' }} ms-1" style="font-size: 10px;">
                                        {{ $resp->user->isOwner() ? 'Pemilik' : 'Penghuni' }}
                                    </span>
                                </div>
                            </div>
                            <span class="text-muted small">{{ $resp->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $resp->response }}</p>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-chat-square-text fs-2 d-block mb-2"></i>
                    Belum ada tanggapan untuk laporan ini. Berikan tanggapan untuk memberi kabar kepada penghuni.
                </div>
                @endif

                <!-- Form Berikan Tanggapan -->
                <form action="{{ route('owner.reports.add-response', $report) }}" method="POST" class="border-top pt-3">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tulis Tanggapan Pemilik Kos</label>
                        <textarea name="response" class="form-control @error('response') is-invalid @enderror" rows="3" placeholder="Tulis instruksi, informasi jadwal perbaikan, atau tindak lanjut..." required>{{ old('response') }}</textarea>
                        @error('response')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i> Kirim Tanggapan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Status Update & Timeline History -->
    <div class="col-lg-4">
        <!-- Update Status Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-sliders me-2"></i>Ubah Status Laporan</h5>
            </div>
            <div class="card-body">
                @if($report->status === 'completed')
                <div class="alert alert-success d-flex align-items-center mb-0">
                    <i class="bi bi-check-circle-fill fs-4 me-2"></i>
                    <div>
                        <strong>Laporan Telah Selesai!</strong>
                        <p class="small mb-0">Laporan ini sudah ditutup dan berstatus selesai.</p>
                    </div>
                </div>
                @else
                <form action="{{ route('owner.reports.update-status', $report) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Status Baru</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="">-- Pilih Status --</option>
                            @if($report->status === 'pending')
                                <option value="in_progress">Diproses (Sedang Ditangani)</option>
                                <option value="cancelled">Dibatalkan</option>
                            @elseif($report->status === 'in_progress')
                                <option value="completed">Selesai (Masalah Teratasi)</option>
                                <option value="cancelled">Dibatalkan</option>
                                <option value="pending">Kembalikan ke Pending</option>
                            @elseif($report->status === 'cancelled')
                                <option value="pending">Buka Kembali (Pending)</option>
                            @endif
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Perubahan (Opsional)</label>
                        <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="2" placeholder="Alasan perubahan atau catatan progres...">{{ old('note') }}</textarea>
                        @error('note')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-arrow-repeat me-1"></i> Simpan Status Baru
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- Tenant Contact Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-person-lines-fill me-2"></i>Kontak Penghuni</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:18px;font-weight:700;">
                        {{ substr($report->user->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="fw-bold">{{ $report->user->name }}</div>
                        <div class="text-muted small">{{ $report->user->email }}</div>
                    </div>
                </div>
                <div class="list-group list-group-flush small">
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">No. Telepon / WA</span>
                        <strong>{{ $report->user->phone ?? '-' }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Kamar</span>
                        <strong>{{ $report->room ? 'Kamar ' . $report->room->room_number : '-' }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Status Akun</span>
                        <span class="badge {{ $report->user->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $report->user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
                @if($report->user->phone)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $report->user->phone) }}" target="_blank" class="btn btn-success btn-sm w-100 mt-3">
                    <i class="bi bi-whatsapp me-1"></i> Hubungi via WhatsApp
                </a>
                @endif
            </div>
        </div>

        <!-- Timeline History Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i>Riwayat Status</h5>
            </div>
            <div class="card-body p-0">
                <div class="p-3">
                    @forelse($report->histories as $history)
                    <div class="d-flex gap-3 position-relative mb-3 {{ !$loop->last ? 'border-start ps-3 ms-2' : 'ps-3 ms-2' }}" style="border-color: #cbd5e1!important;">
                        <div class="position-absolute rounded-circle bg-primary" style="left: -6px; top: 4px; width: 11px; height: 11px;"></div>
                        <div>
                            <div class="small fw-bold">
                                Status diubah ke <span class="badge bg-secondary">{{ $history->new_status }}</span>
                            </div>
                            <div class="text-muted" style="font-size: 11px;">
                                Oleh {{ $history->user->name ?? 'Sistem' }} • {{ $history->created_at->format('d M Y, H:i') }}
                            </div>
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
</div>
@endsection

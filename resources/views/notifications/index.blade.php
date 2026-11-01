@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Pusat Notifikasi')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>Pusat Notifikasi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ auth()->user()->isOwner() ? route('owner.dashboard') : route('tenant.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Notifikasi</li>
            </ol>
        </nav>
    </div>
    <div>
        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">
                <i class="bi bi-check2-all me-1"></i> Tandai Semua Sudah Dibaca
            </button>
        </form>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-bell me-2"></i>Daftar Pemberitahuan</h5>
                <span class="text-muted small">Total: {{ $notifications->total() }}</span>
            </div>
            <div class="list-group list-group-flush">
                @forelse($notifications as $notif)
                <div class="list-group-item p-3 {{ !$notif->is_read ? 'bg-primary-subtle bg-opacity-25' : '' }}">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div class="d-flex align-items-start gap-3 flex-grow-1">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mt-1 flex-shrink-0 {{ !$notif->is_read ? 'bg-primary text-white' : 'bg-light border text-muted' }}" style="width: 38px; height: 38px;">
                                <i class="bi bi-{{ $notif->type === 'status_changed' ? 'sliders' : ($notif->type === 'new_response' ? 'chat-dots' : 'bell') }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h6 class="mb-0 fw-bold {{ !$notif->is_read ? 'text-primary' : 'text-dark' }}">{{ $notif->title }}</h6>
                                    @if(!$notif->is_read)
                                        <span class="badge bg-danger" style="font-size: 9px;">Baru</span>
                                    @endif
                                </div>
                                <p class="text-secondary mb-2 small">{{ $notif->message }}</p>
                                <span class="text-muted" style="font-size: 11px;">
                                    <i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }} ({{ $notif->created_at->format('d M Y, H:i') }})
                                </span>
                            </div>
                        </div>
                        @if($notif->url)
                        <div class="flex-shrink-0">
                            <form action="{{ route('notifications.read', $notif) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    Buka <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="empty-state py-5">
                    <i class="bi bi-bell-slash fs-1"></i>
                    <h5>Tidak Ada Notifikasi</h5>
                    <p>Semua pemberitahuan aktivitas laporan dan tanggapan akan muncul di sini.</p>
                </div>
                @endforelse
            </div>
            @if($notifications->hasPages())
            <div class="card-footer bg-transparent py-3">
                {{ $notifications->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

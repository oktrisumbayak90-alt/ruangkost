@extends('layouts.app')

@section('title', 'Buat Laporan Baru')
@section('page-title', 'Form Pengaduan Keluhan')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>Buat Laporan Pengaduan Baru</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tenant.reports.index') }}">Laporan Saya</a></li>
                <li class="breadcrumb-item active" aria-current="page">Buat Baru</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('tenant.reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Batal
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        @if($activeStay && $activeStay->room)
        <div class="alert alert-primary d-flex align-items-center mb-4">
            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
            <div>
                <strong>Penempatan Kamar Aktif:</strong> Laporan ini akan terdata dari <strong>Kamar {{ $activeStay->room->room_number }}</strong> (Lantai {{ $activeStay->room->floor ?? '1' }}).
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Rincian Laporan Masalah</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tenant.reports.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label">Judul Singkat Masalah <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Contoh: Kran air wastafel bocor, Lampu lorong mati" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Kategori Masalah <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tanggal Kejadian <span class="text-danger">*</span></label>
                            <input type="date" name="incident_date" class="form-control @error('incident_date') is-invalid @enderror" value="{{ old('incident_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                            @error('incident_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tingkat Urgensi / Prioritas <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Rendah (Dapat menunggu)</option>
                                <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Sedang (Perlu dicek)</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Tinggi (Mendesak / Darurat)</option>
                            </select>
                            @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Lokasi Spesifik (Opsional)</label>
                            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" placeholder="Contoh: Kamar mandi dalam, Dapur bersama">
                            @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Deskripsi Lengkap Masalah <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5" placeholder="Ceritakan detail masalah yang terjadi, kapan mulai disadari, dan dampaknya secara jelas..." required>{{ old('description') }}</textarea>
                            <small class="text-muted">Minimal 10 karakter.</small>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Lampiran Foto Bukti (Opsional)</label>
                            <input type="file" name="image" id="imageInput" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, atau WebP. Maksimal 2MB.</small>
                            @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <!-- Image Preview Area -->
                            <div id="imagePreviewContainer" class="mt-3 d-none">
                                <label class="form-label d-block text-muted small">Pratinjau Foto:</label>
                                <img id="imagePreview" src="#" alt="Pratinjau" class="img-fluid rounded border shadow-sm" style="max-height: 250px;">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('tenant.reports.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-send-fill me-1"></i> Kirim Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.getElementById('imagePreview');
                const container = document.getElementById('imagePreviewContainer');
                preview.src = event.target.result;
                container.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection

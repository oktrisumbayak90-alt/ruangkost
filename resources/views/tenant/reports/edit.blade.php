@extends('layouts.app')

@section('title', 'Edit Laporan #' . $report->report_number)
@section('page-title', 'Edit Laporan Pengaduan')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>Edit Laporan #{{ $report->report_number }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tenant.reports.index') }}">Laporan Saya</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tenant.reports.show', $report) }}">{{ $report->report_number }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('tenant.reports.show', $report) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Ubah Rincian Laporan</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tenant.reports.update', $report) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label">Judul Singkat Masalah <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $report->title) }}" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Kategori Masalah <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $report->category_id) == $cat->id ? 'selected' : '' }}>
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
                            <input type="date" name="incident_date" class="form-control @error('incident_date') is-invalid @enderror" value="{{ old('incident_date', $report->incident_date) }}" max="{{ date('Y-m-d') }}" required>
                            @error('incident_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tingkat Urgensi / Prioritas <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                <option value="low" {{ old('priority', $report->priority) === 'low' ? 'selected' : '' }}>Rendah</option>
                                <option value="medium" {{ old('priority', $report->priority) === 'medium' ? 'selected' : '' }}>Sedang</option>
                                <option value="high" {{ old('priority', $report->priority) === 'high' ? 'selected' : '' }}>Tinggi</option>
                            </select>
                            @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Lokasi Spesifik (Opsional)</label>
                            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $report->location) }}" placeholder="Contoh: Kamar Mandi">
                            @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Deskripsi Lengkap Masalah <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5" required>{{ old('description', $report->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Ganti Foto Bukti (Opsional)</label>
                            @if($report->image_path)
                            <div class="mb-2">
                                <span class="small text-muted d-block mb-1">Foto saat ini:</span>
                                <img src="{{ asset('storage/' . $report->image_path) }}" alt="Foto Lama" class="img-thumbnail" style="max-height: 120px;">
                            </div>
                            @endif
                            <input type="file" name="image" id="imageInput" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto. Format: JPG, PNG, atau WebP (Max: 2MB).</small>
                            @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <!-- Image Preview Area -->
                            <div id="imagePreviewContainer" class="mt-3 d-none">
                                <label class="form-label d-block text-muted small">Pratinjau Foto Baru:</label>
                                <img id="imagePreview" src="#" alt="Pratinjau Baru" class="img-fluid rounded border shadow-sm" style="max-height: 220px;">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('tenant.reports.show', $report) }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
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

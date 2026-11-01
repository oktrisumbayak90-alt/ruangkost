@extends('layouts.app')

@section('title', 'Tambah Kamar Baru')
@section('page-title', 'Tambah Kamar Kos')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>Tambah Kamar Baru</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('owner.rooms.index') }}">Kamar</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('owner.rooms.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Form Tambah Kamar Kos</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('owner.rooms.store') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nomor / Nama Kamar <span class="text-danger">*</span></label>
                            <input type="text" name="room_number" class="form-control @error('room_number') is-invalid @enderror" value="{{ old('room_number') }}" placeholder="Contoh: 101, A-02" required>
                            @error('room_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Lantai</label>
                            <input type="text" name="floor" class="form-control @error('floor') is-invalid @enderror" value="{{ old('floor', '1') }}" placeholder="1, 2, atau Dasar">
                            @error('floor')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kapasitas Maksimal (Orang) <span class="text-danger">*</span></label>
                            <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', 1) }}" min="1" max="10" required>
                            @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status Awal <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="tersedia" {{ in_array(old('status', 'tersedia'), ['tersedia', 'available', 'kosong']) ? 'selected' : '' }}>Kosong</option>
                                <option value="pemeliharaan" {{ in_array(old('status'), ['pemeliharaan', 'maintenance', 'perbaikan']) ? 'selected' : '' }}>Perbaikan / Maintenance</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Deskripsi & Fasilitas Kamar</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Contoh: Kasur single bed, lemari pakaian, kamar mandi dalam, AC...">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('owner.rooms.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Kamar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

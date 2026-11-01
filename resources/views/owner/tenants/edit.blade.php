@extends('layouts.app')

@section('title', 'Edit Penghuni - ' . $tenant->nama)
@section('page-title', 'Edit Penghuni')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>Edit Data: {{ $tenant->nama }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('owner.tenants.index') }}">Penghuni</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('owner.tenants.show', $tenant) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Form Perubahan Data Penghuni</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('owner.tenants.update', $tenant) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $tenant->nama) }}" required>
                            @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $tenant->email) }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $tenant->username) }}">
                            @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor WhatsApp / HP</label>
                            <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror" value="{{ old('telepon', $tenant->telepon) }}">
                            @error('telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">No. KTP / NIK</label>
                            <input type="text" name="nomor_identitas" class="form-control @error('nomor_identitas') is-invalid @enderror" value="{{ old('nomor_identitas', $tenant->nomor_identitas) }}">
                            @error('nomor_identitas')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="laki-laki" {{ old('jenis_kelamin', $tenant->jenis_kelamin) === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="perempuan" {{ old('jenis_kelamin', $tenant->jenis_kelamin) === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Alamat Asal / KTP</label>
                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat', $tenant->alamat) }}</textarea>
                            @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if($tenant->activeStay)
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-people me-2"></i>Jumlah Penghuni</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Penghuni <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_penghuni" class="form-control @error('jumlah_penghuni') is-invalid @enderror" value="{{ old('jumlah_penghuni', $tenant->activeStay->jumlah_penghuni ?? 1) }}" min="1" max="{{ $tenant->activeStay->room->kapasitas ?? 10 }}" placeholder="Masukkan jumlah penghuni" required>
                            @error('jumlah_penghuni')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                Kamar {{ $tenant->activeStay->room->nomor_kamar ?? '-' }} — Kapasitas: {{ $tenant->activeStay->room->kapasitas ?? '-' }} orang
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('owner.tenants.show', $tenant) }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

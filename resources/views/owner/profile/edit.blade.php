@extends('layouts.app')

@section('title', 'Pengaturan Profil')
@section('page-title', 'Pengaturan Akun Pemilik')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>Pengaturan Profil Pemilik</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profil</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-4 justify-content-center">
    <!-- Biodata / Profile Info -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-person-circle me-2"></i>Informasi Akun</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('owner.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nama Pemilik / Admin <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $user->nama) }}" required>
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror" value="{{ old('telepon', $user->telepon) }}" placeholder="08123456789">
                        @error('telepon')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end border-top pt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-shield-lock me-2"></i>Ganti Kata Sandi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('owner.profile.change-password') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Kata Sandi Saat Ini <span class="text-danger">*</span></label>
                        <input type="password" name="kata_sandi_saat_ini" class="form-control @error('kata_sandi_saat_ini') is-invalid @enderror" required>
                        @error('kata_sandi_saat_ini')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kata Sandi Baru <span class="text-danger">*</span></label>
                        <input type="password" name="kata_sandi" class="form-control @error('kata_sandi') is-invalid @enderror" minlength="8" required>
                        @error('kata_sandi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Kata Sandi Baru <span class="text-danger">*</span></label>
                        <input type="password" name="kata_sandi_confirmation" class="form-control" required>
                    </div>

                    <div class="d-flex justify-content-end border-top pt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-key me-1"></i> Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

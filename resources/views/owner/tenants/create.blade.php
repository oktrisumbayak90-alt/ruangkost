@extends('layouts.app')

@section('title', 'Tambah Penghuni')
@section('page-title', 'Tambah Penghuni Baru')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>Tambah Penghuni Baru</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('owner.tenants.index') }}">Penghuni</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('owner.tenants.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-person-plus me-2"></i>Formulir Data Penghuni Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('owner.tenants.store') }}" method="POST">
                    @csrf

                    <!-- Section: Informasi Akun -->
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-badge me-2"></i>Informasi Akun Login</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" placeholder="Contoh: Oktri Sumbayak" required>
                            @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="oktri@gmail.com" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Username (Opsional)</label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Contoh: oktri">
                            @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kata Sandi Awal <span class="text-danger">*</span></label>
                            <input type="password" name="kata_sandi" class="form-control @error('kata_sandi') is-invalid @enderror" placeholder="Minimal 8 karakter" required>
                            @error('kata_sandi')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="wajib_ganti_password" value="1" id="must_change" {{ old('wajib_ganti_password') ? 'checked' : '' }}>
                                <label class="form-check-label text-muted small" for="must_change">
                                    Wajibkan penghuni mengganti kata sandi saat pertama kali login
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Data Pribadi & Kontak -->
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle me-2"></i>Data Pribadi & Kontak</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Nomor WhatsApp / HP</label>
                            <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror" value="{{ old('telepon') }}" placeholder="08123456789">
                            @error('telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">No. KTP / NIK</label>
                            <input type="text" name="nomor_identitas" class="form-control @error('nomor_identitas') is-invalid @enderror" value="{{ old('nomor_identitas') }}" placeholder="3271...">
                            @error('nomor_identitas')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="laki-laki" {{ old('jenis_kelamin') === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="perempuan" {{ old('jenis_kelamin') === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Alamat Asal / KTP</label>
                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="2" placeholder="Alamat asal penghuni...">{{ old('alamat') }}</textarea>
                            @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Section: Penempatan Kamar -->
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-door-open me-2"></i>Penempatan Kamar Kos</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Pilih Kamar <span class="text-danger">*</span></label>
                            <select name="kamar_id" id="kamar_id" class="form-select @error('kamar_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kamar Tersedia --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" data-kapasitas="{{ $room->kapasitas }}" data-terisi="{{ $room->getActiveTenantsCount() }}" {{ old('kamar_id') == $room->id ? 'selected' : '' }}>
                                        Kamar {{ $room->nomor_kamar }} (Lantai {{ $room->lantai ?? '1' }} - Kapasitas: {{ $room->getActiveTenantsCount() }}/{{ $room->kapasitas }})
                                    </option>
                                @endforeach
                            </select>
                            @error('kamar_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Jumlah Penghuni <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_penghuni" id="jumlah_penghuni" class="form-control @error('jumlah_penghuni') is-invalid @enderror" value="{{ old('jumlah_penghuni', 1) }}" min="1" placeholder="Masukkan jumlah penghuni" required>
                            @error('jumlah_penghuni')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text" id="kapasitasInfo"></div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_masuk" class="form-control @error('tanggal_masuk') is-invalid @enderror" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
                            @error('tanggal_masuk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('owner.tenants.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Simpan Data Penghuni
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const kamarSelect = document.getElementById('kamar_id');
        const jumlahInput = document.getElementById('jumlah_penghuni');
        const infoText = document.getElementById('kapasitasInfo');

        function updateKapasitasInfo() {
            const selected = kamarSelect.options[kamarSelect.selectedIndex];
            if (selected && selected.value) {
                const kapasitas = parseInt(selected.dataset.kapasitas) || 1;
                const terisi = parseInt(selected.dataset.terisi) || 0;
                const sisa = kapasitas - terisi;
                jumlahInput.max = sisa > 0 ? sisa : 1;
                infoText.textContent = 'Sisa kapasitas: ' + sisa + ' orang';
                infoText.className = 'form-text ' + (sisa <= 0 ? 'text-danger' : 'text-muted');

                if (parseInt(jumlahInput.value) > sisa) {
                    jumlahInput.value = sisa > 0 ? sisa : 1;
                }
            } else {
                jumlahInput.removeAttribute('max');
                infoText.textContent = '';
            }
        }

        kamarSelect.addEventListener('change', updateKapasitasInfo);
        updateKapasitasInfo();
    });
</script>
@endpush

@endsection

@extends('layouts.app')

@section('title', 'Kategori Laporan')
@section('page-title', 'Manajemen Kategori Laporan')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1>Kategori Laporan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kategori</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('owner.categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Kategori</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Urutan</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Laporan</th>
                            <th>Status</th>
                            <th class="text-end" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $category->sort_order }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 36px; height: 36px; background-color: {{ $category->color }}20; color: {{ $category->color }};">
                                        <i class="{{ $category->icon }} fs-6"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $category->name }}</div>
                                        <small class="text-muted font-monospace">{{ $category->slug }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-secondary small">{{ $category->description ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    {{ $category->reports_count ?? 0 }} Laporan
                                </span>
                            </td>
                            <td>
                                @if($category->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('owner.categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if(($category->reports_count ?? 0) === 0)
                                    <form action="{{ route('owner.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="bi bi-tags"></i>
                                    <h5>Belum Ada Kategori</h5>
                                    <p>Tambahkan kategori pengaduan untuk mempermudah klasifikasi masalah.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

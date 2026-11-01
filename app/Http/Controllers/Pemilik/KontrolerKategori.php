<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KontrolerKategori extends Controller
{
    public function index()
    {
        $categories = Category::withCount('reports')->orderBy('urutan')->get();
        return view('owner.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('owner.categories.create');
    }

    public function store(Request $request)
    {
        $nama      = $request->input('nama', $request->input('name'));
        $deskripsi = $request->input('deskripsi', $request->input('description'));
        $ikon      = $request->input('ikon', $request->input('icon', 'bi-tag'));
        $warna     = $request->input('warna', $request->input('color', '#2563EB'));
        $urutan    = $request->input('urutan', $request->input('sort_order', 0));

        $request->merge([
            'nama'      => $nama,
            'deskripsi' => $deskripsi,
            'ikon'      => $ikon,
            'warna'     => $warna,
            'urutan'    => $urutan,
        ]);

        $request->validate([
            'nama'      => 'required|string|max:100|unique:kategori,nama',
            'deskripsi' => 'nullable|string|max:500',
            'ikon'      => 'nullable|string|max:50',
            'warna'     => 'nullable|string|max:20',
            'urutan'    => 'nullable|integer|min:0',
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique'   => 'Nama kategori sudah digunakan.',
        ]);

        Category::create([
            'nama'      => $nama,
            'slug'      => Str::slug($nama),
            'deskripsi' => $deskripsi,
            'ikon'      => $ikon ?: 'bi-tag',
            'warna'     => $warna ?: '#2563EB',
            'is_aktif'  => true,
            'urutan'    => $urutan ?: 0,
        ]);

        return redirect()->route('owner.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('owner.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $nama      = $request->input('nama', $request->input('name'));
        $deskripsi = $request->input('deskripsi', $request->input('description'));
        $ikon      = $request->input('ikon', $request->input('icon', 'bi-tag'));
        $warna     = $request->input('warna', $request->input('color', '#2563EB'));
        $urutan    = $request->input('urutan', $request->input('sort_order', 0));
        $isAktif   = $request->boolean('is_aktif', $request->boolean('is_active', true));

        $request->merge([
            'nama'      => $nama,
            'deskripsi' => $deskripsi,
            'ikon'      => $ikon,
            'warna'     => $warna,
            'urutan'    => $urutan,
            'is_aktif'  => $isAktif,
        ]);

        $request->validate([
            'nama'      => 'required|string|max:100|unique:kategori,nama,' . $category->id,
            'deskripsi' => 'nullable|string|max:500',
            'ikon'      => 'nullable|string|max:50',
            'warna'     => 'nullable|string|max:20',
            'urutan'    => 'nullable|integer|min:0',
            'is_aktif'  => 'boolean',
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique'   => 'Nama kategori sudah digunakan.',
        ]);

        $category->update([
            'nama'      => $nama,
            'slug'      => Str::slug($nama),
            'deskripsi' => $deskripsi,
            'ikon'      => $ikon ?: 'bi-tag',
            'warna'     => $warna ?: '#2563EB',
            'is_aktif'  => $isAktif,
            'urutan'    => $urutan ?: 0,
        ]);

        return redirect()->route('owner.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->hasReports()) {
            return back()->withErrors(['error' => 'Kategori tidak dapat dihapus karena masih memiliki laporan.']);
        }

        $category->delete();

        return redirect()->route('owner.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}

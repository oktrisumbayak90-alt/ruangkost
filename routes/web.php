<?php

use App\Http\Controllers\Autentikasi\KontrolerAutentikasi;
use App\Http\Controllers\KontrolerNotifikasi;
use App\Http\Controllers\Pemilik\KontrolerDasbor as KontrolerDasborPemilik;
use App\Http\Controllers\Pemilik\KontrolerKamar;
use App\Http\Controllers\Pemilik\KontrolerKategori;
use App\Http\Controllers\Pemilik\KontrolerLaporan as KontrolerLaporanPemilik;
use App\Http\Controllers\Pemilik\KontrolerPenghuni;
use App\Http\Controllers\Pemilik\KontrolerProfil as KontrolerProfilPemilik;
use App\Http\Controllers\Pemilik\KontrolerStatistik;
use App\Http\Controllers\Penghuni\KontrolerDasbor as KontrolerDasborPenghuni;
use App\Http\Controllers\Penghuni\KontrolerLaporan as KontrolerLaporanPenghuni;
use App\Http\Controllers\Penghuni\KontrolerProfil as KontrolerProfilPenghuni;
use Illuminate\Support\Facades\Route;

// ─── Redirect root ───────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isOwner()
            ? redirect()->route('owner.dashboard')
            : redirect()->route('tenant.dashboard');
    }
    return redirect()->route('login');
});

// ─── Auth ─────────────────────────────────────────────────────────
Route::match(['get', 'post'], '/logout', [KontrolerAutentikasi::class, 'logout'])->name('logout');

Route::middleware('guest')->group(function () {
    Route::get('/login', [KontrolerAutentikasi::class, 'showLoginForm'])->name('login');
    Route::post('/login', [KontrolerAutentikasi::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    // Ganti password paksa
    Route::get('/ganti-password', [KontrolerAutentikasi::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/ganti-password', [KontrolerAutentikasi::class, 'updatePassword'])->name('password.update.forced');

    // Notifikasi (shared antara owner & tenant)
    Route::prefix('notifikasi')->name('notifications.')->group(function () {
        Route::get('/', [KontrolerNotifikasi::class, 'index'])->name('index');
        Route::post('/{notification}/baca', [KontrolerNotifikasi::class, 'markRead'])->name('read');
        Route::post('/baca-semua', [KontrolerNotifikasi::class, 'markAllRead'])->name('read-all');
        Route::get('/jumlah', [KontrolerNotifikasi::class, 'getUnreadCount'])->name('count');
        Route::get('/terbaru', [KontrolerNotifikasi::class, 'getRecent'])->name('recent');
        Route::post('/{notification}/baca-json', [KontrolerNotifikasi::class, 'markReadJson'])->name('read-json');
    });
});

// ─── Owner Routes ─────────────────────────────────────────────────
Route::prefix('owner')->name('owner.')->middleware(['auth', 'role:owner'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [KontrolerDasborPemilik::class, 'index'])->name('dashboard');

    // Laporan
    Route::prefix('laporan')->name('reports.')->group(function () {
        Route::get('/', [KontrolerLaporanPemilik::class, 'index'])->name('index');
        Route::get('/{report}', [KontrolerLaporanPemilik::class, 'show'])->name('show');
        Route::post('/{report}/status', [KontrolerLaporanPemilik::class, 'updateStatus'])->name('update-status');
        Route::post('/{report}/tanggapan', [KontrolerLaporanPemilik::class, 'addResponse'])->name('add-response');
        Route::get('/ekspor/pdf', [KontrolerLaporanPemilik::class, 'exportPdf'])->name('export-pdf');
        Route::delete('/{report}', [KontrolerLaporanPemilik::class, 'destroy'])->name('destroy');
    });

    // Kategori
    Route::resource('kategori', KontrolerKategori::class)->names([
        'index'   => 'categories.index',
        'create'  => 'categories.create',
        'store'   => 'categories.store',
        'edit'    => 'categories.edit',
        'update'  => 'categories.update',
        'destroy' => 'categories.destroy',
    ])->parameters(['kategori' => 'category']);

    // Data Penghuni
    Route::prefix('penghuni')->name('tenants.')->group(function () {
        Route::get('/', [KontrolerPenghuni::class, 'index'])->name('index');
        Route::get('/tambah', [KontrolerPenghuni::class, 'create'])->name('create');
        Route::post('/tambah', [KontrolerPenghuni::class, 'store'])->name('store');
        Route::get('/riwayat', [KontrolerPenghuni::class, 'history'])->name('history');
        Route::get('/{tenant}', [KontrolerPenghuni::class, 'show'])->name('show');
        Route::get('/{tenant}/edit', [KontrolerPenghuni::class, 'edit'])->name('edit');
        Route::put('/{tenant}', [KontrolerPenghuni::class, 'update'])->name('update');
        Route::post('/{tenant}/toggle-aktif', [KontrolerPenghuni::class, 'toggleActive'])->name('toggle-active');
        Route::post('/{tenant}/keluarkan', [KontrolerPenghuni::class, 'evict'])->name('evict');
        Route::post('/{tenant}/reset-password', [KontrolerPenghuni::class, 'resetPassword'])->name('reset-password');
        Route::delete('/{tenant}', [KontrolerPenghuni::class, 'destroy'])->name('destroy');
    });

    // Data Kamar
    Route::resource('kamar', KontrolerKamar::class)->names([
        'index'   => 'rooms.index',
        'create'  => 'rooms.create',
        'store'   => 'rooms.store',
        'show'    => 'rooms.show',
        'edit'    => 'rooms.edit',
        'update'  => 'rooms.update',
        'destroy' => 'rooms.destroy',
    ])->parameters(['kamar' => 'room']);

    // Statistik & Laporan
    Route::prefix('statistik')->name('statistics.')->group(function () {
        Route::get('/', [KontrolerStatistik::class, 'index'])->name('index');
        Route::get('/ekspor/pdf', [KontrolerLaporanPemilik::class, 'exportPdf'])->name('export-pdf');
    });

    // Profil
    Route::prefix('profil')->name('profile.')->group(function () {
        Route::get('/', [KontrolerProfilPemilik::class, 'edit'])->name('edit');
        Route::put('/', [KontrolerProfilPemilik::class, 'update'])->name('update');
        Route::post('/ganti-password', [KontrolerProfilPemilik::class, 'changePassword'])->name('change-password');
    });
});

// ─── Tenant Routes ────────────────────────────────────────────────
Route::prefix('penghuni')->name('tenant.')->middleware(['auth', 'role:tenant'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [KontrolerDasborPenghuni::class, 'index'])->name('dashboard');

    // Laporan Saya
    Route::prefix('laporan')->name('reports.')->group(function () {
        Route::get('/', [KontrolerLaporanPenghuni::class, 'index'])->name('index');
        Route::get('/buat', [KontrolerLaporanPenghuni::class, 'create'])->name('create');
        Route::post('/buat', [KontrolerLaporanPenghuni::class, 'store'])->name('store');
        Route::get('/{report}', [KontrolerLaporanPenghuni::class, 'show'])->name('show');
        Route::get('/{report}/edit', [KontrolerLaporanPenghuni::class, 'edit'])->name('edit');
        Route::put('/{report}', [KontrolerLaporanPenghuni::class, 'update'])->name('update');
        Route::post('/{report}/batalkan', [KontrolerLaporanPenghuni::class, 'cancel'])->name('cancel');
        Route::delete('/{report}', [KontrolerLaporanPenghuni::class, 'destroy'])->name('destroy');
    });

    // Profil Saya
    Route::prefix('profil')->name('profile.')->group(function () {
        Route::get('/', [KontrolerProfilPenghuni::class, 'edit'])->name('edit');
        Route::put('/', [KontrolerProfilPenghuni::class, 'update'])->name('update');
        Route::post('/ganti-password', [KontrolerProfilPenghuni::class, 'changePassword'])->name('change-password');
    });
});


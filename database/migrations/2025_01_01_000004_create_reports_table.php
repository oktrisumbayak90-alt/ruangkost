<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_laporan')->unique();
            $table->foreignId('pengguna_id')->constrained('pengguna')->onDelete('restrict');
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('restrict');
            $table->foreignId('kamar_id')->nullable()->constrained('kamar')->onDelete('set null');
            $table->foreignId('riwayat_tinggal_id')->nullable()->constrained('riwayat_tinggal')->onDelete('set null');
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('lokasi')->nullable();
            $table->date('tanggal_kejadian');
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi'])->default('sedang');
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->string('jalur_foto')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['pengguna_id', 'status']);
            $table->index(['kategori_id']);
            $table->index(['status', 'prioritas']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};

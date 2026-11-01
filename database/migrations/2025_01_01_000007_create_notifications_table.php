<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->onDelete('cascade');
            $table->string('tipe'); // 'laporan_baru', 'status_berubah', 'tanggapan_baru'
            $table->string('judul');
            $table->text('pesan');
            $table->string('url')->nullable();
            $table->foreignId('laporan_id')->nullable()->constrained('laporan')->onDelete('cascade');
            $table->boolean('is_dibaca')->default(false);
            $table->timestamp('dibaca_pada')->nullable();
            $table->timestamps();

            $table->index(['pengguna_id', 'is_dibaca']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};

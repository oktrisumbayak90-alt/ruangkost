<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_tinggal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->onDelete('restrict');
            $table->foreignId('kamar_id')->constrained('kamar')->onDelete('restrict');
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['pengguna_id', 'status']);
            $table->index(['kamar_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_tinggal');
    }
};

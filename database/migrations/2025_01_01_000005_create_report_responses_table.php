<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tanggapan_laporan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporan')->onDelete('cascade');
            $table->foreignId('pengguna_id')->constrained('pengguna')->onDelete('restrict');
            $table->text('tanggapan');
            $table->timestamps();

            $table->index(['laporan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tanggapan_laporan');
    }
};

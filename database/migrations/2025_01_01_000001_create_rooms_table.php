<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kamar', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kamar')->unique();
            $table->string('lantai')->nullable();
            $table->integer('kapasitas')->default(1);
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['tersedia', 'terisi', 'penuh', 'pemeliharaan'])->default('tersedia');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kamar');
    }
};

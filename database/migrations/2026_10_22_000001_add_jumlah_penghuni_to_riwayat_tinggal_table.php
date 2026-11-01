<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riwayat_tinggal', function (Blueprint $table) {
            $table->unsignedTinyInteger('jumlah_penghuni')->default(1)->after('tanggal_masuk');
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_tinggal', function (Blueprint $table) {
            $table->dropColumn('jumlah_penghuni');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jobs table tidak diperlukan karena menggunakan sync queue driver
    }

    public function down(): void
    {
        //
    }
};

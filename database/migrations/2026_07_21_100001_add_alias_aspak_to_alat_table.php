<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            // Nama padanan alat di ASPAK (untuk sinkronisasi/import ulang capaian ke depan).
            $table->string('alias_aspak')->nullable()->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->dropColumn('alias_aspak');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_pemeriksaan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('labkesmas_id')->constrained('labkesmas')->cascadeOnDelete();
            $table->foreignUuid('jenis_tes_id')->constrained('jenis_pemeriksaan')->cascadeOnDelete();
            $table->unsignedTinyInteger('bulan');   // 1-12
            $table->unsignedSmallInteger('tahun');  // mis. 2026
            $table->unsignedInteger('jumlah')->default(0);
            // PRD hanya menyebut updated_at; created_at ditambahkan untuk audit dasar (integritas data).
            $table->timestamps();

            // Integritas: satu labkesmas hanya boleh punya satu angka per (tes, bulan, tahun).
            $table->unique(['labkesmas_id', 'jenis_tes_id', 'bulan', 'tahun'], 'uq_pemeriksaan_periode');
            // Query tren & agregasi dashboard selalu memfilter/mengurutkan per periode → composite index.
            $table->index(['tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_pemeriksaan');
    }
};

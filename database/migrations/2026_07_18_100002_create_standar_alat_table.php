<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standar_alat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('alat_id')->constrained('alat')->cascadeOnDelete();
            // Tier 1-5 (KMK). App hanya memakai lab tier 2-5, tapi standar disimpan lengkap.
            $table->unsignedTinyInteger('tier');
            // 'umum' untuk tier 1-4; 'biokes'/'kesling' untuk tier 5 (lab terpisah).
            // Pakai string non-null (default 'umum') agar unique constraint andal di MySQL.
            $table->string('jenis_lab')->default('umum');
            $table->unsignedInteger('jumlah_minimal');
            $table->timestamps();

            // Satu alat hanya punya satu angka standar per (tier, jenis_lab).
            $table->unique(['alat_id', 'tier', 'jenis_lab'], 'uq_standar_alat');
            // Pencocokan pemenuhan selalu memfilter per (tier, jenis_lab).
            $table->index(['tier', 'jenis_lab']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standar_alat');
    }
};

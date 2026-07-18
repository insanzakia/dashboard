<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labkesmas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_kantor');
            // Tier 2-5 (validasi rentang ditegakkan di Form Request, kolom cukup tinyint).
            $table->unsignedTinyInteger('tier_labkesmas');
            // Lokasi fisik; constrained() membuat index pada kolom FK.
            $table->foreignUuid('kabupaten_kota_id')->constrained('kabupaten_kota')->cascadeOnDelete();
            $table->timestamps();

            // Sering difilter berdasarkan tier (filter independen di dashboard) → index eksplisit.
            $table->index('tier_labkesmas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labkesmas');
    }
};

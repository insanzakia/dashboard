<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris_alat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('labkesmas_id')->constrained('labkesmas')->cascadeOnDelete();
            $table->foreignUuid('alat_id')->constrained('alat')->cascadeOnDelete();
            $table->unsignedInteger('jumlah')->default(0);
            $table->timestamps();

            // Satu labkesmas hanya punya satu angka kepemilikan per alat (semantik UPSERT).
            $table->unique(['labkesmas_id', 'alat_id'], 'uq_inventaris_alat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris_alat');
    }
};

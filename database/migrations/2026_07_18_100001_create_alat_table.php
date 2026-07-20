<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_alat');
            // Salah satu dari 8 kategori KMK (hematologi_kimia_imunologi, mikrobiologi, dst).
            $table->string('kategori');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Katalog sering dikelompokkan/difilter per kategori → index eksplisit.
            $table->index('kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};

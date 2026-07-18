<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kabupaten_kota', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->foreignUuid('provinsi_id')->constrained('provinsi')->cascadeOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->unique(['nama', 'provinsi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kabupaten_kota');
    }
};

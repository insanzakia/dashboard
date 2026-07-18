<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinsi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->foreignUuid('regional_id')->constrained('regional')->cascadeOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->unique(['nama', 'regional_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provinsi');
    }
};

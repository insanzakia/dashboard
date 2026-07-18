<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regional', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            // FK ke negara.id; constrained() otomatis membuat index pada kolom ini (mempercepat JOIN).
            $table->foreignUuid('negara_id')->constrained('negara')->cascadeOnDelete();
            $table->timestamp('created_at')->nullable();

            // Cegah duplikasi nama regional dalam satu negara (PRD Section 6).
            $table->unique(['nama', 'negara_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regional');
    }
};

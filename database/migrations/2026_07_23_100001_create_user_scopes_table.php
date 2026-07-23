<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cakupan (scope) wilayah untuk akun non-super_admin.
     * Satu akun bisa punya banyak cakupan (regional/provinsi/kab-kota/labkesmas tertentu).
     * super_admin TIDAK punya baris di sini → akses global.
     */
    public function up(): void
    {
        Schema::create('user_scopes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            // 'regional' | 'provinsi' | 'kabupaten_kota' | 'labkesmas'
            $table->string('scope_type');
            // UUID entitas pada level tsb (tidak pakai FK constrained karena tabel tujuan bervariasi).
            $table->uuid('scope_id');
            $table->timestamps();

            $table->index('user_id');
            // Cegah duplikasi cakupan yang sama pada satu akun.
            $table->unique(['user_id', 'scope_type', 'scope_id'], 'uq_user_scope');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_scopes');
    }
};

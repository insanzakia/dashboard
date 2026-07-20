<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('labkesmas', function (Blueprint $table) {
            // Jenis lab hanya bermakna untuk tier 5: 'biokes' | 'kesling'.
            // Tier 2-4 → null (dianggap 'umum' saat mencocokkan standar).
            $table->string('jenis_lab')->nullable()->after('tier_labkesmas');
        });
    }

    public function down(): void
    {
        Schema::table('labkesmas', function (Blueprint $table) {
            $table->dropColumn('jenis_lab');
        });
    }
};

<?php

namespace App\Actions\JenisPemeriksaan;

use App\Models\JenisPemeriksaan;

class DeleteJenisPemeriksaanAction
{
    /** Menghapus jenis tes; data_pemeriksaan terkait ikut terhapus via cascadeOnDelete. */
    public function execute(JenisPemeriksaan $jenisPemeriksaan): void
    {
        $jenisPemeriksaan->delete();
    }
}

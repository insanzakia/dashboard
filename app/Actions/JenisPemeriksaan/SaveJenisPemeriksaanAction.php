<?php

namespace App\Actions\JenisPemeriksaan;

use App\Models\JenisPemeriksaan;

class SaveJenisPemeriksaanAction
{
    /**
     * @param  array{nama_tes: string, deskripsi: ?string}  $data
     */
    public function execute(array $data, ?JenisPemeriksaan $jenisPemeriksaan = null): JenisPemeriksaan
    {
        if ($jenisPemeriksaan !== null) {
            $jenisPemeriksaan->update($data);

            return $jenisPemeriksaan;
        }

        return JenisPemeriksaan::create($data);
    }
}

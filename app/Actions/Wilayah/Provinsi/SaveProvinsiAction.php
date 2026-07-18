<?php

namespace App\Actions\Wilayah\Provinsi;

use App\Models\Provinsi;

class SaveProvinsiAction
{
    /**
     * @param  array{nama: string, regional_id: string}  $data
     */
    public function execute(array $data, ?Provinsi $provinsi = null): Provinsi
    {
        if ($provinsi !== null) {
            $provinsi->update($data);

            return $provinsi;
        }

        return Provinsi::create($data);
    }
}

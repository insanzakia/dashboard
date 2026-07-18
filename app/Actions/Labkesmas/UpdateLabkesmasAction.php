<?php

namespace App\Actions\Labkesmas;

use App\Models\Labkesmas;

class UpdateLabkesmasAction
{
    /**
     * @param  array{nama_kantor: string, tier_labkesmas: int, kabupaten_kota_id: string}  $data
     */
    public function execute(Labkesmas $labkesmas, array $data): Labkesmas
    {
        $labkesmas->update($data);

        return $labkesmas;
    }
}

<?php

namespace App\Actions\Wilayah\KabupatenKota;

use App\Models\KabupatenKota;

class SaveKabupatenKotaAction
{
    /**
     * @param  array{nama: string, provinsi_id: string}  $data
     */
    public function execute(array $data, ?KabupatenKota $kabupatenKota = null): KabupatenKota
    {
        if ($kabupatenKota !== null) {
            $kabupatenKota->update($data);

            return $kabupatenKota;
        }

        return KabupatenKota::create($data);
    }
}

<?php

namespace App\Actions\Labkesmas;

use App\Models\Labkesmas;

class UpdateLabkesmasAction
{
    /**
     * @param  array{nama_kantor: string, tier_labkesmas: int, jenis_lab?: ?string, kabupaten_kota_id: string}  $data
     */
    public function execute(Labkesmas $labkesmas, array $data): Labkesmas
    {
        // Jenis lab hanya relevan untuk tier 5; selain itu selalu null (kebersihan data).
        $data['jenis_lab'] = (int) $data['tier_labkesmas'] === 5 ? ($data['jenis_lab'] ?? null) : null;

        $labkesmas->update($data);

        return $labkesmas;
    }
}

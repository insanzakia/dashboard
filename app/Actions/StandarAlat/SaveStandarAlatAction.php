<?php

namespace App\Actions\StandarAlat;

use App\Models\StandarAlat;

/**
 * Menyimpan standar jumlah minimal satu alat untuk (tier, jenis_lab) tertentu (UPSERT).
 * Keunikan (alat_id + tier + jenis_lab) dijamin unique constraint DB (uq_standar_alat).
 */
class SaveStandarAlatAction
{
    /**
     * @param  array{alat_id: string, tier: int, jenis_lab: string, jumlah_minimal: int}  $data
     */
    public function execute(array $data): StandarAlat
    {
        return StandarAlat::updateOrCreate(
            [
                'alat_id' => $data['alat_id'],
                'tier' => $data['tier'],
                'jenis_lab' => $data['jenis_lab'],
            ],
            ['jumlah_minimal' => $data['jumlah_minimal']],
        );
    }
}

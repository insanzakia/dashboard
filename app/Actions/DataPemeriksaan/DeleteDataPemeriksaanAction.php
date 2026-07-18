<?php

namespace App\Actions\DataPemeriksaan;

use App\Models\DataPemeriksaan;

class DeleteDataPemeriksaanAction
{
    public function execute(DataPemeriksaan $dataPemeriksaan): void
    {
        $dataPemeriksaan->delete();
    }
}

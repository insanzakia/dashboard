<?php

namespace App\Actions\Wilayah\KabupatenKota;

use App\Models\KabupatenKota;

class DeleteKabupatenKotaAction
{
    public function execute(KabupatenKota $kabupatenKota): void
    {
        $kabupatenKota->delete();
    }
}

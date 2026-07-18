<?php

namespace App\Actions\Wilayah\Provinsi;

use App\Models\Provinsi;

class DeleteProvinsiAction
{
    public function execute(Provinsi $provinsi): void
    {
        $provinsi->delete();
    }
}

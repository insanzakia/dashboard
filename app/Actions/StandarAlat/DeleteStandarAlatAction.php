<?php

namespace App\Actions\StandarAlat;

use App\Models\StandarAlat;

class DeleteStandarAlatAction
{
    /** Menghapus satu baris standar (mis. alat tidak lagi diwajibkan untuk tier tsb). */
    public function execute(StandarAlat $standarAlat): void
    {
        $standarAlat->delete();
    }
}

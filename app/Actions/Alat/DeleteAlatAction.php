<?php

namespace App\Actions\Alat;

use App\Models\Alat;

class DeleteAlatAction
{
    /** Menghapus alat; baris standar & inventaris terkait ikut terhapus via cascadeOnDelete. */
    public function execute(Alat $alat): void
    {
        $alat->delete();
    }
}

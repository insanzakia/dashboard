<?php

namespace App\Actions\Labkesmas;

use App\Models\Labkesmas;

class DeleteLabkesmasAction
{
    /** Menghapus labkesmas; data_pemeriksaan terkait ikut terhapus via cascadeOnDelete. */
    public function execute(Labkesmas $labkesmas): void
    {
        $labkesmas->delete();
    }
}

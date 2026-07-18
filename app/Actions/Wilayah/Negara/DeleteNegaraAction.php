<?php

namespace App\Actions\Wilayah\Negara;

use App\Models\Negara;

class DeleteNegaraAction
{
    /** Menghapus Negara beserta seluruh turunannya via cascadeOnDelete. */
    public function execute(Negara $negara): void
    {
        $negara->delete();
    }
}

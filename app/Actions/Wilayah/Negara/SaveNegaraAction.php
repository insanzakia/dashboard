<?php

namespace App\Actions\Wilayah\Negara;

use App\Models\Negara;

class SaveNegaraAction
{
    /**
     * @param  array{nama: string}  $data
     */
    public function execute(array $data, ?Negara $negara = null): Negara
    {
        if ($negara !== null) {
            $negara->update($data);

            return $negara;
        }

        return Negara::create($data);
    }
}

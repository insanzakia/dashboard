<?php

namespace App\Actions\Alat;

use App\Models\Alat;

class SaveAlatAction
{
    /**
     * @param  array{nama_alat: string, kategori: string, keterangan: ?string}  $data
     */
    public function execute(array $data, ?Alat $alat = null): Alat
    {
        if ($alat !== null) {
            $alat->update($data);

            return $alat;
        }

        return Alat::create($data);
    }
}

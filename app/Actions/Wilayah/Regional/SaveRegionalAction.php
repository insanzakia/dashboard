<?php

namespace App\Actions\Wilayah\Regional;

use App\Models\Regional;

/**
 * Membuat atau memperbarui satu entitas Regional.
 * Satu Action = satu tanggung jawab; controller cukup memanggil execute().
 */
class SaveRegionalAction
{
    /**
     * @param  array{nama: string, negara_id: string}  $data
     */
    public function execute(array $data, ?Regional $regional = null): Regional
    {
        if ($regional !== null) {
            $regional->update($data);

            return $regional;
        }

        return Regional::create($data);
    }
}

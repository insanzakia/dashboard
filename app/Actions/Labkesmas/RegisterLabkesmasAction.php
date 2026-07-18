<?php

namespace App\Actions\Labkesmas;

use App\Models\Labkesmas;

/**
 * Mendaftarkan entitas Labkesmas baru (nama kantor, tier 2-5, lokasi kabupaten/kota).
 * Validasi tier & FK sudah dilakukan di StoreLabkesmasRequest sebelum sampai ke sini.
 */
class RegisterLabkesmasAction
{
    /**
     * @param  array{nama_kantor: string, tier_labkesmas: int, kabupaten_kota_id: string}  $data
     */
    public function execute(array $data): Labkesmas
    {
        return Labkesmas::create($data);
    }
}

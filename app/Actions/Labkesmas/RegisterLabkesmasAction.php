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
     * @param  array{nama_kantor: string, tier_labkesmas: int, jenis_lab?: ?string, kabupaten_kota_id: string}  $data
     */
    public function execute(array $data): Labkesmas
    {
        // Jenis lab hanya relevan untuk tier 5; selain itu selalu null (kebersihan data).
        $data['jenis_lab'] = (int) $data['tier_labkesmas'] === 5 ? ($data['jenis_lab'] ?? null) : null;

        return Labkesmas::create($data);
    }
}

<?php

namespace App\Repositories\Eloquent;

use App\Models\Labkesmas;
use App\Repositories\Contracts\LabkesmasRepositoryInterface;

class LabkesmasRepository implements LabkesmasRepositoryInterface
{
    public function labkesmasForAdmin(): array
    {
        // Eager load rantai lokasi agar tidak N+1 (kab/kota → provinsi → regional).
        return Labkesmas::query()
            ->with('kabupatenKota.provinsi.regional')
            ->orderBy('nama_kantor')
            ->get()
            ->map(function (Labkesmas $lab) {
                $kabKota = $lab->kabupatenKota;
                $provinsi = $kabKota?->provinsi;
                $regional = $provinsi?->regional;

                return [
                    'id' => $lab->id,
                    'nama_kantor' => $lab->nama_kantor,
                    'tier_labkesmas' => $lab->tier_labkesmas,
                    'kabupaten_kota_id' => $lab->kabupaten_kota_id,
                    'kabupaten_nama' => $kabKota?->nama,
                    'provinsi_id' => $provinsi?->id,
                    'provinsi_nama' => $provinsi?->nama,
                    'regional_id' => $regional?->id,
                    'regional_nama' => $regional?->nama,
                ];
            })
            ->all();
    }
}

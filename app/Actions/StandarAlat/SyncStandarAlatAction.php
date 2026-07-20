<?php

namespace App\Actions\StandarAlat;

use App\Models\StandarAlat;
use Illuminate\Support\Facades\DB;

/**
 * Menyinkronkan seluruh baris standar satu alat sekaligus.
 * Untuk tiap (tier, jenis_lab): jumlah > 0 → upsert; jumlah 0 → hapus baris (tidak diwajibkan).
 */
class SyncStandarAlatAction
{
    /**
     * @param  array{alat_id: string, items: array<int, array{tier: int, jenis_lab: string, jumlah_minimal: int}>}  $data
     */
    public function execute(array $data): void
    {
        DB::transaction(function () use ($data) {
            foreach ($data['items'] as $item) {
                $key = [
                    'alat_id' => $data['alat_id'],
                    'tier' => $item['tier'],
                    'jenis_lab' => $item['jenis_lab'],
                ];

                if ($item['jumlah_minimal'] > 0) {
                    StandarAlat::updateOrCreate($key, ['jumlah_minimal' => $item['jumlah_minimal']]);
                } else {
                    StandarAlat::where($key)->delete();
                }
            }
        });
    }
}

<?php

namespace App\Actions\InventarisAlat;

use App\Models\InventarisAlat;
use Illuminate\Support\Facades\DB;

/**
 * Menyimpan jumlah alat yang dimiliki satu Labkesmas secara massal (UPSERT per alat).
 * Dijalankan dalam satu transaksi agar simpanan konsisten (semua berhasil atau tidak sama sekali).
 * Keunikan (labkesmas_id + alat_id) dijamin unique constraint DB (uq_inventaris_alat).
 */
class RecordInventarisAlatAction
{
    /**
     * @param  array{labkesmas_id: string, items: array<int, array{alat_id: string, jumlah: int}>}  $data
     */
    public function execute(array $data): void
    {
        DB::transaction(function () use ($data) {
            foreach ($data['items'] as $item) {
                InventarisAlat::updateOrCreate(
                    [
                        'labkesmas_id' => $data['labkesmas_id'],
                        'alat_id' => $item['alat_id'],
                    ],
                    ['jumlah' => $item['jumlah']],
                );
            }
        });
    }
}

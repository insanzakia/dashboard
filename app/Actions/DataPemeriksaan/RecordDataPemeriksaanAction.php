<?php

namespace App\Actions\DataPemeriksaan;

use App\Exceptions\DuplicatePemeriksaanException;
use App\Models\DataPemeriksaan;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

/**
 * Menyimpan angka pemeriksaan bulanan dengan semantik UPSERT (PRD Section 5):
 * jika (labkesmas + tes + bulan + tahun) sudah ada → nilai diperbarui; jika belum → dibuat.
 *
 * Keunikan periode dijamin unique constraint DB (uq_pemeriksaan_periode) — anti race condition.
 */
class RecordDataPemeriksaanAction
{
    /**
     * @param  array{labkesmas_id: string, jenis_tes_id: string, bulan: int, tahun: int, jumlah: int}  $data
     */
    public function execute(array $data): DataPemeriksaan
    {
        try {
            return DataPemeriksaan::updateOrCreate(
                [
                    'labkesmas_id' => $data['labkesmas_id'],
                    'jenis_tes_id' => $data['jenis_tes_id'],
                    'bulan' => $data['bulan'],
                    'tahun' => $data['tahun'],
                ],
                ['jumlah' => $data['jumlah']],
            );
        } catch (QueryException $e) {
            // Catat detail teknis ke log (bukan ke user) untuk audit & debugging.
            Log::error('Gagal menyimpan data pemeriksaan', [
                'labkesmas_id' => $data['labkesmas_id'],
                'periode' => "{$data['tahun']}-{$data['bulan']}",
                'sql_state' => $e->getCode(),
            ]);

            // Lempar exception domain yang ramah — pesan sensitif server tidak bocor.
            throw new DuplicatePemeriksaanException;
        }
    }
}

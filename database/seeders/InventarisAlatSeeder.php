<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\Labkesmas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Mengisi inventaris_alat dari data capaian ASPAK (fakta_kesesuaian_labkesmas.csv).
 * Kolom CSV: lab_id, labkesmas, tier, kategori, alat_kmk, standar, jumlah_berfungsi, status.
 * Join berdasarkan NAMA: labkesmas.nama_kantor & alat.nama_alat (harus cocok dgn seeder lain).
 * Hanya baris jumlah_berfungsi > 0 yang disimpan (repo menganggap tak ada = 0).
 */
class InventarisAlatSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/fakta_kesesuaian_labkesmas.csv');

        if (! is_file($path)) {
            $this->command?->error("CSV tidak ditemukan: {$path}");

            return;
        }

        $labByName = Labkesmas::query()->pluck('id', 'nama_kantor')->all();
        $alatByName = Alat::query()->pluck('id', 'nama_alat')->all();

        $handle = fopen($path, 'r');
        fgetcsv($handle); // buang header

        $now = now();
        $rows = [];
        $unmatchedLab = [];
        $unmatchedAlat = [];
        $skippedZero = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 7) {
                continue;
            }

            $labNama = trim($row[1]);
            $alatNama = trim($row[4]);
            $jumlah = (int) $row[6];

            if ($jumlah <= 0) {
                $skippedZero++;

                continue;
            }

            $labId = $labByName[$labNama] ?? null;
            $alatId = $alatByName[$alatNama] ?? null;

            if ($labId === null) {
                $unmatchedLab[$labNama] = true;

                continue;
            }
            if ($alatId === null) {
                $unmatchedAlat[$alatNama] = true;

                continue;
            }

            $rows[] = [
                'id' => (string) Str::uuid(),
                'labkesmas_id' => $labId,
                'alat_id' => $alatId,
                'jumlah' => $jumlah,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        fclose($handle);

        foreach (array_chunk($rows, 300) as $chunk) {
            DB::table('inventaris_alat')->insert($chunk);
        }

        $this->command?->info('Inventaris terisi: '.count($rows)." baris (>0). Dilewati (0): {$skippedZero}.");
        if ($unmatchedLab !== []) {
            $this->command?->warn('Lab tak cocok: '.implode('; ', array_keys($unmatchedLab)));
        }
        if ($unmatchedAlat !== []) {
            $this->command?->warn('Alat tak cocok: '.implode('; ', array_keys($unmatchedAlat)));
        }
    }
}

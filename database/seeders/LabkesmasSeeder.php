<?php

namespace Database\Seeders;

use App\Models\DataPemeriksaan;
use App\Models\JenisPemeriksaan;
use App\Models\KabupatenKota;
use App\Models\Labkesmas;
use App\Models\Negara;
use App\Models\Provinsi;
use App\Models\Regional;
use Illuminate\Database\Seeder;

/**
 * Mengisi hierarki wilayah + labkesmas + jenis tes + data pemeriksaan 6 bulan (Feb–Jul 2026).
 * Angka bersifat deterministik (berbasis indeks) agar hasil seed konsisten.
 */
class LabkesmasSeeder extends Seeder
{
    public function run(): void
    {
        $negara = Negara::create(['nama' => 'Indonesia']);

        // Regional → Provinsi → Kabupaten/Kota
        $tree = [
            'Sumatera' => [
                'Sumatera Utara' => ['Kota Medan', 'Kabupaten Deli Serdang'],
                'Sumatera Selatan' => ['Kota Palembang'],
            ],
            'Jawa & Bali' => [
                'Jawa Barat' => ['Kota Bandung', 'Kabupaten Bekasi'],
                'Bali' => ['Kota Denpasar'],
            ],
        ];

        /** @var array<int, KabupatenKota> $kabKotaList */
        $kabKotaList = [];

        foreach ($tree as $namaRegional => $provinsiMap) {
            $regional = Regional::create(['nama' => $namaRegional, 'negara_id' => $negara->id]);

            foreach ($provinsiMap as $namaProvinsi => $kabKotaNames) {
                $provinsi = Provinsi::create(['nama' => $namaProvinsi, 'regional_id' => $regional->id]);

                foreach ($kabKotaNames as $namaKabKota) {
                    $kabKotaList[] = KabupatenKota::create([
                        'nama' => $namaKabKota,
                        'provinsi_id' => $provinsi->id,
                    ]);
                }
            }
        }

        // Jenis pemeriksaan (master tes)
        $tesList = collect(['TCM TB', 'Mikroskopis Malaria', 'Kimia Air'])
            ->map(fn (string $nama) => JenisPemeriksaan::create(['nama_tes' => $nama]));

        // Labkesmas lintas tier tersebar di beberapa kab/kota.
        // Kota Denpasar sengaja TIDAK diberi labkesmas → mendemonstrasikan Empty State di dashboard.
        $tierByIndex = [5, 4, 3, 2, 3, 4];
        $labkesmasList = [];

        foreach ($kabKotaList as $i => $kabKota) {
            if ($kabKota->nama === 'Kota Denpasar') {
                continue;
            }

            $labkesmasList[] = Labkesmas::create([
                'nama_kantor' => "Labkesmas {$kabKota->nama}",
                'tier_labkesmas' => $tierByIndex[$i] ?? 2,
                'kabupaten_kota_id' => $kabKota->id,
            ]);
        }

        // Data pemeriksaan 6 periode terakhir untuk tiap labkesmas × tiap tes.
        $periods = [
            ['tahun' => 2026, 'bulan' => 2],
            ['tahun' => 2026, 'bulan' => 3],
            ['tahun' => 2026, 'bulan' => 4],
            ['tahun' => 2026, 'bulan' => 5],
            ['tahun' => 2026, 'bulan' => 6],
            ['tahun' => 2026, 'bulan' => 7],
        ];

        foreach ($labkesmasList as $labIndex => $labkesmas) {
            foreach ($tesList as $tesIndex => $tes) {
                foreach ($periods as $periodIndex => $period) {
                    DataPemeriksaan::create([
                        'labkesmas_id' => $labkesmas->id,
                        'jenis_tes_id' => $tes->id,
                        'bulan' => $period['bulan'],
                        'tahun' => $period['tahun'],
                        // Deterministik: naik tiap bulan, bervariasi per labkesmas & tes.
                        'jumlah' => 400 + ($labIndex * 120) + ($tesIndex * 60) + ($periodIndex * 45),
                    ]);
                }
            }
        }
    }
}

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
 * Wilayah (Regional 7 / Kalimantan) + 34 Labkesmas NYATA sesuai dokumen Analisis Kesesuaian.
 * Nama labkesmas DISAMAKAN PERSIS dengan kolom `labkesmas` pada fakta_kesesuaian_labkesmas.csv
 * agar InventarisAlatSeeder dapat menautkan capaian per lab.
 */
class LabkesmasSeeder extends Seeder
{
    /** Provinsi → daftar kabupaten/kota (hanya yang dipakai lab). */
    private const WILAYAH = [
        'Kalimantan Selatan' => [
            'Kota Banjarbaru', 'Kota Banjarmasin', 'Kab. Banjar', 'Kab. Tanah Laut',
            'Kab. Tanah Bumbu', 'Kab. Tapin', 'Kab. Hulu Sungai Tengah', 'Kab. Barito Kuala',
            'Kab. Kotabaru', 'Kab. Tabalong',
        ],
        'Kalimantan Tengah' => [
            'Kota Palangkaraya', 'Kab. Kapuas', 'Kab. Kotawaringin Barat', 'Kab. Lamandau',
            'Kab. Seruyan', 'Kab. Barito Selatan', 'Kab. Kotawaringin Timur', 'Kab. Barito Utara',
            'Kab. Katingan', 'Kab. Sukamara',
        ],
        'Kalimantan Timur' => [
            'Kota Samarinda', 'Kota Balikpapan', 'Kota Bontang', 'Kab. Kutai Timur',
            'Kab. Berau', 'Kab. Paser',
        ],
        'Kalimantan Utara' => [
            'Kota Tarakan', 'Kab. Bulungan', 'Kab. Nunukan',
        ],
    ];

    /** [nama_kantor (persis CSV), tier, kabupaten/kota]. */
    private const LABS = [
        ['BBLKM Banjarbaru', 4, 'Kota Banjarbaru'],
        ['Loka Labkesmas Tanah Bumbu', 4, 'Kab. Tanah Bumbu'],
        ['Labkesprov Kalimantan Timur', 3, 'Kota Samarinda'],
        ['Labkesprov Kalimantan Selatan', 3, 'Kota Banjarbaru'],
        ['Labkesprov Kalimantan Tengah', 3, 'Kota Palangkaraya'],
        ['Labkesda Kab. Banjar', 2, 'Kab. Banjar'],
        ['Labkesda Kota Banjarmasin', 2, 'Kota Banjarmasin'],
        ['Labkesda Kab. Tanah Laut', 2, 'Kab. Tanah Laut'],
        ['Labkesda Kab. Tanah Bumbu', 2, 'Kab. Tanah Bumbu'],
        ['Labkesda Kota Banjarbaru', 2, 'Kota Banjarbaru'],
        ['Labkesda Kab. Tapin', 2, 'Kab. Tapin'],
        ['Labkesda Kab. Hulu Sungai Tengah', 2, 'Kab. Hulu Sungai Tengah'],
        ['Labkesda Kab. Barito Kuala', 2, 'Kab. Barito Kuala'],
        ['Labkesda Kab. Kotabaru', 2, 'Kab. Kotabaru'],
        ['Labkesda Kab. Tabalong', 2, 'Kab. Tabalong'],
        ['Labkesda Kota Palangkaraya', 2, 'Kota Palangkaraya'],
        ['Labkesda Kab. Kapuas', 2, 'Kab. Kapuas'],
        ['Labkesda Kab. Kotawaringin Barat', 2, 'Kab. Kotawaringin Barat'],
        ['Labkesda Lamandau', 2, 'Kab. Lamandau'],
        ['Labkesda Kab. Seruyan', 2, 'Kab. Seruyan'],
        ['Labkesda Kab. Barito Selatan', 2, 'Kab. Barito Selatan'],
        ['Labkesda Kab. Kotawaringin Timur', 2, 'Kab. Kotawaringin Timur'],
        ['Labkesda Kab. Barito Utara', 2, 'Kab. Barito Utara'],
        ['Labkesda Kab. Katingan', 2, 'Kab. Katingan'],
        ['Labkesda Kab. Sukamara', 2, 'Kab. Sukamara'],
        ['Labkesda Kota Balikpapan', 2, 'Kota Balikpapan'],
        ['Labkesda Kab. Kutai Timur', 2, 'Kab. Kutai Timur'],
        ['Labkesda Kab. Berau', 2, 'Kab. Berau'],
        ['Labkesda Kab. Paser', 2, 'Kab. Paser'],
        ['Labkesda Kota Samarinda', 2, 'Kota Samarinda'],
        ['Labkesda Kota Bontang', 2, 'Kota Bontang'],
        ['Labkesda Kota Tarakan', 2, 'Kota Tarakan'],
        ['Labkesda Kab. Bulungan', 2, 'Kab. Bulungan'],
        ['Labkesda Kab. Nunukan', 2, 'Kab. Nunukan'],
    ];

    public function run(): void
    {
        $negara = Negara::create(['nama' => 'Indonesia']);
        $regional = Regional::create(['nama' => 'Regional 7', 'negara_id' => $negara->id]);

        // Buat provinsi + kabupaten/kota; simpan referensi kab/kota per nama.
        $kabByName = [];
        foreach (self::WILAYAH as $namaProvinsi => $kabList) {
            $provinsi = Provinsi::create(['nama' => $namaProvinsi, 'regional_id' => $regional->id]);
            foreach ($kabList as $namaKab) {
                $kabByName[$namaKab] = KabupatenKota::create([
                    'nama' => $namaKab,
                    'provinsi_id' => $provinsi->id,
                ]);
            }
        }

        // 34 Labkesmas nyata.
        $labs = [];
        foreach (self::LABS as [$nama, $tier, $kabNama]) {
            $labs[] = Labkesmas::create([
                'nama_kantor' => $nama,
                'tier_labkesmas' => $tier,
                'jenis_lab' => null,
                'kabupaten_kota_id' => $kabByName[$kabNama]->id,
            ]);
        }

        // Master jenis pemeriksaan + data pemeriksaan demo ringan (6 lab pertama) agar
        // dashboard Pemeriksaan tetap ada isinya. Fokus utama fitur ini = pemenuhan alat.
        $tesList = collect(['TCM TB', 'Mikroskopis Malaria', 'Kimia Air'])
            ->map(fn (string $nama) => JenisPemeriksaan::create(['nama_tes' => $nama]));

        $periods = [
            ['tahun' => 2026, 'bulan' => 2], ['tahun' => 2026, 'bulan' => 3],
            ['tahun' => 2026, 'bulan' => 4], ['tahun' => 2026, 'bulan' => 5],
            ['tahun' => 2026, 'bulan' => 6], ['tahun' => 2026, 'bulan' => 7],
        ];

        foreach (array_slice($labs, 0, 6) as $labIndex => $labkesmas) {
            foreach ($tesList as $tesIndex => $tes) {
                foreach ($periods as $periodIndex => $period) {
                    DataPemeriksaan::create([
                        'labkesmas_id' => $labkesmas->id,
                        'jenis_tes_id' => $tes->id,
                        'bulan' => $period['bulan'],
                        'tahun' => $period['tahun'],
                        'jumlah' => 400 + ($labIndex * 120) + ($tesIndex * 60) + ($periodIndex * 45),
                    ]);
                }
            }
        }
    }
}

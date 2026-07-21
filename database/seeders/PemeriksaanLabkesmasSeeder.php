<?php

namespace Database\Seeders;

use App\Models\DataPemeriksaan;
use App\Models\JenisPemeriksaan;
use App\Models\KabupatenKota;
use App\Models\Labkesmas;
use App\Models\Provinsi;
use Illuminate\Database\Seeder;

/**
 * Rekap riil jumlah pemeriksaan Labkesmas Regional 7, Jan-Jun 2026
 * (sumber: Rekap_Pemeriksaan_Labkesmas_Jan-Jun2026.md). Menggantikan
 * data demo dari LabkesmasSeeder (TCM TB/Mikroskopis Malaria/Kimia Air).
 *
 * Baris tier 3 dicocokkan via provinsi (nama kab di dokumen untuk lab
 * provinsi Kalsel berbeda dari kab domisili di LabkesmasSeeder), baris
 * tier 2/4 dicocokkan via (tier, kabupaten/kota).
 */
class PemeriksaanLabkesmasSeeder extends Seeder
{
    /** Nama kabupaten/kota pada dokumen -> nama persis di KabupatenKota (LabkesmasSeeder::WILAYAH). */
    private const KAB_MAP = [
        'Kota Banjarbaru' => 'Kota Banjarbaru',
        'Kota Banjarmasin' => 'Kota Banjarmasin',
        'Tanah Bumbu' => 'Kab. Tanah Bumbu',
        'Banjar' => 'Kab. Banjar',
        'Tanah Laut' => 'Kab. Tanah Laut',
        'Banjarbaru' => 'Kota Banjarbaru',
        'Tapin' => 'Kab. Tapin',
        'HST' => 'Kab. Hulu Sungai Tengah',
        'Kotabaru' => 'Kab. Kotabaru',
        'Barito Kuala' => 'Kab. Barito Kuala',
        'Kotawaringin Barat' => 'Kab. Kotawaringin Barat',
        'Lamandau' => 'Kab. Lamandau',
        'Kapuas' => 'Kab. Kapuas',
        'Seruyan' => 'Kab. Seruyan',
        'Barito Selatan' => 'Kab. Barito Selatan',
        'Kotawaringin Timur' => 'Kab. Kotawaringin Timur',
        'Kota Palangkaraya' => 'Kota Palangkaraya',
        'Barito Utara' => 'Kab. Barito Utara',
        'Kab. Sukamara' => 'Kab. Sukamara',
        'Katingan' => 'Kab. Katingan',
        'Kutai Timur' => 'Kab. Kutai Timur',
        'Berau' => 'Kab. Berau',
        'Kota Balikpapan' => 'Kota Balikpapan',
        'Paser' => 'Kab. Paser',
        'Kota Samarinda' => 'Kota Samarinda',
        'Kota Bontang' => 'Kota Bontang',
        'Nunukan' => 'Kab. Nunukan',
        'Bulungan' => 'Kab. Bulungan',
        'Kota Tarakan' => 'Kota Tarakan',
    ];

    /**
     * [tier, provinsi, kabDokumen|null, diabetes, ilisari, malaria].
     * kabDokumen null untuk baris tier 3 (dicocokkan via provinsi saja).
     * Sel kosong pada dokumen sudah ditulis sebagai 0 di sini.
     */
    private const BULAN = [
        1 => [
            [4, 'Kalimantan Selatan', 'Kota Banjarbaru', 0, 122, 75],
            [4, 'Kalimantan Selatan', 'Tanah Bumbu', 0, 0, 0],
            [3, 'Kalimantan Selatan', null, 69, 0, 1],
            [2, 'Kalimantan Selatan', 'Kota Banjarmasin', 17, 0, 0],
            [2, 'Kalimantan Selatan', 'Tanah Bumbu', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Banjar', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Tanah Laut', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Banjarbaru', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Tapin', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'HST', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Kotabaru', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Barito Kuala', 0, 0, 0],
            [3, 'Kalimantan Tengah', null, 26, 0, 0],
            [2, 'Kalimantan Tengah', 'Kotawaringin Barat', 102, 0, 0],
            [2, 'Kalimantan Tengah', 'Lamandau', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Kapuas', 6, 0, 1],
            [2, 'Kalimantan Tengah', 'Seruyan', 87, 0, 0],
            [2, 'Kalimantan Tengah', 'Barito Selatan', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Kotawaringin Timur', 119, 0, 0],
            [2, 'Kalimantan Tengah', 'Kota Palangkaraya', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Barito Utara', 3, 0, 0],
            [2, 'Kalimantan Tengah', 'Kab. Sukamara', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Katingan', 0, 0, 0],
            [3, 'Kalimantan Timur', null, 52, 0, 0],
            [2, 'Kalimantan Timur', 'Kutai Timur', 0, 0, 0],
            [2, 'Kalimantan Timur', 'Berau', 0, 0, 0],
            [2, 'Kalimantan Timur', 'Kota Balikpapan', 7, 0, 0],
            [2, 'Kalimantan Timur', 'Paser', 28, 0, 0],
            [2, 'Kalimantan Timur', 'Kota Samarinda', 439, 0, 0],
            [2, 'Kalimantan Timur', 'Kota Bontang', 227, 0, 0],
            [2, 'Kalimantan Utara', 'Nunukan', 0, 0, 0],
            [2, 'Kalimantan Utara', 'Bulungan', 21, 0, 0],
            [2, 'Kalimantan Utara', 'Kota Tarakan', 91, 0, 0],
        ],
        2 => [
            [4, 'Kalimantan Selatan', 'Kota Banjarbaru', 0, 82, 0],
            [4, 'Kalimantan Selatan', 'Tanah Bumbu', 0, 0, 0],
            [3, 'Kalimantan Selatan', null, 60, 0, 0],
            [2, 'Kalimantan Selatan', 'Kota Banjarmasin', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Tanah Bumbu', 1, 0, 0],
            [2, 'Kalimantan Selatan', 'Banjar', 15, 0, 0],
            [2, 'Kalimantan Selatan', 'Tanah Laut', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Banjarbaru', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Tapin', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'HST', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Kotabaru', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Barito Kuala', 0, 0, 0],
            [3, 'Kalimantan Tengah', null, 18, 0, 0],
            [2, 'Kalimantan Tengah', 'Kotawaringin Barat', 117, 0, 0],
            [2, 'Kalimantan Tengah', 'Lamandau', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Kapuas', 5, 0, 0],
            [2, 'Kalimantan Tengah', 'Seruyan', 54, 0, 0],
            [2, 'Kalimantan Tengah', 'Barito Selatan', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Kotawaringin Timur', 74, 0, 3],
            [2, 'Kalimantan Tengah', 'Kota Palangkaraya', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Barito Utara', 3, 0, 0],
            [2, 'Kalimantan Tengah', 'Kab. Sukamara', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Katingan', 0, 0, 0],
            [3, 'Kalimantan Timur', null, 29, 0, 1],
            [2, 'Kalimantan Timur', 'Kutai Timur', 0, 0, 0],
            [2, 'Kalimantan Timur', 'Berau', 0, 0, 0],
            [2, 'Kalimantan Timur', 'Kota Balikpapan', 29, 0, 0],
            [2, 'Kalimantan Timur', 'Paser', 7, 0, 0],
            [2, 'Kalimantan Timur', 'Kota Samarinda', 527, 0, 0],
            [2, 'Kalimantan Timur', 'Kota Bontang', 56, 0, 0],
            [2, 'Kalimantan Utara', 'Nunukan', 6, 0, 0],
            [2, 'Kalimantan Utara', 'Bulungan', 7, 0, 0],
            [2, 'Kalimantan Utara', 'Kota Tarakan', 64, 0, 0],
        ],
        3 => [
            [4, 'Kalimantan Selatan', 'Kota Banjarbaru', 0, 36, 5],
            [4, 'Kalimantan Selatan', 'Tanah Bumbu', 0, 0, 0],
            [3, 'Kalimantan Selatan', null, 43, 0, 0],
            [2, 'Kalimantan Selatan', 'Kota Banjarmasin', 12, 0, 0],
            [2, 'Kalimantan Selatan', 'Tanah Bumbu', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Banjar', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Tanah Laut', 0, 0, 0],
            // Baris "Labkes Keamanan Pangan MBG Kab. Tabalong" dilewati: entitas satu-off
            // di luar 34 lab standar, dan semua nilainya 0 pada dokumen sumber.
            [2, 'Kalimantan Selatan', 'Banjarbaru', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Tapin', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'HST', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Kotabaru', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Barito Kuala', 0, 0, 0],
            [3, 'Kalimantan Tengah', null, 11, 0, 0],
            [2, 'Kalimantan Tengah', 'Kotawaringin Barat', 88, 0, 0],
            [2, 'Kalimantan Tengah', 'Lamandau', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Kapuas', 1, 0, 1],
            [2, 'Kalimantan Tengah', 'Seruyan', 8, 0, 0],
            [2, 'Kalimantan Tengah', 'Barito Selatan', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Kotawaringin Timur', 77, 0, 1],
            [2, 'Kalimantan Tengah', 'Kota Palangkaraya', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Barito Utara', 4, 0, 0],
            [2, 'Kalimantan Tengah', 'Kab. Sukamara', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Katingan', 0, 0, 0],
            [3, 'Kalimantan Timur', null, 35, 0, 0],
            [2, 'Kalimantan Timur', 'Kutai Timur', 0, 0, 0],
            [2, 'Kalimantan Timur', 'Berau', 0, 0, 0],
            [2, 'Kalimantan Timur', 'Kota Balikpapan', 8, 0, 0],
            [2, 'Kalimantan Timur', 'Paser', 9, 0, 0],
            [2, 'Kalimantan Timur', 'Kota Samarinda', 244, 0, 0],
            [2, 'Kalimantan Timur', 'Kota Bontang', 22, 0, 0],
            [2, 'Kalimantan Utara', 'Nunukan', 0, 0, 0],
            [2, 'Kalimantan Utara', 'Bulungan', 7, 0, 0],
            [2, 'Kalimantan Utara', 'Kota Tarakan', 48, 0, 0],
        ],
        4 => [
            [4, 'Kalimantan Selatan', 'Kota Banjarbaru', 1, 82, 20],
            [4, 'Kalimantan Selatan', 'Tanah Bumbu', 0, 0, 3],
            [3, 'Kalimantan Selatan', null, 96, 0, 0],
            [2, 'Kalimantan Selatan', 'Kota Banjarmasin', 20, 0, 0],
            [2, 'Kalimantan Selatan', 'Tanah Bumbu', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Banjar', 46, 0, 0],
            [2, 'Kalimantan Selatan', 'Tanah Laut', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Banjarbaru', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Tapin', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'HST', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Kotabaru', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Barito Kuala', 0, 0, 0],
            [3, 'Kalimantan Tengah', null, 24, 0, 0],
            [2, 'Kalimantan Tengah', 'Kotawaringin Barat', 101, 0, 0],
            [2, 'Kalimantan Tengah', 'Lamandau', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Kapuas', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Seruyan', 49, 0, 0],
            [2, 'Kalimantan Tengah', 'Barito Selatan', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Kotawaringin Timur', 117, 0, 2],
            [2, 'Kalimantan Tengah', 'Kota Palangkaraya', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Barito Utara', 3, 0, 1],
            [2, 'Kalimantan Tengah', 'Kab. Sukamara', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Katingan', 0, 0, 0],
            [3, 'Kalimantan Timur', null, 48, 0, 1],
            [2, 'Kalimantan Timur', 'Kutai Timur', 2, 0, 0],
            [2, 'Kalimantan Timur', 'Berau', 0, 0, 0],
            [2, 'Kalimantan Timur', 'Kota Balikpapan', 11, 0, 0],
            [2, 'Kalimantan Timur', 'Paser', 15, 0, 0],
            [2, 'Kalimantan Timur', 'Kota Samarinda', 608, 0, 0],
            [2, 'Kalimantan Timur', 'Kota Bontang', 67, 0, 0],
            [2, 'Kalimantan Utara', 'Nunukan', 8, 0, 0],
            [2, 'Kalimantan Utara', 'Bulungan', 13, 0, 0],
            [2, 'Kalimantan Utara', 'Kota Tarakan', 83, 0, 0],
        ],
        5 => [
            [4, 'Kalimantan Selatan', 'Kota Banjarbaru', 0, 0, 0],
            [4, 'Kalimantan Selatan', 'Tanah Bumbu', 0, 0, 10],
            [3, 'Kalimantan Selatan', null, 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Kota Banjarmasin', 13, 0, 0],
            [2, 'Kalimantan Selatan', 'Tanah Bumbu', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Banjar', 20, 0, 0],
            [2, 'Kalimantan Selatan', 'Tanah Laut', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Banjarbaru', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Tapin', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'HST', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Kotabaru', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Barito Kuala', 0, 0, 0],
            [3, 'Kalimantan Tengah', null, 10, 0, 0],
            [2, 'Kalimantan Tengah', 'Kotawaringin Barat', 84, 0, 0],
            [2, 'Kalimantan Tengah', 'Lamandau', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Kapuas', 4, 0, 0],
            [2, 'Kalimantan Tengah', 'Seruyan', 17, 0, 0],
            [2, 'Kalimantan Tengah', 'Barito Selatan', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Kotawaringin Timur', 76, 0, 1],
            [2, 'Kalimantan Tengah', 'Kota Palangkaraya', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Barito Utara', 3, 0, 0],
            [2, 'Kalimantan Tengah', 'Kab. Sukamara', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Katingan', 0, 0, 0],
            [3, 'Kalimantan Timur', null, 25, 0, 0],
            [2, 'Kalimantan Timur', 'Kutai Timur', 1, 0, 0],
            [2, 'Kalimantan Timur', 'Berau', 1, 0, 0],
            [2, 'Kalimantan Timur', 'Kota Balikpapan', 6, 0, 0],
            [2, 'Kalimantan Timur', 'Paser', 3, 0, 0],
            [2, 'Kalimantan Timur', 'Kota Samarinda', 534, 0, 4],
            [2, 'Kalimantan Timur', 'Kota Bontang', 192, 0, 0],
            [2, 'Kalimantan Utara', 'Nunukan', 3, 0, 0],
            [2, 'Kalimantan Utara', 'Bulungan', 16, 0, 0],
            [2, 'Kalimantan Utara', 'Kota Tarakan', 67, 0, 0],
        ],
        6 => [
            [4, 'Kalimantan Selatan', 'Kota Banjarbaru', 0, 0, 0],
            [4, 'Kalimantan Selatan', 'Tanah Bumbu', 0, 0, 0],
            [3, 'Kalimantan Selatan', null, 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Kota Banjarmasin', 3, 0, 0],
            [2, 'Kalimantan Selatan', 'Tanah Bumbu', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Banjar', 22, 0, 0],
            [2, 'Kalimantan Selatan', 'Tanah Laut', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Banjarbaru', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Tapin', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'HST', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Kotabaru', 0, 0, 0],
            [2, 'Kalimantan Selatan', 'Barito Kuala', 0, 0, 0],
            [3, 'Kalimantan Tengah', null, 16, 0, 0],
            [2, 'Kalimantan Tengah', 'Kotawaringin Barat', 122, 0, 0],
            [2, 'Kalimantan Tengah', 'Lamandau', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Kapuas', 3, 0, 2],
            [2, 'Kalimantan Tengah', 'Seruyan', 97, 0, 0],
            [2, 'Kalimantan Tengah', 'Barito Selatan', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Kotawaringin Timur', 645, 0, 0],
            [2, 'Kalimantan Tengah', 'Kota Palangkaraya', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Barito Utara', 7, 0, 0],
            [2, 'Kalimantan Tengah', 'Kab. Sukamara', 0, 0, 0],
            [2, 'Kalimantan Tengah', 'Katingan', 0, 0, 0],
            [3, 'Kalimantan Timur', null, 53, 0, 0],
            [2, 'Kalimantan Timur', 'Kutai Timur', 1, 0, 0],
            [2, 'Kalimantan Timur', 'Berau', 3, 0, 0],
            [2, 'Kalimantan Timur', 'Kota Balikpapan', 9, 0, 0],
            [2, 'Kalimantan Timur', 'Paser', 3, 0, 0],
            [2, 'Kalimantan Timur', 'Kota Samarinda', 405, 0, 0],
            [2, 'Kalimantan Timur', 'Kota Bontang', 191, 0, 0],
            [2, 'Kalimantan Utara', 'Nunukan', 1, 0, 0],
            [2, 'Kalimantan Utara', 'Bulungan', 12, 0, 0],
            [2, 'Kalimantan Utara', 'Kota Tarakan', 153, 0, 0],
        ],
    ];

    public function run(): void
    {
        // Ganti data pemeriksaan yang ada (demo TCM TB/Mikroskopis Malaria/Kimia Air).
        DataPemeriksaan::query()->delete();
        JenisPemeriksaan::query()->delete();

        $diabetes = JenisPemeriksaan::create([
            'nama_tes' => 'Diabetes (DM/HbA1C)',
            'deskripsi' => 'Pemeriksaan DM dengan HbA1C',
        ]);
        $ilisari = JenisPemeriksaan::create([
            'nama_tes' => 'ILI-SARI',
            'deskripsi' => 'Influenza-Like Illness / Severe Acute Respiratory Infection',
        ]);
        $malaria = JenisPemeriksaan::create([
            'nama_tes' => 'Malaria',
            'deskripsi' => 'Malaria (Mikroskopis, PCR, dan RDT)',
        ]);

        // Lookup labkesmas: tier 2/4 via (tier, kabupaten/kota); tier 3 via (tier, provinsi).
        $labkesmasList = Labkesmas::with('kabupatenKota.provinsi')->get();

        $byTierKab = [];
        $byTierProvinsi = [];
        foreach ($labkesmasList as $lab) {
            $byTierKab[$lab->tier_labkesmas.'|'.$lab->kabupatenKota->nama] = $lab;
            if ($lab->tier_labkesmas === 3) {
                $byTierProvinsi[$lab->kabupatenKota->provinsi->nama] = $lab;
            }
        }

        foreach (self::BULAN as $bulan => $baris) {
            foreach ($baris as [$tier, $provinsi, $kabDokumen, $diabetesJumlah, $ilisariJumlah, $malariaJumlah]) {
                $lab = $tier === 3
                    ? ($byTierProvinsi[$provinsi] ?? null)
                    : ($byTierKab[$tier.'|'.(self::KAB_MAP[$kabDokumen] ?? $kabDokumen)] ?? null);

                if (! $lab) {
                    throw new \RuntimeException("Labkesmas tidak ditemukan: tier {$tier}, provinsi {$provinsi}, kab {$kabDokumen}");
                }

                foreach ([
                    [$diabetes, $diabetesJumlah],
                    [$ilisari, $ilisariJumlah],
                    [$malaria, $malariaJumlah],
                ] as [$tes, $jumlah]) {
                    DataPemeriksaan::create([
                        'labkesmas_id' => $lab->id,
                        'jenis_tes_id' => $tes->id,
                        'bulan' => $bulan,
                        'tahun' => 2026,
                        'jumlah' => $jumlah,
                    ]);
                }
            }
        }
    }
}

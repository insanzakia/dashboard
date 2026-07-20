<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Katalog alat + standar jumlah minimal per tier, transkrip dari
 * KMK No. HK.01.07/MENKES/1801/2024, Tabel 8 (Standar Peralatan Labkesmas).
 *
 * Format tiap baris: [nama_alat, [T1, T2, T3, T4, T5-Biokes, T5-Kesling]].
 * Angka = jumlah unit minimal; 0 = tidak dipersyaratkan (tidak dibuat baris standar).
 * Tier 1-4 disimpan sebagai jenis_lab 'umum'; tier 5 dipisah 'biokes' & 'kesling'.
 */
class AlatStandarSeeder extends Seeder
{
    /** Peta indeks kolom → [tier, jenis_lab]. */
    private const TIER_MAP = [
        0 => [1, 'umum'],
        1 => [2, 'umum'],
        2 => [3, 'umum'],
        3 => [4, 'umum'],
        4 => [5, 'biokes'],
        5 => [5, 'kesling'],
    ];

    public function run(): void
    {
        $now = now();
        $alatRows = [];
        $standarRows = [];

        foreach ($this->catalog() as $kategori => $items) {
            foreach ($items as [$nama, $qty]) {
                $alatId = (string) Str::uuid();

                $alatRows[] = [
                    'id' => $alatId,
                    'nama_alat' => $nama,
                    'kategori' => $kategori,
                    'keterangan' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                foreach ($qty as $idx => $jumlah) {
                    if ($jumlah <= 0) {
                        continue;
                    }
                    [$tier, $jenisLab] = self::TIER_MAP[$idx];
                    $standarRows[] = [
                        'id' => (string) Str::uuid(),
                        'alat_id' => $alatId,
                        'tier' => $tier,
                        'jenis_lab' => $jenisLab,
                        'jumlah_minimal' => $jumlah,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        // Bulk insert (dipecah agar aman dari batas parameter driver).
        foreach (array_chunk($alatRows, 100) as $chunk) {
            DB::table('alat')->insert($chunk);
        }
        foreach (array_chunk($standarRows, 200) as $chunk) {
            DB::table('standar_alat')->insert($chunk);
        }
    }

    /**
     * @return array<string, array<int, array{0: string, 1: array<int, int>}>>
     */
    private function catalog(): array
    {
        return [
            // A. Hematologi, Kimia Klinik & Imunologi
            'hematologi_kimia_imunologi' => [
                ['Alat Tes Darah Portabel (Hemoglobin)', [1, 0, 0, 0, 0, 0]],
                ['Alat Tes Darah Portabel (Gula Darah)', [1, 0, 0, 0, 0, 0]],
                ['Alat Tes Darah Portabel (Asam Urat)', [1, 0, 0, 0, 0, 0]],
                ['Alat Tes Darah Portabel (Kolesterol)', [1, 0, 0, 0, 0, 0]],
                ['Hematology Analyzer', [1, 1, 1, 1, 0, 0]],
                ['Chemistry Analyzer', [1, 1, 1, 1, 0, 0]],
                ['Semi Automated Urine Analyzer', [1, 1, 1, 0, 0, 0]],
                ['Automated Urine Analyzer', [0, 0, 0, 1, 0, 0]],
                ['ELISA Set (Reader + Washer)', [0, 1, 1, 1, 2, 2]],
                ['Immunoassay Newborn Screening (FIA)', [0, 0, 1, 1, 0, 0]],
                ['Immuno Analyzer (CLIA)', [0, 0, 1, 1, 1, 0]],
                ['Hb Electrophoresis Analyzer', [0, 0, 1, 1, 0, 0]],
                ['Flow Cytometer', [0, 0, 0, 1, 1, 0]],
            ],

            // B. Mikrobiologi
            'mikrobiologi' => [
                ['Rak dan Bak Pewarnaan', [1, 2, 2, 2, 2, 2]],
                ['Digital Colony Counter', [0, 2, 2, 0, 0, 0]],
                ['Automated Colony Counter', [0, 0, 0, 1, 1, 1]],
                ['Inkubator', [0, 2, 2, 4, 6, 6]],
                ['Inkubator CO2', [0, 1, 1, 1, 2, 2]],
                ['Biosafety Cabinet Class II Type A2 (Mikrobiologi)', [0, 1, 1, 1, 1, 1]],
                ['Biosafety Cabinet Class II Type B2', [0, 0, 1, 1, 2, 1]],
                ['Nephelometer', [0, 1, 1, 1, 1, 1]],
                ['Mikrobiologi Air Sampler', [0, 1, 1, 3, 5, 3]],
                ['Antibiotik Disc-dispenser', [0, 0, 1, 1, 1, 0]],
                ['Mesin Identifikasi & Uji Kepekaan Otomatik (Microba Analyzer)', [0, 0, 1, 1, 1, 1]],
                ['MALDI-TOF', [0, 0, 0, 1, 1, 1]],
                ['Mycobacteriology Analyzer (Liquid Media)', [0, 0, 1, 1, 1, 0]],
                ['Endotoxin LAL Assay (Quantitative)', [0, 0, 1, 1, 1, 0]],
                ['Anaerobic Jar', [0, 0, 1, 1, 1, 1]],
                ['Automated Blood Culture System', [0, 0, 1, 1, 1, 0]],
            ],

            // C. Biomolekuler
            'biomolekuler' => [
                ['Tes Cepat Molekuler', [1, 1, 0, 0, 0, 0]],
                ['Automatic Extractor', [0, 1, 1, 1, 1, 1]],
                ['Biosafety Cabinet Class II Type A2 (Biomolekuler)', [0, 1, 1, 1, 1, 1]],
                ['Real Time PCR', [0, 1, 2, 2, 3, 3]],
                ['Laminar Air Flow (Biomolekuler)', [0, 1, 1, 1, 1, 1]],
                ['Spindown', [0, 1, 1, 2, 3, 3]],
                ['Heat Block', [0, 1, 1, 1, 1, 1]],
                ['Cooling Rack', [0, 1, 2, 2, 3, 3]],
                ['NGS Long Read Sequencer', [0, 0, 1, 1, 1, 1]],
                ['NGS Short Read Sequencer', [0, 0, 0, 1, 1, 1]],
                ['PCR Konvensional', [0, 0, 0, 1, 1, 1]],
                ['Digital PCR', [0, 0, 0, 1, 1, 1]],
                ['Shaker Plate', [0, 0, 0, 1, 1, 1]],
                ['Alat Kuantifikasi DNA', [0, 0, 0, 1, 2, 2]],
                ['Magnetic Stand', [0, 0, 0, 1, 2, 2]],
                ['Sanger Sequencer', [0, 0, 0, 1, 1, 1]],
                ['Elektroforesis Set', [0, 0, 0, 1, 1, 1]],
                ['Trans Illuminator Gel Electrophoresis', [0, 0, 0, 1, 1, 1]],
                ['Sonicator Chamber', [0, 0, 0, 1, 1, 1]],
                ['Rocking Shaker', [0, 0, 0, 1, 1, 1]],
                ['Cell Disruptor', [0, 0, 0, 1, 1, 1]],
                ['Tissue Disruptor', [0, 0, 0, 1, 1, 1]],
                ['Bioinformatics System (Komputer + Software)', [0, 0, 0, 1, 1, 1]],
                ['Oligosynthesizer', [0, 0, 0, 0, 1, 0]],
            ],

            // D. Kesehatan Lingkungan
            'kesehatan_lingkungan' => [
                ['Sanitarian Kit', [1, 0, 0, 0, 0, 0]],
                ['Anemometer', [0, 1, 1, 1, 0, 1]],
                ['Flowmeter Udara', [0, 1, 1, 1, 0, 1]],
                ['Thermohygro-barometer', [0, 1, 2, 2, 0, 1]],
                ['Low Volume Air Sampler', [0, 1, 1, 1, 0, 1]],
                ['High Volume Air Sampler', [0, 1, 1, 1, 0, 1]],
                ['CO Detector', [0, 1, 1, 1, 0, 1]],
                ['CO2 Detector', [0, 1, 1, 1, 0, 1]],
                ['Air Sampler Impinger', [0, 1, 1, 1, 0, 1]],
                ['Chlorine Test', [0, 1, 1, 1, 0, 1]],
                ['pH Meter Include Suhu', [0, 1, 1, 1, 0, 1]],
                ['Total Dissolved Solids (TDS) Meter', [0, 1, 1, 1, 0, 1]],
                ['Chemical Oxygen Demand (COD) Reactor', [0, 1, 1, 1, 0, 1]],
                ['Biochemical Oxygen Demand (BOD) Incubator', [0, 1, 1, 1, 0, 1]],
                ['Manifold Pump Set / Membrane Filter', [0, 1, 1, 1, 0, 1]],
                ['Desiccator', [0, 1, 1, 1, 0, 1]],
                ['Stomacher', [0, 1, 1, 1, 0, 1]],
                ['Turbidimeter', [0, 1, 1, 1, 0, 1]],
                ['Surveymeter (Radiation Meter)', [0, 0, 1, 1, 0, 1]],
                ['Radon Gas Detector', [0, 0, 1, 1, 0, 1]],
                ['Air Quality Monitoring System (AQMS)', [0, 0, 0, 1, 0, 1]],
                ['Liquid Scintillation Counting (LSC)', [0, 0, 0, 1, 0, 1]],
                ['Ion Kromatografi', [0, 0, 0, 1, 0, 1]],
                ['Digital Particulate Meter PM 2,5 & 10', [0, 1, 1, 1, 0, 1]],
                ['Pompa Vakum (Portable)', [0, 1, 1, 1, 0, 1]],
                ['Dissolved Oxygen (DO) Meter', [0, 1, 1, 1, 0, 1]],
                ['Secchi Disk', [0, 1, 1, 1, 0, 1]],
                ['Aerator Pump', [0, 1, 1, 1, 0, 1]],
                ['UV Index Test', [0, 1, 1, 0, 0, 0]],
                ['Termometer (Kesling)', [0, 1, 1, 0, 0, 0]],
            ],

            // E. Toksikologi Klinik & Lingkungan
            'toksikologi' => [
                ['Spektrofotometer UV Vis', [0, 1, 1, 1, 1, 1]],
                ['Atomic Absorption Spectrophotometry (AAS)', [0, 1, 1, 1, 1, 1]],
                ['ICP OES (Optical Emission)', [0, 0, 1, 1, 1, 1]],
                ['ICP-MS (Mass Spectrometry)', [0, 0, 1, 1, 1, 1]],
                ['GC-MS (FID Headspace, Pyrolysis)', [0, 0, 1, 1, 1, 1]],
                ['HPLC', [0, 0, 1, 1, 0, 0]],
                ['GC-MS/MS (FID Headspace, Pyrolysis)', [0, 0, 0, 0, 1, 1]],
                ['LC-MS/MS Detector', [0, 0, 0, 0, 1, 1]],
            ],

            // F. Vektor & Binatang Pembawa Penyakit
            'vektor_bpp' => [
                ['Entomologi Kit', [1, 1, 0, 0, 0, 0]],
                ['Aspirator', [0, 5, 5, 10, 0, 15]],
                ['Dipper', [0, 5, 5, 10, 0, 15]],
                ['Disection Kit Vektor', [0, 2, 3, 7, 0, 10]],
                ['Susceptibility Test Set', [0, 1, 2, 4, 0, 7]],
                ['Light Trap', [0, 1, 1, 1, 0, 1]],
                ['Kandang Nyamuk + Rak', [0, 5, 5, 10, 0, 15]],
                ['CDC Bottle Assay for Mosquito', [0, 0, 5, 10, 0, 15]],
                ['Box Specimen Vektor & Reservoir', [0, 0, 10, 10, 0, 20]],
            ],

            // G. Penunjang
            'penunjang' => [
                ['Lemari Pendingin Penyimpan Reagen', [1, 0, 0, 0, 0, 0]],
                ['Mikropipet (5 Ukuran) + Carousel', [2, 3, 10, 15, 25, 25]],
                ['Mikroskop Binokuler', [2, 3, 5, 8, 8, 8]],
                ['Low Speed Centrifuge (Darah & Urin)', [2, 3, 3, 3, 3, 3]],
                ['Rotator Plate', [1, 1, 1, 2, 2, 2]],
                ['Dehumidifier', [2, 5, 8, 10, 10, 20]],
                ['Cool Box Sample/Spesimen', [5, 10, 20, 20, 20, 20]],
                ['Global Positioning System (GPS)', [0, 1, 1, 1, 0, 0]],
                ['Pipette Gun', [0, 1, 1, 1, 1, 1]],
                ['Multichannel Pipet (4 Ukuran, 8 Row)', [0, 1, 3, 6, 10, 10]],
                ['Water Purification System', [0, 1, 2, 6, 6, 6]],
                ['Dispenser Pipet', [0, 3, 6, 10, 10, 10]],
                ['Mikroskop Stereo', [0, 1, 2, 4, 0, 5]],
                ['Refrigerated Centrifuge (High Speed)', [0, 2, 2, 2, 3, 3]],
                ['Oven', [0, 2, 2, 4, 6, 6]],
                ['Lemari Asam', [0, 2, 3, 4, 4, 4]],
                ['Lemari Reagen Flammable', [0, 3, 4, 4, 4, 4]],
                ['Refrigerator Lab Grade (Sampel Lingkungan & Makanan)', [0, 2, 3, 8, 0, 10]],
                ['Refrigerator Lab Grade (Spesimen)', [0, 1, 2, 5, 5, 5]],
                ['Refrigerator Lab Grade (Reagen)', [0, 2, 3, 8, 10, 10]],
                ['Freezer -20 with Rack System', [0, 2, 5, 8, 10, 10]],
                ['Thermocouple', [0, 1, 1, 2, 2, 4]],
                ['Analytical Balance', [0, 1, 4, 6, 6, 6]],
                ['Analytical Balance Micro', [0, 1, 1, 1, 1, 1]],
                ['Autoklaf Basah', [0, 1, 2, 2, 2, 2]],
                ['Autoklaf Kering', [0, 1, 1, 2, 2, 2]],
                ['Waterbath', [0, 1, 1, 1, 1, 1]],
                ['Vortex', [0, 2, 2, 4, 6, 6]],
                ['Magnetic Stirrer with Hotplate', [0, 2, 2, 3, 3, 3]],
                ['Laminar Air Flow (Penunjang)', [0, 1, 1, 1, 1, 1]],
                ['Deep Freezer (-80) with Rack System', [0, 0, 1, 3, 5, 5]],
                ['Temperature Monitoring System (Refrigerator & Freezer)', [0, 0, 1, 1, 1, 1]],
                ['Freeze Dry Machine (Cryopreservation)', [0, 0, 1, 1, 1, 1]],
                ['Microwave Digester', [0, 0, 1, 1, 1, 1]],
                ['Teaching Microscope', [0, 0, 1, 1, 1, 1]],
                ['Mikroskop Inverted', [0, 0, 0, 1, 3, 3]],
                ['Mikroskop Lapangan Gelap', [0, 0, 0, 1, 1, 3]],
                ['Mikroskop Fluorescence', [0, 0, 0, 1, 1, 3]],
                ['Liquid Nitrogen Tank with Canister', [0, 0, 0, 1, 1, 1]],
                ['Refrigerated Centrifuge (Ultra High Speed)', [0, 0, 0, 0, 1, 1]],
                ['Nitrogen Evaporator', [0, 0, 0, 0, 1, 1]],
            ],

            // H. Kalibrasi
            'kalibrasi' => [
                ['Anak Timbangan Class F1 (1 mg - 2 kg)', [0, 0, 1, 1, 0, 0]],
                ['Anak Timbangan Standar E2 (1 mg - 2 kg)', [0, 0, 1, 1, 0, 0]],
                ['Mass Comparator', [0, 0, 1, 1, 0, 0]],
                ['Thermometer (Kalibrasi)', [0, 0, 1, 1, 0, 0]],
                ['Tachometer', [0, 0, 1, 1, 0, 0]],
                ['Stopwatch', [0, 0, 1, 1, 0, 0]],
                ['Filter Standard', [0, 0, 0, 1, 0, 0]],
                ['Oil Bath', [0, 0, 0, 1, 0, 0]],
                ['PRT (Platinum Resistance Thermometer) + Read Out', [0, 0, 0, 1, 0, 0]],
                ['Dry Block', [0, 0, 0, 1, 0, 0]],
                ['Thermocouple + Temperatur Data Logger (Min 10 Channel)', [0, 0, 0, 1, 0, 0]],
                ['Humidity Chamber', [0, 0, 0, 1, 0, 0]],
                ['Temperature & Pressure Data Logger', [0, 0, 0, 1, 0, 0]],
                ['Sistem Kalibrasi Luxmeter', [0, 0, 0, 1, 0, 0]],
            ],
        ];
    }
}

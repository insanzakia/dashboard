<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Katalog alat + standar jumlah minimal per tier, versi ACUAN (ASPAK-aligned).
 * Sumber: Analisis Kesesuaian Peralatan Labkesmas (Tabel 8 KMK 1801/2024, dim_alat_standar).
 *
 * Format tiap baris: [nama_alat_kmk, [std_t2, std_t3, std_t4], alias_aspak].
 * Angka = jumlah unit minimal; 0 = tidak dipersyaratkan (tidak dibuat baris standar).
 * Semua tier 2-4 memakai jenis_lab 'umum' (dataset ini tidak memuat lab tier 5).
 *
 * nama_alat DISAMAKAN PERSIS dengan kolom `alat_kmk` pada fakta_kesesuaian_labkesmas.csv
 * agar InventarisAlatSeeder bisa join berdasarkan nama.
 */
class AlatStandarSeeder extends Seeder
{
    /** Indeks kolom qty → tier (semua jenis_lab 'umum'). */
    private const TIERS = [0 => 2, 1 => 3, 2 => 4];

    public function run(): void
    {
        $now = now();
        $alatRows = [];
        $standarRows = [];

        foreach ($this->catalog() as $kategori => $items) {
            foreach ($items as [$nama, $qty, $alias]) {
                $alatId = (string) Str::uuid();

                $alatRows[] = [
                    'id' => $alatId,
                    'nama_alat' => $nama,
                    'kategori' => $kategori,
                    'keterangan' => null,
                    'alias_aspak' => $alias !== '' ? $alias : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                foreach ($qty as $idx => $jumlah) {
                    if ($jumlah <= 0) {
                        continue;
                    }
                    $standarRows[] = [
                        'id' => (string) Str::uuid(),
                        'alat_id' => $alatId,
                        'tier' => self::TIERS[$idx],
                        'jenis_lab' => 'umum',
                        'jumlah_minimal' => $jumlah,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        foreach (array_chunk($alatRows, 100) as $chunk) {
            DB::table('alat')->insert($chunk);
        }
        foreach (array_chunk($standarRows, 200) as $chunk) {
            DB::table('standar_alat')->insert($chunk);
        }
    }

    /**
     * @return array<string, array<int, array{0: string, 1: array<int, int>, 2: string}>>
     */
    private function catalog(): array
    {
        return [
            'hematologi_kimia_imunologi' => [
                ['Hematology Analyzer', [1, 1, 1], 'Hematology Analyzer; Hematology Analyzer 5 Diff'],
                ['Chemistry Analyzer', [1, 1, 1], 'Chemistry Analyzer'],
                ['Semi Automated Urine Analyzer', [1, 1, 0], 'Semi Automated Urine Analyzer'],
                ['Automated Urine Analyzer', [0, 0, 1], 'Automated Urine Analyzer'],
                ['ELISA Set (Reader + Washer)', [1, 1, 1], 'ELISA Set (ELISA Reader dan ELISA washer)'],
                ['Immunoassay Newborn Screening (FIA)', [0, 1, 1], 'Immunoassay For Newborn Screening (FIA)'],
                ['Immuno Analyzer (CLIA)', [0, 1, 1], 'Immuno Analyzer (Chemiluminescence Immunoassay/ CLIA)'],
                ['Hb Electrophoresis Analyzer', [0, 1, 1], 'Hb Electrophoresis Analyzer'],
                ['Flow Cytometer', [0, 0, 1], 'Flow Cytometer'],
            ],

            'mikrobiologi' => [
                ['Rak dan Bak Pewarnaan', [2, 2, 2], 'Rak dan Bak Pewarnaan'],
                ['Digital Colony Counter', [2, 2, 0], 'Digital Colony Counter'],
                ['Automated Colony Counter', [0, 0, 1], 'Automated colony counter'],
                ['Inkubator (Microbiological incubator)', [2, 2, 4], 'Microbiological incubator'],
                ['Inkubator CO2', [1, 1, 1], 'Incubator CO2'],
                ['Biosafety Cabinet Class II Type A2 (mikro+biomol)', [2, 2, 2], 'Biosafety Cabinet Class II Type A2'],
                ['Biosafety Cabinet Class II Type B2', [0, 1, 1], 'Biosafety Cabinet Class II Type B2'],
                ['Nephelometer', [1, 1, 1], 'Nephelometer for clinical use'],
                ['Mikrobiologi Air Sampler', [1, 1, 3], 'Microbiology Air Sampler'],
                ['Antibiotik Disc-dispenser', [0, 1, 1], 'Antibiotik Disc-dispenser'],
                ['Microba Analyzer (identifikasi & uji kepekaan)', [0, 1, 1], 'Mesin identifikasi dan uji kepekaan otomatik/microba analyzer'],
                ['MALDI-TOF', [0, 0, 1], 'Mass spectrometer MALDI-TOF'],
                ['Mycobacteriology Analyzer (Liquid media)', [0, 1, 1], 'Mycobacteriology analyzer for tuberculosis using Liquid media'],
                ['Endotoxin LAL Assay (Quantitative)', [0, 1, 1], 'Endotoxin LAL Assay (Quantitative)'],
                ['Anaerobic Jar', [0, 1, 1], 'Anaerobic Jar'],
                ['Automated Blood Culture System', [0, 1, 1], 'Automated Blood Culture System'],
            ],

            'biomolekuler' => [
                ['Tes Cepat Molekuler (TCM)', [1, 0, 0], 'Tes Cepat Molekuler (TCM)'],
                ['Automatic Extractor', [1, 1, 1], 'Automatic Extractor'],
                ['Real Time PCR', [1, 2, 2], 'Real Time PCR'],
                ['Laminar Air Flow', [1, 1, 1], 'Laminar Air Flow'],
                ['Spindown', [1, 1, 2], 'Spindown'],
                ['Heat Block', [1, 1, 1], 'Heat Block'],
                ['Cooling Rack', [1, 2, 2], 'Cooling Rack'],
                ['NGS Long Read Sequencer', [0, 1, 1], 'Next Generation Sequencer (NGS) Long Read Sequencer'],
                ['NGS Short Read Sequencer', [0, 0, 1], 'Next Generation Sequencer (NGS) Short Read Sequencer'],
                ['PCR Konvensional', [0, 0, 1], 'PCR Konvensional'],
                ['Digital PCR', [0, 0, 1], ''],
                ['Shaker Plate', [0, 0, 1], 'Shaker Plate'],
                ['Alat Kuantifikasi DNA', [0, 0, 1], 'Alat kuantifikasi DNA'],
                ['Magnetic Stand', [0, 0, 1], 'Magnetic stand'],
                ['Sanger Sequencer', [0, 0, 1], 'Sanger sequencer'],
                ['Elektroforesis Set', [0, 0, 1], 'Elektroforesis set'],
                ['Trans Illuminator Gel Electrophoresis', [0, 0, 1], ''],
                ['Sonicator Chamber', [0, 0, 1], ''],
                ['Rocking Shaker', [0, 0, 1], 'Rocking Shaker'],
                ['Cell Disruptor', [0, 0, 1], 'Cell Disruptor'],
                ['Tissue Disruptor', [0, 0, 1], ''],
                ['Bioinformatics System', [0, 0, 1], 'Bioinformatics System (computer dan Software)'],
            ],

            'kesehatan_lingkungan' => [
                ['Anemometer', [1, 1, 1], 'Anemometer'],
                ['Flowmeter Udara', [1, 1, 1], 'Flowmeter udara'],
                ['Thermohygro-barometer', [1, 2, 2], 'Thermohygro-barometer'],
                ['Low Volume Air Sampler', [1, 1, 1], 'Low Volume Air Sampler'],
                ['High Volume Air Sampler', [1, 1, 1], 'High Volume Air Sampler'],
                ['CO Detector', [1, 1, 1], 'CO detector'],
                ['CO2 Detector', [1, 1, 1], 'CO2 detector'],
                ['Air Sampler Impinger', [1, 1, 1], 'Air Sampler Impinger'],
                ['Chlorine Test', [1, 1, 1], 'Chlorine Test'],
                ['pH Meter Include Suhu', [1, 1, 1], 'pH Meter Include Suhu'],
                ['Total Dissolved Solids (TDS) Meter', [1, 1, 1], 'Total Dissolved Solids (TDS) Meter'],
                ['COD Reactor', [1, 1, 1], 'Chemical Oxygen Demand (COD) Reactor'],
                ['BOD Incubator', [1, 1, 1], 'Biochemical Oxygen Demand (BOD) Incubator'],
                ['Manifold Pump Set / Membrane Filter', [1, 1, 1], 'Manifold pump set/membrane filter'],
                ['Desiccator', [1, 1, 1], 'Desiccator'],
                ['Stomacher', [1, 1, 1], 'Stomacher'],
                ['Turbidimeter', [1, 1, 1], 'Turbidimeter'],
                ['Surveymeter (Radiation Meter)', [0, 1, 1], ''],
                ['Radon Gas Detector', [0, 1, 1], 'Radon gas detector'],
                ['Air Quality Monitoring System (AQMS)', [0, 0, 1], 'Air Quality Monitorium System (AQMS)'],
                ['Liquid Scintillation Counting (LSC)', [0, 0, 1], 'Liquid Scinstilation Counting (LSC)'],
                ['Ion Kromatografi', [0, 0, 1], 'Ion Kromatografi'],
                ['Digital Particulate Meter PM 2,5 & 10', [1, 1, 1], 'Digital Particulate meter PM 2,5 dan 10'],
                ['Pompa Vakum (portable)', [1, 1, 1], 'Pompa vakum (portable)'],
                ['Dissolved Oxygen (DO) Meter', [1, 1, 1], 'Dissolved Oxygen (DO) Meter'],
                ['Secchi Disk', [1, 1, 1], 'Secchi disk'],
                ['Aerator Pump', [1, 1, 1], 'Aerator Pump'],
                ['UV Index Test', [1, 1, 0], 'UV index test'],
                ['Termometer', [1, 1, 0], 'Termometer'],
            ],

            'toksikologi' => [
                ['Spektrofotometer UV Vis', [1, 1, 1], 'Spektrofotometer UV Vis'],
                ['Atomic Absorption Spectrophotometry (AAS)', [1, 1, 1], 'Atomic Absorption Spectrophotometry (AAS)'],
                ['ICP OES', [0, 1, 1], 'Inductively Coupled Plasma Optical Emission Spectrometry (ICP OES)'],
                ['ICP-MS', [0, 1, 1], 'Inductively Coupled Plasma Mass Spectrometry (ICPMS)'],
                ['GC-MS', [0, 1, 1], 'Gas Chromatografy Mass Spectrometry (GCMS)'],
                ['HPLC', [0, 1, 1], 'High Performance Liquid Chromatography (HPLC)'],
            ],

            'vektor_bpp' => [
                ['Entomologi Kit', [1, 0, 0], 'Entomologi Kit'],
                ['Aspirator', [5, 5, 10], 'Aspirator'],
                ['Dipper', [5, 5, 10], 'Dipper'],
                ['Disection Kit Vektor', [2, 3, 7], 'Disection Kit Vektor'],
                ['Susceptibility Test Set', [1, 2, 4], 'Susceptibility Test Set'],
                ['Light Trap', [1, 1, 1], 'Light Trap'],
                ['Kandang Nyamuk + Rak', [5, 5, 10], 'Kandang Nyamuk + Rak'],
                ['CDC Bottle Assay for Mosquito', [0, 5, 10], 'CDC Bottle assay for mosquito'],
                ['Box Specimen - Vektor & Reservoir', [0, 10, 10], 'Box Specimen - vektor dan reservoir'],
            ],

            'penunjang' => [
                ['Mikropipet (5 ukuran) + Carousel', [3, 10, 15], 'Mikropipet (5 ukuran) + Carousel'],
                ['Mikroskop Binokuler', [3, 5, 8], 'Microscope binocular'],
                ['Low Speed Centrifuge (darah & urin)', [3, 3, 3], 'Low speed centrifuge (darah dan urin)'],
                ['Rotator Plate', [1, 1, 2], 'Rotator Plate'],
                ['Dehumidifier', [5, 8, 10], 'Dehumidifier'],
                ['Cool Box Sample/Spesimen', [10, 20, 20], 'Cool Box Sample/spesimen'],
                ['Global Positioning System (GPS)', [1, 1, 1], 'Global Positioning System (GPS)'],
                ['Pipette Gun', [1, 1, 1], 'Pipette Gun'],
                ['Multichannel Pipet (4 ukuran, 8 row)', [1, 3, 6], 'Multichannel Pipet (8 row)'],
                ['Water Purification System', [1, 2, 6], 'Water purification system'],
                ['Dispenser Pipet', [3, 6, 10], 'Dispenser Pipet'],
                ['Mikroskop Stereo', [1, 2, 4], 'Mikroskop Stereo'],
                ['Refrigerated Centrifuge (high speed)', [2, 2, 2], 'Refrigerated Centrifuge (highspeed)'],
                ['Oven', [2, 2, 4], 'Oven'],
                ['Lemari Asam', [2, 3, 4], 'Lemari Asam'],
                ['Lemari Reagen Flammable', [3, 4, 4], 'Lemari Reagen Flammable'],
                ['Refrigerator Lab Grade (sampel lingk. & makanan)', [2, 3, 8], 'Refrigerator Laboratory Grade (untuk sampel lingkungan dan makanan)'],
                ['Refrigerator Lab Grade (spesimen)', [1, 2, 5], 'Refrigerator Laboratory Grade (untuk Specimen)'],
                ['Refrigerator Lab Grade (reagen)', [2, 3, 8], 'Refrigerator Laboratory Grade (untuk reagen)'],
                ['Freezer -20 with Rack System', [2, 5, 8], 'Freezer -20 with rack system'],
                ['Thermocouple', [1, 1, 2], 'Thermocouple'],
                ['Analytical Balance', [1, 4, 6], 'Analytical Balance'],
                ['Analytical Balance Micro', [1, 1, 1], 'Analytical Balance micro'],
                ['Autoklaf Basah (Steam sterilizer)', [1, 2, 2], 'Steam sterilizer'],
                ['Autoklaf Kering', [1, 1, 2], 'Autoklaf Kering'],
                ['Waterbath', [1, 1, 1], 'Waterbath'],
                ['Vortex', [2, 2, 4], 'Vortex'],
                ['Magnetic Stirrer with Hotplate', [2, 2, 3], 'Magnetic Stirer with hotplate'],
                ['Deep Freezer (-80) with Rack System', [0, 1, 3], 'Deep Freezer (-80) with rack system (drawer)'],
                ['Temperature Monitoring System', [0, 1, 1], ''],
                ['Freeze Dry Machine (cryopreservation)', [0, 1, 1], 'Freeze dry machine (cryopreservation)'],
                ['Microwave Digester', [0, 1, 1], 'Microwave Digestion'],
                ['Teaching Microscope', [0, 1, 1], ''],
                ['Mikroskop Inverted', [0, 0, 1], 'Mikroskop Inverted'],
                ['Mikroskop Lapangan Gelap', [0, 0, 1], 'Mikroskop Lapangan Gelap'],
                ['Mikroskop Fluorescence', [0, 0, 1], 'Mikroskop Fluorescence'],
                ['Liquid Nitrogen Tank with Canister', [0, 0, 1], 'Liquid Nitrogen Tank with canester'],
            ],

            'kalibrasi' => [
                ['Anak Timbangan Class F1 (1 mg - 2 kg)', [0, 1, 1], 'Anak Timbangan Class F1 (1 mg - 2 kg)'],
                ['Anak Timbangan Standar E2 (1 mg - 2 kg)', [0, 1, 1], 'Anak Timbangan standar E2 (1 mg - 2 kg)'],
                ['Mass Comparator', [0, 1, 1], 'Mass Comparator'],
                ['Thermometer (kalibrasi)', [0, 1, 1], ''],
                ['Tachometer', [0, 1, 1], ''],
                ['Stopwatch', [0, 1, 1], 'Stopwatch'],
                ['Filter Standard', [0, 0, 1], 'Filter standard'],
                ['Oil Bath', [0, 0, 1], 'Oil Bath'],
                ['PRT + Read Out', [0, 0, 1], 'PRT (Platinum Resistance Thermometer) dengan Read out'],
                ['Dry Block', [0, 0, 1], 'Dry block'],
                ['Thermocouple + Data Logger (min 10 channel)', [0, 0, 1], ''],
                ['Humidity Chamber', [0, 0, 1], 'Humidity Chamber'],
                ['Temperature & Pressure Data Logger', [0, 0, 1], ''],
                ['Sistem Kalibrasi Luxmeter', [0, 0, 1], 'Sistem kalibrasi luxmeter'],
            ],
        ];
    }
}

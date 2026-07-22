import type { TierLabkesmas } from '@/types/labkesmas';

export const APP_NAME = 'Dashboard Labkesmas';

export interface TierOption {
    value: TierLabkesmas;
    label: string;
    description: string;
}

/** Sumber tunggal label Tier Labkesmas — jangan hardcode "Tier 5"/"Nasional" dkk di komponen manapun. */
export const TIER_OPTIONS: TierOption[] = [
    { value: 5, label: 'Tier 5', description: 'Nasional' },
    { value: 4, label: 'Tier 4', description: 'Regional' },
    { value: 3, label: 'Tier 3', description: 'Provinsi' },
    { value: 2, label: 'Tier 2', description: 'Kabupaten/Kota' },
];

export const MONTH_NAMES = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
] as const;

export const DASHBOARD_ENDPOINTS = {
    summary: '/dashboard-data/summary',
    trend: '/dashboard-data/trend',
    trendByJenis: '/dashboard-data/trend-by-jenis',
    jenisPemeriksaan: '/dashboard-data/jenis-pemeriksaan',
    trendGrouped: '/dashboard-data/trend-grouped',
    trendMultiLabkesmas: '/dashboard-data/trend-multi-labkesmas',
    labPemeriksaan: (id: string) => `/dashboard-data/lab-pemeriksaan/${id}`,
    regional: '/dashboard-data/wilayah/regional',
    provinsi: '/dashboard-data/wilayah/provinsi',
    kabupatenKota: '/dashboard-data/wilayah/kabupaten-kota',
} as const;

/** Dimensi pengelompokan tren pemeriksaan (Card "Tren per Wilayah/Tier"). */
export const TREND_GROUP_BY_OPTIONS = [
    { value: 'provinsi', label: 'Provinsi' },
    { value: 'regional', label: 'Regional' },
    { value: 'tier', label: 'Tier' },
    { value: 'labkesmas', label: 'Labkesmas' },
] as const;

export type TrendGroupByDimension = (typeof TREND_GROUP_BY_OPTIONS)[number]['value'];

/** Endpoint pemenuhan standar alat (dikonsumsi standarService). */
export const STANDAR_ENDPOINTS = {
    lab: (id: string) => `/standar-data/lab/${id}`,
    agregat: '/standar-data/agregat',
    perbandingan: '/standar-data/perbandingan',
    grouped: '/standar-data/grouped',
    multi: '/standar-data/multi',
} as const;

/** Dimensi pengelompokan grafik ringkasan (Feature 3). */
export const GROUP_BY_OPTIONS = [
    { value: 'tier', label: 'Tier' },
    { value: 'provinsi', label: 'Provinsi' },
    { value: 'regional', label: 'Regional' },
    { value: 'kabupaten_kota', label: 'Kabupaten/Kota' },
] as const;

export type GroupByDimension = (typeof GROUP_BY_OPTIONS)[number]['value'];

/** Status pemenuhan per alat (ASPAK 3-nilai) — label + kelas warna badge. */
export const STATUS_ALAT: Record<string, { label: string; className: string }> = {
    sesuai: { label: 'Sesuai', className: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' },
    kurang: { label: 'Kurang', className: 'bg-amber-500/10 text-amber-600 dark:text-amber-400' },
    tidak_ada: { label: 'Tidak Ada', className: 'bg-rose-500/10 text-rose-600 dark:text-rose-400' },
};

/** Label 8 kategori alat KMK — SUMBER TUNGGAL, jangan hardcode di komponen. */
export const KATEGORI_ALAT: Record<string, string> = {
    hematologi_kimia_imunologi: 'Hematologi, Kimia Klinik & Imunologi',
    mikrobiologi: 'Mikrobiologi',
    biomolekuler: 'Biomolekuler',
    kesehatan_lingkungan: 'Kesehatan Lingkungan',
    toksikologi: 'Toksikologi Klinik & Lingkungan',
    vektor_bpp: 'Vektor & Binatang Pembawa Penyakit',
    penunjang: 'Penunjang',
    kalibrasi: 'Kalibrasi',
};

/** Urutan kategori untuk tampilan (mengikuti dokumen KMK). */
export const KATEGORI_ORDER = Object.keys(KATEGORI_ALAT);

/** Opsi jenis lab (khusus tier 5). */
export const JENIS_LAB_OPTIONS = [
    { value: 'biokes', label: 'Lab Biokes (Biologi Kesehatan)' },
    { value: 'kesling', label: 'Lab Kesling (Kesehatan Lingkungan)' },
] as const;

export const JENIS_LAB_LABEL: Record<string, string> = {
    biokes: 'Biokes',
    kesling: 'Kesling',
    umum: 'Umum',
};

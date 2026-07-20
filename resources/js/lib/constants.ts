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
    regional: '/dashboard-data/wilayah/regional',
    provinsi: '/dashboard-data/wilayah/provinsi',
    kabupatenKota: '/dashboard-data/wilayah/kabupaten-kota',
} as const;

/** Endpoint pemenuhan standar alat (dikonsumsi standarService). */
export const STANDAR_ENDPOINTS = {
    lab: (id: string) => `/standar-data/lab/${id}`,
    agregat: '/standar-data/agregat',
    perbandingan: '/standar-data/perbandingan',
} as const;

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

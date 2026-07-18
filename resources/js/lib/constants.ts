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
    negara: '/dashboard-data/wilayah/negara',
    regional: '/dashboard-data/wilayah/regional',
    provinsi: '/dashboard-data/wilayah/provinsi',
    kabupatenKota: '/dashboard-data/wilayah/kabupaten-kota',
} as const;

import type { TierLabkesmas } from './labkesmas';
import type { WilayahFilterValue } from './wilayah';

/** Status generik untuk setiap request data — dipakai semua hook agar UI selalu bisa menangani 4 kondisi. */
export type RequestStatus = 'loading' | 'success' | 'empty' | 'error';

export interface RequestState<T> {
    status: RequestStatus;
    data: T | null;
    errorMessage: string | null;
}

export interface DashboardFilter {
    wilayah: WilayahFilterValue;
    tier: TierLabkesmas | null;
}

export interface SummaryCardData {
    id: string;
    label: string;
    totalPemeriksaan: number;
    deltaPercentase: number | null;
}

export interface TrendDataPoint {
    periode: string; // contoh: "2026-01"
    jumlah: number;
}

export interface TrendSeries {
    id: string;
    label: string;
    points: TrendDataPoint[];
}

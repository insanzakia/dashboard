import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function formatNumber(value: number): string {
    return new Intl.NumberFormat('id-ID').format(value);
}

export function formatPercentage(value: number): string {
    const sign = value > 0 ? '+' : '';
    return `${sign}${value.toFixed(1)}%`;
}

/** Format persentase pemenuhan (0-100); null → "N/A". */
export function formatPersen(value: number | null): string {
    return value === null ? 'N/A' : `${value.toFixed(1)}%`;
}

/** Warna indikator pemenuhan: hijau ≥80, kuning ≥50, merah <50, abu-abu jika N/A. */
export function persenColor(value: number | null): string {
    if (value === null) return '#94a3b8'; // slate-400
    if (value >= 80) return '#10b981'; // emerald-500
    if (value >= 50) return '#f59e0b'; // amber-500
    return '#f43f5e'; // rose-500
}

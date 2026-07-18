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

import { formatPersen, persenColor } from '@/lib/utils';

export interface PercentMeterProps {
    label: string;
    /** 0-100, atau null (N/A). */
    value: number | null;
    /** Teks kecil di kanan bawah, mis. "12/20 terpenuhi". */
    sublabel?: string;
}

/** Bar horizontal berlabel untuk menampilkan persentase pemenuhan (dengan warna indikatif). */
export function PercentMeter({ label, value, sublabel }: PercentMeterProps) {
    const width = value === null ? 0 : Math.min(100, Math.max(0, value));

    return (
        <div className="flex flex-col gap-1">
            <div className="flex items-baseline justify-between gap-2">
                <span className="text-sm text-foreground">{label}</span>
                <span className="text-sm font-semibold tabular-nums text-foreground">{formatPersen(value)}</span>
            </div>
            <div className="h-2 w-full overflow-hidden rounded-full bg-muted">
                <div
                    className="h-full rounded-full transition-[width] duration-500"
                    style={{ width: `${width}%`, backgroundColor: persenColor(value) }}
                />
            </div>
            {sublabel && <span className="text-xs text-muted-foreground">{sublabel}</span>}
        </div>
    );
}

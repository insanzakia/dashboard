import { formatNumber } from '@/lib/utils';

interface ChartTooltipPayloadEntry {
    name: string;
    value: number;
    color?: string;
}

export interface ChartTooltipProps {
    active?: boolean;
    label?: string;
    payload?: ChartTooltipPayloadEntry[];
}

/** Custom tooltip Recharts — nama series & warna diambil dari payload, tidak ada teks hardcoded. */
export function ChartTooltip({ active, label, payload }: ChartTooltipProps) {
    if (!active || !payload?.length) {
        return null;
    }

    return (
        <div className="rounded-lg border bg-popover px-3 py-2 text-xs shadow-md">
            <p className="mb-1 font-medium text-popover-foreground">{label}</p>
            {payload.map((entry) => (
                <p key={entry.name} className="flex items-center gap-1.5 text-muted-foreground">
                    <span className="h-2 w-2 rounded-full" style={{ backgroundColor: entry.color }} />
                    {entry.name}: <span className="font-medium text-foreground">{formatNumber(entry.value)}</span>
                </p>
            ))}
        </div>
    );
}

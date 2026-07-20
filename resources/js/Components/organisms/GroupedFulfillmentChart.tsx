import { Bar, BarChart, Cell, LabelList, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import { EmptyState } from '@/Components/molecules/EmptyState';
import { ErrorState } from '@/Components/molecules/ErrorState';
import { Skeleton } from '@/Components/atoms/Skeleton';
import { formatPersen, persenColor } from '@/lib/utils';
import type { RequestState } from '@/types/dashboard';
import type { GroupedRow } from '@/types/pemenuhanAlat';

interface TooltipEntry {
    payload: GroupedRow;
}

function GroupTooltip({ active, payload }: { active?: boolean; payload?: TooltipEntry[] }) {
    if (!active || !payload?.length) return null;
    const row = payload[0].payload;
    return (
        <div className="rounded-md border bg-background px-3 py-2 text-xs shadow-sm">
            <p className="font-medium text-foreground">{row.label}</p>
            <p className="text-muted-foreground">
                {formatPersen(row.persen_rata)} · rata-rata {row.jumlah_lab} lab
            </p>
        </div>
    );
}

/** Grafik batang % pemenuhan rata-rata per grup (tier/provinsi/regional/kab-kota). */
export function GroupedFulfillmentChart({ state }: { state: RequestState<GroupedRow[]> }) {
    if (state.status === 'loading') {
        return <Skeleton className="h-64 w-full" />;
    }

    if (state.status === 'error') {
        return <ErrorState message={state.errorMessage ?? 'Gagal memuat ringkasan.'} />;
    }

    if (state.status === 'empty' || !state.data || state.data.length === 0) {
        return (
            <EmptyState
                title="Belum Ada Data"
                description="Tidak ada Labkesmas berstandar pada cakupan ini."
            />
        );
    }

    const rows = state.data;
    const height = Math.max(180, rows.length * 40 + 24);

    return (
        <div className="w-full overflow-x-auto">
            <ResponsiveContainer width="100%" height={height} minWidth={320}>
                <BarChart data={rows} layout="vertical" margin={{ top: 4, right: 52, left: 8, bottom: 4 }}>
                    <XAxis type="number" domain={[0, 100]} tick={{ fontSize: 12 }} stroke="var(--muted-foreground)" unit="%" />
                    <YAxis
                        type="category"
                        dataKey="label"
                        width={160}
                        tick={{ fontSize: 12 }}
                        stroke="var(--muted-foreground)"
                    />
                    <Tooltip content={<GroupTooltip />} cursor={{ fill: 'var(--muted)', opacity: 0.3 }} />
                    <Bar dataKey="persen_rata" radius={[0, 4, 4, 0]} isAnimationActive={false}>
                        {rows.map((row) => (
                            <Cell key={row.key} fill={persenColor(row.persen_rata)} />
                        ))}
                        <LabelList
                            dataKey="persen_rata"
                            position="right"
                            formatter={(value) => `${Math.round(Number(value))}%`}
                            className="fill-foreground"
                            style={{ fontSize: 11 }}
                        />
                    </Bar>
                </BarChart>
            </ResponsiveContainer>
        </div>
    );
}

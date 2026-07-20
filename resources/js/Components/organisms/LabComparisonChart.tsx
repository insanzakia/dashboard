import { Bar, BarChart, Cell, LabelList, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import { EmptyState } from '@/Components/molecules/EmptyState';
import { ErrorState } from '@/Components/molecules/ErrorState';
import { Skeleton } from '@/Components/atoms/Skeleton';
import { formatPersen, persenColor } from '@/lib/utils';
import type { RequestState } from '@/types/dashboard';
import type { LabComparisonRow } from '@/types/pemenuhanAlat';

export interface LabComparisonChartProps {
    state: RequestState<LabComparisonRow[]>;
}

interface TooltipEntry {
    payload: LabComparisonRow & { persenNum: number };
}

function CompTooltip({ active, payload }: { active?: boolean; payload?: TooltipEntry[] }) {
    if (!active || !payload?.length) return null;
    const row = payload[0].payload;
    return (
        <div className="rounded-md border bg-background px-3 py-2 text-xs shadow-sm">
            <p className="font-medium text-foreground">{row.nama_kantor}</p>
            <p className="text-muted-foreground">
                Tier {row.tier}
                {row.jenis_lab ? ` · ${row.jenis_lab}` : ''} · {formatPersen(row.persen)}
            </p>
            <p className="text-muted-foreground">
                {row.total_terpenuhi}/{row.total_wajib} jenis alat terpenuhi
            </p>
        </div>
    );
}

/** Bar chart horizontal peringkat pemenuhan antar-lab (hanya lab yang punya standar). */
export function LabComparisonChart({ state }: LabComparisonChartProps) {
    if (state.status === 'loading') {
        return <Skeleton className="h-64 w-full" />;
    }

    if (state.status === 'error') {
        return <ErrorState message={state.errorMessage ?? 'Gagal memuat perbandingan.'} />;
    }

    if (state.status === 'empty' || !state.data) {
        return (
            <EmptyState
                title="Belum Ada Lab untuk Dibandingkan"
                description="Tidak ada Labkesmas pada cakupan wilayah/tier ini."
            />
        );
    }

    const rows = state.data
        .filter((r) => r.persen !== null)
        .map((r) => ({ ...r, persenNum: r.persen as number }));

    if (rows.length === 0) {
        return (
            <EmptyState
                title="Belum Ada Data Pemenuhan"
                description="Lab pada cakupan ini belum memiliki standar/inventaris untuk dibandingkan."
            />
        );
    }

    // Tinggi menyesuaikan jumlah lab agar label terbaca; area chart bisa di-scroll.
    const height = Math.max(180, rows.length * 38 + 24);

    return (
        <div className="w-full overflow-x-auto">
            <ResponsiveContainer width="100%" height={height} minWidth={320}>
                <BarChart data={rows} layout="vertical" margin={{ top: 4, right: 48, left: 8, bottom: 4 }}>
                    <XAxis type="number" domain={[0, 100]} tick={{ fontSize: 12 }} stroke="var(--muted-foreground)" unit="%" />
                    <YAxis
                        type="category"
                        dataKey="nama_kantor"
                        width={150}
                        tick={{ fontSize: 12 }}
                        stroke="var(--muted-foreground)"
                    />
                    <Tooltip content={<CompTooltip />} cursor={{ fill: 'var(--muted)', opacity: 0.3 }} />
                    <Bar dataKey="persenNum" radius={[0, 4, 4, 0]} isAnimationActive={false}>
                        {rows.map((row) => (
                            <Cell key={row.labkesmas_id} fill={persenColor(row.persenNum)} />
                        ))}
                        <LabelList
                            dataKey="persenNum"
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

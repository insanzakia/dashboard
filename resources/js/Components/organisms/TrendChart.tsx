import { CartesianGrid, Line, LineChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import { EmptyState } from '@/Components/molecules/EmptyState';
import { ErrorState } from '@/Components/molecules/ErrorState';
import { ChartTooltip } from '@/Components/molecules/ChartTooltip';
import { Skeleton } from '@/Components/atoms/Skeleton';
import type { RequestState, TrendSeries } from '@/types/dashboard';

export interface TrendChartProps {
    state: RequestState<TrendSeries[]>;
    onRetry?: () => void;
    height?: number;
}

export const CHART_COLORS = ['var(--chart-1)', 'var(--chart-2)', 'var(--chart-3)', 'var(--chart-4)', 'var(--chart-5)'];

/** Menyusun TrendSeries[] (per-series) menjadi baris data lebar yang dibutuhkan Recharts <LineChart>. */
function toChartRows(series: TrendSeries[]) {
    const periods = Array.from(new Set(series.flatMap((s) => s.points.map((p) => p.periode)))).sort();

    return periods.map((periode) => {
        const row: Record<string, string | number> = { periode };
        for (const s of series) {
            const point = s.points.find((p) => p.periode === periode);
            row[s.label] = point?.jumlah ?? 0;
        }
        return row;
    });
}

export function TrendChart({ state, onRetry, height = 320 }: TrendChartProps) {
    if (state.status === 'loading') {
        return <Skeleton style={{ height }} className="w-full" />;
    }

    if (state.status === 'error') {
        return <ErrorState message={state.errorMessage ?? 'Gagal memuat tren pemeriksaan.'} onRetry={onRetry} />;
    }

    if (state.status === 'empty' || !state.data) {
        return (
            <EmptyState
                title="Belum Ada Tren untuk Ditampilkan"
                description="Data pemeriksaan bulanan belum tersedia untuk cakupan wilayah/tier yang dipilih."
            />
        );
    }

    const rows = toChartRows(state.data);

    return (
        <ResponsiveContainer width="100%" height={height}>
            <LineChart data={rows} margin={{ top: 8, right: 16, left: 0, bottom: 0 }}>
                <CartesianGrid strokeDasharray="3 3" className="stroke-border" />
                <XAxis dataKey="periode" tick={{ fontSize: 12 }} stroke="var(--muted-foreground)" />
                <YAxis tick={{ fontSize: 12 }} stroke="var(--muted-foreground)" width={48} />
                <Tooltip content={<ChartTooltip />} />
                {state.data.map((series, index) => (
                    <Line
                        key={series.id}
                        type="monotone"
                        dataKey={series.label}
                        name={series.label}
                        stroke={CHART_COLORS[index % CHART_COLORS.length]}
                        strokeWidth={2}
                        dot={false}
                        // Matikan animasi line-draw: mencegah garis hilang setelah animasi selesai
                        // (isu Recharts) sekaligus membuat update saat ganti filter terasa instan.
                        isAnimationActive={false}
                    />
                ))}
            </LineChart>
        </ResponsiveContainer>
    );
}

import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { StatCard } from '@/Components/molecules/StatCard';
import { EmptyState } from '@/Components/molecules/EmptyState';
import { ErrorState } from '@/Components/molecules/ErrorState';
import { Skeleton } from '@/Components/atoms/Skeleton';
import { MultiSeriesTrendChart } from '@/Components/organisms/MultiSeriesTrendChart';
import { CHART_COLORS } from '@/Components/organisms/TrendChart';
import { useLabPemeriksaan } from '@/hooks/useLabPemeriksaan';
import { formatNumber } from '@/lib/utils';

export interface LabPemeriksaanSectionProps {
    labId: string;
}

/** Ringkasan pemeriksaan satu Labkesmas: total, rincian per jenis, dan tren bulanan per jenis. */
export function LabPemeriksaanSection({ labId }: LabPemeriksaanSectionProps) {
    const state = useLabPemeriksaan(labId);

    if (state.status === 'loading') {
        return <Skeleton className="h-64 w-full" />;
    }

    if (state.status === 'error') {
        return <ErrorState message={state.errorMessage ?? 'Gagal memuat data pemeriksaan.'} />;
    }

    if (state.status === 'empty' || !state.data || state.data.total === 0) {
        return (
            <EmptyState
                title="Belum Ada Data Pemeriksaan"
                description="Labkesmas ini belum memiliki data jumlah pemeriksaan yang dilaporkan."
            />
        );
    }

    const data = state.data;
    const maxTotal = Math.max(...data.per_jenis.map((j) => j.total), 1);

    // Warnai bar per jenis mengikuti warna deret pada grafik tren agar konsisten.
    const colorById = new Map(data.trend.map((s, i) => [s.id, CHART_COLORS[i % CHART_COLORS.length]]));

    return (
        <div className="flex flex-col gap-6">
            <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <StatCard
                    label="Total Pemeriksaan"
                    value={formatNumber(data.total)}
                    deltaLabel={`${data.per_jenis.length} jenis pemeriksaan`}
                    accent="var(--chart-1)"
                />
                <Card className="lg:col-span-2">
                    <CardHeader>
                        <CardTitle className="text-sm font-semibold text-foreground">Per Jenis Pemeriksaan</CardTitle>
                    </CardHeader>
                    <CardContent className="flex flex-col gap-3">
                        {data.per_jenis.map((jenis) => {
                            const share = data.total > 0 ? (jenis.total / data.total) * 100 : 0;
                            const width = (jenis.total / maxTotal) * 100;
                            const color = colorById.get(jenis.id) ?? 'var(--chart-1)';
                            return (
                                <div key={jenis.id} className="flex flex-col gap-1">
                                    <div className="flex items-baseline justify-between gap-2">
                                        <span className="text-sm text-foreground">{jenis.nama_tes}</span>
                                        <span className="text-sm font-semibold tabular-nums text-foreground">
                                            {formatNumber(jenis.total)}
                                            <span className="ml-1 text-xs font-normal text-muted-foreground">
                                                {share.toFixed(0)}%
                                            </span>
                                        </span>
                                    </div>
                                    <div className="h-2 w-full overflow-hidden rounded-full bg-muted">
                                        <div
                                            className="h-full rounded-full transition-[width] duration-500"
                                            style={{ width: `${width}%`, backgroundColor: color }}
                                        />
                                    </div>
                                </div>
                            );
                        })}
                    </CardContent>
                </Card>
            </div>

            <div>
                <h3 className="mb-3 text-sm font-medium text-muted-foreground">Tren Bulanan per Jenis</h3>
                <MultiSeriesTrendChart
                    state={{ status: 'success', data: data.trend, errorMessage: null }}
                    emptyDescription="Belum ada tren pemeriksaan bulanan untuk lab ini."
                />
            </div>
        </div>
    );
}

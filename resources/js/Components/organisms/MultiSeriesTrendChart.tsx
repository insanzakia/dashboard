import { useState } from 'react';
import { Button } from '@/Components/ui/button';
import { EmptyState } from '@/Components/molecules/EmptyState';
import { ErrorState } from '@/Components/molecules/ErrorState';
import { Skeleton } from '@/Components/atoms/Skeleton';
import { TrendChart } from '@/Components/organisms/TrendChart';
import { cn } from '@/lib/utils';
import type { RequestState, TrendSeries } from '@/types/dashboard';

export interface MultiSeriesTrendChartProps {
    state: RequestState<TrendSeries[]>;
    emptyTitle?: string;
    emptyDescription?: string;
}

/**
 * Tampilan tren multi-series dengan pilihan mode: satu grafik gabungan (semua series ditumpuk
 * sebagai garis dalam satu chart) atau grafik terpisah (satu chart kecil per series).
 * Toggle mode hanya muncul kalau ada lebih dari satu series — dengan satu series, keduanya identik.
 */
export function MultiSeriesTrendChart({
    state,
    emptyTitle = 'Belum Ada Tren untuk Ditampilkan',
    emptyDescription = 'Data pemeriksaan bulanan belum tersedia untuk cakupan yang dipilih.',
}: MultiSeriesTrendChartProps) {
    const [mode, setMode] = useState<'gabungan' | 'terpisah'>('gabungan');

    if (state.status === 'loading') {
        return <Skeleton className="h-80 w-full" />;
    }

    if (state.status === 'error') {
        return <ErrorState message={state.errorMessage ?? 'Gagal memuat tren pemeriksaan.'} />;
    }

    if (state.status === 'empty' || !state.data) {
        return <EmptyState title={emptyTitle} description={emptyDescription} />;
    }

    const canSplit = state.data.length > 1;

    return (
        <div className="flex flex-col gap-4">
            {canSplit && (
                <div className="inline-flex w-fit rounded-md border p-0.5">
                    <Button
                        type="button"
                        size="sm"
                        variant="ghost"
                        className={cn(
                            'rounded-sm',
                            mode === 'gabungan' && 'bg-primary text-primary-foreground hover:bg-primary/90',
                        )}
                        onClick={() => setMode('gabungan')}
                    >
                        Satu Grafik (Tumpuk)
                    </Button>
                    <Button
                        type="button"
                        size="sm"
                        variant="ghost"
                        className={cn(
                            'rounded-sm',
                            mode === 'terpisah' && 'bg-primary text-primary-foreground hover:bg-primary/90',
                        )}
                        onClick={() => setMode('terpisah')}
                    >
                        Grafik Terpisah
                    </Button>
                </div>
            )}

            {!canSplit || mode === 'gabungan' ? (
                <TrendChart state={state} height={320} />
            ) : (
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    {state.data.map((series) => (
                        <div key={series.id} className="flex flex-col gap-2 rounded-lg border p-3">
                            <span className="text-sm font-medium text-foreground">{series.label}</span>
                            <TrendChart
                                state={{ status: 'success', data: [series], errorMessage: null }}
                                height={220}
                            />
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

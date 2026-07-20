import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { StatCard } from '@/Components/molecules/StatCard';
import { PercentMeter } from '@/Components/molecules/PercentMeter';
import { EmptyState } from '@/Components/molecules/EmptyState';
import { ErrorState } from '@/Components/molecules/ErrorState';
import { Skeleton } from '@/Components/atoms/Skeleton';
import { KATEGORI_ALAT } from '@/lib/constants';
import { formatPersen, persenColor } from '@/lib/utils';
import type { RequestState } from '@/types/dashboard';
import type { AggregateFulfillment } from '@/types/pemenuhanAlat';

export interface FulfillmentSummaryProps {
    state: RequestState<AggregateFulfillment>;
    scopeLabel: string;
}

/** Ringkasan agregat pemenuhan alat pada cakupan wilayah/tier (rata-rata antar-lab). */
export function FulfillmentSummary({ state, scopeLabel }: FulfillmentSummaryProps) {
    if (state.status === 'loading') {
        return <Skeleton className="h-48 w-full" />;
    }

    if (state.status === 'error') {
        return <ErrorState message={state.errorMessage ?? 'Gagal memuat agregat pemenuhan.'} />;
    }

    if (state.status === 'empty' || !state.data) {
        return (
            <EmptyState
                title="Belum Ada Data Pemenuhan"
                description="Belum ada Labkesmas dengan standar pada cakupan ini, atau data kepemilikan alat belum diinput."
            />
        );
    }

    const { persen_rata, jumlah_lab, per_kategori } = state.data;

    return (
        <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div className="flex flex-col gap-4">
                <StatCard
                    label={`Rata-rata Pemenuhan · ${scopeLabel}`}
                    value={formatPersen(persen_rata)}
                    accent={persenColor(persen_rata)}
                />
                <StatCard label="Jumlah Labkesmas" value={String(jumlah_lab)} />
            </div>

            <Card className="lg:col-span-2">
                <CardHeader>
                    <CardTitle className="text-sm font-semibold text-foreground">
                        Rata-rata Pemenuhan per Kategori
                    </CardTitle>
                </CardHeader>
                <CardContent className="flex flex-col gap-3">
                    {per_kategori.length === 0 && (
                        <p className="text-sm text-muted-foreground">Tidak ada kategori untuk ditampilkan.</p>
                    )}
                    {per_kategori.map((k) => (
                        <PercentMeter
                            key={k.kategori}
                            label={KATEGORI_ALAT[k.kategori] ?? k.kategori}
                            value={k.persen_rata}
                            sublabel={`rata-rata dari ${k.jumlah_lab} lab`}
                        />
                    ))}
                </CardContent>
            </Card>
        </div>
    );
}

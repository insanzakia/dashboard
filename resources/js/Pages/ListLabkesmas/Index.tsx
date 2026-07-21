import { Head } from '@inertiajs/react';
import { PublicLayout } from '@/Layouts/PublicLayout';
import { Card, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { WilayahCascadeFilter } from '@/Components/organisms/WilayahCascadeFilter';
import { TierSelector } from '@/Components/molecules/TierSelector';
import { LabFulfillmentGrid } from '@/Components/organisms/LabFulfillmentGrid';
import { DashboardFilterProvider, useDashboardFilter } from '@/context/DashboardFilterContext';
import { useLabComparison } from '@/hooks/useLabComparison';
import { formatPersen } from '@/lib/utils';

function ListContent() {
    const { filter, setTier, resetFilter } = useDashboardFilter();
    const comparison = useLabComparison(filter);

    const rows = comparison.status === 'success' ? (comparison.data ?? []) : [];
    const scored = rows.filter((r) => r.persen !== null);
    const rataRata =
        scored.length > 0 ? scored.reduce((sum, r) => sum + (r.persen ?? 0), 0) / scored.length : null;

    return (
        <div className="flex flex-col gap-6">
            <section className="flex flex-col gap-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-lg font-semibold text-foreground">List Labkesmas</h1>
                        <p className="mt-1 text-sm text-muted-foreground">
                            Persentase kesesuaian alat tiap Labkesmas terhadap standar KMK. Klik sebuah lab untuk
                            melihat rincian per alatnya.
                        </p>
                    </div>
                    <Button variant="ghost" size="sm" onClick={resetFilter}>
                        Reset Filter
                    </Button>
                </div>

                <Card>
                    <CardContent className="pt-6">
                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-4">
                            <div className="sm:col-span-3">
                                <WilayahCascadeFilter />
                            </div>
                            <TierSelector value={filter.tier} onChange={setTier} />
                        </div>
                    </CardContent>
                </Card>
            </section>

            <section className="flex flex-col gap-4">
                <div className="flex flex-wrap items-baseline justify-between gap-2">
                    <h2 className="text-base font-semibold text-foreground">Pemenuhan per Labkesmas</h2>
                    {rows.length > 0 && (
                        <p className="text-sm text-muted-foreground">
                            {rows.length} Labkesmas
                            {rataRata !== null && (
                                <>
                                    {' · '}rata-rata{' '}
                                    <span className="font-semibold text-foreground">{formatPersen(rataRata)}</span>
                                </>
                            )}
                        </p>
                    )}
                </div>
                <LabFulfillmentGrid state={comparison} />
            </section>
        </div>
    );
}

export default function ListLabkesmasIndex() {
    return (
        <PublicLayout>
            <Head title="List Labkesmas" />
            <DashboardFilterProvider>
                <ListContent />
            </DashboardFilterProvider>
        </PublicLayout>
    );
}

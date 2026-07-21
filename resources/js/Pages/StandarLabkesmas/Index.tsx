import { useState } from 'react';
import { Head } from '@inertiajs/react';
import { PublicLayout } from '@/Layouts/PublicLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { FilterDropdown } from '@/Components/molecules/FilterDropdown';
import { WilayahCascadeFilter } from '@/Components/organisms/WilayahCascadeFilter';
import { TierSelector } from '@/Components/molecules/TierSelector';
import { GroupedFulfillmentChart } from '@/Components/organisms/GroupedFulfillmentChart';
import { LabComparisonChart } from '@/Components/organisms/LabComparisonChart';
import { DashboardFilterProvider, useDashboardFilter } from '@/context/DashboardFilterContext';
import { useGroupedFulfillment } from '@/hooks/useGroupedFulfillment';
import { useLabComparison } from '@/hooks/useLabComparison';
import { GROUP_BY_OPTIONS, type GroupByDimension } from '@/lib/constants';

const GROUP_BY_LABEL: Record<string, string> = Object.fromEntries(GROUP_BY_OPTIONS.map((o) => [o.value, o.label]));

function StandarContent() {
    const { filter, setTier, resetFilter } = useDashboardFilter();
    const comparison = useLabComparison(filter);

    const [groupBy, setGroupBy] = useState<GroupByDimension>('tier');
    const grouped = useGroupedFulfillment(filter, groupBy);

    return (
        <div className="flex flex-col gap-6">
            <section className="flex flex-col gap-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-lg font-semibold text-foreground">Standar Peralatan Labkesmas</h1>
                        <p className="mt-1 text-sm text-muted-foreground">
                            Grafik pemenuhan alat Labkesmas terhadap standar KMK per cakupan wilayah/tier.
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

            {/* Grafik utama: peringkat pemenuhan tiap Labkesmas */}
            <section>
                <Card>
                    <CardHeader>
                        <CardTitle className="text-base font-semibold text-foreground">
                            Peringkat Pemenuhan Antar-Labkesmas
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <LabComparisonChart state={comparison} />
                    </CardContent>
                </Card>
            </section>

            {/* Grafik agregasi rata-rata pemenuhan per dimensi */}
            <section>
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between gap-4 space-y-0">
                        <CardTitle className="text-base font-semibold text-foreground">
                            Rata-rata Pemenuhan per {GROUP_BY_LABEL[groupBy]}
                        </CardTitle>
                        <div className="w-52">
                            <FilterDropdown
                                label="Kelompokkan menurut"
                                placeholder="Pilih dimensi"
                                value={groupBy}
                                options={GROUP_BY_OPTIONS.map((o) => ({ value: o.value, label: o.label }))}
                                onChange={(v) => setGroupBy((v as GroupByDimension) ?? 'tier')}
                            />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <GroupedFulfillmentChart state={grouped} />
                    </CardContent>
                </Card>
            </section>
        </div>
    );
}

export default function StandarLabkesmasIndex() {
    return (
        <PublicLayout>
            <Head title="Standar Labkesmas" />
            <DashboardFilterProvider>
                <StandarContent />
            </DashboardFilterProvider>
        </PublicLayout>
    );
}

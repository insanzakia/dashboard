import { useState } from 'react';
import { Head } from '@inertiajs/react';
import { PublicLayout } from '@/Layouts/PublicLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { FilterDropdown } from '@/Components/molecules/FilterDropdown';
import { WilayahCascadeFilter } from '@/Components/organisms/WilayahCascadeFilter';
import { TierSelector } from '@/Components/molecules/TierSelector';
import { FulfillmentSummary } from '@/Components/organisms/FulfillmentSummary';
import { LabComparisonChart } from '@/Components/organisms/LabComparisonChart';
import { LabFulfillmentDetail } from '@/Components/organisms/LabFulfillmentDetail';
import { DashboardFilterProvider, useDashboardFilter } from '@/context/DashboardFilterContext';
import { useAggregateFulfillment } from '@/hooks/useAggregateFulfillment';
import { useLabComparison } from '@/hooks/useLabComparison';
import { useLabFulfillment } from '@/hooks/useLabFulfillment';
import { JENIS_LAB_LABEL } from '@/lib/constants';
import type { DashboardFilter } from '@/types/dashboard';
import type { InventarisLabkesmasOption } from '@/types/inventarisAlat';

function scopeLabelOf(filter: DashboardFilter): string {
    const base = filter.wilayah.kabupatenKotaId
        ? 'Kab/Kota Terpilih'
        : filter.wilayah.provinsiId
          ? 'Provinsi Terpilih'
          : filter.wilayah.regionalId
            ? 'Regional Terpilih'
            : 'Nasional';
    return filter.tier ? `${base} · Tier ${filter.tier}` : base;
}

function labOptionLabel(o: InventarisLabkesmasOption): string {
    const jenis = o.jenis_lab ? ` · ${JENIS_LAB_LABEL[o.jenis_lab]}` : '';
    return `${o.nama_kantor} (Tier ${o.tier_labkesmas}${jenis})`;
}

function StandarContent({ labkesmasOptions }: { labkesmasOptions: InventarisLabkesmasOption[] }) {
    const { filter, setTier, resetFilter } = useDashboardFilter();
    const aggregate = useAggregateFulfillment(filter);
    const comparison = useLabComparison(filter);

    const [selectedLabId, setSelectedLabId] = useState<string | null>(null);
    const detail = useLabFulfillment(selectedLabId);

    return (
        <div className="flex flex-col gap-8">
            <section className="flex flex-col gap-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-lg font-semibold text-foreground">Pemenuhan Standar Peralatan</h1>
                        <p className="mt-1 text-sm text-muted-foreground">
                            Persentase kesesuaian alat yang dimiliki Labkesmas terhadap standar KMK per tier.
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

            <section>
                <FulfillmentSummary state={aggregate} scopeLabel={scopeLabelOf(filter)} />
            </section>

            <section>
                <Card>
                    <CardHeader>
                        <CardTitle className="text-base font-semibold text-foreground">
                            Perbandingan Antar-Labkesmas
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <LabComparisonChart state={comparison} />
                    </CardContent>
                </Card>
            </section>

            <section>
                <Card>
                    <CardHeader>
                        <CardTitle className="text-base font-semibold text-foreground">
                            Rincian per Labkesmas
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="flex flex-col gap-6">
                        <div className="max-w-md">
                            <FilterDropdown
                                label="Pilih Labkesmas"
                                placeholder="Pilih Labkesmas"
                                value={selectedLabId}
                                options={labkesmasOptions.map((o) => ({ value: o.id, label: labOptionLabel(o) }))}
                                onChange={setSelectedLabId}
                            />
                        </div>
                        <LabFulfillmentDetail state={detail} />
                    </CardContent>
                </Card>
            </section>
        </div>
    );
}

export default function StandarLabkesmasIndex({
    labkesmasOptions,
}: {
    labkesmasOptions: InventarisLabkesmasOption[];
}) {
    return (
        <PublicLayout>
            <Head title="Standar Labkesmas" />
            <DashboardFilterProvider>
                <StandarContent labkesmasOptions={labkesmasOptions} />
            </DashboardFilterProvider>
        </PublicLayout>
    );
}

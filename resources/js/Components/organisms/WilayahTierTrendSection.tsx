import { useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { FilterDropdown } from '@/Components/molecules/FilterDropdown';
import { MultiLabSelect } from '@/Components/molecules/MultiLabSelect';
import { MultiSeriesTrendChart } from '@/Components/organisms/MultiSeriesTrendChart';
import { useTrendGrouped } from '@/hooks/useTrendGrouped';
import { useTrendMultiLabkesmas } from '@/hooks/useTrendMultiLabkesmas';
import { JENIS_LAB_LABEL, TREND_GROUP_BY_OPTIONS, type TrendGroupByDimension } from '@/lib/constants';
import type { DashboardFilter } from '@/types/dashboard';
import type { InventarisLabkesmasOption } from '@/types/inventarisAlat';

export interface WilayahTierTrendSectionProps {
    filter: DashboardFilter;
    labkesmasOptions: InventarisLabkesmasOption[];
}

const GROUP_BY_LABEL: Record<string, string> = Object.fromEntries(
    TREND_GROUP_BY_OPTIONS.map((o) => [o.value, o.label]),
);

function labOptionLabel(o: InventarisLabkesmasOption): string {
    const jenis = o.jenis_lab ? ` · ${JENIS_LAB_LABEL[o.jenis_lab]}` : '';
    return `${o.nama_kantor} (Tier ${o.tier_labkesmas}${jenis})`;
}

/**
 * Tren bulanan dikelompokkan menurut Provinsi/Regional/Tier (otomatis, sesuai cakupan filter aktif),
 * atau — kalau dimensi "Labkesmas" dipilih — perbandingan beberapa Labkesmas terpilih secara manual
 * (satu-vs-satu atau multi-labkesmas), lepas dari filter wilayah/tier di atas.
 */
export function WilayahTierTrendSection({ filter, labkesmasOptions }: WilayahTierTrendSectionProps) {
    const [groupBy, setGroupBy] = useState<TrendGroupByDimension>('tier');
    const [selectedLabIds, setSelectedLabIds] = useState<string[]>([]);

    const isLabkesmasMode = groupBy === 'labkesmas';
    const grouped = useTrendGrouped(filter, isLabkesmasMode ? null : groupBy);
    const multi = useTrendMultiLabkesmas(selectedLabIds);

    return (
        <Card>
            <CardHeader className="flex flex-row items-center justify-between gap-4 space-y-0">
                <CardTitle className="text-base font-semibold text-foreground">
                    Tren per {GROUP_BY_LABEL[groupBy]}
                </CardTitle>
                <div className="w-52">
                    <FilterDropdown
                        label="Kelompokkan menurut"
                        placeholder="Pilih dimensi"
                        value={groupBy}
                        options={TREND_GROUP_BY_OPTIONS.map((o) => ({ value: o.value, label: o.label }))}
                        onChange={(v) => setGroupBy((v as TrendGroupByDimension) ?? 'tier')}
                    />
                </div>
            </CardHeader>
            <CardContent className="flex flex-col gap-6">
                {isLabkesmasMode ? (
                    <div className="grid grid-cols-1 gap-6 lg:grid-cols-[320px_1fr]">
                        <MultiLabSelect
                            options={labkesmasOptions.map((o) => ({ value: o.id, label: labOptionLabel(o) }))}
                            selected={selectedLabIds}
                            onChange={setSelectedLabIds}
                        />
                        <div>
                            {selectedLabIds.length === 0 ? (
                                <p className="flex h-full min-h-[200px] items-center justify-center rounded-lg border border-dashed text-center text-sm text-muted-foreground">
                                    Centang satu atau lebih Labkesmas untuk membandingkan tren pemeriksaannya
                                    (mis. Labkesmas A vs Labkesmas B).
                                </p>
                            ) : (
                                <MultiSeriesTrendChart
                                    state={multi}
                                    emptyDescription="Data pemeriksaan bulanan belum tersedia untuk Labkesmas yang dipilih."
                                />
                            )}
                        </div>
                    </div>
                ) : (
                    <MultiSeriesTrendChart
                        state={grouped}
                        emptyDescription="Data pemeriksaan bulanan belum tersedia untuk cakupan wilayah/tier yang dipilih."
                    />
                )}
            </CardContent>
        </Card>
    );
}

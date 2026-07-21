import { useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { ErrorState } from '@/Components/molecules/ErrorState';
import { Skeleton } from '@/Components/atoms/Skeleton';
import { MultiLabSelect } from '@/Components/molecules/MultiLabSelect';
import { MultiSeriesTrendChart } from '@/Components/organisms/MultiSeriesTrendChart';
import { useJenisPemeriksaanOptions } from '@/hooks/useJenisPemeriksaanOptions';
import { useTrendByJenis } from '@/hooks/useTrendByJenis';
import type { DashboardFilter } from '@/types/dashboard';

export interface JenisTrendSectionProps {
    filter: DashboardFilter;
}

/** Tren bulanan per jenis pemeriksaan: checklist jenis + tampilan gabungan/terpisah (MultiSeriesTrendChart). */
export function JenisTrendSection({ filter }: JenisTrendSectionProps) {
    const [selectedIds, setSelectedIds] = useState<string[]>([]);

    const options = useJenisPemeriksaanOptions();
    const trend = useTrendByJenis(filter, selectedIds);

    return (
        <Card>
            <CardHeader>
                <CardTitle className="text-base font-semibold text-foreground">Tren per Jenis Pemeriksaan</CardTitle>
            </CardHeader>
            <CardContent className="flex flex-col gap-6">
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-[280px_1fr]">
                    <div className="flex flex-col gap-3">
                        {options.status === 'loading' && <Skeleton className="h-64 w-full" />}
                        {options.status === 'error' && (
                            <ErrorState message={options.errorMessage ?? 'Gagal memuat jenis pemeriksaan.'} />
                        )}
                        {options.status === 'empty' && (
                            <p className="text-xs text-muted-foreground">Belum ada jenis pemeriksaan terdaftar.</p>
                        )}
                        {options.status === 'success' && options.data && (
                            <MultiLabSelect
                                options={options.data.map((o) => ({ value: o.id, label: o.nama_tes }))}
                                selected={selectedIds}
                                onChange={setSelectedIds}
                                max={options.data.length}
                            />
                        )}
                    </div>

                    <div>
                        {selectedIds.length === 0 ? (
                            <p className="flex h-full min-h-[200px] items-center justify-center rounded-lg border border-dashed text-center text-sm text-muted-foreground">
                                Centang satu atau lebih jenis pemeriksaan di samping untuk menampilkan tren bulanannya.
                            </p>
                        ) : (
                            <MultiSeriesTrendChart
                                state={trend}
                                emptyDescription="Data pemeriksaan bulanan belum tersedia untuk jenis dan cakupan yang dipilih."
                            />
                        )}
                    </div>
                </div>
            </CardContent>
        </Card>
    );
}

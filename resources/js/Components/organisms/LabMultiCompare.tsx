import { useState } from 'react';
import { PercentMeter } from '@/Components/molecules/PercentMeter';
import { MultiLabSelect } from '@/Components/molecules/MultiLabSelect';
import { EmptyState } from '@/Components/molecules/EmptyState';
import { ErrorState } from '@/Components/molecules/ErrorState';
import { Skeleton } from '@/Components/atoms/Skeleton';
import { useMultiLabFulfillment } from '@/hooks/useMultiLabFulfillment';
import { KATEGORI_ALAT, KATEGORI_ORDER, JENIS_LAB_LABEL } from '@/lib/constants';
import { formatPersen, persenColor } from '@/lib/utils';
import type { InventarisLabkesmasOption } from '@/types/inventarisAlat';
import type { MultiLabRow } from '@/types/pemenuhanAlat';

function labLabel(o: InventarisLabkesmasOption): string {
    const jenis = o.jenis_lab ? ` · ${JENIS_LAB_LABEL[o.jenis_lab]}` : '';
    return `${o.nama_kantor} (Tier ${o.tier_labkesmas}${jenis})`;
}

/** Peta kategori→persen per lab (untuk sel tabel perbandingan). */
function kategoriMap(row: MultiLabRow): Record<string, number> {
    const m: Record<string, number> = {};
    for (const k of row.per_kategori) m[k.kategori] = k.persen;
    return m;
}

/** Feature 2: pilih beberapa lab → bandingkan % keseluruhan & per kategori berdampingan. */
export function LabMultiCompare({ labkesmasOptions }: { labkesmasOptions: InventarisLabkesmasOption[] }) {
    const [selected, setSelected] = useState<string[]>([]);
    const state = useMultiLabFulfillment(selected);

    const options = labkesmasOptions.map((o) => ({ value: o.id, label: labLabel(o) }));

    return (
        <div className="grid grid-cols-1 gap-6 lg:grid-cols-4">
            <div className="lg:col-span-1">
                <MultiLabSelect options={options} selected={selected} onChange={setSelected} />
            </div>

            <div className="lg:col-span-3">
                {selected.length === 0 ? (
                    <EmptyState
                        title="Pilih Labkesmas"
                        description="Centang 2 atau lebih Labkesmas di samping untuk membandingkan pemenuhan alatnya."
                    />
                ) : state.status === 'loading' ? (
                    <Skeleton className="h-64 w-full" />
                ) : state.status === 'error' ? (
                    <ErrorState message={state.errorMessage ?? 'Gagal memuat perbandingan.'} />
                ) : state.data && state.data.length > 0 ? (
                    <CompareContent rows={state.data} />
                ) : (
                    <EmptyState title="Tidak Ada Data" description="Lab terpilih belum memiliki data." />
                )}
            </div>
        </div>
    );
}

function CompareContent({ rows }: { rows: MultiLabRow[] }) {
    const maps = rows.map(kategoriMap);
    // Kategori yang muncul di minimal satu lab, urut sesuai dokumen.
    const categories = KATEGORI_ORDER.filter((k) => maps.some((m) => k in m));

    return (
        <div className="flex flex-col gap-6">
            {/* % keseluruhan tiap lab */}
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                {rows.map((r) => (
                    <PercentMeter
                        key={r.labkesmas.id}
                        label={r.labkesmas.nama_kantor}
                        value={r.persen_total}
                        sublabel={`${r.total_terpenuhi}/${r.total_wajib} jenis alat · Tier ${r.labkesmas.tier}`}
                    />
                ))}
            </div>

            {/* Tabel perbandingan per kategori */}
            <div className="overflow-x-auto rounded-lg border">
                <table className="w-full text-sm">
                    <thead>
                        <tr className="border-b bg-muted/50 text-left text-xs font-medium text-muted-foreground">
                            <th className="px-4 py-2.5">Kategori</th>
                            {rows.map((r) => (
                                <th key={r.labkesmas.id} className="px-4 py-2.5 text-right">
                                    {r.labkesmas.nama_kantor}
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {categories.map((kat) => (
                            <tr key={kat} className="border-b last:border-0">
                                <td className="px-4 py-2 text-foreground">{KATEGORI_ALAT[kat] ?? kat}</td>
                                {maps.map((m, i) => {
                                    const p = kat in m ? m[kat] : null;
                                    return (
                                        <td
                                            key={rows[i].labkesmas.id}
                                            className="px-4 py-2 text-right font-medium tabular-nums"
                                            style={{ color: p === null ? undefined : persenColor(p) }}
                                        >
                                            {p === null ? '–' : formatPersen(p)}
                                        </td>
                                    );
                                })}
                            </tr>
                        ))}
                        <tr className="border-t bg-muted/30 font-semibold">
                            <td className="px-4 py-2 text-foreground">Keseluruhan</td>
                            {rows.map((r) => (
                                <td
                                    key={r.labkesmas.id}
                                    className="px-4 py-2 text-right tabular-nums"
                                    style={{ color: r.persen_total === null ? undefined : persenColor(r.persen_total) }}
                                >
                                    {formatPersen(r.persen_total)}
                                </td>
                            ))}
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    );
}

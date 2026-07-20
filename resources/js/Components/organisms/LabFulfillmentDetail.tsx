import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { StatCard } from '@/Components/molecules/StatCard';
import { PercentMeter } from '@/Components/molecules/PercentMeter';
import { EmptyState } from '@/Components/molecules/EmptyState';
import { ErrorState } from '@/Components/molecules/ErrorState';
import { Skeleton } from '@/Components/atoms/Skeleton';
import { DataTable, type Column } from '@/Components/organisms/DataTable';
import { KATEGORI_ALAT, JENIS_LAB_LABEL, STATUS_ALAT } from '@/lib/constants';
import { formatPersen, persenColor } from '@/lib/utils';
import type { RequestState } from '@/types/dashboard';
import type { AlatPemenuhanItem, LabFulfillment } from '@/types/pemenuhanAlat';

type ItemRow = AlatPemenuhanItem & { id: string };

const columns: Column<ItemRow>[] = [
    { header: 'Alat', cell: (r) => <span className="font-medium">{r.nama_alat}</span> },
    { header: 'Kategori', cell: (r) => <span className="text-muted-foreground">{KATEGORI_ALAT[r.kategori] ?? r.kategori}</span> },
    { header: 'Standar', className: 'text-right tabular-nums', cell: (r) => r.jumlah_minimal },
    { header: 'Dimiliki', className: 'text-right tabular-nums', cell: (r) => r.jumlah_dimiliki },
    {
        header: 'Status',
        className: 'w-28 text-right',
        cell: (r) => {
            const s = STATUS_ALAT[r.status] ?? STATUS_ALAT.tidak_ada;
            return (
                <span className={'inline-block rounded-full px-2 py-0.5 text-xs font-medium ' + s.className}>
                    {s.label}
                </span>
            );
        },
    },
];

/** Rincian pemenuhan alat satu Labkesmas: % total, per kategori, dan tabel per item. */
export function LabFulfillmentDetail({ state }: { state: RequestState<LabFulfillment | null> }) {
    if (state.status === 'loading') {
        return <Skeleton className="h-64 w-full" />;
    }

    if (state.status === 'error') {
        return <ErrorState message={state.errorMessage ?? 'Gagal memuat rincian pemenuhan.'} />;
    }

    if (state.status === 'empty' || !state.data) {
        return (
            <EmptyState
                title="Pilih Labkesmas"
                description="Pilih sebuah Labkesmas di atas untuk melihat rincian pemenuhan alatnya."
            />
        );
    }

    const data = state.data;

    if (data.total_wajib === 0) {
        return (
            <EmptyState
                title="Standar Belum Tersedia"
                description={
                    data.labkesmas.tier === 5 && !data.labkesmas.jenis_lab
                        ? 'Lab tier 5 ini belum menetapkan Jenis Lab (Biokes/Kesling), sehingga standar belum bisa dicocokkan.'
                        : 'Belum ada standar alat untuk tier lab ini.'
                }
            />
        );
    }

    const rows: ItemRow[] = data.items.map((it) => ({ ...it, id: it.nama_alat }));

    return (
        <div className="flex flex-col gap-6">
            <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <StatCard
                    label="Pemenuhan Alat"
                    value={formatPersen(data.persen_total)}
                    deltaLabel={`${data.total_terpenuhi} dari ${data.total_wajib} jenis alat terpenuhi`}
                    accent={persenColor(data.persen_total)}
                />
                <Card className="lg:col-span-2">
                    <CardHeader>
                        <CardTitle className="text-sm font-semibold text-foreground">Pemenuhan per Kategori</CardTitle>
                    </CardHeader>
                    <CardContent className="flex flex-col gap-3">
                        {data.per_kategori.map((k) => (
                            <PercentMeter
                                key={k.kategori}
                                label={KATEGORI_ALAT[k.kategori] ?? k.kategori}
                                value={k.persen}
                                sublabel={`${k.terpenuhi}/${k.wajib} terpenuhi`}
                            />
                        ))}
                    </CardContent>
                </Card>
            </div>

            <div>
                <h3 className="mb-3 text-sm font-medium text-muted-foreground">
                    Rincian Alat
                    {data.labkesmas.tier === 5 && data.labkesmas.jenis_lab
                        ? ` (Tier 5 · ${JENIS_LAB_LABEL[data.labkesmas.jenis_lab]})`
                        : ` (Tier ${data.labkesmas.tier})`}
                </h3>
                <DataTable
                    columns={columns}
                    rows={rows}
                    emptyTitle="Tidak Ada Item"
                    emptyDescription="Tidak ada alat wajib untuk lab ini."
                />
            </div>
        </div>
    );
}

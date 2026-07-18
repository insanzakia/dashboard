import { FormEventHandler } from 'react';
import { useForm } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { FilterDropdown } from '@/Components/molecules/FilterDropdown';
import { ConfirmButton } from '@/Components/molecules/ConfirmButton';
import { DataTable, type Column } from '@/Components/organisms/DataTable';
import { formatNumber } from '@/lib/utils';
import { MONTH_NAMES } from '@/lib/constants';
import type { JenisTesOption, LabkesmasOption, RecentEntry } from '@/types/dataPemeriksaan';

interface DataPemeriksaanManagerProps {
    labkesmasOptions: LabkesmasOption[];
    jenisTesOptions: JenisTesOption[];
    recentEntries: RecentEntry[];
}

const MONTH_OPTIONS = MONTH_NAMES.map((name, i) => ({ value: String(i + 1), label: name }));

/** Rentang tahun pilihan: 2 tahun ke belakang s/d 1 tahun ke depan dari tahun berjalan. */
function buildYearOptions(): { value: string; label: string }[] {
    const current = new Date().getFullYear();
    const years: { value: string; label: string }[] = [];
    for (let y = current + 1; y >= current - 2; y--) {
        years.push({ value: String(y), label: String(y) });
    }
    return years;
}

const formatPeriode = (bulan: number, tahun: number) => `${MONTH_NAMES[bulan - 1] ?? bulan} ${tahun}`;

/**
 * Form input pemeriksaan bulanan (UPSERT) + tabel entri terbaru.
 * Menyimpan angka per (labkesmas + tes + periode); resubmit periode sama = memperbarui.
 */
export function DataPemeriksaanManager({ labkesmasOptions, jenisTesOptions, recentEntries }: DataPemeriksaanManagerProps) {
    const now = new Date();
    const form = useForm({
        labkesmas_id: '',
        jenis_tes_id: '',
        bulan: String(now.getMonth() + 1),
        tahun: String(now.getFullYear()),
        jumlah: '',
    });

    const onSubmit: FormEventHandler = (e) => {
        e.preventDefault();
        form.post('/admin/data-pemeriksaan', {
            preserveScroll: true,
            onSuccess: () => form.setData('jumlah', ''), // pertahankan periode/labkesmas untuk input cepat berturut.
        });
    };

    const columns: Column<RecentEntry>[] = [
        { header: 'Labkesmas', cell: (row) => <span className="font-medium">{row.labkesmas_nama ?? '—'}</span> },
        { header: 'Jenis Tes', cell: (row) => row.jenis_nama ?? '—' },
        { header: 'Periode', cell: (row) => formatPeriode(row.bulan, row.tahun) },
        { header: 'Jumlah', className: 'text-right', cell: (row) => formatNumber(row.jumlah) },
        {
            header: 'Aksi',
            className: 'w-28 text-right',
            cell: (row) => (
                <ConfirmButton onConfirm={() => form.delete(`/admin/data-pemeriksaan/${row.id}`, { preserveScroll: true })}>
                    Hapus
                </ConfirmButton>
            ),
        },
    ];

    return (
        <div className="grid grid-cols-1 gap-6 lg:grid-cols-5">
            <Card className="lg:col-span-2">
                <CardHeader>
                    <CardTitle className="text-sm font-semibold text-foreground">Input Angka Pemeriksaan</CardTitle>
                </CardHeader>
                <CardContent>
                    <form onSubmit={onSubmit} className="flex flex-col gap-4">
                        <div className="grid grid-cols-2 gap-3">
                            <div className="flex flex-col gap-1.5">
                                <FilterDropdown
                                    label="Bulan"
                                    placeholder="Bulan"
                                    value={form.data.bulan || null}
                                    options={MONTH_OPTIONS}
                                    onChange={(v) => form.setData('bulan', v ?? '')}
                                />
                                {form.errors.bulan && <p className="text-xs text-destructive">{form.errors.bulan}</p>}
                            </div>
                            <div className="flex flex-col gap-1.5">
                                <FilterDropdown
                                    label="Tahun"
                                    placeholder="Tahun"
                                    value={form.data.tahun || null}
                                    options={buildYearOptions()}
                                    onChange={(v) => form.setData('tahun', v ?? '')}
                                />
                                {form.errors.tahun && <p className="text-xs text-destructive">{form.errors.tahun}</p>}
                            </div>
                        </div>

                        <div className="flex flex-col gap-1.5">
                            <FilterDropdown
                                label="Labkesmas"
                                placeholder="Pilih Labkesmas"
                                value={form.data.labkesmas_id || null}
                                options={labkesmasOptions.map((l) => ({ value: l.id, label: l.nama_kantor }))}
                                onChange={(v) => form.setData('labkesmas_id', v ?? '')}
                            />
                            {form.errors.labkesmas_id && <p className="text-xs text-destructive">{form.errors.labkesmas_id}</p>}
                        </div>

                        <div className="flex flex-col gap-1.5">
                            <FilterDropdown
                                label="Jenis Tes"
                                placeholder="Pilih Jenis Tes"
                                value={form.data.jenis_tes_id || null}
                                options={jenisTesOptions.map((j) => ({ value: j.id, label: j.nama_tes }))}
                                onChange={(v) => form.setData('jenis_tes_id', v ?? '')}
                            />
                            {form.errors.jenis_tes_id && <p className="text-xs text-destructive">{form.errors.jenis_tes_id}</p>}
                        </div>

                        <div className="flex flex-col gap-1.5">
                            <Label htmlFor="jumlah">Jumlah Pemeriksaan</Label>
                            <Input
                                id="jumlah"
                                type="number"
                                min={0}
                                value={form.data.jumlah}
                                onChange={(e) => form.setData('jumlah', e.target.value)}
                                placeholder="0"
                            />
                            {form.errors.jumlah && <p className="text-xs text-destructive">{form.errors.jumlah}</p>}
                        </div>

                        <Button type="submit" disabled={form.processing}>
                            Simpan
                        </Button>
                    </form>
                </CardContent>
            </Card>

            <div className="lg:col-span-3">
                <h2 className="mb-3 text-sm font-medium text-muted-foreground">Entri Terbaru</h2>
                <DataTable
                    columns={columns}
                    rows={recentEntries}
                    emptyTitle="Belum Ada Entri"
                    emptyDescription="Input angka pemeriksaan pertama melalui form di samping."
                />
            </div>
        </div>
    );
}

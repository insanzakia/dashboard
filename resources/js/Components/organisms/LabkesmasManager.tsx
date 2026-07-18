import { FormEventHandler, useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { FilterDropdown } from '@/Components/molecules/FilterDropdown';
import { ConfirmButton } from '@/Components/molecules/ConfirmButton';
import { DataTable, type Column } from '@/Components/organisms/DataTable';
import { KabupatenKotaPicker } from '@/Components/organisms/KabupatenKotaPicker';
import { TIER_OPTIONS } from '@/lib/constants';
import type { LabkesmasRow } from '@/types/labkesmas';

const TIER_LABEL: Record<number, string> = Object.fromEntries(
    TIER_OPTIONS.map((o) => [o.value, `${o.label} — ${o.description}`]),
);

/** Pendaftaran & pengelolaan Labkesmas: nama kantor, tier, lokasi (cascade kab/kota). */
export function LabkesmasManager({ items }: { items: LabkesmasRow[] }) {
    const [editingId, setEditingId] = useState<string | null>(null);
    const [pickerKey, setPickerKey] = useState(0);
    const [initialLoc, setInitialLoc] = useState<{ regionalId: string | null; provinsiId: string | null }>({
        regionalId: null,
        provinsiId: null,
    });
    const form = useForm({ nama_kantor: '', tier_labkesmas: '', kabupaten_kota_id: '' });

    const isEditing = editingId !== null;

    const resetForm = () => {
        form.reset();
        form.clearErrors();
        setEditingId(null);
        setInitialLoc({ regionalId: null, provinsiId: null });
        setPickerKey((k) => k + 1); // remount picker → state kaskade bersih.
    };

    const startEdit = (row: LabkesmasRow) => {
        setEditingId(row.id);
        form.setData({
            nama_kantor: row.nama_kantor,
            tier_labkesmas: String(row.tier_labkesmas),
            kabupaten_kota_id: row.kabupaten_kota_id,
        });
        setInitialLoc({ regionalId: row.regional_id, provinsiId: row.provinsi_id });
        setPickerKey((k) => k + 1); // remount picker dengan lokasi awal baris.
    };

    const onSubmit: FormEventHandler = (e) => {
        e.preventDefault();
        const options = { preserveScroll: true, onSuccess: () => resetForm() };
        if (isEditing) {
            form.put(`/admin/labkesmas/${editingId}`, options);
        } else {
            form.post('/admin/labkesmas', options);
        }
    };

    const columns: Column<LabkesmasRow>[] = [
        { header: 'Nama Kantor', cell: (row) => <span className="font-medium">{row.nama_kantor}</span> },
        { header: 'Tier', cell: (row) => `Tier ${row.tier_labkesmas}` },
        {
            header: 'Lokasi',
            cell: (row) => (
                <span>
                    {row.kabupaten_nama ?? '—'}
                    {row.provinsi_nama && <span className="text-muted-foreground"> · {row.provinsi_nama}</span>}
                </span>
            ),
        },
        {
            header: 'Aksi',
            className: 'w-40 text-right',
            cell: (row) => (
                <div className="flex justify-end gap-1">
                    <Button size="sm" variant="ghost" onClick={() => startEdit(row)}>
                        Edit
                    </Button>
                    <ConfirmButton onConfirm={() => form.delete(`/admin/labkesmas/${row.id}`, { preserveScroll: true })}>
                        Hapus
                    </ConfirmButton>
                </div>
            ),
        },
    ];

    return (
        <div className="grid grid-cols-1 gap-6 lg:grid-cols-5">
            <Card className="lg:col-span-2">
                <CardHeader>
                    <CardTitle className="text-sm font-semibold text-foreground">
                        {isEditing ? 'Edit Labkesmas' : 'Daftarkan Labkesmas'}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <form onSubmit={onSubmit} className="flex flex-col gap-4">
                        <div className="flex flex-col gap-1.5">
                            <Label htmlFor="nama_kantor">Nama Kantor</Label>
                            <Input
                                id="nama_kantor"
                                value={form.data.nama_kantor}
                                onChange={(e) => form.setData('nama_kantor', e.target.value)}
                                placeholder="mis. Labkesmas Kota Medan"
                            />
                            {form.errors.nama_kantor && <p className="text-xs text-destructive">{form.errors.nama_kantor}</p>}
                        </div>

                        <div className="flex flex-col gap-1.5">
                            <FilterDropdown
                                label="Tier Labkesmas"
                                placeholder="Pilih Tier"
                                value={form.data.tier_labkesmas || null}
                                options={TIER_OPTIONS.map((o) => ({ value: String(o.value), label: TIER_LABEL[o.value] }))}
                                onChange={(v) => form.setData('tier_labkesmas', v ?? '')}
                            />
                            {form.errors.tier_labkesmas && <p className="text-xs text-destructive">{form.errors.tier_labkesmas}</p>}
                        </div>

                        <div className="flex flex-col gap-1.5">
                            <Label>Lokasi (Kabupaten/Kota)</Label>
                            <KabupatenKotaPicker
                                key={pickerKey}
                                value={form.data.kabupaten_kota_id || null}
                                onChange={(id) => form.setData('kabupaten_kota_id', id ?? '')}
                                initialRegionalId={initialLoc.regionalId}
                                initialProvinsiId={initialLoc.provinsiId}
                                errorText={form.errors.kabupaten_kota_id}
                            />
                        </div>

                        <div className="flex gap-2">
                            <Button type="submit" disabled={form.processing} className="flex-1">
                                {isEditing ? 'Simpan Perubahan' : 'Daftarkan'}
                            </Button>
                            {isEditing && (
                                <Button type="button" variant="ghost" onClick={resetForm}>
                                    Batal
                                </Button>
                            )}
                        </div>
                    </form>
                </CardContent>
            </Card>

            <div className="lg:col-span-3">
                <DataTable
                    columns={columns}
                    rows={items}
                    emptyTitle="Belum Ada Labkesmas"
                    emptyDescription="Daftarkan labkesmas pertama melalui form di samping."
                />
            </div>
        </div>
    );
}

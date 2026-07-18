import { FormEventHandler, useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import { ConfirmButton } from '@/Components/molecules/ConfirmButton';
import { DataTable, type Column } from '@/Components/organisms/DataTable';
import type { JenisPemeriksaanRow } from '@/types/jenisPemeriksaan';

/** CRUD master jenis pemeriksaan (nama_tes + deskripsi) memakai Inertia useForm. */
export function JenisPemeriksaanManager({ items }: { items: JenisPemeriksaanRow[] }) {
    const [editingId, setEditingId] = useState<string | null>(null);
    const form = useForm({ nama_tes: '', deskripsi: '' });

    const isEditing = editingId !== null;

    const resetForm = () => {
        form.reset();
        form.clearErrors();
        setEditingId(null);
    };

    const startEdit = (row: JenisPemeriksaanRow) => {
        setEditingId(row.id);
        form.setData({ nama_tes: row.nama_tes, deskripsi: row.deskripsi ?? '' });
    };

    const onSubmit: FormEventHandler = (e) => {
        e.preventDefault();
        const options = { preserveScroll: true, onSuccess: () => resetForm() };
        if (isEditing) {
            form.put(`/admin/jenis-pemeriksaan/${editingId}`, options);
        } else {
            form.post('/admin/jenis-pemeriksaan', options);
        }
    };

    const columns: Column<JenisPemeriksaanRow>[] = [
        { header: 'Nama Tes', cell: (row) => <span className="font-medium">{row.nama_tes}</span> },
        { header: 'Deskripsi', cell: (row) => row.deskripsi ?? '—' },
        {
            header: 'Aksi',
            className: 'w-40 text-right',
            cell: (row) => (
                <div className="flex justify-end gap-1">
                    <Button size="sm" variant="ghost" onClick={() => startEdit(row)}>
                        Edit
                    </Button>
                    <ConfirmButton onConfirm={() => form.delete(`/admin/jenis-pemeriksaan/${row.id}`, { preserveScroll: true })}>
                        Hapus
                    </ConfirmButton>
                </div>
            ),
        },
    ];

    return (
        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <Card className="lg:col-span-1">
                <CardHeader>
                    <CardTitle className="text-sm font-semibold text-foreground">
                        {isEditing ? 'Edit Jenis Pemeriksaan' : 'Tambah Jenis Pemeriksaan'}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <form onSubmit={onSubmit} className="flex flex-col gap-4">
                        <div className="flex flex-col gap-1.5">
                            <Label htmlFor="nama_tes">Nama Tes</Label>
                            <Input
                                id="nama_tes"
                                value={form.data.nama_tes}
                                onChange={(e) => form.setData('nama_tes', e.target.value)}
                                placeholder="mis. TCM TB"
                            />
                            {form.errors.nama_tes && <p className="text-xs text-destructive">{form.errors.nama_tes}</p>}
                        </div>

                        <div className="flex flex-col gap-1.5">
                            <Label htmlFor="deskripsi">Deskripsi (opsional)</Label>
                            <Textarea
                                id="deskripsi"
                                value={form.data.deskripsi}
                                onChange={(e) => form.setData('deskripsi', e.target.value)}
                                placeholder="Keterangan tambahan"
                            />
                            {form.errors.deskripsi && <p className="text-xs text-destructive">{form.errors.deskripsi}</p>}
                        </div>

                        <div className="flex gap-2">
                            <Button type="submit" disabled={form.processing} className="flex-1">
                                {isEditing ? 'Simpan Perubahan' : 'Tambah'}
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

            <div className="lg:col-span-2">
                <DataTable
                    columns={columns}
                    rows={items}
                    emptyTitle="Belum Ada Jenis Pemeriksaan"
                    emptyDescription="Tambahkan jenis tes pertama melalui form di samping."
                />
            </div>
        </div>
    );
}

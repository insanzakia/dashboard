import { FormEventHandler } from 'react';
import { usePage } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { FilterDropdown } from '@/Components/molecules/FilterDropdown';
import { ConfirmButton } from '@/Components/molecules/ConfirmButton';
import { DataTable, type Column } from '@/Components/organisms/DataTable';
import { useWilayahCrud } from '@/hooks/useWilayahCrud';
import type { WilayahLevelConfig, WilayahOption, WilayahRow } from '@/types/wilayah';

interface WilayahManagerProps {
    level: WilayahLevelConfig;
    items: WilayahRow[];
    parents: WilayahOption[];
}

/**
 * Orkestrator CRUD wilayah yang generik untuk semua jenjang.
 * UI didorong sepenuhnya oleh `level` (config) — tidak ada teks/field jenjang yang di-hardcode.
 */
export function WilayahManager({ level, items, parents }: WilayahManagerProps) {
    const crud = useWilayahCrud(level);
    const errors = usePage().props.errors as Record<string, string>;
    const parentError = level.parentField ? errors[level.parentField] : undefined;

    const onSubmit: FormEventHandler = (e) => {
        e.preventDefault();
        crud.submit();
    };

    const columns: Column<WilayahRow>[] = [
        { header: 'Nama', cell: (row) => <span className="font-medium">{row.nama}</span> },
        ...(level.parentLabel
            ? [{ header: level.parentLabel, cell: (row: WilayahRow) => row.parent_nama ?? '—' }]
            : []),
        {
            header: 'Aksi',
            className: 'w-40 text-right',
            cell: (row) => (
                <div className="flex justify-end gap-1">
                    <Button size="sm" variant="ghost" onClick={() => crud.startEdit(row)}>
                        Edit
                    </Button>
                    <ConfirmButton onConfirm={() => crud.remove(row.id)}>Hapus</ConfirmButton>
                </div>
            ),
        },
    ];

    const isEditing = crud.editingId !== null;

    return (
        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {/* Form create/edit */}
            <Card className="lg:col-span-1">
                <CardHeader>
                    <CardTitle className="text-sm font-semibold text-foreground">
                        {isEditing ? `Edit ${level.singular}` : `Tambah ${level.singular}`}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <form onSubmit={onSubmit} className="flex flex-col gap-4">
                        {level.parentField && (
                            <div className="flex flex-col gap-1.5">
                                <FilterDropdown
                                    label={level.parentLabel ?? 'Parent'}
                                    placeholder={`Pilih ${level.parentLabel}`}
                                    value={crud.parentId || null}
                                    options={parents.map((p) => ({ value: p.id, label: p.nama }))}
                                    onChange={(value) => crud.setParentId(value ?? '')}
                                />
                                {parentError && <p className="text-xs text-destructive">{parentError}</p>}
                            </div>
                        )}

                        <div className="flex flex-col gap-1.5">
                            <Label htmlFor="nama">Nama {level.singular}</Label>
                            <Input
                                id="nama"
                                value={crud.nama}
                                onChange={(e) => crud.setNama(e.target.value)}
                                placeholder={`Nama ${level.singular}`}
                            />
                            {errors.nama && <p className="text-xs text-destructive">{errors.nama}</p>}
                        </div>

                        <div className="flex gap-2">
                            <Button type="submit" disabled={crud.processing} className="flex-1">
                                {isEditing ? 'Simpan Perubahan' : 'Tambah'}
                            </Button>
                            {isEditing && (
                                <Button type="button" variant="ghost" onClick={crud.reset}>
                                    Batal
                                </Button>
                            )}
                        </div>
                    </form>
                </CardContent>
            </Card>

            {/* Tabel daftar */}
            <div className="lg:col-span-2">
                <DataTable
                    columns={columns}
                    rows={items}
                    emptyTitle={`Belum Ada ${level.singular}`}
                    emptyDescription={`Tambahkan ${level.singular} pertama melalui form di samping.`}
                />
            </div>
        </div>
    );
}

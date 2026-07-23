import { type FormEventHandler, useMemo, useState } from 'react';
import { useForm } from '@inertiajs/react';
import { X } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { FilterDropdown } from '@/Components/molecules/FilterDropdown';
import { ConfirmButton } from '@/Components/molecules/ConfirmButton';
import { DataTable, type Column } from '@/Components/organisms/DataTable';
import {
    SCOPE_TYPE_LABEL,
    SCOPE_TYPE_OPTIONS,
    type AdminUserRow,
    type ScopeType,
    type WilayahOptions,
} from '@/types/user';

interface ScopeEntry {
    scope_type: ScopeType;
    scope_id: string;
}

export interface UserManagerProps {
    users: AdminUserRow[];
    wilayah: WilayahOptions;
}

/** Kelola akun admin terbatas: buat/edit akun + atur cakupan wilayah (banyak cakupan per akun). */
export function UserManager({ users, wilayah }: UserManagerProps) {
    const [editingId, setEditingId] = useState<string | null>(null);
    const [pendingType, setPendingType] = useState<ScopeType>('provinsi');
    const [pendingEntity, setPendingEntity] = useState<string | null>(null);

    const form = useForm<{ username: string; password: string; scopes: ScopeEntry[] }>({
        username: '',
        password: '',
        scopes: [],
    });

    const isEditing = editingId !== null;

    // Peta id → label untuk menampilkan cakupan yang sudah dipilih.
    const labelMaps = useMemo(() => {
        const regional = Object.fromEntries(wilayah.regional.map((r) => [r.id, r.nama]));
        const provinsi = Object.fromEntries(wilayah.provinsi.map((p) => [p.id, p.nama]));
        const kabupatenKota = Object.fromEntries(wilayah.kabupaten_kota.map((k) => [k.id, k.nama]));
        const labkesmas = Object.fromEntries(wilayah.labkesmas.map((l) => [l.id, l.nama_kantor]));
        return { regional, provinsi, kabupaten_kota: kabupatenKota, labkesmas };
    }, [wilayah]);

    const labelOf = (type: ScopeType, id: string): string => labelMaps[type][id] ?? '—';

    const entityOptions = (type: ScopeType): { value: string; label: string }[] => {
        switch (type) {
            case 'regional':
                return wilayah.regional.map((r) => ({ value: r.id, label: r.nama }));
            case 'provinsi':
                return wilayah.provinsi.map((p) => ({ value: p.id, label: p.nama }));
            case 'kabupaten_kota':
                return wilayah.kabupaten_kota.map((k) => ({ value: k.id, label: k.nama }));
            case 'labkesmas':
                return wilayah.labkesmas.map((l) => ({ value: l.id, label: l.nama_kantor }));
        }
    };

    const resetForm = () => {
        form.reset();
        form.clearErrors();
        setEditingId(null);
        setPendingType('provinsi');
        setPendingEntity(null);
    };

    const startEdit = (row: AdminUserRow) => {
        setEditingId(row.id);
        form.setData({
            username: row.username,
            password: '',
            scopes: row.scopes.map((s) => ({ scope_type: s.scope_type, scope_id: s.scope_id })),
        });
        form.clearErrors();
    };

    const addScope = () => {
        if (!pendingEntity) return;
        const exists = form.data.scopes.some(
            (s) => s.scope_type === pendingType && s.scope_id === pendingEntity,
        );
        if (!exists) {
            form.setData('scopes', [...form.data.scopes, { scope_type: pendingType, scope_id: pendingEntity }]);
        }
        setPendingEntity(null);
    };

    const removeScope = (index: number) => {
        form.setData('scopes', form.data.scopes.filter((_, i) => i !== index));
    };

    const onSubmit: FormEventHandler = (e) => {
        e.preventDefault();
        const options = { preserveScroll: true, onSuccess: () => resetForm() };
        if (isEditing) {
            form.put(`/admin/users/${editingId}`, options);
        } else {
            form.post('/admin/users', options);
        }
    };

    const columns: Column<AdminUserRow>[] = [
        { header: 'Username', cell: (row) => <span className="font-medium">{row.username}</span> },
        {
            header: 'Role',
            cell: (row) => (
                <span className="inline-block rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary">
                    {row.is_super_admin ? 'Super Admin' : 'Admin'}
                </span>
            ),
        },
        {
            header: 'Cakupan',
            cell: (row) =>
                row.is_super_admin ? (
                    <span className="text-xs text-muted-foreground">Semua wilayah (global)</span>
                ) : row.scopes.length === 0 ? (
                    <span className="text-xs text-destructive">Belum ada cakupan</span>
                ) : (
                    <div className="flex flex-wrap gap-1">
                        {row.scopes.map((s) => (
                            <span
                                key={`${s.scope_type}|${s.scope_id}`}
                                className="rounded-full bg-muted px-2 py-0.5 text-[11px] text-foreground"
                            >
                                {SCOPE_TYPE_LABEL[s.scope_type]}: {s.label}
                            </span>
                        ))}
                    </div>
                ),
        },
        {
            header: 'Aksi',
            className: 'w-32 text-right',
            cell: (row) =>
                row.is_super_admin ? (
                    <span className="text-xs text-muted-foreground">—</span>
                ) : (
                    <div className="flex justify-end gap-1">
                        <Button size="sm" variant="ghost" onClick={() => startEdit(row)}>
                            Edit
                        </Button>
                        <ConfirmButton onConfirm={() => form.delete(`/admin/users/${row.id}`, { preserveScroll: true })}>
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
                        {isEditing ? 'Edit Akun' : 'Buat Akun Admin'}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <form onSubmit={onSubmit} className="flex flex-col gap-4">
                        <div className="flex flex-col gap-1.5">
                            <Label htmlFor="username">Username</Label>
                            <Input
                                id="username"
                                value={form.data.username}
                                disabled={isEditing}
                                autoComplete="off"
                                onChange={(e) => form.setData('username', e.target.value)}
                                placeholder="mis. admin_kalsel"
                            />
                            {form.errors.username && <p className="text-xs text-destructive">{form.errors.username}</p>}
                        </div>

                        <div className="flex flex-col gap-1.5">
                            <Label htmlFor="password">
                                {isEditing ? 'Password Baru (kosongkan bila tak diubah)' : 'Password'}
                            </Label>
                            <Input
                                id="password"
                                type="password"
                                value={form.data.password}
                                autoComplete="new-password"
                                onChange={(e) => form.setData('password', e.target.value)}
                            />
                            {form.errors.password && <p className="text-xs text-destructive">{form.errors.password}</p>}
                        </div>

                        <div className="flex flex-col gap-2 rounded-lg border p-3">
                            <Label>Cakupan Wilayah</Label>
                            <p className="text-xs text-muted-foreground">
                                Akun hanya bisa input data untuk labkesmas di dalam cakupan ini. Bisa lebih dari satu.
                            </p>

                            <div className="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                <FilterDropdown
                                    label="Level"
                                    placeholder="Pilih level"
                                    value={pendingType}
                                    options={SCOPE_TYPE_OPTIONS.map((o) => ({ value: o.value, label: o.label }))}
                                    onChange={(v) => {
                                        setPendingType((v as ScopeType) ?? 'provinsi');
                                        setPendingEntity(null);
                                    }}
                                />
                                <FilterDropdown
                                    label="Entitas"
                                    placeholder="Pilih entitas"
                                    value={pendingEntity}
                                    options={entityOptions(pendingType)}
                                    onChange={setPendingEntity}
                                />
                            </div>
                            <Button type="button" variant="outline" size="sm" onClick={addScope} disabled={!pendingEntity}>
                                Tambah Cakupan
                            </Button>

                            {form.data.scopes.length > 0 && (
                                <div className="flex flex-wrap gap-1.5 pt-1">
                                    {form.data.scopes.map((s, i) => (
                                        <span
                                            key={`${s.scope_type}|${s.scope_id}`}
                                            className="inline-flex items-center gap-1 rounded-full bg-primary/10 px-2 py-0.5 text-xs text-primary"
                                        >
                                            {SCOPE_TYPE_LABEL[s.scope_type]}: {labelOf(s.scope_type, s.scope_id)}
                                            <button
                                                type="button"
                                                onClick={() => removeScope(i)}
                                                className="rounded-full hover:bg-primary/20"
                                                aria-label="Hapus cakupan"
                                            >
                                                <X className="h-3 w-3" />
                                            </button>
                                        </span>
                                    ))}
                                </div>
                            )}
                            {form.errors.scopes && <p className="text-xs text-destructive">{form.errors.scopes}</p>}
                        </div>

                        <div className="flex gap-2">
                            <Button type="submit" disabled={form.processing} className="flex-1">
                                {isEditing ? 'Simpan Perubahan' : 'Buat Akun'}
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
                    rows={users}
                    emptyTitle="Belum Ada Akun"
                    emptyDescription="Buat akun admin terbatas melalui form di samping."
                />
            </div>
        </div>
    );
}

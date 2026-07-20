import { FormEventHandler, useMemo, useState } from 'react';
import { router, useForm } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import { FilterDropdown } from '@/Components/molecules/FilterDropdown';
import { ConfirmButton } from '@/Components/molecules/ConfirmButton';
import { EmptyState } from '@/Components/molecules/EmptyState';
import { KATEGORI_ALAT, KATEGORI_ORDER } from '@/lib/constants';
import type { AlatCatalogItem, StandarRow } from '@/types/alat';

/** 6 slot standar: tier 1-4 (umum) + tier 5 biokes & kesling. */
const STANDAR_SLOTS = [
    { tier: 1, jenis_lab: 'umum', label: 'T1' },
    { tier: 2, jenis_lab: 'umum', label: 'T2' },
    { tier: 3, jenis_lab: 'umum', label: 'T3' },
    { tier: 4, jenis_lab: 'umum', label: 'T4' },
    { tier: 5, jenis_lab: 'biokes', label: 'T5-BK' },
    { tier: 5, jenis_lab: 'kesling', label: 'T5-KL' },
] as const;

const KATEGORI_OPTIONS = KATEGORI_ORDER.map((k) => ({ value: k, label: KATEGORI_ALAT[k] }));

function slotValue(standar: StandarRow[], tier: number, jenis: string): number {
    return standar.find((s) => s.tier === tier && s.jenis_lab === jenis)?.jumlah_minimal ?? 0;
}

function standarSummary(standar: StandarRow[]): string {
    const parts = STANDAR_SLOTS.map((slot) => {
        const v = slotValue(standar, slot.tier, slot.jenis_lab);
        return v > 0 ? `${slot.label}:${v}` : null;
    }).filter(Boolean);
    return parts.length ? parts.join('  ') : '—';
}

/** Kelola katalog alat (CRUD) + editor standar jumlah minimal per tier. */
export function AlatManager({ items }: { items: AlatCatalogItem[] }) {
    const [editingId, setEditingId] = useState<string | null>(null);
    const [standarFor, setStandarFor] = useState<string | null>(null);
    const [kategoriFilter, setKategoriFilter] = useState<string | null>(null);
    const [search, setSearch] = useState('');

    const form = useForm({ nama_alat: '', kategori: '', keterangan: '' });
    const isEditing = editingId !== null;

    const resetForm = () => {
        form.reset();
        form.clearErrors();
        setEditingId(null);
    };

    const startEdit = (alat: AlatCatalogItem) => {
        setEditingId(alat.id);
        setStandarFor(null);
        form.setData({ nama_alat: alat.nama_alat, kategori: alat.kategori, keterangan: alat.keterangan ?? '' });
    };

    const onSubmit: FormEventHandler = (e) => {
        e.preventDefault();
        const options = { preserveScroll: true, onSuccess: () => resetForm() };
        if (isEditing) {
            form.put(`/admin/alat/${editingId}`, options);
        } else {
            form.post('/admin/alat', options);
        }
    };

    const filtered = useMemo(() => {
        const q = search.trim().toLowerCase();
        return items.filter(
            (a) =>
                (!kategoriFilter || a.kategori === kategoriFilter) &&
                (!q || a.nama_alat.toLowerCase().includes(q)),
        );
    }, [items, kategoriFilter, search]);

    return (
        <div className="grid grid-cols-1 gap-6 lg:grid-cols-5">
            <Card className="lg:col-span-2 self-start">
                <CardHeader>
                    <CardTitle className="text-sm font-semibold text-foreground">
                        {isEditing ? 'Edit Alat' : 'Tambah Alat'}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <form onSubmit={onSubmit} className="flex flex-col gap-4">
                        <div className="flex flex-col gap-1.5">
                            <Label htmlFor="nama_alat">Nama Alat</Label>
                            <Input
                                id="nama_alat"
                                value={form.data.nama_alat}
                                onChange={(e) => form.setData('nama_alat', e.target.value)}
                                placeholder="mis. Real Time PCR"
                            />
                            {form.errors.nama_alat && <p className="text-xs text-destructive">{form.errors.nama_alat}</p>}
                        </div>

                        <div className="flex flex-col gap-1.5">
                            <FilterDropdown
                                label="Kategori"
                                placeholder="Pilih Kategori"
                                value={form.data.kategori || null}
                                options={KATEGORI_OPTIONS}
                                onChange={(v) => form.setData('kategori', v ?? '')}
                            />
                            {form.errors.kategori && <p className="text-xs text-destructive">{form.errors.kategori}</p>}
                        </div>

                        <div className="flex flex-col gap-1.5">
                            <Label htmlFor="keterangan">Keterangan (opsional)</Label>
                            <Textarea
                                id="keterangan"
                                value={form.data.keterangan}
                                onChange={(e) => form.setData('keterangan', e.target.value)}
                                rows={2}
                            />
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

            <div className="flex flex-col gap-4 lg:col-span-3">
                <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <FilterDropdown
                        label="Filter Kategori"
                        placeholder="Semua Kategori"
                        value={kategoriFilter}
                        options={KATEGORI_OPTIONS}
                        onChange={setKategoriFilter}
                    />
                    <div className="flex flex-col gap-1.5">
                        <Label htmlFor="cari-alat">Cari Alat</Label>
                        <Input
                            id="cari-alat"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Ketik nama alat…"
                        />
                    </div>
                </div>

                {filtered.length === 0 ? (
                    <EmptyState title="Tidak Ada Alat" description="Ubah filter/pencarian atau tambah alat baru." />
                ) : (
                    <div className="flex flex-col gap-2">
                        <p className="text-xs text-muted-foreground">{filtered.length} alat</p>
                        {filtered.map((alat) => (
                            <div key={alat.id} className="rounded-lg border">
                                <div className="flex items-start justify-between gap-3 p-3">
                                    <div className="min-w-0">
                                        <p className="truncate text-sm font-medium text-foreground">{alat.nama_alat}</p>
                                        <p className="text-xs text-muted-foreground">{KATEGORI_ALAT[alat.kategori] ?? alat.kategori}</p>
                                        <p className="mt-1 font-mono text-xs text-muted-foreground">{standarSummary(alat.standar)}</p>
                                    </div>
                                    <div className="flex shrink-0 gap-1">
                                        <Button size="sm" variant="ghost" onClick={() => startEdit(alat)}>
                                            Edit
                                        </Button>
                                        <Button
                                            size="sm"
                                            variant="ghost"
                                            onClick={() => setStandarFor(standarFor === alat.id ? null : alat.id)}
                                        >
                                            Standar
                                        </Button>
                                        <ConfirmButton
                                            onConfirm={() => router.delete(`/admin/alat/${alat.id}`, { preserveScroll: true })}
                                        >
                                            Hapus
                                        </ConfirmButton>
                                    </div>
                                </div>
                                {standarFor === alat.id && (
                                    <StandarEditor
                                        key={alat.id}
                                        alat={alat}
                                        onDone={() => setStandarFor(null)}
                                    />
                                )}
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
}

function StandarEditor({ alat, onDone }: { alat: AlatCatalogItem; onDone: () => void }) {
    const form = useForm<{ values: Record<string, string> }>({
        values: Object.fromEntries(
            STANDAR_SLOTS.map((s) => [`${s.tier}_${s.jenis_lab}`, String(slotValue(alat.standar, s.tier, s.jenis_lab))]),
        ),
    });

    const onSave = () => {
        form.transform((data) => ({
            items: STANDAR_SLOTS.map((s) => ({
                tier: s.tier,
                jenis_lab: s.jenis_lab,
                jumlah_minimal: Number(data.values[`${s.tier}_${s.jenis_lab}`] || 0),
            })),
        }));
        form.post(`/admin/alat/${alat.id}/standar`, { preserveScroll: true, onSuccess: onDone });
    };

    return (
        <div className="border-t bg-muted/30 p-3">
            <p className="mb-2 text-xs font-medium text-muted-foreground">
                Standar jumlah minimal per tier (0 = tidak diwajibkan)
            </p>
            <div className="grid grid-cols-3 gap-2 sm:grid-cols-6">
                {STANDAR_SLOTS.map((slot) => {
                    const key = `${slot.tier}_${slot.jenis_lab}`;
                    return (
                        <div key={key} className="flex flex-col gap-1">
                            <span className="text-center text-xs text-muted-foreground">{slot.label}</span>
                            <Input
                                type="number"
                                min={0}
                                value={form.data.values[key] ?? '0'}
                                onChange={(e) => form.setData('values', { ...form.data.values, [key]: e.target.value })}
                                className="text-center"
                            />
                        </div>
                    );
                })}
            </div>
            <div className="mt-3 flex justify-end gap-2">
                <Button size="sm" variant="ghost" onClick={onDone}>
                    Tutup
                </Button>
                <Button size="sm" onClick={onSave} disabled={form.processing}>
                    Simpan Standar
                </Button>
            </div>
        </div>
    );
}

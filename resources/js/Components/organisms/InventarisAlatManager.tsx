import { FormEventHandler } from 'react';
import { router, useForm } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { FilterDropdown } from '@/Components/molecules/FilterDropdown';
import { EmptyState } from '@/Components/molecules/EmptyState';
import { KATEGORI_ALAT, KATEGORI_ORDER, JENIS_LAB_LABEL } from '@/lib/constants';
import type { InventarisLabkesmasOption, RequiredItem } from '@/types/inventarisAlat';

interface InventarisAlatManagerProps {
    labkesmasOptions: InventarisLabkesmasOption[];
    selectedLabkesmasId: string | null;
    items: RequiredItem[];
}

function labOptionLabel(o: InventarisLabkesmasOption): string {
    const jenis = o.jenis_lab ? ` · ${JENIS_LAB_LABEL[o.jenis_lab]}` : '';
    return `${o.nama_kantor} (Tier ${o.tier_labkesmas}${jenis})`;
}

/**
 * Input massal kepemilikan alat satu Labkesmas.
 * Pilih lab → daftar alat wajib (dikelompokkan kategori) → isi jumlah dimiliki → simpan sekali.
 */
export function InventarisAlatManager({ labkesmasOptions, selectedLabkesmasId, items }: InventarisAlatManagerProps) {
    // Ganti lab → muat ulang halaman dengan item milik lab tsb (data diambil server-side).
    const onSelectLab = (id: string | null) => {
        router.get('/admin/inventaris-alat', id ? { labkesmas_id: id } : {}, {
            preserveScroll: true,
        });
    };

    const selected = labkesmasOptions.find((o) => o.id === selectedLabkesmasId) ?? null;
    const isTier5NoJenis = selected?.tier_labkesmas === 5 && !selected?.jenis_lab;

    return (
        <div className="flex flex-col gap-6">
            <Card>
                <CardContent className="pt-6">
                    <div className="max-w-md">
                        <FilterDropdown
                            label="Labkesmas"
                            placeholder="Pilih Labkesmas"
                            value={selectedLabkesmasId}
                            options={labkesmasOptions.map((o) => ({ value: o.id, label: labOptionLabel(o) }))}
                            onChange={onSelectLab}
                        />
                    </div>
                </CardContent>
            </Card>

            {!selectedLabkesmasId && (
                <EmptyState
                    title="Pilih Labkesmas"
                    description="Pilih Labkesmas untuk menampilkan daftar alat wajib sesuai tier-nya."
                />
            )}

            {selectedLabkesmasId && isTier5NoJenis && (
                <EmptyState
                    title="Jenis Lab Belum Ditetapkan"
                    description="Lab tier 5 ini belum memiliki Jenis Lab (Biokes/Kesling). Lengkapi dulu di menu Labkesmas agar standar alatnya muncul."
                />
            )}

            {selectedLabkesmasId && !isTier5NoJenis && items.length === 0 && (
                <EmptyState
                    title="Tidak Ada Alat Wajib"
                    description="Tidak ada standar alat untuk tier lab ini."
                />
            )}

            {selectedLabkesmasId && items.length > 0 && (
                <InventarisForm key={selectedLabkesmasId} labkesmasId={selectedLabkesmasId} items={items} />
            )}
        </div>
    );
}

function InventarisForm({ labkesmasId, items }: { labkesmasId: string; items: RequiredItem[] }) {
    const form = useForm<{ labkesmas_id: string; quantities: Record<string, string> }>({
        labkesmas_id: labkesmasId,
        quantities: Object.fromEntries(items.map((i) => [i.alat_id, String(i.jumlah_dimiliki)])),
    });

    const setQty = (alatId: string, value: string) =>
        form.setData('quantities', { ...form.data.quantities, [alatId]: value });

    const onSubmit: FormEventHandler = (e) => {
        e.preventDefault();
        form.transform((data) => ({
            labkesmas_id: data.labkesmas_id,
            items: Object.entries(data.quantities).map(([alat_id, jumlah]) => ({
                alat_id,
                jumlah: Number(jumlah || 0),
            })),
        }));
        form.post('/admin/inventaris-alat', { preserveScroll: true });
    };

    // Kelompokkan item per kategori (urut sesuai dokumen KMK).
    const grouped = KATEGORI_ORDER.map((kat) => ({
        kategori: kat,
        items: items.filter((i) => i.kategori === kat),
    })).filter((g) => g.items.length > 0);

    return (
        <form onSubmit={onSubmit} className="flex flex-col gap-6">
            {grouped.map((group) => (
                <Card key={group.kategori}>
                    <CardHeader>
                        <CardTitle className="text-sm font-semibold text-foreground">
                            {KATEGORI_ALAT[group.kategori] ?? group.kategori}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="overflow-x-auto rounded-lg border">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b bg-muted/50 text-left text-xs font-medium text-muted-foreground">
                                        <th className="px-4 py-2.5">Alat</th>
                                        <th className="w-24 px-4 py-2.5 text-right">Standar</th>
                                        <th className="w-32 px-4 py-2.5 text-right">Dimiliki</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {group.items.map((it) => (
                                        <tr key={it.alat_id} className="border-b last:border-0">
                                            <td className="px-4 py-2 text-foreground">{it.nama_alat}</td>
                                            <td className="px-4 py-2 text-right tabular-nums text-muted-foreground">
                                                {it.jumlah_minimal}
                                            </td>
                                            <td className="px-4 py-2 text-right">
                                                <Input
                                                    type="number"
                                                    min={0}
                                                    value={form.data.quantities[it.alat_id] ?? ''}
                                                    onChange={(e) => setQty(it.alat_id, e.target.value)}
                                                    className="ml-auto w-24 text-right"
                                                />
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            ))}

            <div className="sticky bottom-4 flex justify-end">
                <Button type="submit" disabled={form.processing} className="shadow-lg">
                    {form.processing ? 'Menyimpan…' : 'Simpan Semua'}
                </Button>
            </div>
        </form>
    );
}

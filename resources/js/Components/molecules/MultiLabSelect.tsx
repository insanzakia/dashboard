import { useMemo, useState } from 'react';
import { Input } from '@/Components/ui/input';
import { Button } from '@/Components/ui/button';
import { cn } from '@/lib/utils';

export interface MultiSelectOption {
    value: string;
    label: string;
}

interface MultiLabSelectProps {
    options: MultiSelectOption[];
    selected: string[];
    onChange: (ids: string[]) => void;
    max?: number;
}

/** Pemilih banyak Labkesmas (checklist + pencarian) untuk perbandingan berdampingan. */
export function MultiLabSelect({ options, selected, onChange, max = 6 }: MultiLabSelectProps) {
    const [search, setSearch] = useState('');

    const filtered = useMemo(() => {
        const q = search.trim().toLowerCase();
        return q ? options.filter((o) => o.label.toLowerCase().includes(q)) : options;
    }, [options, search]);

    const toggle = (id: string) => {
        if (selected.includes(id)) {
            onChange(selected.filter((s) => s !== id));
        } else if (selected.length < max) {
            onChange([...selected, id]);
        }
    };

    const atLimit = selected.length >= max;

    return (
        <div className="flex flex-col gap-2">
            <div className="flex items-center justify-between gap-2">
                <span className="text-xs font-medium text-muted-foreground">
                    {selected.length} dipilih{atLimit ? ` (maks ${max})` : ''}
                </span>
                {selected.length > 0 && (
                    <Button size="sm" variant="ghost" onClick={() => onChange([])}>
                        Bersihkan
                    </Button>
                )}
            </div>
            <Input placeholder="Cari Labkesmas…" value={search} onChange={(e) => setSearch(e.target.value)} />
            <div className="max-h-64 overflow-y-auto rounded-lg border">
                {filtered.length === 0 && (
                    <p className="px-3 py-4 text-center text-xs text-muted-foreground">Tidak ada hasil.</p>
                )}
                {filtered.map((o) => {
                    const checked = selected.includes(o.value);
                    const disabled = !checked && atLimit;
                    return (
                        <button
                            key={o.value}
                            type="button"
                            disabled={disabled}
                            onClick={() => toggle(o.value)}
                            className={cn(
                                'flex w-full items-center gap-2 border-b px-3 py-2 text-left text-sm last:border-0',
                                checked ? 'bg-primary/5' : 'hover:bg-muted/50',
                                disabled && 'cursor-not-allowed opacity-40',
                            )}
                        >
                            <span
                                className={cn(
                                    'flex h-4 w-4 shrink-0 items-center justify-center rounded border text-[10px]',
                                    checked ? 'border-primary bg-primary text-primary-foreground' : 'border-muted-foreground/40',
                                )}
                                aria-hidden="true"
                            >
                                {checked ? '✓' : ''}
                            </span>
                            <span className="text-foreground">{o.label}</span>
                        </button>
                    );
                })}
            </div>
        </div>
    );
}

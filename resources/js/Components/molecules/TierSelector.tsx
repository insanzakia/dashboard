import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { TIER_OPTIONS } from '@/lib/constants';
import type { TierLabkesmas } from '@/types/labkesmas';

const ALL_TIER_VALUE = 'all';

export interface TierSelectorProps {
    value: TierLabkesmas | null;
    onChange: (tier: TierLabkesmas | null) => void;
}

export function TierSelector({ value, onChange }: TierSelectorProps) {
    return (
        <div className="flex flex-col gap-1.5">
            <label className="text-xs font-medium text-muted-foreground">Tier Labkesmas</label>
            <Select
                value={value ? String(value) : ALL_TIER_VALUE}
                onValueChange={(next) => onChange(next === ALL_TIER_VALUE ? null : (Number(next) as TierLabkesmas))}
            >
                <SelectTrigger>
                    <SelectValue placeholder="Semua Tier" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value={ALL_TIER_VALUE}>Semua Tier</SelectItem>
                    {TIER_OPTIONS.map((option) => (
                        <SelectItem key={option.value} value={String(option.value)}>
                            {option.label} — {option.description}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
        </div>
    );
}

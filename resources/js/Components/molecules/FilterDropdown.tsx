import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Spinner } from '@/Components/atoms/Spinner';

export interface FilterOption {
    value: string;
    label: string;
}

export interface FilterDropdownProps {
    label: string;
    placeholder: string;
    value: string | null;
    options: FilterOption[];
    onChange: (value: string | null) => void;
    disabled?: boolean;
    isLoading?: boolean;
}

/** Satu dropdown filter generik — dipakai berulang untuk tiap jenjang Wilayah. */
export function FilterDropdown({
    label,
    placeholder,
    value,
    options,
    onChange,
    disabled = false,
    isLoading = false,
}: FilterDropdownProps) {
    return (
        <div className="flex flex-col gap-1.5">
            <label className="flex items-center gap-1.5 text-xs font-medium text-muted-foreground">
                {label}
                {isLoading && <Spinner className="h-3 w-3" />}
            </label>
            <Select
                value={value ?? ''}
                onValueChange={(next) => onChange(next)}
                disabled={disabled || isLoading}
            >
                <SelectTrigger>
                    <SelectValue placeholder={placeholder} />
                </SelectTrigger>
                <SelectContent>
                    {options.map((option) => (
                        <SelectItem key={option.value} value={option.value}>
                            {option.label}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
        </div>
    );
}

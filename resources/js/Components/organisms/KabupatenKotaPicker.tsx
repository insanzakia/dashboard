import { useState } from 'react';
import { FilterDropdown, type FilterOption } from '@/Components/molecules/FilterDropdown';
import { useWilayahOptions } from '@/hooks/useWilayahOptions';
import type { RequestState } from '@/types/dashboard';

interface KabupatenKotaPickerProps {
    value: string | null;
    onChange: (kabupatenKotaId: string | null) => void;
    /** Nilai awal untuk memulihkan kaskade saat mode edit (picker sebaiknya di-remount via key). */
    initialRegionalId?: string | null;
    initialProvinsiId?: string | null;
    errorText?: string;
}

function toOptions<T extends { id: string; nama: string }>(state: RequestState<T[]>): FilterOption[] {
    return (state.data ?? []).map((item) => ({ value: item.id, label: item.nama }));
}

/**
 * Picker lokasi kaskade Regional → Provinsi → Kabupaten/Kota untuk form.
 * Mengelola state parent secara lokal dan melaporkan kabupaten_kota_id terpilih via onChange.
 * Reuse useWilayahOptions (endpoint /dashboard-data/wilayah/*).
 */
export function KabupatenKotaPicker({
    value,
    onChange,
    initialRegionalId = null,
    initialProvinsiId = null,
    errorText,
}: KabupatenKotaPickerProps) {
    const [regionalId, setRegionalId] = useState<string | null>(initialRegionalId);
    const [provinsiId, setProvinsiId] = useState<string | null>(initialProvinsiId);
    const { regional, provinsi, kabupatenKota } = useWilayahOptions(regionalId, provinsiId);

    return (
        <div className="flex flex-col gap-4">
            <FilterDropdown
                label="Regional"
                placeholder="Pilih Regional"
                value={regionalId}
                options={toOptions(regional)}
                isLoading={regional.status === 'loading'}
                onChange={(v) => {
                    setRegionalId(v);
                    setProvinsiId(null);
                    onChange(null);
                }}
            />
            <FilterDropdown
                label="Provinsi"
                placeholder="Pilih Provinsi"
                value={provinsiId}
                options={toOptions(provinsi)}
                disabled={!regionalId}
                isLoading={provinsi.status === 'loading'}
                onChange={(v) => {
                    setProvinsiId(v);
                    onChange(null);
                }}
            />
            <FilterDropdown
                label="Kabupaten/Kota"
                placeholder="Pilih Kabupaten/Kota"
                value={value}
                options={toOptions(kabupatenKota)}
                disabled={!provinsiId}
                isLoading={kabupatenKota.status === 'loading'}
                onChange={(v) => onChange(v)}
            />
            {errorText && <p className="text-xs text-destructive">{errorText}</p>}
        </div>
    );
}

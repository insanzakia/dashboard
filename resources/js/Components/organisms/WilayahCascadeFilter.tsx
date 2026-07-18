import { FilterDropdown, type FilterOption } from '@/Components/molecules/FilterDropdown';
import { useDashboardFilter } from '@/context/DashboardFilterContext';
import { useWilayahOptions } from '@/hooks/useWilayahOptions';
import type { RequestState } from '@/types/dashboard';

function toOptions<T extends { id: string; nama: string }>(state: RequestState<T[]>): FilterOption[] {
    return (state.data ?? []).map((item) => ({ value: item.id, label: item.nama }));
}

/** Filter kaskade Regional → Provinsi → Kabupaten/Kota. State pilihan hidup di DashboardFilterContext. */
export function WilayahCascadeFilter() {
    const { filter, setRegional, setProvinsi, setKabupatenKota } = useDashboardFilter();
    const { regional, provinsi, kabupatenKota } = useWilayahOptions(
        filter.wilayah.regionalId,
        filter.wilayah.provinsiId,
    );

    return (
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <FilterDropdown
                label="Regional"
                placeholder="Semua Regional"
                value={filter.wilayah.regionalId}
                options={toOptions(regional)}
                onChange={setRegional}
                isLoading={regional.status === 'loading'}
            />
            <FilterDropdown
                label="Provinsi"
                placeholder="Semua Provinsi"
                value={filter.wilayah.provinsiId}
                options={toOptions(provinsi)}
                onChange={setProvinsi}
                disabled={!filter.wilayah.regionalId}
                isLoading={provinsi.status === 'loading'}
            />
            <FilterDropdown
                label="Kabupaten/Kota"
                placeholder="Semua Kabupaten/Kota"
                value={filter.wilayah.kabupatenKotaId}
                options={toOptions(kabupatenKota)}
                onChange={setKabupatenKota}
                disabled={!filter.wilayah.provinsiId}
                isLoading={kabupatenKota.status === 'loading'}
            />
        </div>
    );
}

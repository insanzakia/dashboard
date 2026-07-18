import { createContext, useContext, useMemo, useState, type ReactNode } from 'react';
import type { DashboardFilter } from '@/types/dashboard';
import type { TierLabkesmas } from '@/types/labkesmas';
import type { WilayahFilterValue } from '@/types/wilayah';

const EMPTY_WILAYAH: WilayahFilterValue = {
    regionalId: null,
    provinsiId: null,
    kabupatenKotaId: null,
};

interface DashboardFilterContextValue {
    filter: DashboardFilter;
    setRegional: (regionalId: string | null) => void;
    setProvinsi: (provinsiId: string | null) => void;
    setKabupatenKota: (kabupatenKotaId: string | null) => void;
    setTier: (tier: TierLabkesmas | null) => void;
    resetFilter: () => void;
}

const DashboardFilterContext = createContext<DashboardFilterContextValue | null>(null);

/**
 * Menyimpan state filter Wilayah (kaskade) & Tier yang dipakai bersama oleh
 * SummaryCardsGrid, TrendChart, dan WilayahCascadeFilter — menghindari prop-drilling.
 */
export function DashboardFilterProvider({ children }: { children: ReactNode }) {
    const [wilayah, setWilayah] = useState<WilayahFilterValue>(EMPTY_WILAYAH);
    const [tier, setTierState] = useState<TierLabkesmas | null>(null);

    const value = useMemo<DashboardFilterContextValue>(
        () => ({
            filter: { wilayah, tier },
            setRegional: (regionalId) =>
                setWilayah({ regionalId, provinsiId: null, kabupatenKotaId: null }),
            setProvinsi: (provinsiId) =>
                setWilayah((prev) => ({ ...prev, provinsiId, kabupatenKotaId: null })),
            setKabupatenKota: (kabupatenKotaId) =>
                setWilayah((prev) => ({ ...prev, kabupatenKotaId })),
            setTier: (nextTier) => setTierState(nextTier),
            resetFilter: () => {
                setWilayah(EMPTY_WILAYAH);
                setTierState(null);
            },
        }),
        [wilayah, tier],
    );

    return <DashboardFilterContext.Provider value={value}>{children}</DashboardFilterContext.Provider>;
}

export function useDashboardFilter() {
    const ctx = useContext(DashboardFilterContext);
    if (!ctx) {
        throw new Error('useDashboardFilter harus dipakai di dalam <DashboardFilterProvider>');
    }
    return ctx;
}

import { StatCard } from '@/Components/molecules/StatCard';
import { EmptyState } from '@/Components/molecules/EmptyState';
import { ErrorState } from '@/Components/molecules/ErrorState';
import { Skeleton } from '@/Components/atoms/Skeleton';
import { formatNumber, formatPercentage } from '@/lib/utils';
import type { RequestState, SummaryCardData } from '@/types/dashboard';

export interface SummaryCardsGridProps {
    state: RequestState<SummaryCardData[]>;
    onRetry?: () => void;
    skeletonCount?: number;
}

/** Warna aksen strip per kartu, mengikuti urutan palet grafik. */
const CARD_ACCENTS = ['var(--chart-1)', 'var(--chart-5)', 'var(--chart-2)'];

/** "Dashboard First Win" — menangani 4 kondisi request secara eksplisit, tidak pernah render UI kosong diam-diam. */
export function SummaryCardsGrid({ state, onRetry, skeletonCount = 3 }: SummaryCardsGridProps) {
    if (state.status === 'loading') {
        return (
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {Array.from({ length: skeletonCount }).map((_, index) => (
                    <Skeleton key={index} className="h-28 w-full" />
                ))}
            </div>
        );
    }

    if (state.status === 'error') {
        return <ErrorState message={state.errorMessage ?? 'Gagal memuat ringkasan.'} onRetry={onRetry} />;
    }

    if (state.status === 'empty') {
        return (
            <EmptyState
                title="Belum Ada Data Pemeriksaan"
                description="Tidak ditemukan data pemeriksaan untuk cakupan wilayah/tier yang dipilih."
            />
        );
    }

    return (
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {state.data?.map((card, index) => (
                <StatCard
                    key={card.id}
                    label={card.label}
                    value={formatNumber(card.totalPemeriksaan)}
                    deltaLabel={card.deltaPercentase !== null ? `${formatPercentage(card.deltaPercentase)} dari bulan lalu` : undefined}
                    deltaTone={card.deltaPercentase === null ? 'neutral' : card.deltaPercentase >= 0 ? 'positive' : 'negative'}
                    accent={CARD_ACCENTS[index % CARD_ACCENTS.length]}
                />
            ))}
        </div>
    );
}

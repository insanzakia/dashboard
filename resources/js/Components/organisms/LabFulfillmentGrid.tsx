import { Link } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';
import { EmptyState } from '@/Components/molecules/EmptyState';
import { ErrorState } from '@/Components/molecules/ErrorState';
import { Skeleton } from '@/Components/atoms/Skeleton';
import { JENIS_LAB_LABEL } from '@/lib/constants';
import { formatPersen, persenColor } from '@/lib/utils';
import type { RequestState } from '@/types/dashboard';
import type { LabComparisonRow } from '@/types/pemenuhanAlat';

export interface LabFulfillmentGridProps {
    state: RequestState<LabComparisonRow[]>;
}

/**
 * Kisi kartu pemenuhan per Labkesmas — data utama halaman Standar Peralatan.
 * Setiap kartu menampilkan persentase pemenuhan alat lab tsb dan menautkan ke halaman profilnya.
 */
export function LabFulfillmentGrid({ state }: LabFulfillmentGridProps) {
    if (state.status === 'loading') {
        return (
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {Array.from({ length: 6 }).map((_, i) => (
                    <Skeleton key={i} className="h-36 w-full" />
                ))}
            </div>
        );
    }

    if (state.status === 'error') {
        return <ErrorState message={state.errorMessage ?? 'Gagal memuat data pemenuhan.'} />;
    }

    if (state.status === 'empty' || !state.data || state.data.length === 0) {
        return (
            <EmptyState
                title="Belum Ada Labkesmas"
                description="Tidak ada Labkesmas pada cakupan wilayah/tier yang dipilih."
            />
        );
    }

    return (
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {state.data.map((lab) => (
                <LabCard key={lab.labkesmas_id} lab={lab} />
            ))}
        </div>
    );
}

function LabCard({ lab }: { lab: LabComparisonRow }) {
    const tierLabel =
        lab.tier === 5 && lab.jenis_lab ? `Tier 5 · ${JENIS_LAB_LABEL[lab.jenis_lab]}` : `Tier ${lab.tier}`;
    const width = lab.persen === null ? 0 : Math.min(100, Math.max(0, lab.persen));
    const color = persenColor(lab.persen);

    return (
        <Link
            href={`/list-labkesmas/${lab.labkesmas_id}`}
            className="group flex flex-col gap-3 rounded-xl border bg-card p-4 transition-all hover:border-primary/50 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
        >
            <div className="flex items-start justify-between gap-2">
                <div className="min-w-0">
                    <h3 className="truncate text-sm font-semibold text-foreground" title={lab.nama_kantor}>
                        {lab.nama_kantor}
                    </h3>
                    <span className="mt-1 inline-block rounded-full bg-muted px-2 py-0.5 text-[11px] font-medium text-muted-foreground">
                        {tierLabel}
                    </span>
                </div>
                <ChevronRight className="mt-0.5 h-4 w-4 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-0.5 group-hover:text-foreground" />
            </div>

            {lab.persen === null ? (
                <p className="mt-auto text-xs text-muted-foreground">Standar belum tersedia untuk lab ini.</p>
            ) : (
                <div className="mt-auto flex flex-col gap-1.5">
                    <div className="flex items-baseline justify-between">
                        <span className="text-2xl font-bold tabular-nums" style={{ color }}>
                            {formatPersen(lab.persen)}
                        </span>
                        <span className="text-xs text-muted-foreground tabular-nums">
                            {lab.total_terpenuhi}/{lab.total_wajib} alat
                        </span>
                    </div>
                    <div className="h-2 w-full overflow-hidden rounded-full bg-muted">
                        <div
                            className="h-full rounded-full transition-[width] duration-500"
                            style={{ width: `${width}%`, backgroundColor: color }}
                        />
                    </div>
                </div>
            )}
        </Link>
    );
}

import { Head } from '@inertiajs/react';
import { Wrench } from 'lucide-react';
import { PublicLayout } from '@/Layouts/PublicLayout';
import { EmptyState } from '@/Components/molecules/EmptyState';

/**
 * Dashboard Standar Peralatan Labkesmas — kerangka awal.
 * Konten menyusul setelah model data peralatan/standar didefinisikan.
 */
export default function StandarLabkesmasIndex() {
    return (
        <PublicLayout>
            <Head title="Standar Labkesmas" />
            <div className="mb-6">
                <h1 className="text-lg font-semibold text-foreground">Dashboard Standar Peralatan</h1>
                <p className="mt-1 text-sm text-muted-foreground">
                    Pemantauan kesesuaian standar peralatan laboratorium per tier labkesmas.
                </p>
            </div>
            <EmptyState
                icon={Wrench}
                title="Segera Hadir"
                description="Modul standar peralatan sedang disiapkan. Struktur data peralatan & indikator standar akan ditentukan pada tahap berikutnya."
            />
        </PublicLayout>
    );
}

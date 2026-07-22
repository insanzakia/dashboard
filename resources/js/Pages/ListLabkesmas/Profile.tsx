import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { PublicLayout } from '@/Layouts/PublicLayout';
import { LabFulfillmentDetail } from '@/Components/organisms/LabFulfillmentDetail';
import { LabPemeriksaanSection } from '@/Components/organisms/LabPemeriksaanSection';
import { useLabFulfillment } from '@/hooks/useLabFulfillment';
import { JENIS_LAB_LABEL } from '@/lib/constants';
import type { JenisLab } from '@/types/alat';

interface ProfileLab {
    id: string;
    nama_kantor: string;
    tier: number;
    jenis_lab: JenisLab | null;
}

export default function ListLabkesmasProfile({ lab }: { lab: ProfileLab }) {
    const detail = useLabFulfillment(lab.id);

    const tierLabel =
        lab.tier === 5 && lab.jenis_lab ? `Tier 5 · ${JENIS_LAB_LABEL[lab.jenis_lab]}` : `Tier ${lab.tier}`;

    return (
        <PublicLayout>
            <Head title={`Profil · ${lab.nama_kantor}`} />
            <div className="flex flex-col gap-6">
                <div>
                    <Link
                        href="/list-labkesmas"
                        className="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
                    >
                        <ArrowLeft className="h-4 w-4" />
                        Kembali ke List Labkesmas
                    </Link>
                </div>

                <header className="flex flex-col gap-2">
                    <h1 className="text-xl font-semibold text-foreground">{lab.nama_kantor}</h1>
                    <span className="inline-block w-fit rounded-full bg-muted px-2.5 py-0.5 text-xs font-medium text-muted-foreground">
                        {tierLabel}
                    </span>
                </header>

                <section className="flex flex-col gap-4">
                    <h2 className="text-base font-semibold text-foreground">Data Pemeriksaan</h2>
                    <LabPemeriksaanSection labId={lab.id} />
                </section>

                <section className="flex flex-col gap-4">
                    <h2 className="text-base font-semibold text-foreground">Pemenuhan Standar Alat</h2>
                    <LabFulfillmentDetail state={detail} />
                </section>
            </div>
        </PublicLayout>
    );
}

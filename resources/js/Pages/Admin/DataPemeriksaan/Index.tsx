import { Head } from '@inertiajs/react';
import { AdminLayout } from '@/Layouts/AdminLayout';
import { DataPemeriksaanManager } from '@/Components/organisms/DataPemeriksaanManager';
import type { JenisTesOption, LabkesmasOption, RecentEntry } from '@/types/dataPemeriksaan';

interface AdminDataPemeriksaanProps {
    labkesmasOptions: LabkesmasOption[];
    jenisTesOptions: JenisTesOption[];
    recentEntries: RecentEntry[];
}

export default function AdminDataPemeriksaanIndex(props: AdminDataPemeriksaanProps) {
    return (
        <AdminLayout title="Input Pemeriksaan Bulanan">
            <Head title="Admin · Input Bulanan" />
            <DataPemeriksaanManager {...props} />
        </AdminLayout>
    );
}

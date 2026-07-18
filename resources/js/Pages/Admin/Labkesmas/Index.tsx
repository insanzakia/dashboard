import { Head } from '@inertiajs/react';
import { AdminLayout } from '@/Layouts/AdminLayout';
import { LabkesmasManager } from '@/Components/organisms/LabkesmasManager';
import type { LabkesmasRow } from '@/types/labkesmas';

export default function AdminLabkesmasIndex({ items }: { items: LabkesmasRow[] }) {
    return (
        <AdminLayout title="Pendaftaran Labkesmas">
            <Head title="Admin · Labkesmas" />
            <LabkesmasManager items={items} />
        </AdminLayout>
    );
}

import { Head } from '@inertiajs/react';
import { AdminLayout } from '@/Layouts/AdminLayout';
import { AlatManager } from '@/Components/organisms/AlatManager';
import type { AlatCatalogItem } from '@/types/alat';

export default function AdminAlatIndex({ items }: { items: AlatCatalogItem[] }) {
    return (
        <AdminLayout title="Alat & Standar Peralatan">
            <Head title="Admin · Alat & Standar" />
            <AlatManager items={items} />
        </AdminLayout>
    );
}

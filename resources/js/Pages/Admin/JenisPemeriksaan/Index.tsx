import { Head } from '@inertiajs/react';
import { AdminLayout } from '@/Layouts/AdminLayout';
import { JenisPemeriksaanManager } from '@/Components/organisms/JenisPemeriksaanManager';
import type { JenisPemeriksaanRow } from '@/types/jenisPemeriksaan';

export default function AdminJenisPemeriksaanIndex({ items }: { items: JenisPemeriksaanRow[] }) {
    return (
        <AdminLayout title="Master Jenis Pemeriksaan">
            <Head title="Admin · Jenis Pemeriksaan" />
            <JenisPemeriksaanManager items={items} />
        </AdminLayout>
    );
}

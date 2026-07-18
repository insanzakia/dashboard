import { Head } from '@inertiajs/react';
import { AdminLayout } from '@/Layouts/AdminLayout';
import { WilayahManager } from '@/Components/organisms/WilayahManager';
import type { WilayahLevelConfig, WilayahOption, WilayahRow } from '@/types/wilayah';

interface AdminWilayahProps {
    level: WilayahLevelConfig;
    items: WilayahRow[];
    parents: WilayahOption[];
}

/** Halaman generik Master Wilayah — thin: teruskan props backend ke WilayahManager. */
export default function AdminWilayahIndex({ level, items, parents }: AdminWilayahProps) {
    return (
        <AdminLayout title={`Master Wilayah — ${level.label}`}>
            <Head title={`Admin · ${level.label}`} />
            <WilayahManager level={level} items={items} parents={parents} />
        </AdminLayout>
    );
}

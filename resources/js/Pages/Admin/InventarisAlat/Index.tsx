import { Head } from '@inertiajs/react';
import { AdminLayout } from '@/Layouts/AdminLayout';
import { InventarisAlatManager } from '@/Components/organisms/InventarisAlatManager';
import type { InventarisLabkesmasOption, RequiredItem } from '@/types/inventarisAlat';

interface Props {
    labkesmasOptions: InventarisLabkesmasOption[];
    selectedLabkesmasId: string | null;
    items: RequiredItem[];
}

export default function AdminInventarisAlatIndex({ labkesmasOptions, selectedLabkesmasId, items }: Props) {
    return (
        <AdminLayout title="Input Kepemilikan Alat">
            <Head title="Admin · Input Alat" />
            <InventarisAlatManager
                labkesmasOptions={labkesmasOptions}
                selectedLabkesmasId={selectedLabkesmasId}
                items={items}
            />
        </AdminLayout>
    );
}

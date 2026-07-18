import { Head } from '@inertiajs/react';
import { AdminLayout } from '@/Layouts/AdminLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';

interface AdminMenuItem {
    title: string;
    description: string;
}

/** Landing panel admin setelah login. Kartu menu adalah placeholder untuk slice CRUD berikutnya. */
const ADMIN_MENU: AdminMenuItem[] = [
    { title: 'Master Wilayah', description: 'Kelola Negara, Regional, Provinsi, dan Kabupaten/Kota.' },
    { title: 'Pendaftaran Labkesmas', description: 'Daftarkan labkesmas baru beserta tier & lokasi.' },
    { title: 'Input Pemeriksaan', description: 'Masukkan angka pemeriksaan bulanan per labkesmas.' },
];

export default function AdminDashboard() {
    return (
        <AdminLayout title="Dashboard Administrasi">
            <Head title="Admin" />
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {ADMIN_MENU.map((item) => (
                    <Card key={item.title}>
                        <CardHeader>
                            <CardTitle className="text-sm font-semibold text-foreground">{item.title}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-sm text-muted-foreground">{item.description}</p>
                        </CardContent>
                    </Card>
                ))}
            </div>
        </AdminLayout>
    );
}

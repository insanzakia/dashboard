import { Head } from '@inertiajs/react';
import { AdminLayout } from '@/Layouts/AdminLayout';
import { UserManager } from '@/Components/organisms/UserManager';
import type { AdminUserRow, WilayahOptions } from '@/types/user';

export default function AdminUsersIndex({ users, wilayah }: { users: AdminUserRow[]; wilayah: WilayahOptions }) {
    return (
        <AdminLayout title="Kelola Akun">
            <Head title="Admin · Akun" />
            <UserManager users={users} wilayah={wilayah} />
        </AdminLayout>
    );
}

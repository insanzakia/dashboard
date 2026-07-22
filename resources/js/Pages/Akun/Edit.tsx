import { type FormEventHandler } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { AdminLayout } from '@/Layouts/AdminLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import type { SharedPageProps } from '@/types/inertia';

const ROLE_LABEL: Record<string, string> = {
    super_admin: 'Super Admin',
    admin: 'Admin',
};

export default function AkunEdit() {
    const user = usePage<SharedPageProps>().props.auth.user;

    const { data, setData, put, processing, errors, reset, recentlySuccessful } = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        put('/user/password', {
            errorBag: 'updatePassword',
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };

    return (
        <AdminLayout title="Akun Saya">
            <Head title="Akun Saya" />
            <div className="flex max-w-xl flex-col gap-6">
                <Card>
                    <CardHeader>
                        <CardTitle className="text-base font-semibold text-foreground">Informasi Akun</CardTitle>
                    </CardHeader>
                    <CardContent className="flex flex-col gap-3">
                        <div className="flex items-center justify-between border-b pb-3">
                            <span className="text-sm text-muted-foreground">Username</span>
                            <span className="text-sm font-medium text-foreground">{user?.username ?? '-'}</span>
                        </div>
                        <div className="flex items-center justify-between">
                            <span className="text-sm text-muted-foreground">Role</span>
                            <span className="inline-block rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-medium text-primary">
                                {ROLE_LABEL[user?.role ?? ''] ?? user?.role ?? '-'}
                            </span>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle className="text-base font-semibold text-foreground">Ganti Password</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="flex flex-col gap-4">
                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="current_password">Password Saat Ini</Label>
                                <Input
                                    id="current_password"
                                    type="password"
                                    autoComplete="current-password"
                                    value={data.current_password}
                                    onChange={(e) => setData('current_password', e.target.value)}
                                />
                                {errors.current_password && (
                                    <p className="text-xs text-destructive">{errors.current_password}</p>
                                )}
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="password">Password Baru</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    autoComplete="new-password"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                />
                                {errors.password && <p className="text-xs text-destructive">{errors.password}</p>}
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="password_confirmation">Konfirmasi Password Baru</Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    autoComplete="new-password"
                                    value={data.password_confirmation}
                                    onChange={(e) => setData('password_confirmation', e.target.value)}
                                />
                                {errors.password_confirmation && (
                                    <p className="text-xs text-destructive">{errors.password_confirmation}</p>
                                )}
                            </div>

                            <div className="flex items-center gap-3">
                                <Button type="submit" disabled={processing}>
                                    Simpan Password
                                </Button>
                                {recentlySuccessful && (
                                    <span className="text-sm text-emerald-600 dark:text-emerald-400">
                                        Password berhasil diperbarui.
                                    </span>
                                )}
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}

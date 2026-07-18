import { FormEventHandler } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { GuestLayout } from '@/Layouts/GuestLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';

/** Halaman login admin. Form dikirim ke POST /login (ditangani Laravel Fortify). */
export default function Login() {
    const { data, setData, post, processing, errors, reset } = useForm({
        username: '',
        password: '',
        remember: false,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post('/login', {
            onFinish: () => reset('password'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Login Admin" />
            <Card>
                <CardHeader>
                    <CardTitle className="text-base font-semibold text-foreground">Masuk</CardTitle>
                </CardHeader>
                <CardContent>
                    <form onSubmit={submit} className="flex flex-col gap-4">
                        <div className="flex flex-col gap-1.5">
                            <Label htmlFor="username">Username</Label>
                            <Input
                                id="username"
                                type="text"
                                value={data.username}
                                autoFocus
                                autoComplete="username"
                                onChange={(e) => setData('username', e.target.value)}
                            />
                            {errors.username && <p className="text-xs text-destructive">{errors.username}</p>}
                        </div>

                        <div className="flex flex-col gap-1.5">
                            <Label htmlFor="password">Password</Label>
                            <Input
                                id="password"
                                type="password"
                                value={data.password}
                                autoComplete="current-password"
                                onChange={(e) => setData('password', e.target.value)}
                            />
                            {errors.password && <p className="text-xs text-destructive">{errors.password}</p>}
                        </div>

                        <label className="flex items-center gap-2 text-sm text-muted-foreground">
                            <input
                                type="checkbox"
                                checked={data.remember}
                                onChange={(e) => setData('remember', e.target.checked)}
                                className="h-4 w-4 rounded border-input"
                            />
                            Ingat saya
                        </label>

                        <Button type="submit" disabled={processing} className="w-full">
                            {processing ? 'Memproses…' : 'Masuk'}
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </GuestLayout>
    );
}

import { useState } from 'react';
import { Button } from '@/Components/ui/button';

interface ConfirmButtonProps {
    onConfirm: () => void;
    children: React.ReactNode;
    confirmLabel?: string;
    cancelLabel?: string;
}

/**
 * Tombol aksi destruktif dengan konfirmasi inline (dua langkah), tanpa modal/window.confirm.
 * Klik pertama "meng-arm" tombol; klik kedua mengeksekusi.
 */
export function ConfirmButton({
    onConfirm,
    children,
    confirmLabel = 'Ya, hapus',
    cancelLabel = 'Batal',
}: ConfirmButtonProps) {
    const [armed, setArmed] = useState(false);

    if (armed) {
        return (
            <span className="inline-flex items-center gap-1">
                <Button
                    size="sm"
                    variant="outline"
                    className="text-destructive"
                    onClick={() => {
                        onConfirm();
                        setArmed(false);
                    }}
                >
                    {confirmLabel}
                </Button>
                <Button size="sm" variant="ghost" onClick={() => setArmed(false)}>
                    {cancelLabel}
                </Button>
            </span>
        );
    }

    return (
        <Button size="sm" variant="ghost" className="text-destructive" onClick={() => setArmed(true)}>
            {children}
        </Button>
    );
}

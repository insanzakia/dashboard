import { useState } from 'react';
import { router } from '@inertiajs/react';
import type { WilayahLevelConfig, WilayahRow } from '@/types/wilayah';

/**
 * Logika bisnis CRUD wilayah (create/update/delete) yang terpisah dari UI.
 * Membangun payload sesuai parentField level, dan memakai Inertia router (redirect + flash native).
 */
export function useWilayahCrud(level: WilayahLevelConfig) {
    const [nama, setNama] = useState('');
    const [parentId, setParentId] = useState('');
    const [editingId, setEditingId] = useState<string | null>(null);
    const [processing, setProcessing] = useState(false);

    const baseUrl = `/admin/wilayah/${level.key}`;

    const buildPayload = (): Record<string, string> => {
        const payload: Record<string, string> = { nama };
        if (level.parentField) {
            payload[level.parentField] = parentId;
        }
        return payload;
    };

    const reset = () => {
        setNama('');
        setParentId('');
        setEditingId(null);
    };

    const startEdit = (row: WilayahRow) => {
        setEditingId(row.id);
        setNama(row.nama);
        setParentId(row.parent_id ?? '');
    };

    const submit = () => {
        const options = {
            preserveScroll: true,
            onStart: () => setProcessing(true),
            onFinish: () => setProcessing(false),
            onSuccess: () => reset(), // hanya reset bila sukses; error → form & pesan dipertahankan.
        };

        if (editingId) {
            router.put(`${baseUrl}/${editingId}`, buildPayload(), options);
        } else {
            router.post(baseUrl, buildPayload(), options);
        }
    };

    const remove = (id: string) => {
        router.delete(`${baseUrl}/${id}`, { preserveScroll: true });
    };

    return { nama, setNama, parentId, setParentId, editingId, processing, startEdit, submit, remove, reset };
}

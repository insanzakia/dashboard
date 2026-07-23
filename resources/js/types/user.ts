export type ScopeType = 'regional' | 'provinsi' | 'kabupaten_kota' | 'labkesmas';

export interface UserScopeRow {
    scope_type: ScopeType;
    scope_id: string;
    label: string;
}

export interface AdminUserRow {
    id: string;
    username: string;
    role: string;
    is_super_admin: boolean;
    scopes: UserScopeRow[];
}

export interface WilayahOptions {
    regional: { id: string; nama: string }[];
    provinsi: { id: string; nama: string; regional_id: string }[];
    kabupaten_kota: { id: string; nama: string; provinsi_id: string }[];
    labkesmas: { id: string; nama_kantor: string; kabupaten_kota_id: string }[];
}

export const SCOPE_TYPE_LABEL: Record<ScopeType, string> = {
    regional: 'Regional',
    provinsi: 'Provinsi',
    kabupaten_kota: 'Kab/Kota',
    labkesmas: 'Labkesmas',
};

export const SCOPE_TYPE_OPTIONS: { value: ScopeType; label: string }[] = [
    { value: 'regional', label: 'Regional' },
    { value: 'provinsi', label: 'Provinsi' },
    { value: 'kabupaten_kota', label: 'Kabupaten/Kota' },
    { value: 'labkesmas', label: 'Labkesmas' },
];

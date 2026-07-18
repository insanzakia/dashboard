export interface LabkesmasOption {
    id: string;
    nama_kantor: string;
}

export interface JenisTesOption {
    id: string;
    nama_tes: string;
}

export interface RecentEntry {
    id: string;
    labkesmas_nama: string | null;
    jenis_nama: string | null;
    bulan: number;
    tahun: number;
    jumlah: number;
}

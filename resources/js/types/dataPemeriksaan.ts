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

/** Total pemeriksaan satu jenis pada satu lab. */
export interface LabPemeriksaanJenis {
    id: string;
    nama_tes: string;
    total: number;
}

/** Ringkasan pemeriksaan satu Labkesmas (halaman profil): total, rincian per jenis, dan tren bulanan. */
export interface LabPemeriksaan {
    total: number;
    per_jenis: LabPemeriksaanJenis[];
    trend: import('./dashboard').TrendSeries[];
}

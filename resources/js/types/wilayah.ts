export interface Negara {
    id: string;
    nama: string;
}

export interface Regional {
    id: string;
    nama: string;
    negara_id: string;
}

export interface Provinsi {
    id: string;
    nama: string;
    regional_id: string;
}

export interface KabupatenKota {
    id: string;
    nama: string;
    provinsi_id: string;
}

/** Level wilayah yang aktif dipilih pada filter kaskade. Semua bersifat opsional/independen. */
export interface WilayahFilterValue {
    regionalId: string | null;
    provinsiId: string | null;
    kabupatenKotaId: string | null;
}

/** Baris tabel admin untuk satu entitas wilayah (bentuk seragam lintas jenjang). */
export interface WilayahRow {
    id: string;
    nama: string;
    parent_id?: string | null;
    parent_nama?: string | null;
}

/** Opsi dropdown parent (id + nama). */
export interface WilayahOption {
    id: string;
    nama: string;
}

/** Konfigurasi level yang dikirim backend → mendorong satu komponen CRUD generik. */
export interface WilayahLevelConfig {
    key: string;                 // segmen URL, mis. "regional", "kabupaten-kota"
    label: string;               // judul jamak, mis. "Regional"
    singular: string;            // untuk label tombol/aksi
    parentLabel: string | null;  // null → jenjang tanpa parent (Negara)
    parentField: string | null;  // nama field FK saat submit, mis. "negara_id"
}

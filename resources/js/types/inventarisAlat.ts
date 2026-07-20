import type { JenisLab, KategoriAlat } from './alat';

/** Opsi Labkesmas untuk form input inventaris (dengan konteks tier & jenis lab). */
export interface InventarisLabkesmasOption {
    id: string;
    nama_kantor: string;
    tier_labkesmas: number;
    jenis_lab: JenisLab | null;
}

/** Item alat wajib untuk tier lab terpilih + jumlah yang sudah dimiliki. */
export interface RequiredItem {
    alat_id: string;
    nama_alat: string;
    kategori: KategoriAlat;
    jumlah_minimal: number;
    jumlah_dimiliki: number;
}

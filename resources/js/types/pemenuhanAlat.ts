import type { JenisLab, KategoriAlat } from './alat';

/** Rincian pemenuhan satu kategori pada satu lab. */
export interface KategoriPemenuhan {
    kategori: KategoriAlat;
    wajib: number;
    terpenuhi: number;
    persen: number;
}

/** Satu baris item pada rincian pemenuhan per lab. */
export interface AlatPemenuhanItem {
    nama_alat: string;
    kategori: KategoriAlat;
    jumlah_minimal: number;
    jumlah_dimiliki: number;
    terpenuhi: boolean;
}

export interface LabFulfillment {
    labkesmas: { id: string; nama_kantor: string; tier: number; jenis_lab: JenisLab | null };
    persen_total: number | null;
    total_wajib: number;
    total_terpenuhi: number;
    per_kategori: KategoriPemenuhan[];
    items: AlatPemenuhanItem[];
}

export interface AggregateKategori {
    kategori: KategoriAlat;
    persen_rata: number;
    jumlah_lab: number;
}

export interface AggregateFulfillment {
    persen_rata: number | null;
    jumlah_lab: number;
    per_kategori: AggregateKategori[];
}

export interface LabComparisonRow {
    labkesmas_id: string;
    nama_kantor: string;
    tier: number;
    jenis_lab: JenisLab | null;
    total_wajib: number;
    total_terpenuhi: number;
    persen: number | null;
}

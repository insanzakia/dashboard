export type TierLabkesmas = 2 | 3 | 4 | 5;

/** Jenis lab khusus tier 5 (Biokes/Kesling); null untuk tier 2-4. */
export type JenisLabValue = 'biokes' | 'kesling' | null;

export interface Labkesmas {
    id: string;
    nama_kantor: string;
    tier_labkesmas: TierLabkesmas;
    jenis_lab: JenisLabValue;
    kabupaten_kota_id: string;
}

/** Baris tabel admin labkesmas, lengkap dengan konteks lokasi untuk memulihkan form edit. */
export interface LabkesmasRow {
    id: string;
    nama_kantor: string;
    tier_labkesmas: TierLabkesmas;
    jenis_lab: JenisLabValue;
    kabupaten_kota_id: string;
    kabupaten_nama: string | null;
    provinsi_id: string | null;
    provinsi_nama: string | null;
    regional_id: string | null;
    regional_nama: string | null;
}

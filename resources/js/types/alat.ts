export type KategoriAlat =
    | 'hematologi_kimia_imunologi'
    | 'mikrobiologi'
    | 'biomolekuler'
    | 'kesehatan_lingkungan'
    | 'toksikologi'
    | 'vektor_bpp'
    | 'penunjang'
    | 'kalibrasi';

/** Jenis lab khusus tier 5 (tier 2-4 tidak memakai ini). */
export type JenisLab = 'biokes' | 'kesling';

/** Satu baris standar (jumlah minimal) untuk kombinasi tier + jenis lab. */
export interface StandarRow {
    id: string;
    tier: number;
    jenis_lab: 'umum' | 'biokes' | 'kesling';
    jumlah_minimal: number;
}

/** Item katalog alat + seluruh baris standarnya (halaman admin). */
export interface AlatCatalogItem {
    id: string;
    nama_alat: string;
    kategori: KategoriAlat;
    keterangan: string | null;
    standar: StandarRow[];
}

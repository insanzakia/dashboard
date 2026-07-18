interface KemenkesEmblemProps {
    className?: string;
}

/**
 * Placeholder emblem (blur di belakang landing).
 * GANTI dengan logo Kemenkes asli: taruh file di `public/images/kemenkes-logo.png`
 * lalu ganti isi komponen ini menjadi <img src="/images/kemenkes-logo.png" ... />.
 */
export function KemenkesEmblem({ className }: KemenkesEmblemProps) {
    return (
        <svg viewBox="0 0 200 200" className={className} fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <circle cx="100" cy="100" r="94" stroke="currentColor" strokeWidth="4" />
            <circle cx="100" cy="100" r="72" stroke="currentColor" strokeWidth="2" opacity="0.6" />
            {/* Palang medis */}
            <rect x="86" y="46" width="28" height="108" rx="6" fill="currentColor" />
            <rect x="46" y="86" width="108" height="28" rx="6" fill="currentColor" />
        </svg>
    );
}

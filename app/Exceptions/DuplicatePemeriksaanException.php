<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Dilempar saat input data pemeriksaan melanggar keunikan (labkesmas + tes + bulan + tahun).
 * Ditangkap di bootstrap/app.php dan dikonversi menjadi ValidationException yang ramah Inertia.
 */
class DuplicatePemeriksaanException extends RuntimeException
{
    public function __construct(string $message = 'Data pemeriksaan untuk periode ini sudah ada.')
    {
        parent::__construct($message);
    }
}

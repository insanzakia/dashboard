<?php

namespace App\Actions\Wilayah\Regional;

use App\Models\Regional;

class DeleteRegionalAction
{
    /**
     * Menghapus Regional. Provinsi & Kabupaten/Kota di bawahnya ikut terhapus
     * via cascadeOnDelete pada FK (lihat migration).
     */
    public function execute(Regional $regional): void
    {
        $regional->delete();
    }
}

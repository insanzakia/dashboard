<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPemeriksaan extends Model
{
    use HasUuids;

    protected $table = 'jenis_pemeriksaan';

    const UPDATED_AT = null;

    protected $fillable = ['nama_tes', 'deskripsi'];

    public function dataPemeriksaan(): HasMany
    {
        return $this->hasMany(DataPemeriksaan::class, 'jenis_tes_id');
    }
}

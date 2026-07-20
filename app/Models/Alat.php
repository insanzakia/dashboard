<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alat extends Model
{
    use HasUuids;

    protected $table = 'alat';

    protected $fillable = ['nama_alat', 'kategori', 'keterangan'];

    public function standar(): HasMany
    {
        return $this->hasMany(StandarAlat::class, 'alat_id');
    }

    public function inventaris(): HasMany
    {
        return $this->hasMany(InventarisAlat::class, 'alat_id');
    }
}

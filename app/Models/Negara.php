<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Negara extends Model
{
    use HasUuids;

    protected $table = 'negara';

    /** Tabel ini hanya menyimpan created_at (lihat migration) — matikan pengelolaan updated_at. */
    const UPDATED_AT = null;

    protected $fillable = ['nama'];

    public function regional(): HasMany
    {
        return $this->hasMany(Regional::class, 'negara_id');
    }
}

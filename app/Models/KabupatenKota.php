<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KabupatenKota extends Model
{
    use HasUuids;

    protected $table = 'kabupaten_kota';

    const UPDATED_AT = null;

    protected $fillable = ['nama', 'provinsi_id'];

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }

    public function labkesmas(): HasMany
    {
        return $this->hasMany(Labkesmas::class, 'kabupaten_kota_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provinsi extends Model
{
    use HasUuids;

    protected $table = 'provinsi';

    const UPDATED_AT = null;

    protected $fillable = ['nama', 'regional_id'];

    public function regional(): BelongsTo
    {
        return $this->belongsTo(Regional::class, 'regional_id');
    }

    public function kabupatenKota(): HasMany
    {
        return $this->hasMany(KabupatenKota::class, 'provinsi_id');
    }
}

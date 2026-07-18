<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Regional extends Model
{
    use HasUuids;

    protected $table = 'regional';

    const UPDATED_AT = null;

    protected $fillable = ['nama', 'negara_id'];

    public function negara(): BelongsTo
    {
        return $this->belongsTo(Negara::class, 'negara_id');
    }

    public function provinsi(): HasMany
    {
        return $this->hasMany(Provinsi::class, 'regional_id');
    }
}

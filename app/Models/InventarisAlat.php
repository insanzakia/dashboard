<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarisAlat extends Model
{
    use HasUuids;

    protected $table = 'inventaris_alat';

    protected $fillable = ['labkesmas_id', 'alat_id', 'jumlah'];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
        ];
    }

    public function labkesmas(): BelongsTo
    {
        return $this->belongsTo(Labkesmas::class, 'labkesmas_id');
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }
}

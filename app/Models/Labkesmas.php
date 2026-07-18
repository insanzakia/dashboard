<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Labkesmas extends Model
{
    use HasUuids;

    protected $table = 'labkesmas';

    protected $fillable = ['nama_kantor', 'tier_labkesmas', 'kabupaten_kota_id'];

    protected function casts(): array
    {
        return [
            'tier_labkesmas' => 'integer',
        ];
    }

    public function kabupatenKota(): BelongsTo
    {
        return $this->belongsTo(KabupatenKota::class, 'kabupaten_kota_id');
    }

    public function dataPemeriksaan(): HasMany
    {
        return $this->hasMany(DataPemeriksaan::class, 'labkesmas_id');
    }
}

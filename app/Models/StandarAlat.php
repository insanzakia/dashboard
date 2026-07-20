<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StandarAlat extends Model
{
    use HasUuids;

    protected $table = 'standar_alat';

    protected $fillable = ['alat_id', 'tier', 'jenis_lab', 'jumlah_minimal'];

    protected function casts(): array
    {
        return [
            'tier' => 'integer',
            'jumlah_minimal' => 'integer',
        ];
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }
}

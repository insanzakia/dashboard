<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataPemeriksaan extends Model
{
    use HasUuids;

    protected $table = 'data_pemeriksaan';

    protected $fillable = ['labkesmas_id', 'jenis_tes_id', 'bulan', 'tahun', 'jumlah'];

    protected function casts(): array
    {
        return [
            'bulan' => 'integer',
            'tahun' => 'integer',
            'jumlah' => 'integer',
        ];
    }

    public function labkesmas(): BelongsTo
    {
        return $this->belongsTo(Labkesmas::class, 'labkesmas_id');
    }

    public function jenisPemeriksaan(): BelongsTo
    {
        return $this->belongsTo(JenisPemeriksaan::class, 'jenis_tes_id');
    }
}

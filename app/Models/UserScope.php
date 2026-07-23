<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu cakupan wilayah yang diberikan ke sebuah akun.
 * scope_type menentukan tabel entitas yang dirujuk scope_id.
 */
class UserScope extends Model
{
    use HasUuids;

    protected $table = 'user_scopes';

    public const TYPES = ['regional', 'provinsi', 'kabupaten_kota', 'labkesmas'];

    protected $fillable = ['user_id', 'scope_type', 'scope_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

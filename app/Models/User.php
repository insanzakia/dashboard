<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    /** Memo agar resolusi cakupan tidak dihitung ulang berkali-kali dalam satu request. */
    private ?array $memoLabkesmasIds = null;
    private ?array $memoKabupatenKotaIds = null;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'password',
        'role',
        'last_login',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',   // hashing modern (bcrypt/argon2id) otomatis saat set.
            'last_login' => 'datetime',
        ];
    }

    /** Cakupan wilayah yang diberikan ke akun ini (kosong untuk super_admin = global). */
    public function scopes(): HasMany
    {
        return $this->hasMany(UserScope::class, 'user_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Kumpulan id labkesmas yang boleh diakses akun ini untuk input data.
     * null = tanpa batas (super_admin).
     *
     * @return array<int, string>|null
     */
    public function allowedLabkesmasIds(): ?array
    {
        if ($this->isSuperAdmin()) {
            return null;
        }

        if ($this->memoLabkesmasIds !== null) {
            return $this->memoLabkesmasIds;
        }

        $byType = $this->scopes()->get(['scope_type', 'scope_id'])->groupBy('scope_type');

        $ids = collect($byType->get('labkesmas', collect())->pluck('scope_id'));

        $kabIds = $byType->get('kabupaten_kota', collect())->pluck('scope_id')->all();
        $provIds = $byType->get('provinsi', collect())->pluck('scope_id')->all();
        $regIds = $byType->get('regional', collect())->pluck('scope_id')->all();

        if ($kabIds || $provIds || $regIds) {
            $ids = $ids->merge(
                Labkesmas::query()
                    ->join('kabupaten_kota as kk', 'kk.id', '=', 'labkesmas.kabupaten_kota_id')
                    ->join('provinsi as p', 'p.id', '=', 'kk.provinsi_id')
                    ->where(function ($w) use ($kabIds, $provIds, $regIds) {
                        if ($kabIds) {
                            $w->orWhereIn('labkesmas.kabupaten_kota_id', $kabIds);
                        }
                        if ($provIds) {
                            $w->orWhereIn('kk.provinsi_id', $provIds);
                        }
                        if ($regIds) {
                            $w->orWhereIn('p.regional_id', $regIds);
                        }
                    })
                    ->pluck('labkesmas.id')
            );
        }

        return $this->memoLabkesmasIds = $ids->unique()->values()->all();
    }

    /**
     * Kumpulan id kabupaten/kota tempat akun boleh menambah/mengelola Labkesmas.
     * Hanya dari cakupan wilayah (regional/provinsi/kab-kota), bukan cakupan labkesmas.
     * null = tanpa batas (super_admin).
     *
     * @return array<int, string>|null
     */
    public function allowedKabupatenKotaIds(): ?array
    {
        if ($this->isSuperAdmin()) {
            return null;
        }

        if ($this->memoKabupatenKotaIds !== null) {
            return $this->memoKabupatenKotaIds;
        }

        $byType = $this->scopes()->get(['scope_type', 'scope_id'])->groupBy('scope_type');

        $kabIds = collect($byType->get('kabupaten_kota', collect())->pluck('scope_id'));

        $provIds = $byType->get('provinsi', collect())->pluck('scope_id')->all();
        $regIds = $byType->get('regional', collect())->pluck('scope_id')->all();

        if ($provIds || $regIds) {
            $kabIds = $kabIds->merge(
                KabupatenKota::query()
                    ->join('provinsi as p', 'p.id', '=', 'kabupaten_kota.provinsi_id')
                    ->where(function ($w) use ($provIds, $regIds) {
                        if ($provIds) {
                            $w->orWhereIn('kabupaten_kota.provinsi_id', $provIds);
                        }
                        if ($regIds) {
                            $w->orWhereIn('p.regional_id', $regIds);
                        }
                    })
                    ->pluck('kabupaten_kota.id')
            );
        }

        return $this->memoKabupatenKotaIds = $kabIds->unique()->values()->all();
    }

    public function canAccessLabkesmas(?string $labkesmasId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $labkesmasId !== null && in_array($labkesmasId, $this->allowedLabkesmasIds() ?? [], true);
    }

    public function canCreateLabkesmasInKab(?string $kabupatenKotaId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $kabupatenKotaId !== null && in_array($kabupatenKotaId, $this->allowedKabupatenKotaIds() ?? [], true);
    }
}

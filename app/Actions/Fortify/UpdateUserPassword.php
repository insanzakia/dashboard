<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

/**
 * Ganti password pengguna terautentikasi (dipakai Fortify pada PUT /user/password).
 * Memvalidasi password lama + aturan password baru, lalu menyimpan hash-nya.
 */
class UpdateUserPassword implements UpdatesUserPasswords
{
    /**
     * @param  \App\Models\User  $user
     * @param  array<string, string>  $input
     */
    public function update($user, array $input): void
    {
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
        ], [
            'current_password.current_password' => 'Password saat ini tidak cocok.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ])->validateWithBag('updatePassword');

        // Cast 'hashed' pada model User tidak akan mem-hash ulang nilai yang sudah ter-hash.
        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}

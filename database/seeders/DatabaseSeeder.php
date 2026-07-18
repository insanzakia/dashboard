<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun admin default untuk mengakses panel /admin.
        User::factory()->create([
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
        ]);

        $this->call(LabkesmasSeeder::class);
    }
}

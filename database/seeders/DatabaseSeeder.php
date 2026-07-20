<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun admin default untuk mengakses panel /admin.
        // Pakai create() langsung (bukan factory) agar tidak bergantung pada Faker (dev-only).
        // Kolom password otomatis di-hash via cast 'hashed' pada model User.
        User::create([
            'username' => 'admin',
            'password' => 'password123',
            'role' => 'super_admin',
        ]);

        $this->call(LabkesmasSeeder::class);
        $this->call(AlatStandarSeeder::class);
        $this->call(InventarisAlatSeeder::class);
    }
}

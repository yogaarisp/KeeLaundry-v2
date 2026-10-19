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
        // Buat akun per role untuk demo & pengujian
        $users = [
            ['name' => 'Kee Admin', 'email' => 'admin@keelaundry.id', 'role' => 'ADMIN'],
            ['name' => 'Siti Rahayu (Kasir)', 'email' => 'kasir@keelaundry.id', 'role' => 'KASIR'],
            ['name' => 'Budi Santoso (Produksi)', 'email' => 'produksi@keelaundry.id', 'role' => 'PRODUKSI'],
            ['name' => 'Rina Handayani (Kurir)', 'email' => 'kurir@keelaundry.id', 'role' => 'KURIR'],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name'     => $u['name'],
                    'role'     => $u['role'],
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                ]
            );
        }

        // Seed seluruh data operasional Kee Laundry - Pusat
        $this->call(KeeLaundrySeeder::class);
    }
}

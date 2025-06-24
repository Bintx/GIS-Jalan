<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // Panggil seeder pengguna
        $this->call([
            RolesAndAdminSeeder::class,
            // Jika Anda memiliki seeder lain yang ingin dijalankan di masa depan, tambahkan di sini:
            // RegionalSeeder::class,
            // JalanSeeder::class,
        ]);
    }
}

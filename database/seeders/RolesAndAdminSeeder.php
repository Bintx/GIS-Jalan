<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Buat user Admin jika belum ada
        User::firstOrCreate(
            ['email' => 'admin@example.com'], // Kriteria pencarian
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('admin123'), // Ganti dengan password yang kuat
                'role' => 'admin',
                'email_verified_at' => now(), // Verifikasi langsung
            ]
        );

        // Buat user Pejabat Desa jika belum ada
        User::firstOrCreate(
            ['email' => 'pejabat@example.com'], // Kriteria pencarian
            [
                'name' => 'Pejabat Desa',
                'password' => Hash::make('admin123'), // Ganti dengan password yang kuat
                'role' => 'pejabat_desa',
                'email_verified_at' => now(), // Verifikasi langsung
            ]
        );

        // Anda bisa menambahkan user lain di sini jika diperlukan
        // User::firstOrCreate(
        //     ['email' => 'user@example.com'],
        //     [
        //         'name' => 'Contoh User',
        //         'password' => Hash::make('password'),
        //         'role' => 'pejabat_desa',
        //         'email_verified_at' => now(),
        //     ]
        // );
    }
}

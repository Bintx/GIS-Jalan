<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Regional; // Pastikan Model Regional diimpor

class RegionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Data Dusun
        $dusunNames = ['Candirejo', 'Gunungrejo', 'Jelobo', 'Ngrodon', 'Wantilan'];
        foreach ($dusunNames as $name) {
            Regional::firstOrCreate(
                ['nama_regional' => $name, 'tipe_regional' => 'Dusun'],
                ['nama_regional' => $name, 'tipe_regional' => 'Dusun']
            );
        }

        // Data RW
        for ($i = 1; $i <= 9; $i++) {
            $rwName = '' . sprintf('%02d', $i); // Format RW 01, 02, dst. (2 digit)
            Regional::firstOrCreate(
                ['nama_regional' => $rwName, 'tipe_regional' => 'RW'],
                ['nama_regional' => $rwName, 'tipe_regional' => 'RW']
            );
        }

        // Data RT
        for ($i = 1; $i <= 24; $i++) {
            $rtName = '' . sprintf('%02d', $i); // Format RT 01, 02, dst. (2 digit)
            Regional::firstOrCreate(
                ['nama_regional' => $rtName, 'tipe_regional' => 'RT'],
                ['nama_regional' => $rtName, 'tipe_regional' => 'RT']
            );
        }

        $this->command->info('Data Regional (RT, RW, Dusun) berhasil di-seed!');
    }
}

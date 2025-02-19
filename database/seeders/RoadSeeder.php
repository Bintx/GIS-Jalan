<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Road;
use App\Models\RoadCondition;

class RoadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Road::factory(10)->create()->each(function ($road) {
            RoadCondition::create([
                'road_id' => $road->id,
                'condition' => ['Baik', 'Sedang', 'Rusak'][rand(0, 2)],
                'priority_level' => rand(1, 5),
            ]);
        });
    }
}

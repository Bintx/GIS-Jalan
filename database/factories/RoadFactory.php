<?php

namespace Database\Factories;

use App\Models\Road;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoadFactory extends Factory
{
    protected $model = Road::class;

    public function definition()
    {
        return [
            'name' => $this->faker->streetName,
            'description' => $this->faker->sentence,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
        ];
    }
}

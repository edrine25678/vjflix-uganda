<?php

namespace Database\Factories;

use App\Models\Vj;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VjFactory extends Factory
{
    protected $model = Vj::class;

    public function definition()
    {
        $stageName = 'VJ '.$this->faker->firstName;

        return [
            'name' => $this->faker->name,
            'slug' => Str::slug($stageName.'-'.Str::random(4)),
            'stage_name' => $stageName,
            'biography' => $this->faker->paragraph,
            'specialization' => 'Action & Sci-Fi Translations',
            'is_verified' => true,
            'is_active' => true,
            'views_count' => $this->faker->numberBetween(1000, 50000),
            'rating' => 4.8,
        ];
    }
}

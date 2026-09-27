<?php

namespace Database\Factories;

use App\Models\Season;
use App\Models\Series;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeasonFactory extends Factory
{
    protected $model = Season::class;

    public function definition(): array
    {
        return [
            'series_id' => Series::factory(),
            'season_number' => 1,
            'title' => 'Season 1',
            'overview' => $this->faker->paragraph(2),
            'poster' => null,
            'release_year' => $this->faker->numberBetween(2020, 2024),
        ];
    }
}

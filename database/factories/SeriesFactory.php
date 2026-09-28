<?php

namespace Database\Factories;

use App\Models\Series;
use App\Models\Vj;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SeriesFactory extends Factory
{
    protected $model = Series::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(100, 999),
            'synopsis' => $this->faker->paragraph(2),
            'description' => $this->faker->paragraphs(3, true),
            'poster' => 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?auto=format&fit=crop&w=600&q=80',
            'backdrop' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&w=1200&q=80',
            'vj_id' => Vj::factory(),
            'first_air_year' => $this->faker->numberBetween(2018, 2024),
            'status' => 'published',
            'featured' => false,
            'trending' => false,
            'views' => $this->faker->numberBetween(100, 50000),
            'average_rating' => $this->faker->randomFloat(2, 4.0, 5.0),
            'published_at' => now(),
        ];
    }
}

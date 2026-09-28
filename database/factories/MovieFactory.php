<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\Vj;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MovieFactory extends Factory
{
    protected $model = Movie::class;

    public function definition()
    {
        $title = $this->faker->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title.'-'.Str::random(5)),
            'original_title' => $title,
            'description' => $this->faker->paragraph,
            'synopsis' => $this->faker->paragraphs(2, true),
            'duration' => $this->faker->numberBetween(80, 160),
            'release_year' => $this->faker->numberBetween(2015, 2026),
            'original_language' => 'en',
            'translated_language' => 'Luganda',
            'vj_id' => Vj::factory(),
            'country_of_origin' => 'United States',
            'age_rating' => 'PG-13',
            'status' => 'published',
            'featured' => false,
            'trending' => false,
            'views' => $this->faker->numberBetween(100, 25000),
            'average_rating' => 4.5,
            'ratings_count' => 10,
            'published_at' => now(),
        ];
    }
}

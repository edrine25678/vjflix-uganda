<?php

namespace Database\Factories;

use App\Models\Episode;
use App\Models\Season;
use App\Models\Vj;
use Illuminate\Database\Eloquent\Factories\Factory;

class EpisodeFactory extends Factory
{
    protected $model = Episode::class;

    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'episode_number' => $this->faker->numberBetween(1, 12),
            'title' => 'Episode ' . $this->faker->words(3, true),
            'overview' => $this->faker->paragraph(2),
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
            'video_path' => null,
            'duration' => $this->faker->numberBetween(35, 60),
            'thumbnail' => 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?auto=format&fit=crop&w=600&q=80',
            'vj_id' => Vj::factory(),
            'views' => $this->faker->numberBetween(50, 25000),
            'is_free' => false,
        ];
    }
}

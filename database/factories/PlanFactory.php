<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        $intervalUnit = $this->faker->randomElement(['day', 'week', 'month', 'year']);
        $intervalCount = $this->faker->numberBetween(1, 3);

        $durationDays = match ($intervalUnit) {
            'day' => $intervalCount,
            'week' => $intervalCount * 7,
            'month' => $intervalCount * 30,
            'year' => $intervalCount * 365,
        };

        return [
            'name' => ucfirst($this->faker->words(2, true)).' Plan',
            'slug' => $this->faker->unique()->slug(2),
            'description' => $this->faker->sentence(),
            'price_ugx' => $this->faker->numberBetween(5, 100) * 1000,
            'interval_unit' => $intervalUnit,
            'interval_count' => $intervalCount,
            'duration_days' => $durationDays,
            'features' => [
                $this->faker->sentence(4),
                $this->faker->sentence(4),
            ],
            'badge' => null,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}

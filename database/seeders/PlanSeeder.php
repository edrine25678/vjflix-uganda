<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Basic access with limited content',
                'price_ugx' => 0,
                'interval_unit' => 'month',
                'interval_count' => 1,
                'duration_days' => 30,
                'features' => [
                    'Access to free movies',
                    'Standard quality (480p)',
                    '1 device',
                    'Limited catalog',
                ],
                'badge' => null,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Great for casual viewers',
                'price_ugx' => 15000,
                'interval_unit' => 'month',
                'interval_count' => 1,
                'duration_days' => 30,
                'features' => [
                    'Access to all movies',
                    'HD quality (720p)',
                    '2 devices',
                    'Full catalog',
                    'No ads',
                ],
                'badge' => 'Popular',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Best experience for serious viewers',
                'price_ugx' => 30000,
                'interval_unit' => 'month',
                'interval_count' => 1,
                'duration_days' => 30,
                'features' => [
                    'Access to all movies and series',
                    'Full HD quality (1080p)',
                    '4 devices',
                    'Full catalog',
                    'No ads',
                    'Download for offline viewing',
                    'Priority support',
                ],
                'badge' => 'Best Value',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Annual Premium',
                'slug' => 'annual-premium',
                'description' => 'Save 20% with annual subscription',
                'price_ugx' => 288000,
                'interval_unit' => 'year',
                'interval_count' => 1,
                'duration_days' => 365,
                'features' => [
                    'All Premium features',
                    'Full HD quality (1080p)',
                    '4 devices',
                    'Full catalog',
                    'No ads',
                    'Download for offline viewing',
                    'Priority support',
                    '20% savings',
                ],
                'badge' => 'Best Value',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        $this->command->info('Subscription plans seeded successfully.');
    }
}

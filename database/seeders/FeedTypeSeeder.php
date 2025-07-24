<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeedType;

class FeedTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific feed types for aquaculture
        $feedTypes = [
            [
                'feed_type' => 'Starter Feed',
                'brand' => 'AquaPro',
            ],
            [
                'feed_type' => 'Grower Feed',
                'brand' => 'AquaPro',
            ],
            [
                'feed_type' => 'Finisher Feed',
                'brand' => 'AquaPro',
            ],
            [
                'feed_type' => 'Premium Starter',
                'brand' => 'FishMax',
            ],
            [
                'feed_type' => 'Premium Grower',
                'brand' => 'FishMax',
            ],
            [
                'feed_type' => 'Premium Finisher',
                'brand' => 'FishMax',
            ],
            [
                'feed_type' => 'Tilapia Starter',
                'brand' => 'TilapiaFeed',
            ],
            [
                'feed_type' => 'Tilapia Grower',
                'brand' => 'TilapiaFeed',
            ],
            [
                'feed_type' => 'Tilapia Finisher',
                'brand' => 'TilapiaFeed',
            ],
            [
                'feed_type' => 'High Protein Feed',
                'brand' => 'NutriFish',
            ],
            [
                'feed_type' => 'Economy Feed',
                'brand' => 'BudgetFeed',
            ],
            [
                'feed_type' => 'Organic Feed',
                'brand' => 'EcoFish',
            ],
            [
                'feed_type' => 'Floating Feed',
                'brand' => 'FloatPro',
            ],
            [
                'feed_type' => 'Sinking Feed',
                'brand' => 'SinkPro',
            ],
            [
                'feed_type' => 'Medicated Feed',
                'brand' => 'HealthFish',
            ],
        ];

        foreach ($feedTypes as $feedTypeData) {
            FeedType::firstOrCreate([
                'feed_type' => $feedTypeData['feed_type'],
                'brand' => $feedTypeData['brand'],
            ], $feedTypeData);
        }

        // Create additional feed types using factory
        FeedType::factory(10)->create();

        $this->command->info('Feed Types seeded successfully!');
        $this->command->info('Created ' . count($feedTypes) . ' specific feed types and 10 random feed types.');
    }
}

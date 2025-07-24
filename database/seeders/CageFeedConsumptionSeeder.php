<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cage;
use App\Models\CageFeedConsumption;

class CageFeedConsumptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing cages or create some if none exist
        $cages = Cage::all();
        
        if ($cages->isEmpty()) {
            // Create some cages if none exist
            $cages = Cage::factory(3)->create();
        }

        // Method 1: Basic factory usage - create 50 random feed consumptions
        for ($i = 0; $i < 50; $i++) {
            CageFeedConsumption::firstOrCreate([
                'cage_id' => $cages->random()->id,
                'day_number' => rand(200, 500), // Use higher day numbers to avoid conflicts
            ], [
                'feed_amount' => fake()->randomFloat(2, 0.5, 10.0),
                'consumption_date' => fake()->dateTimeBetween('-1 year', 'now'),
                'notes' => fake()->optional()->sentence(),
            ]);
        }

        // Method 2: Create feed consumptions for specific cages
        foreach ($cages as $cage) {
            // Create 30 days of feed consumption for each cage
            for ($day = 1; $day <= 30; $day++) {
                CageFeedConsumption::firstOrCreate([
                    'cage_id' => $cage->id,
                    'day_number' => $day,
                ], [
                    'feed_amount' => fake()->randomFloat(2, 0.5, 10.0),
                    'consumption_date' => now()->subDays(30 - $day),
                ]);
            }
        }

        // Method 3: Using factory with custom states
        for ($i = 0; $i < 20; $i++) {
            CageFeedConsumption::firstOrCreate([
                'cage_id' => $cages->random()->id,
                'day_number' => rand(600, 800), // Use higher day numbers
            ], [
                'feed_amount' => 3.5, // Fixed amount
                'consumption_date' => fake()->dateTimeBetween('-1 year', 'now'),
                'notes' => 'Regular feeding schedule',
            ]);
        }

        // Method 4: Create feed consumptions with specific date ranges
        for ($i = 0; $i < 15; $i++) {
            CageFeedConsumption::firstOrCreate([
                'cage_id' => $cages->random()->id,
                'day_number' => rand(900, 1000), // Use higher day numbers
            ], [
                'feed_amount' => fake()->randomFloat(2, 0.5, 10.0),
                'consumption_date' => now()->subDays(rand(1, 60)),
            ]);
        }

        // Method 5: Using factory with relationships and callbacks
        Cage::factory(2)->create()->each(function ($cage) {
            // Create feed consumptions for each new cage
            for ($day = 1; $day <= 25; $day++) {
                CageFeedConsumption::firstOrCreate([
                    'cage_id' => $cage->id,
                    'day_number' => $day,
                ], [
                    'feed_amount' => fake()->randomFloat(2, 0.5, 10.0),
                    'consumption_date' => now()->subDays(25 - $day),
                ]);
            }
        });

        // Method 6: Create feed consumptions with realistic patterns
        $cages->each(function ($cage) {
            // Create 90 days of feed consumption with realistic patterns
            for ($day = 1; $day <= 90; $day++) {
                // Feed amount increases over time (fish grow)
                $baseAmount = 2.0 + ($day * 0.02); // Start at 2kg, increase by 0.02kg per day
                $variation = rand(-10, 10) / 100; // Add some random variation
                $feedAmount = max(1.0, $baseAmount + $variation); // Minimum 1kg

                CageFeedConsumption::firstOrCreate([
                    'cage_id' => $cage->id,
                    'day_number' => $day,
                ], [
                    'feed_amount' => round($feedAmount, 2),
                    'consumption_date' => now()->subDays(90 - $day),
                    'notes' => $day % 7 === 0 ? 'Weekly feeding review' : null,
                ]);
            }
        });

        // Method 7: Create feed consumptions with different feeding strategies
        $feedingStrategies = [
            'conservative' => 0.015, // 0.015kg increase per day
            'moderate' => 0.025,     // 0.025kg increase per day
            'aggressive' => 0.035,   // 0.035kg increase per day
        ];

        $cages->take(5)->each(function ($cage, $index) use ($feedingStrategies) {
            $strategies = array_values($feedingStrategies);
            $strategy = $strategies[$index % count($strategies)];
            
            for ($day = 1; $day <= 60; $day++) {
                $baseAmount = 1.5 + ($day * $strategy);
                $variation = rand(-15, 15) / 100;
                $feedAmount = max(0.5, $baseAmount + $variation);

                CageFeedConsumption::firstOrCreate([
                    'cage_id' => $cage->id,
                    'day_number' => $day + 100, // Different day range
                ], [
                    'feed_amount' => round($feedAmount, 2),
                    'consumption_date' => now()->subDays(60 - $day),
                    'notes' => "Feeding strategy: " . array_keys($feedingStrategies)[$index % count($feedingStrategies)],
                ]);
            }
        });
    }
}

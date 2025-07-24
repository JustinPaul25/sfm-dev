<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sample;
use App\Models\Sampling;
use App\Models\Investor;

class SampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get samplings and investors for creating samples
        $samplings = Sampling::all();
        $investors = Investor::all();

        if ($samplings->isEmpty() || $investors->isEmpty()) {
            $this->command->warn('Samplings or Investors not found. Please run SamplingSeeder first.');
            return;
        }

        // Create samples for each sampling
        foreach ($samplings as $sampling) {
            // Create 30 samples per sampling (standard practice)
            for ($sampleNo = 1; $sampleNo <= 30; $sampleNo++) {
                // Generate realistic weight data based on sampling date
                $daysSinceStart = now()->diffInDays($sampling->sampling_date);
                
                // Base weight increases over time (fish grow)
                $baseWeight = 50 + ($daysSinceStart * 2); // Start at 50g, grow 2g per day
                $variation = rand(-20, 20); // Add some random variation
                $weight = max(30, $baseWeight + $variation); // Minimum 30g

                Sample::firstOrCreate([
                    'investor_id' => $sampling->investor_id,
                    'sampling_id' => $sampling->id,
                    'sample_no' => $sampleNo,
                ], [
                    'weight' => $weight,
                ]);
            }
        }

        // Create additional samples using factory
        Sample::factory(100)->create([
            'investor_id' => $investors->random()->id,
            'sampling_id' => $samplings->random()->id,
        ]);

        // Create samples with specific weight ranges for testing
        $weightRanges = [
            'small' => [30, 80],    // Small fish
            'medium' => [80, 150],  // Medium fish
            'large' => [150, 300],  // Large fish
            'extra_large' => [300, 500], // Extra large fish
        ];

        foreach ($weightRanges as $range => $weights) {
            for ($i = 0; $i < 25; $i++) {
                $randomSampling = $samplings->random();
                $weight = rand($weights[0], $weights[1]);
                
                Sample::firstOrCreate([
                    'investor_id' => $randomSampling->investor_id,
                    'sampling_id' => $randomSampling->id,
                    'sample_no' => rand(31, 50), // Different sample numbers
                ], [
                    'weight' => $weight,
                ]);
            }
        }

        // Create samples with specific patterns for analysis
        $samplings->take(10)->each(function ($sampling) {
            // Create samples with normal distribution pattern
            for ($i = 1; $i <= 50; $i++) {
                // Normal distribution around 120g with standard deviation of 30g
                $mean = 120;
                $stdDev = 30;
                $weight = max(30, round($mean + ($stdDev * (rand(-100, 100) / 100))));
                
                Sample::firstOrCreate([
                    'investor_id' => $sampling->investor_id,
                    'sampling_id' => $sampling->id,
                    'sample_no' => $i,
                ], [
                    'weight' => $weight,
                ]);
            }
        });

        $this->command->info('Samples seeded successfully!');
        $this->command->info('Created samples for ' . $samplings->count() . ' samplings (30 each), 100 factory samples, and additional test samples.');
    }
}

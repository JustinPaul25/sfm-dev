<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sampling;
use App\Models\Investor;
use App\Models\Cage;

class SamplingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get investors and cages for creating samplings
        $investors = Investor::all();
        $cages = Cage::all();

        if ($investors->isEmpty() || $cages->isEmpty()) {
            $this->command->warn('Investors or Cages not found. Please run InvestorSeeder and CageSeeder first.');
            return;
        }

        // Create specific samplings for testing
        $samplings = [
            [
                'investor_id' => $investors->where('name', 'John Smith')->first()->id,
                'cage_no' => $cages->where('investor_id', $investors->where('name', 'John Smith')->first()->id)->first()->id,
                'date_sampling' => now()->subDays(30),
                'doc' => 'DOC-001',
            ],
            [
                'investor_id' => $investors->where('name', 'Maria Garcia')->first()->id,
                'cage_no' => $cages->where('investor_id', $investors->where('name', 'Maria Garcia')->first()->id)->first()->id,
                'date_sampling' => now()->subDays(25),
                'doc' => 'DOC-002',
            ],
            [
                'investor_id' => $investors->where('name', 'Robert Johnson')->first()->id,
                'cage_no' => $cages->where('investor_id', $investors->where('name', 'Robert Johnson')->first()->id)->first()->id,
                'date_sampling' => now()->subDays(20),
                'doc' => 'DOC-003',
            ],
            [
                'investor_id' => $investors->where('name', 'Ana Santos')->first()->id,
                'cage_no' => $cages->where('investor_id', $investors->where('name', 'Ana Santos')->first()->id)->first()->id,
                'date_sampling' => now()->subDays(15),
                'doc' => 'DOC-004',
            ],
            [
                'investor_id' => $investors->where('name', 'Carlos Rodriguez')->first()->id,
                'cage_no' => $cages->where('investor_id', $investors->where('name', 'Carlos Rodriguez')->first()->id)->first()->id,
                'date_sampling' => now()->subDays(10),
                'doc' => 'DOC-005',
            ],
        ];

        foreach ($samplings as $samplingData) {
            Sampling::firstOrCreate([
                'investor_id' => $samplingData['investor_id'],
                'date_sampling' => $samplingData['date_sampling'],
            ], $samplingData);
        }

        // Create additional samplings without factory (for production)
        for ($i = 0; $i < 20; $i++) {
            $randomInvestor = $investors->random();
            $randomCage = $cages->where('investor_id', $randomInvestor->id)->first();
            
            if ($randomCage) {
                Sampling::firstOrCreate([
                    'investor_id' => $randomInvestor->id,
                    'date_sampling' => now()->subDays(rand(1, 60)),
                ], [
                    'cage_no' => $randomCage->id,
                    'doc' => 'DOC-FACTORY-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                ]);
            }
        }

        // Create historical samplings for the last 3 months
        for ($i = 1; $i <= 90; $i++) {
            $randomInvestor = $investors->random();
            $randomCage = $cages->where('investor_id', $randomInvestor->id)->first();
            
            if ($randomCage) {
                Sampling::firstOrCreate([
                    'investor_id' => $randomInvestor->id,
                    'date_sampling' => now()->subDays($i),
                ], [
                    'cage_no' => $randomCage->id,
                    'doc' => 'DOC-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                ]);
            }
        }

        $this->command->info('Samplings seeded successfully!');
        $this->command->info('Created ' . count($samplings) . ' specific samplings, 20 factory samplings, and 90 historical samplings.');
    }
}

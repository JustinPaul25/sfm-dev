<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Investor;

class InvestorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific investors for testing
        $investors = [
            [
                'name' => 'John Smith',
                'phone' => '09123456789',
                'address' => '123 Main Street, Davao City, Philippines',
            ],
            [
                'name' => 'Maria Garcia',
                'phone' => '09234567890',
                'address' => '456 Oak Avenue, Cebu City, Philippines',
            ],
            [
                'name' => 'Robert Johnson',
                'phone' => '09345678901',
                'address' => '789 Pine Road, Manila, Philippines',
            ],
            [
                'name' => 'Ana Santos',
                'phone' => '09456789012',
                'address' => '321 Elm Street, Iloilo City, Philippines',
            ],
            [
                'name' => 'Carlos Rodriguez',
                'phone' => '09567890123',
                'address' => '654 Maple Drive, Bacolod City, Philippines',
            ],
            [
                'name' => 'Luz Cruz',
                'phone' => '09678901234',
                'address' => '987 Cedar Lane, Zamboanga City, Philippines',
            ],
            [
                'name' => 'Miguel Torres',
                'phone' => '09789012345',
                'address' => '147 Birch Court, Tacloban City, Philippines',
            ],
            [
                'name' => 'Isabel Reyes',
                'phone' => '09890123456',
                'address' => '258 Spruce Way, Baguio City, Philippines',
            ],
        ];

        foreach ($investors as $investorData) {
            Investor::firstOrCreate([
                'name' => $investorData['name'],
            ], $investorData);
        }

        // Create additional investors using factory
        Investor::factory(12)->create();

        $this->command->info('Investors seeded successfully!');
        $this->command->info('Created ' . count($investors) . ' specific investors and 12 random investors.');
    }
}

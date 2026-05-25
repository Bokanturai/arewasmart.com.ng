<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceField;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get the Loan service (only if not exists)
        $service = Service::firstOrCreate(
            ['name' => 'Loan'],
            [
                'description' => '0% Interest Loan Facility for Arewa Smart Agents and Users',
                'is_active' => 1
            ]
        );

        // Define loan products - will only create if they don't exist
        $loanProducts = [
            ['field_code' => 'LOAN-SOLAR', 'field_name' => 'Solar Loan', 'base_price' => 100000],
            ['field_code' => 'LOAN-SCHOOL', 'field_name' => 'School Fees Loan', 'base_price' => 50000],
            ['field_code' => 'LOAN-BUSINESS', 'field_name' => 'Business Loan', 'base_price' => 200000],
            ['field_code' => 'LOAN-EMERGENCY', 'field_name' => 'Emergency Loan', 'base_price' => 30000],
            ['field_code' => 'LOAN-ASSET', 'field_name' => 'Asset Finance Loan', 'base_price' => 150000],
        ];

        // Create each loan product only if it doesn't exist
        foreach ($loanProducts as $product) {
            ServiceField::firstOrCreate(
                [
                    'service_id' => $service->id,
                    'field_code' => $product['field_code'],
                ],
                [
                    'field_name' => $product['field_name'],
                    'base_price' => $product['base_price'],
                    'is_active' => 1,
                ]
            );
        }
    }
}
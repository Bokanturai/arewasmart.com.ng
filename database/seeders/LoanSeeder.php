<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceField;
use Illuminate\Database\Seeder;

/**
 * LoanSeeder
 *
 * Creates the Loan service and all loan type fields.
 * Safe to run multiple times — checks if records already exist.
 *
 * Run with:
 *   php artisan db:seed --class=LoanSeeder
 */
class LoanSeeder extends Seeder
{
    /**
     * Loan types with default qualifying amounts (in Naira).
     *
     * Structure:
     *   'field_name'  => Human-readable loan type name
     *   'field_code'  => Short unique code (used as DB key)
     *   'base_price'  => Default qualifying amount
     *   'description' => Short description shown on the loan type card
     */
    protected array $loanTypes = [
        [
            'field_name'  => 'Solar Loan',
            'field_code'  => 'LOAN-SOLAR',
            'base_price'  => 100_000,
            'description' => 'Finance solar energy systems for your home or business.',
        ],
        [
            'field_name'  => 'School Fees Loan',
            'field_code'  => 'LOAN-SCHOOL',
            'base_price'  => 50_000,
            'description' => 'Cover tuition and educational expenses with ease.',
        ],
        [
            'field_name'  => 'Business Loan',
            'field_code'  => 'LOAN-BUSINESS',
            'base_price'  => 200_000,
            'description' => 'Grow your business with flexible 0% interest financing.',
        ],
        [
            'field_name'  => 'Emergency Loan',
            'field_code'  => 'LOAN-EMERGENCY',
            'base_price'  => 30_000,
            'description' => 'Quick access to funds for urgent personal needs.',
        ],
        [
            'field_name'  => 'Asset Finance Loan',
            'field_code'  => 'LOAN-ASSET',
            'base_price'  => 150_000,
            'description' => 'Purchase equipment, devices, or business assets.',
        ],
    ];

    public function run(): void
    {
        // 1. Fetch or create the Loan service
        $service = Service::where('name', 'Loan')->first();
        if (!$service) {
            $service = Service::create([
                'name'        => 'Loan',
                'description' => '0% Interest Loan Facility for Arewa Smart Agents and Users',
                'is_active'   => true,
            ]);
        } else {
            // Ensure the service is active even if it already existed but was disabled
            if (!$service->is_active) {
                $service->update(['is_active' => true]);
            }
        }

        // 2. Create each loan type (ServiceField) if it doesn't already exist
        foreach ($this->loanTypes as $loanType) {
            $field = ServiceField::where('service_id', $service->id)
                ->where('field_code', $loanType['field_code'])
                ->first();

            if (!$field) {
                $field = ServiceField::create([
                    'service_id'  => $service->id,
                    'field_code'  => $loanType['field_code'],
                    'field_name'  => $loanType['field_name'],
                    'base_price'  => $loanType['base_price'],
                    'description' => $loanType['description'],
                    'is_active'   => true,
                ]);
            } else {
                // Ensure the field is active
                if (!$field->is_active) {
                    $field->update(['is_active' => true]);
                }
            }
        }

        $this->command->info('✅ Loan service seeded successfully with ' . count($this->loanTypes) . ' loan types.');
    }
}

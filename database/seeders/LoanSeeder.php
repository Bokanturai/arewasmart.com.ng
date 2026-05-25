<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceField;
use App\Models\ServicePrice;
use Illuminate\Database\Seeder;

/**
 * LoanSeeder
 *
 * Creates the Loan service, all loan type fields, and per-role qualifying amounts.
 * Safe to run multiple times — uses firstOrCreate throughout.
 *
 * Roles:  personal | agent | business | partner | staff | checker | super_admin
 *
 * The "price" for each ServicePrice row = the MAXIMUM LOAN AMOUNT the user
 * of that role is allowed to apply for under that loan type.
 * No wallet is charged; this is purely an eligibility/qualifying limit.
 *
 * Run with:
 *   php artisan db:seed --class=LoanSeeder
 */
class LoanSeeder extends Seeder
{
    /**
     * Loan types with default qualifying amounts per role (in Naira).
     *
     * Structure:
     *   'field_name'  => Human-readable loan type name
     *   'field_code'  => Short unique code (used as DB key)
     *   'base_price'  => Default qualifying amount if no role-specific price exists
     *   'description' => Short description shown on the loan type card
     *   'prices'      => [ role => qualifying_amount_in_naira ]
     */
    protected array $loanTypes = [
        [
            'field_name'  => 'Solar Loan',
            'field_code'  => 'LOAN-SOLAR',
            'base_price'  => 100_000,
            'description' => 'Finance solar energy systems for your home or business.',
            'prices'      => [
                'personal'    => 100_000,
                'agent'       => 200_000,
                'business'    => 500_000,
                'partner'     => 500_000,
                'staff'       => 1_000_000,
                'checker'     => 1_000_000,
                'super_admin' => 2_000_000,
            ],
        ],
        [
            'field_name'  => 'School Fees Loan',
            'field_code'  => 'LOAN-SCHOOL',
            'base_price'  => 50_000,
            'description' => 'Cover tuition and educational expenses with ease.',
            'prices'      => [
                'personal'    => 50_000,
                'agent'       => 100_000,
                'business'    => 300_000,
                'partner'     => 300_000,
                'staff'       => 500_000,
                'checker'     => 500_000,
                'super_admin' => 1_000_000,
            ],
        ],
        [
            'field_name'  => 'Business Loan',
            'field_code'  => 'LOAN-BUSINESS',
            'base_price'  => 200_000,
            'description' => 'Grow your business with flexible 0% interest financing.',
            'prices'      => [
                'personal'    => 100_000,
                'agent'       => 300_000,
                'business'    => 1_000_000,
                'partner'     => 1_000_000,
                'staff'       => 1_500_000,
                'checker'     => 1_500_000,
                'super_admin' => 2_000_000,
            ],
        ],
        [
            'field_name'  => 'Emergency Loan',
            'field_code'  => 'LOAN-EMERGENCY',
            'base_price'  => 30_000,
            'description' => 'Quick access to funds for urgent personal needs.',
            'prices'      => [
                'personal'    => 30_000,
                'agent'       => 80_000,
                'business'    => 150_000,
                'partner'     => 200_000,
                'staff'       => 300_000,
                'checker'     => 300_000,
                'super_admin' => 500_000,
            ],
        ],
        [
            'field_name'  => 'Asset Finance Loan',
            'field_code'  => 'LOAN-ASSET',
            'base_price'  => 150_000,
            'description' => 'Purchase equipment, devices, or business assets.',
            'prices'      => [
                'personal'    => 80_000,
                'agent'       => 200_000,
                'business'    => 500_000,
                'partner'     => 750_000,
                'staff'       => 1_000_000,
                'checker'     => 1_000_000,
                'super_admin' => 2_000_000,
            ],
        ],
    ];

    public function run(): void
    {
        // 1. Create or fetch the Loan service
        $service = Service::firstOrCreate(
            ['name' => 'Loan'],
            [
                'description' => '0% Interest Loan Facility for Arewa Smart Agents and Users',
                'is_active'   => true,
            ]
        );

        // Ensure the service is active even if it already existed but was disabled
        if (!$service->is_active) {
            $service->update(['is_active' => true]);
        }

        // 2. Create each loan type (ServiceField) and its per-role prices (ServicePrice)
        foreach ($this->loanTypes as $loanType) {
            $field = ServiceField::firstOrCreate(
                [
                    'service_id' => $service->id,
                    'field_code' => $loanType['field_code'],
                ],
                [
                    'field_name'  => $loanType['field_name'],
                    'base_price'  => $loanType['base_price'],
                    'description' => $loanType['description'],
                    'is_active'   => true,
                ]
            );

            // Ensure the field is active
            if (!$field->is_active) {
                $field->update(['is_active' => true]);
            }

            // 3. Create per-role qualifying amounts
            foreach ($loanType['prices'] as $userType => $qualifyingAmount) {
                ServicePrice::firstOrCreate(
                    [
                        'service_id'       => $service->id,
                        'service_fields_id' => $field->id,
                        'user_type'        => $userType,
                    ],
                    [
                        'price' => $qualifyingAmount,
                    ]
                );
            }
        }

        $this->command->info('✅ Loan service seeded successfully with ' . count($this->loanTypes) . ' loan types.');
    }
}

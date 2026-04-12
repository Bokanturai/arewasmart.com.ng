<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NecoNabtedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $service = \App\Models\Service::firstOrCreate(
            ['name' => 'NECO & NABTED'],
            ['description' => 'NECO and NABTED Pin Purchase Service', 'is_active' => 1]
        );

        $neco = \App\Models\ServiceField::firstOrCreate(
            ['service_id' => $service->id, 'field_code' => 'N100'],
            ['field_name' => 'NECO', 'base_price' => 1000, 'is_active' => 1]
        );

        $nabted = \App\Models\ServiceField::firstOrCreate(
            ['service_id' => $service->id, 'field_code' => 'N101'],
            ['field_name' => 'NABTED', 'base_price' => 1500, 'is_active' => 1]
        );

        // Add default prices for super_admin role
        \App\Models\ServicePrice::firstOrCreate(
            ['service_id' => $service->id, 'service_fields_id' => $neco->id, 'user_type' => 'super_admin'],
            ['price' => 1000]
        );

        \App\Models\ServicePrice::firstOrCreate(
            ['service_id' => $service->id, 'service_fields_id' => $nabted->id, 'user_type' => 'super_admin'],
            ['price' => 1500]
        );
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BrandBillingProfileSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('en_US');
        $brands = DB::table('brands')->get();

        foreach ($brands as $brand) {
            $payload = [
                'legal_company_name' => $brand->brand_name . ' LLC',
                'vat_id' => strtoupper($faker->bothify('VAT-??####??')),
                'billing_address' => $faker->streetAddress(),
                'billing_city' => $brand->city ?? $faker->city(),
                'billing_country' => $brand->country ?? $faker->country(),
                'billing_postal_code' => $brand->postal_code ?? (string) $faker->postcode(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $payload = collect($payload)
                ->filter(fn($_, $column) => Schema::hasColumn('brand_billing_profiles', $column))
                ->all();

            DB::table('brand_billing_profiles')->updateOrInsert(
                ['brand_id' => $brand->id],
                $payload
            );
        }
    }
}

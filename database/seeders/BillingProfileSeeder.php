<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BillingProfileSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('en_US');
        $brands = DB::table('brands')->get();
        $creators = DB::table('creators')->get();

        foreach ($brands as $brand) {
            $payload = [
                'legal_company_name' => $brand->brand_name . ' LLC',
                'vat_id' => strtoupper($faker->bothify('VAT-??####??')),
                'billing_address' => $faker->streetAddress(),
                'billing_city' => $faker->city(),
                'billing_country' => $faker->country(),
                'billing_postal_code' => (string) $faker->postcode(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $payload = collect($payload)
                ->filter(fn($_, $column) => Schema::hasColumn('billing_profiles', $column))
                ->all();

            DB::table('billing_profiles')->updateOrInsert(
                [
                    'user_id' => $brand->id,
                    'user_type' => 'brand'
                ],
                $payload
            );
        }

        foreach ($creators as $creator) {
            $payload = [
                'legal_company_name' => null,
                'vat_id' => strtoupper($faker->bothify('TAX-??####??')),
                'billing_address' => $faker->streetAddress(),
                'billing_city' => $faker->city(),
                'billing_country' => $faker->country(),
                'billing_postal_code' => (string) $faker->postcode(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $payload = collect($payload)
                ->filter(fn($_, $column) => Schema::hasColumn('billing_profiles', $column))
                ->all();

            DB::table('billing_profiles')->updateOrInsert(
                [
                    'user_id' => $creator->id,
                    'user_type' => 'creator'
                ],
                $payload
            );
        }
    }
}

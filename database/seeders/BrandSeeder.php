<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('en_US');

        $industryPool = [
            'Beauty', 'Fashion', 'Travel', 'Food', 'Technology', 'Fitness', 'Finance', 'Education'
        ];

        $brandUsers = DB::table('users')->where('user_type', 'brand')->get();

        foreach ($brandUsers as $user) {
            $brandName = str_replace(' Team', '', (string) $user->name);
            $slug = (string) $user->slug;

            $payload = [
                'brand_name' => $brandName,
                'description' => $faker->paragraphs(2, true),
                'industry' => $faker->randomElement($industryPool),
                'phone' => $user->phone,
                'email' => $user->email,
                'website' => 'https://www.' . $slug . '.com',
                'location' => trim(($user->city ?? '') . ', ' . ($user->country ?? ''), ', '),
                'city' => $user->city,
                'country' => $user->country,
                'postal_code' => $user->postal_code,
                'profile_image_path' => null,
                'cover_image_path' => null,
                'is_verified' => $faker->boolean(30),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $payload = collect($payload)
                ->filter(fn($_, $column) => Schema::hasColumn('brands', $column))
                ->all();

            DB::table('brands')->updateOrInsert(
                ['user_id' => $user->id],
                $payload
            );
        }
    }
}

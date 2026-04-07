<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('en_US');

        $users = [
            [
                'name'         => 'System Administrator',
                'email'        => 'admin@rockies.local',
                'user_type'    => 'admin',
                'phone'        => '+1-415-555-0100',
                'gender'       => 'other',
                'address_line' => '123 Admin St',
                'country'      => 'United States',
                'city'         => 'San Francisco',
                'postal_code'  => '94107',
                'bio'          => 'Platform administrator responsible for operations and moderation.'
            ],
            [
                'name'         => 'Community Moderator',
                'email'        => 'moderator@rockies.local',
                'user_type'    => 'moderator',
                'phone'        => '+1-646-555-0112',
                'gender'       => 'female',
                'country'      => 'United States',
                'address_line' => '456 Moderator Ave',
                'city'         => 'New York',
                'postal_code'  => '10012',
                'bio'          => 'Reviews campaigns, profiles, and user reports.'
            ]
        ];

        for ($i = 1; $i <= 12; $i++) {
            $users[] = [
                'name'         => $faker->company() . ' Team',
                'email'        => sprintf('brand%02d@rockies.local', $i),
                'user_type'    => 'brand',
                'phone'        => $faker->numerify('+1-###-555-####'),
                'gender'       => $faker->randomElement(['male', 'female', 'other']),
                'country'      => $faker->country(),
                'city'         => $faker->city(),
                'address_line' => $faker->streetAddress(),
                'postal_code'  => (string) $faker->postcode(),
                'bio'          => $faker->sentence(12)
            ];
        }

        for ($i = 1; $i <= 12; $i++) {
            $users[] = [
                'name'         => $faker->name(),
                'email'        => sprintf('influencer%02d@rockies.local', $i),
                'user_type'    => 'influencer',
                'phone'        => $faker->numerify('+1-###-555-####'),
                'gender'       => $faker->randomElement(['male', 'female', 'other']),
                'country'      => $faker->country(),
                'city'         => $faker->city(),
                'address_line' => $faker->streetAddress(),
                'postal_code'  => (string) $faker->postcode(),
                'bio'          => $faker->sentence(10)
            ];
        }

        foreach ($users as $entry) {
            $baseSlug = Str::slug($entry['name']) ?: Str::before($entry['email'], '@');
            $slug     = $baseSlug;
            $suffix   = 1;
            while (DB::table('users')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $suffix;
                $suffix++;
            }

            DB::table('users')->updateOrInsert(
                ['email' => $entry['email']],
                [
                    'slug'                         => $slug,
                    'name'                         => $entry['name'],
                    'password'                     => Hash::make('password'),
                    'user_type'                    => $entry['user_type'],
                    'phone'                        => $entry['phone'],
                    'date_of_birth'                => $faker->dateTimeBetween('-45 years', '-20 years')->format('Y-m-d'),
                    'gender'                       => $entry['gender'],
                    'country'                      => $entry['country'],
                    'city'                         => $entry['city'],
                    'address_line'                 => $entry['address_line'],
                    'postal_code'                  => $entry['postal_code'],
                    'bio'                          => $entry['bio'],
                    'profile_image_path'           => null,
                    'is_active'                    => true,
                    'verification_code'            => null,
                    'verification_code_expires_at' => null,
                    'email_verified_at'            => now()->subDays(random_int(1, 120)),
                    'last_login_at'                => now()->subDays(random_int(0, 7)),
                    'created_at'                   => now(),
                    'updated_at'                   => now()
                ]
            );
        }
    }
}

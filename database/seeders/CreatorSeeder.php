<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('en_US');
        $creatorUsers = DB::table('users')->where('user_type', 'creator')->get();
        $featuredCount = max(4, (int) floor($creatorUsers->count() / 3));
        $featuredUserIds = $creatorUsers->pluck('id')->shuffle()->take($featuredCount)->values();

        foreach ($creatorUsers as $user) {
            $bio = $faker->paragraphs(2, true);

            $payload = [
                'display_name' => $user->name,
                'title_name' => $faker->randomElement([
                    'Lifestyle Creator',
                    'Travel Storyteller',
                    'UGC Creator',
                    'Beauty Reviewer',
                    'Fitness Coach',
                ]),
                'audience' => $faker->sentence(10),
                'brands_worked_with' => implode(', ', $faker->randomElements([
                    'Nike', 'L\'Oreal', 'Samsung', 'Adobe', 'H&M', 'Sephora', 'Notion', 'Canva'
                ], random_int(2, 4))),
                'location' => trim(($user->city ?? '') . ', ' . ($user->country ?? ''), ', '),
                'city' => $user->city,
                'country' => $user->country,
                'postal_code' => $user->postal_code,
                'gender' => $user->gender,
                'profile_image_path' => null,
                'cover_image_path' => null,
                'is_active' => true,
                'is_featured' => $featuredUserIds->contains($user->id),
                'featured_priority' => $featuredUserIds->search($user->id),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (($payload['featured_priority'] ?? false) !== false) {
                $payload['featured_priority'] = ((int) $payload['featured_priority']) + 1;
            } else {
                $payload['featured_priority'] = null;
            }

            $payload = collect($payload)
                ->filter(fn($_, $column) => Schema::hasColumn('creators', $column))
                ->all();

            DB::table('creators')->updateOrInsert(
                ['user_id' => $user->id],
                $payload
            );

            DB::table('users')
                ->where('id', $user->id)
                ->update(['bio' => $bio]);
        }
    }
}

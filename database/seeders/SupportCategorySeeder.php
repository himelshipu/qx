<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupportCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Account & Access', 'description' => 'Login, security, verification, and account settings issues.'],
            ['name' => 'Campaign Support', 'description' => 'Campaign setup, approvals, targeting, and delivery questions.'],
            ['name' => 'Orders & Payments', 'description' => 'Order status, refunds, payment processing, and billing questions.'],
            ['name' => 'Technical Issue', 'description' => 'Platform bugs, upload issues, and performance problems.'],
        ];

        foreach ($categories as $index => $category) {
            DB::table('support_categories')->updateOrInsert(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

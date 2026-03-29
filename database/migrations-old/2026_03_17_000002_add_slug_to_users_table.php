<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('slug')->nullable()->after('name');
        });

        $usedSlugs = [];

        DB::table('users')
            ->select(['id', 'name'])
            ->orderBy('id')
            ->chunkById(200, function ($users) use (&$usedSlugs): void {
                foreach ($users as $user) {
                    $base = Str::slug((string) $user->name);

                    if ($base === '') {
                        $base = 'user';
                    }

                    $slug   = $base;
                    $suffix = 1;

                    while (isset($usedSlugs[$slug])) {
                        $slug = $base . '-' . $suffix;
                        $suffix++;
                    }

                    $usedSlugs[$slug] = true;

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['slug' => $slug]);
                }
            });

        Schema::table('users', function (Blueprint $table): void {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};

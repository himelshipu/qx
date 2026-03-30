<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateSeedersFromMigrations extends Command
{
    protected $signature = 'generate:seeders';
    protected $description = 'Generate seeders automatically from migration files';

    public function handle()
    {
        $migrationPath = database_path('migrations');
        $seederPath = database_path('seeders');

        if (!File::exists($seederPath)) {
            File::makeDirectory($seederPath, 0755, true);
        }

        $files = File::files($migrationPath);

        foreach ($files as $file) {
            $content = File::get($file->getPathname());

            // Get table name
            preg_match('/Schema::create\\([\'"](.+?)[\'"]/', $content, $tableMatch);
            if (!$tableMatch) continue;

            $table = $tableMatch[1];
            $className = Str::studly(Str::singular($table)) . 'Seeder';

            // Extract columns
            preg_match_all('/\\$table->(\\w+)\\(([^;]+)\\);/', $content, $columns, PREG_SET_ORDER);

            $fields = [];

            foreach ($columns as $col) {
                $type = $col[1];
                $definition = $col[2];

                if (preg_match('/[\'"](.+?)[\'"]/', $definition, $nameMatch)) {
                    $column = $nameMatch[1];
                } else {
                    continue;
                }

                if (in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                    continue;
                }

                $fields[$column] = $this->mapFaker($column, $type);
            }

            $this->generateSeeder($seederPath, $className, $table, $fields);

            $this->info("✅ Generated: $className");
        }

        $this->updateDatabaseSeeder($seederPath);
        $this->info("🎉 All seeders generated successfully!");
    }

    private function updateDatabaseSeeder($seederPath)
{
    $files = collect(File::files($seederPath))
    ->sortBy(fn($f) => $f->getFilename())
    ->values();

    $calls = [];

    foreach ($files as $file) {
        $className = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        $calls[] = "            \\Database\\Seeders\\$className::class,";
    }

    $callsString = implode("\n", $calls);

    $content = <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \$this->call([
$callsString
        ]);
    }
}
PHP;

    File::put(database_path('seeders/DatabaseSeeder.php'), $content);

    $this->info("✅ DatabaseSeeder.php updated!");
}

private function mapFaker($column, $type)
{
    $column = strtolower($column);

    if ($type === 'enum') {
        return match (true) {
            str_contains($column, 'user_type') => "\$faker->randomElement(['admin','brand','creator','moderator'])",
            str_contains($column, 'gender') => "\$faker->randomElement(['male','female','other'])",
            str_contains($column, 'status') => "\$faker->randomElement(['pending','approved','rejected','completed'])",
            str_contains($column, 'campaign_type') => "\$faker->randomElement(['one_time','ongoing'])",
            str_contains($column, 'platform') => "\$faker->randomElement(['instagram','tiktok','youtube'])",
            default => "'active'",
        };
    }

    return match (true) {
        str_contains($column, 'email') => "\$faker->unique()->safeEmail",
        str_contains($column, 'name') => "\$faker->name",
        str_contains($column, 'title') => "\$faker->sentence",
        str_contains($column, 'description') => "\$faker->paragraph",
        str_contains($column, 'phone') => "\$faker->phoneNumber",
        str_contains($column, 'address') => "\$faker->address",
        str_contains($column, 'url') => "\$faker->url",
        str_contains($column, 'image') => "\$faker->imageUrl",
        str_contains($column, 'price') => "\$faker->randomFloat(2, 10, 1000)",
        str_contains($column, 'amount') => "\$faker->randomFloat(2, 10, 5000)",
        str_contains($column, 'country') => "\$faker->countryCode",
        str_contains($column, 'city') => "\$faker->city",
        str_contains($column, 'date') => "\$faker->date()",
        str_contains($column, 'time') => "\$faker->time()",
        str_contains($column, 'is_') => "\$faker->boolean",
        str_contains($column, 'count') => "\$faker->numberBetween(1, 1000)",
        str_contains($column, 'id') => "1", // temp FK
        default => "\$faker->word",
    };
}

    private function generateSeeder($path, $className, $table, $fields)
    {
        $fieldsString = '';

        foreach ($fields as $column => $faker) {
            $fieldsString .= "                    '$column' => $faker,\n";
        }

        $template = <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class $className extends Seeder
{
    public function run(): void
    {
        \$faker = \\Faker\\Factory::create();

        for (\$i = 0; \$i < 20; \$i++) {
            DB::table('$table')->insert([
$fieldsString
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
PHP;

        File::put("$path/$className.php", $template);
    }
}
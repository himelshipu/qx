<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AnalyzeMigrations extends Command
{
    protected $signature = 'analyze:migrations';
    protected $description = 'Analyze all migration files and export table + columns info';

    public function handle()
    {
        $migrationPath = database_path('migrations');
        $files = File::files($migrationPath);

        $output = "=========================================\n";
        $output .= "DETAILED MIGRATION COLUMN ANALYSIS\n";
        $output .= "Generated: " . now() . "\n";
        $output .= "=========================================\n\n";

        $count = 1;

        foreach ($files as $file) {
            $content = File::get($file->getPathname());
            $filename = $file->getFilename();

            preg_match('/Schema::create\\([\'"](.+?)[\'"]/', $content, $tableMatch);
            $table = $tableMatch[1] ?? 'UNKNOWN_TABLE';

            $output .= "---------------------------------------------------\n";
            $output .= "[{$count}] File: {$filename}\n";
            $output .= "---------------------------------------------------\n";
            $output .= "Table: {$table}\n\n";

            // Extract columns
            $output .= "COLUMN DEFINITIONS:\n";
            $output .= "-------------------\n";

            preg_match_all('/\\$table->(\\w+)\\(([^;]+)\\);/', $content, $columns, PREG_SET_ORDER);

            foreach ($columns as $col) {
                $type = $col[1];
                $definition = trim($col[2]);

                // Clean column name
                if (preg_match('/[\'"](.+?)[\'"]/', $definition, $nameMatch)) {
                    $columnName = $nameMatch[1];
                } else {
                    $columnName = $definition;
                }

                $output .= "  - {$columnName} ({$type})\n";
            }

            // Foreign keys
            preg_match_all('/foreignId\\([\'"](.+?)[\'"]\\).*?constrained\\([\'"](.+?)[\'"]\\)/', $content, $fks, PREG_SET_ORDER);

            if (!empty($fks)) {
                $output .= "\nFOREIGN KEY CONSTRAINTS:\n";
                $output .= "-----------------------\n";

                foreach ($fks as $fk) {
                    $output .= "  - {$fk[1]} → {$fk[2]}\n";
                }
            }

            // Unique constraints
            preg_match_all('/unique\\(([^)]+)\\)/', $content, $uniques, PREG_SET_ORDER);

            if (!empty($uniques)) {
                $output .= "\nUNIQUE CONSTRAINTS:\n";
                $output .= "------------------\n";

                foreach ($uniques as $unique) {
                    $output .= "  - {$unique[1]}\n";
                }
            }

            $output .= "\n\n";
            $count++;
        }

        $output .= "=========================================\n";
        $output .= "SUMMARY\n";
        $output .= "=========================================\n";
        $output .= "Total Migration Files: " . count($files) . "\n";
        $output .= "Generated on: " . now() . "\n";
        $output .= "=========================================\n";

        // Save file
        $filePath = storage_path('migration_analysis.txt');
        File::put($filePath, $output);

        $this->info("✅ Migration analysis saved to:");
        $this->info($filePath);
    }
}
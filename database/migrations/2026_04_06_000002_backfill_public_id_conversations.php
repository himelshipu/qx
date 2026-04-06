<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Backfill public_id for existing conversations that don't have one
        DB::statement("
            UPDATE conversations
            SET public_id = SUBSTRING(MD5(CONCAT(id, NOW(), RAND())), 1, 16)
            WHERE public_id IS NULL
        ");
    }

    public function down()
    {
        // No rollback needed
    }
};

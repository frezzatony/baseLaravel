<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS books');
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
        DB::statement('CREATE EXTENSION IF NOT EXISTS unaccent');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP SCHEMA IF EXISTS books');
        DB::statement('DROP EXTENSION IF EXISTS "uuid-ossp";');
        DB::statement('DROP EXTENSION IF EXISTS unaccent;');
    }
};

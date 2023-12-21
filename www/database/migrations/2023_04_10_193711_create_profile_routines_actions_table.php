<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('profile_routines_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("profiles_id")->constrained("profiles")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("routines_actions_id")->constrained("routines_actions")->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS profile_routines_actions CASCADE;');
    }
};

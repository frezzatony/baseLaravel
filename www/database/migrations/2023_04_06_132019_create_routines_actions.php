<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('routines_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('routines_id')->constrained('routines')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('slug', 128);
            $table->string('description', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS routines_actions CASCADE;');
    }
};

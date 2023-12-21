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
        Schema::create("modules", function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->string('slug', 128);
            $table->string('icon', 30)->nullable(true);
            $table->integer('list_order')->default(0);
            $table->boolean('is_active')->default(false);
            $table->boolean('can_edit')->default(true);
            $table->boolean('can_delete')->default(true);
            $table->boolean('is_master')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS modules CASCADE;');
    }
};

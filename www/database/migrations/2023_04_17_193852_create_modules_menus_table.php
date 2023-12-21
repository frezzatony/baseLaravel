<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules_menus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('modules_menus_id_parent')->nullable();
            $table->integer('modules_id');
            $table->integer('routines_actions_id')->nullable();
            $table->smallInteger('list_order')->default(0);
            $table->jsonb('attributes')->nullable()->default('[]');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP TABLE IF EXISTS modules_menus CASCADE;');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('modules_menus', function (Blueprint $table) {
            $table->foreign(['modules_id'], 'modules_menus_fk_modules')->references(['id'])->on('modules')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['modules_menus_id_parent'], 'modules_menus_fk_modules_menus_parent')->references(['id'])->on('modules_menus')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['routines_actions_id'], 'modules_menus_fk_routines_actions')->references(['id'])->on('routines_actions')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules_menus', function (Blueprint $table) {
            $table->dropForeign('modules_menus_fk_modules');
            $table->dropForeign('modules_menus_fk_modules_menus_parent');
            $table->dropForeign('modules_menus_fk_routines_actions');
        });
    }
};

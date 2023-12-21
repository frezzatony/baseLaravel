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
        Schema::table('attendance_unit_managers_users', function (Blueprint $table) {
            $table->foreign(['attendance_units_id'], 'attendance_unit_managers_users_fk_attendances')->references(['id'])->on('attendance_units')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['users_id'], 'attendance_unit_managers_users_fk_users')->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP TABLE IF EXISTS attendance_unit_managers_users CASCADE;');
    }
};

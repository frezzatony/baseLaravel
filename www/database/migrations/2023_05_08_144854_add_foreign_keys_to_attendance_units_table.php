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
        Schema::table('attendance_units', function (Blueprint $table) {
            $table->foreign(['addresses_id'], 'attendance_units_fk_addresses')->references(['id'])->on('addresses')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign(['attachments_catalog_id'], 'attendance_units_fk_attachments')->references(['id'])->on('attachments_catalog')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP TABLE IF EXISTS attendance_units CASCADE;');
    }
};

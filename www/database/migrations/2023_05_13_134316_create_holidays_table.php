<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->boolean('annual')->nullable()->default(false);
            $table->date('date');
            $table->string('type');
            $table->boolean('optional')->nullable()->default(false);
            $table->time('time_start')->nullable()->default('00:00:00');
            $table->time('time_end')->nullable()->default('00:00:00');
            $table->timestamps();
        });
    }

    public function down()
    {
        DB::statement('DROP TABLE IF EXISTS holidays CASCADE;');
    }
};

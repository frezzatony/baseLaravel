<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HolidaysTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('holidays')->delete();
        
        \DB::table('holidays')->insert(array (
            0 => 
            array (
                'id' => 1,
                'description' => 'ANO NOVO',
                'date' => '1900-01-01',
                'type' => 'FEDERAL',
                'optional' => false,
                'time_start' => '00:00:00',
                'time_end' => '00:00:00',
                'annual' => true,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'description' => 'TIRADENTES',
                'date' => '1900-04-21',
                'type' => 'FEDERAL',
                'optional' => false,
                'time_start' => '00:00:00',
                'time_end' => '00:00:00',
                'annual' => true,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'description' => 'DIA DO TRABALHADOR',
                'date' => '1900-05-01',
                'type' => 'FEDERAL',
                'optional' => false,
                'time_start' => '00:00:00',
                'time_end' => '00:00:00',
                'annual' => true,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'description' => 'INDEPENDÊNCIA DO BRASIL',
                'date' => '1900-09-07',
                'type' => 'FEDERAL',
                'optional' => false,
                'time_start' => '00:00:00',
                'time_end' => '00:00:00',
                'annual' => true,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'description' => 'N. S. APARECIDA',
                'date' => '1900-10-12',
                'type' => 'FEDERAL',
                'optional' => false,
                'time_start' => '00:00:00',
                'time_end' => '00:00:00',
                'annual' => true,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'description' => 'FINADOS',
                'date' => '1900-11-02',
                'type' => 'FEDERAL',
                'optional' => false,
                'time_start' => '00:00:00',
                'time_end' => '00:00:00',
                'annual' => true,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'description' => 'PROCLAMAÇÃO DA REPÚBLICA',
                'date' => '1900-11-15',
                'type' => 'FEDERAL',
                'optional' => false,
                'time_start' => '00:00:00',
                'time_end' => '00:00:00',
                'annual' => true,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'description' => 'NATAL',
                'date' => '1900-12-25',
                'type' => 'FEDERAL',
                'optional' => false,
                'time_start' => '00:00:00',
                'time_end' => '00:00:00',
                'annual' => true,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoutinesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('routines')->delete();
        
        \DB::table('routines')->insert(array (
            0 => 
            array (
                'id' => 4,
                'name' => 'GESTAO DE PERFIS',
                'slug' => 'gesta_perfis',
                'modules_id' => 1,
                'created_at' => '2023-05-11 08:40:36',
                'updated_at' => '2023-05-11 08:40:36',
                'is_active' => true,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'GESTAO DE ROTINAS',
                'slug' => 'gestao_rotinas',
                'modules_id' => 3,
                'created_at' => '2023-05-11 08:32:58',
                'updated_at' => '2023-05-11 13:23:02',
                'is_active' => true,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'GESTAO DE MODULOS',
                'slug' => 'gestao_modulos',
                'modules_id' => 3,
                'created_at' => '2023-05-11 08:35:25',
                'updated_at' => '2023-05-11 13:23:07',
                'is_active' => true,
            ),
            3 => 
            array (
                'id' => 1,
                'name' => 'GESTAO DE USUARIOS',
                'slug' => 'gestao_usuarios',
                'modules_id' => 1,
                'created_at' => '2023-05-11 08:21:11',
                'updated_at' => '2023-05-11 15:02:55',
                'is_active' => true,
            ),
            4 => 
            array (
                'id' => 10,
                'name' => 'GESTÃO DE UNIDADES DE ATENDIMENTO',
                'slug' => 'gestao_unidades_atendimento',
                'modules_id' => 1,
                'created_at' => '2023-05-11 15:20:27',
                'updated_at' => '2023-05-11 15:36:53',
                'is_active' => true,
            ),
        ));
        
        
    }
}
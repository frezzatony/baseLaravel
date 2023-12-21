<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoutinesActionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('routines_actions')->delete();
        
        \DB::table('routines_actions')->insert(array (
            0 => 
            array (
                'id' => 4,
                'routines_id' => 4,
                'slug' => 'gerenciamento_perfis',
                'description' => 'Gerenciamento completo de Perfis de Usuários',
                'created_at' => '2023-05-11 08:40:36',
                'updated_at' => '2023-05-11 08:40:36',
            ),
            1 => 
            array (
                'id' => 2,
                'routines_id' => 2,
                'slug' => 'gerenciar_rotinas',
                'description' => 'Gerenciamento completo de rotinas',
                'created_at' => '2023-05-11 08:32:58',
                'updated_at' => '2023-05-11 13:23:02',
            ),
            2 => 
            array (
                'id' => 3,
                'routines_id' => 3,
                'slug' => 'gerenciar_modulos',
                'description' => 'Gerenciamento completo de Módulos do sistema',
                'created_at' => '2023-05-11 08:35:48',
                'updated_at' => '2023-05-11 13:23:07',
            ),
            3 => 
            array (
                'id' => 1,
                'routines_id' => 1,
                'slug' => 'gerenciar_usuarios',
                'description' => 'Gerenciamento completo de usuários do sistema',
                'created_at' => '2023-05-11 08:21:11',
                'updated_at' => '2023-05-11 15:02:55',
            ),
            4 => 
            array (
                'id' => 5,
                'routines_id' => 10,
                'slug' => 'gerenciamento_unidades_atendimento',
                'description' => 'Gerenciamento completo das Unidades de Atendimento de Serviços',
                'created_at' => '2023-05-11 15:20:27',
                'updated_at' => '2023-05-11 15:36:53',
            ),
        ));
        
        
    }
}
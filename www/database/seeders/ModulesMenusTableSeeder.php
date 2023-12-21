<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModulesMenusTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('modules_menus')->delete();
        
        \DB::table('modules_menus')->insert(array (
            0 => 
            array (
                'id' => 4,
                'modules_menus_id_parent' => 3,
                'modules_id' => 1,
                'routines_actions_id' => NULL,
                'list_order' => 1,
                'attributes' => '{"label": "Gerencimento de Usuários"}',
                'created_at' => '2023-05-11 08:27:56',
                'updated_at' => '2023-05-11 08:27:56',
            ),
            1 => 
            array (
                'id' => 5,
                'modules_menus_id_parent' => 4,
                'modules_id' => 1,
                'routines_actions_id' => 1,
                'list_order' => 1,
                'attributes' => '{"href": "/system/users", "label": "Usuários"}',
                'created_at' => '2023-05-11 08:30:54',
                'updated_at' => '2023-05-11 08:30:54',
            ),
            2 => 
            array (
                'id' => 9,
                'modules_menus_id_parent' => 4,
                'modules_id' => 1,
                'routines_actions_id' => 4,
                'list_order' => 2,
                'attributes' => '{"href": "/system/profiles", "label": "Perfis"}',
                'created_at' => '2023-05-11 08:30:54',
                'updated_at' => '2023-05-11 08:30:54',
            ),
            3 => 
            array (
                'id' => 6,
                'modules_menus_id_parent' => NULL,
                'modules_id' => 3,
                'routines_actions_id' => NULL,
                'list_order' => 1,
                'attributes' => '{"label": "Manutenção"}',
                'created_at' => '2023-05-11 08:31:53',
                'updated_at' => '2023-05-11 08:31:53',
            ),
            4 => 
            array (
                'id' => 7,
                'modules_menus_id_parent' => 6,
                'modules_id' => 3,
                'routines_actions_id' => 2,
                'list_order' => 2,
                'attributes' => '{"href": "/system/routines", "label": "Rotinas"}',
                'created_at' => '2023-05-11 08:33:51',
                'updated_at' => '2023-05-11 08:33:51',
            ),
            5 => 
            array (
                'id' => 8,
                'modules_menus_id_parent' => 6,
                'modules_id' => 3,
                'routines_actions_id' => 3,
                'list_order' => 1,
                'attributes' => '{"href": "/system/modules", "label": "Módulos"}',
                'created_at' => '2023-05-11 08:33:51',
                'updated_at' => '2023-05-11 08:33:51',
            ),
            6 => 
            array (
                'id' => 16,
                'modules_menus_id_parent' => 10,
                'modules_id' => 1,
                'routines_actions_id' => 5,
                'list_order' => 1,
                'attributes' => '{"href": "/system/attendanceunits", "label": "Unidades de Atendimento"}',
                'created_at' => '2023-05-11 15:22:47',
                'updated_at' => '2023-05-11 15:22:47',
            ),
            7 => 
            array (
                'id' => 17,
                'modules_menus_id_parent' => NULL,
                'modules_id' => 1,
                'routines_actions_id' => NULL,
                'list_order' => 1,
                'attributes' => '{"label": "Manutenção"}',
                'created_at' => '2023-05-13 14:13:20',
                'updated_at' => '2023-05-13 14:13:20',
            ),
            8 => 
            array (
                'id' => 10,
                'modules_menus_id_parent' => NULL,
                'modules_id' => 1,
                'routines_actions_id' => NULL,
                'list_order' => 3,
                'attributes' => '{"label": "Entidade"}',
                'created_at' => '2023-05-11 15:21:05',
                'updated_at' => '2023-05-11 15:21:05',
            ),
            9 => 
            array (
                'id' => 3,
                'modules_menus_id_parent' => NULL,
                'modules_id' => 1,
                'routines_actions_id' => NULL,
                'list_order' => 2,
                'attributes' => '{"label": "Sistema"}',
                'created_at' => '2023-05-11 08:22:42',
                'updated_at' => '2023-05-11 08:22:42',
            ),
            10 => 
            array (
                'id' => 18,
                'modules_menus_id_parent' => 17,
                'modules_id' => 1,
                'routines_actions_id' => 6,
                'list_order' => 1,
                'attributes' => '{"href": "/system/holidays", "label": "Feriados e Pontos Facultativos"}',
                'created_at' => '2023-05-13 14:13:42',
                'updated_at' => '2023-05-13 14:13:42',
            ),
        ));
        
        
    }
}
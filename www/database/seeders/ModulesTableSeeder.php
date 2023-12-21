<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModulesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('modules')->delete();
        
        \DB::table('modules')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'GERENCIAMENTO DO SISTEMA',
                'slug' => 'gestao_sistema',
                'icon' => 'ph-gear-six',
                'list_order' => 2,
                'is_active' => true,
                'can_edit' => true,
                'can_delete' => false,
                'is_master' => false,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 3,
                'name' => 'MANUTENÇÃO INTERNA DO SISTEMA',
                'slug' => 'manutencao_interna_sistema',
                'icon' => 'ph-globe',
                'list_order' => 1,
                'is_active' => true,
                'can_edit' => true,
                'can_delete' => false,
                'is_master' => true,
                'created_at' => '2023-05-11 09:14:49',
                'updated_at' => '2023-05-11 13:26:56',
            ),
            2 => 
            array (
                'id' => 2,
                'name' => 'SERVIÇOS E ATENDIMENTO',
                'slug' => 'servicos_atendimentos',
                'icon' => 'ph-users-three ',
                'list_order' => 3,
                'is_active' => true,
                'can_edit' => true,
                'can_delete' => true,
                'is_master' => false,
                'created_at' => NULL,
                'updated_at' => '2023-05-13 14:10:48',
            ),
        ));
        
        
    }
}
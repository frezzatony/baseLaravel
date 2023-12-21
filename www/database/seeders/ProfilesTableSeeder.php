<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProfilesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('profiles')->delete();
        
        \DB::table('profiles')->insert(array (
            0 => 
            array (
                'id' => 2,
                'name' => 'ACESSO PÚBLICO',
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_active' => true,
                'can_delete' => false,
                'can_edit' => false,
            ),
            1 => 
            array (
                'id' => 4,
                'name' => 'ATENDENTES DE FILAS',
                'created_at' => '2023-05-11 09:04:15',
                'updated_at' => '2023-05-11 09:04:15',
                'is_active' => true,
                'can_delete' => true,
                'can_edit' => true,
            ),
            2 => 
            array (
                'id' => 1,
                'name' => 'ADMINISTRADORES DO SISTEMA',
                'created_at' => NULL,
                'updated_at' => '2023-05-11 15:30:33',
                'is_active' => true,
                'can_delete' => false,
                'can_edit' => false,
            ),
        ));
        
        
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProfileRoutinesActionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('profile_routines_actions')->delete();
        
        \DB::table('profile_routines_actions')->insert(array (
            0 => 
            array (
                'id' => 4,
                'profiles_id' => 1,
                'routines_actions_id' => 1,
                'created_at' => '2023-05-11 15:10:17',
                'updated_at' => '2023-05-11 15:10:17',
            ),
            1 => 
            array (
                'id' => 5,
                'profiles_id' => 1,
                'routines_actions_id' => 4,
                'created_at' => '2023-05-11 15:10:26',
                'updated_at' => '2023-05-11 15:10:26',
            ),
            2 => 
            array (
                'id' => 7,
                'profiles_id' => 1,
                'routines_actions_id' => 5,
                'created_at' => '2023-05-11 15:30:33',
                'updated_at' => '2023-05-11 15:30:33',
            ),
        ));
        
        
    }
}
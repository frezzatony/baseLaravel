<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'MASTER',
                'social_name' => NULL,
                'email' => 'admin@admin.com.br',
                'email_verified_at' => NULL,
                'password' => '$2y$10$VgScfu932jXjUNEgLqpBROG.brOJwMZKe0zKEi6DQGzM9RfgI6B42',
                'remember_token' => NULL,
                'login' => '11111111111',
                'attributes' => '{"module_id":1}',
                'is_master' => true,
                'is_active' => true,
                'api_token' => NULL,
                'created_at' => '2023-05-10 13:46:25',
                'updated_at' => '2023-05-11 08:07:25',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}
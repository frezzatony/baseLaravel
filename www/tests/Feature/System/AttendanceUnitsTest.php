<?php

namespace Tests\Feature\System;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\AuthenticatedTestCase;

class AttendanceUnitsTest extends AuthenticatedTestCase
{
    use WithFaker;

    public function testIndex()
    {
        $response = $this->get(route('system.attendanceunits.index'));
        $response->assertStatus(200);
    }

    public function testAddForm()
    {
        $response = $this->get(route('system.attendanceunits.create'));
        $response->assertStatus(200);
    }

    public function testStore()
    {
        $faker = \Faker\Factory::create();
        $response = $this->post(route('system.attendanceunits.store'), [
            'is_active'             =>  't',
            'name'                  =>  $faker->name,
            'slug'                  =>  $faker->name,
            'address_cep'           =>  $faker->numerify('##.###-###'),
            'address_state'         =>  $faker->name,
            'address_city'          =>  $faker->name,
            'address_neighborhood'  => $faker->name,
            'address_street'        => $faker->name,
        ]);
        $response->assertStatus(200);
    }

    public function testStoreWithValidationErrors()
    {
        $faker = \Faker\Factory::create();
        $response = $this->post(route('system.attendanceunits.store'));
        $response->assertStatus(302);
    }

    public function testEditForm()
    {
        $response = $this->createFake();
        $response = $this->get(route('system.attendanceunits.edit', ['id' => $response['id']]));
        $response->assertStatus(200);
    }

    public function testUpdate()
    {
        $response = $this->createFake();

        $faker = \Faker\Factory::create();
        $response = $this->put(route('system.attendanceunits.update'), [
            'id'                    =>  $response['id'],
            'is_active'             =>  't',
            'name'                  =>  $faker->name,
            'slug'                  =>  $faker->name,
            'address_cep'           =>  $faker->numerify('##.###-###'),
            'address_state'         =>  $faker->name,
            'address_city'          =>  $faker->name,
            'address_neighborhood'  => $faker->name,
            'address_street'        => $faker->name,
        ]);
        $response->assertStatus(200);
    }

    public function testUpdateWithValidationErrors()
    {
        $response = $this->createFake();
        $response = $this->put(route('system.attendanceunits.update'), [
            'id'                    =>  $response['id'],
        ]);
        $response->assertStatus(302);
    }

    public function testDestroy()
    {
        $response = $this->createFake();
        $response = $this->delete(route('system.attendanceunits.destroy'), [
            'ids'   =>  [$response['id']]
        ]);
        $response->assertStatus(200);
    }

    public function testAttachments()
    {
        $response = $this->createFake();
        $response = $this->get(route('system.attendanceunits.attachments', [
            'action'    =>  'fetch',
            'crud_id'   =>  $response['id']
        ]));
        $response->assertStatus(200);
    }

    private function createFake()
    {
        $faker = \Faker\Factory::create();
        return $this->postJson(route('system.attendanceunits.store'), [
            'is_active'             =>  't',
            'name'                  =>  $faker->name,
            'slug'                  =>  $faker->name,
            'address_cep'           =>  $faker->numerify('##.###-###'),
            'address_state'         =>  $faker->name,
            'address_city'          =>  $faker->name,
            'address_neighborhood'  => $faker->name,
            'address_street'        => $faker->name,
        ])->decodeResponseJson();
    }
}

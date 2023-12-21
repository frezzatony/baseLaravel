<?php

namespace Tests\Feature\System;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\AuthenticatedTestCase;

class HolidaysTest extends AuthenticatedTestCase
{
    use WithFaker;

    public function testIndex()
    {
        $response = $this->get(route('system.holidays.index'));
        $response->assertStatus(200);
    }

    public function testAddForm()
    {
        $response = $this->get(route('system.holidays.create'));
        $response->assertStatus(200);
    }

    public function testStore()
    {
        $faker = \Faker\Factory::create();
        $response = $this->post(route('system.holidays.store'), [
            'description'   =>  $faker->name,
            'date'          =>  $faker->date(),
            'type'          =>  \App\Enums\HolidayType::fromKey('FEDERAL'),
            'annual'        =>  't',
            'optional'      =>  't',
            'time_start'    =>  $faker->time(),
            'time_end'      =>  $faker->time(),
        ]);
        $response->assertStatus(200);
    }

    public function testStoreWithValidationErrors()
    {
        $faker = \Faker\Factory::create();
        $response = $this->post(route('system.holidays.store'));
        $response->assertStatus(302);
    }

    public function testEditForm()
    {
        $response = $this->createFake();
        $response = $this->get(route('system.holidays.edit', ['id' => $response['id']]));
        $response->assertStatus(200);
    }

    public function testUpdate()
    {
        $response = $this->createFake();

        $faker = \Faker\Factory::create();
        $response = $this->put(route('system.holidays.update'), [
            'id'            =>  $response['id'],
            'description'   =>  $faker->name,
            'date'          =>  $faker->date(),
            'type'          =>  \App\Enums\HolidayType::fromKey('MUNICIPAL'),
            'annual'        =>  't',
            'optional'      =>  't',
            'time_start'    =>  $faker->time(),
            'time_end'      =>  $faker->time(),
        ]);
        $response->assertStatus(200);
    }

    public function testUpdateWithValidationErrors()
    {
        $response = $this->createFake();
        $response = $this->put(route('system.holidays.update'), [
            'id'                    =>  $response['id'],
        ]);
        $response->assertStatus(302);
    }

    public function testDestroy()
    {
        $response = $this->createFake();
        $response = $this->delete(route('system.holidays.destroy'), [
            'ids'   =>  [$response['id']]
        ]);
        $response->assertStatus(200);
    }

    private function createFake()
    {
        $faker = \Faker\Factory::create();
        return $this->postJson(route('system.holidays.store'), [
            'description'   =>  $faker->name,
            'date'          =>  $faker->date(),
            'type'          =>  \App\Enums\HolidayType::fromKey('FEDERAL'),
            'annual'        =>  't',
            'optional'      =>  't',
            'time_start'    =>  $faker->time(),
            'time_end'      =>  $faker->time(),
        ])->decodeResponseJson();
    }
}

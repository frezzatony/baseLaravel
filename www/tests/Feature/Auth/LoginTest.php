<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class LoginTest extends TestCase
{
    public function testLoginFormShow(): void
    {
        $response = $this->get(route('public.auth.show'));
        $response->assertStatus(200);
    }

    public function testLoginSuccess(): void
    {
        $response = $this->post(route('public.auth.login'), ["username" => '111.111.111-11', "password" => "1234"]);
        $response->assertRedirect("/system");
    }

    public function testLoginError(): void
    {
        $response = $this->post(route('public.auth.login'), ["username" => '989.989.989-98', "password" => "989898"]);
        $response->assertRedirect("/auth");
    }

    public function testLogout(): void
    {
        $response = $this->get(route('public.auth.logout'));
        $response->assertRedirect("/auth");
    }
}

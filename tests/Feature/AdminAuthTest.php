<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    /** @test */
    public function testAdminCanSeedAndLogin()
    {
        $this->artisan('db:seed');

        $response = $this->json('POST', '/auth', [
            'login' => 'admin@admin.com',
            'senha' => 'admin123'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['status' => 'ok']);
    }

    /** @test */
    public function testAdminCanLogIntoGaviao()
    {
        $this->artisan('db:seed');

        $response = $this->json('POST', '/auth_gaviao', [
            'login' => 'admin@admin.com',
            'senha' => 'admin123'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['status' => 'ok']);
    }
}

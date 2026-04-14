# tests/Feature/AdminAuthTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class AdminAuthTest extends TestCase
{
    /** @test */
    public function o_sistema_pode_ser_semeado_e_o_admin_consegue_logar()
    {
        // 1. Executa o Seed
        $this->artisan('db:seed');

        // 2. Verifica se o usuário existe no banco
        $this->assertDatabaseHas('users', ['email' => 'admin@admin.com']);
        $this->assertDatabaseHas('operadores', ['email' => 'admin@admin.com', 'qms_matriz_id' => 9999]);

        // 3. Tenta realizar o login via rota AJAX (Atalaia)
        $response = $this->json('POST', '/auth', [
            'login' => 'admin@admin.com',
            'senha' => 'admin123'
        ]);

        // 4. Verifica resposta e Sessão
        $response->assertStatus(200)
                 ->assertJson(['status' => 'ok']);

        $this->assertTrue(session()->has('login'));
        $this->assertEquals(1, session('login.omctID'));
        $this->assertContains('1', session('login.permissoes'));
    }

    /** @test */
    public function o_admin_consegue_logar_no_modulo_gaviao()
    {
        $this->artisan('db:seed');

        $response = $this->json('POST', '/auth_gaviao', [
            'login' => 'admin@admin.com',
            'senha' => 'admin123'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['status' => 'ok']);

        $this->assertEquals(9999, session('login.qmsID.0.qms_matriz_id'));
    }
}
# tests/Feature/AssetIntegrityTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Operadores;
use App\User;

class AssetIntegrityTest extends TestCase
{
    /** @test */
    public function a_view_fatd_usa_recursos_locais_em_vez_de_cdn()
    {
        // Simula um usuário logado
        $user = factory(User::class)->create();
        $operador = factory(Operadores::class)->create(['email' => $user->email]);

        $this->actingAs($user);

        // Acessa a rota que renderiza o lancamentoFATD (ajuste o ID conforme necessário)
        // Como é carregado via AJAX no sistema, podemos testar a view diretamente se houver rota
        $response = $this->get('/ajax/fatd/1'); 

        $response->assertStatus(200);
        
        // Verifica se NÃO contém o link do CDN
        $response->assertSee('css/jquery/1.13.2/jquery-ui.css');
        $response->assertSee('js/jquery/1.13.2/jquery-ui.min.js');
        $response->assertDontSee('https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');
    }
}
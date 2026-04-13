<?php

namespace Tests\Unit\SSAA\Calendario;

use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\SSAA\Calendario\ControllerAvaliacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class ControllerAvaliacaoRemoveRapTest extends TestCase
{
    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_aborta_exclusao_do_rap_quando_existirem_lancamentos_vinculados()
    {
        DB::shouldReceive('connection')->once()->with('mysql_ssaa')->andReturnSelf();
        DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
            return $callback();
        });

        $ownAuth = Mockery::mock(OwnAuthController::class);
        $ownAuth->shouldReceive('PerfilCheck')->once()->with([9005])->andReturn(true);

        $rapMock = Mockery::mock();
        $rapMock->id_esa_avaliacoes = 20;
        $rapMock->id_turmas_esa = 10;
        $rapMock->shouldNotReceive('delete');

        $rapAlias = Mockery::mock('alias:App\Models\EsaAvaliacoesRap');
        $rapAlias->shouldReceive('where')->once()->andReturnSelf();
        $rapAlias->shouldReceive('first')->once()->andReturn($rapMock);

        $alunosAlias = Mockery::mock('alias:App\Models\Alunos');
        $alunosAlias->shouldReceive('where')->once()->with('turma_esa_id', 10)->andReturnSelf();
        $alunosAlias->shouldReceive('pluck')->once()->with('id')->andReturn(collect([1, 2]));

        $resultadosAlias = Mockery::mock('alias:App\Models\EsaAvaliacoesResultados');
        $resultadosAlias->shouldReceive('where')->once()->with('id_esa_avaliacoes', 20)->andReturnSelf();
        $resultadosAlias->shouldReceive('whereIn')->once()->with('id_aluno', Mockery::type(\Illuminate\Support\Collection::class))->andReturnSelf();
        $resultadosAlias->shouldReceive('exists')->once()->andReturn(true);

        $request = Request::create('/gaviao/ajax/gerenciar-avaliacao/rap', 'DELETE', [
            'hash_rap' => encrypt('teste-20-10'),
        ]);

        $controller = new ControllerAvaliacao($request, $ownAuth);
        $response = $controller->removeRap();
        $payload = $response->getData(true);

        $this->assertSame('err', $payload['status']);
        $this->assertNotEmpty($payload['response']);
        $this->assertTrue(
            strpos(implode(' ', $payload['response']), 'lan') !== false
            || strpos(implode(' ', $payload['response']), 'vinculad') !== false
        );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_remove_rap_com_sucesso_quando_nao_existirem_lancamentos_vinculados()
    {
        DB::shouldReceive('connection')->once()->with('mysql_ssaa')->andReturnSelf();
        DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
            return $callback();
        });

        $ownAuth = Mockery::mock(OwnAuthController::class);
        $ownAuth->shouldReceive('PerfilCheck')->once()->with([9005])->andReturn(true);

        $rapMock = Mockery::mock();
        $rapMock->id_esa_avaliacoes = 20;
        $rapMock->id_turmas_esa = 10;
        $rapMock->shouldReceive('delete')->once()->andReturn(true);

        $rapAlias = Mockery::mock('alias:App\Models\EsaAvaliacoesRap');
        $rapAlias->shouldReceive('where')->once()->andReturnSelf();
        $rapAlias->shouldReceive('first')->once()->andReturn($rapMock);

        $alunosAlias = Mockery::mock('alias:App\Models\Alunos');
        $alunosAlias->shouldReceive('where')->once()->with('turma_esa_id', 10)->andReturnSelf();
        $alunosAlias->shouldReceive('pluck')->once()->with('id')->andReturn(collect([1, 2]));

        $resultadosAlias = Mockery::mock('alias:App\Models\EsaAvaliacoesResultados');
        $resultadosAlias->shouldReceive('where')->once()->with('id_esa_avaliacoes', 20)->andReturnSelf();
        $resultadosAlias->shouldReceive('whereIn')->once()->with('id_aluno', Mockery::type(\Illuminate\Support\Collection::class))->andReturnSelf();
        $resultadosAlias->shouldReceive('exists')->once()->andReturn(false);

        $gboAlias = Mockery::mock('alias:App\Models\EsaAvaliacoesGbo');
        $gboAlias->shouldReceive('join')->once()->andReturnSelf();
        $gboAlias->shouldReceive('where')->once()->with('esa_avaliacoes_indice.id_esa_avaliacoes', 20)->andReturnSelf();
        $gboAlias->shouldReceive('whereIn')->once()->with('esa_avaliacoes_gbo.id_aluno', Mockery::type(\Illuminate\Support\Collection::class))->andReturnSelf();
        $gboAlias->shouldReceive('exists')->once()->andReturn(false);

        $request = Request::create('/gaviao/ajax/gerenciar-avaliacao/rap', 'DELETE', [
            'hash_rap' => encrypt('teste-20-10'),
        ]);

        $controller = new ControllerAvaliacao($request, $ownAuth);
        $response = $controller->removeRap();
        $payload = $response->getData(true);

        $this->assertSame('success', $payload['status']);
        $this->assertSame('RAP removido com sucesso.', $payload['response']);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_retorna_erro_quando_delete_do_rap_lanca_excecao()
    {
        DB::shouldReceive('connection')->once()->with('mysql_ssaa')->andReturnSelf();
        DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
            return $callback();
        });

        $ownAuth = Mockery::mock(OwnAuthController::class);
        $ownAuth->shouldReceive('PerfilCheck')->once()->with([9005])->andReturn(true);

        $rapMock = Mockery::mock();
        $rapMock->id_esa_avaliacoes = 20;
        $rapMock->id_turmas_esa = 10;
        $rapMock->shouldReceive('delete')->once()->andThrow(new \Exception('erro ao excluir'));

        $rapAlias = Mockery::mock('alias:App\Models\EsaAvaliacoesRap');
        $rapAlias->shouldReceive('where')->once()->andReturnSelf();
        $rapAlias->shouldReceive('first')->once()->andReturn($rapMock);

        $alunosAlias = Mockery::mock('alias:App\Models\Alunos');
        $alunosAlias->shouldReceive('where')->once()->with('turma_esa_id', 10)->andReturnSelf();
        $alunosAlias->shouldReceive('pluck')->once()->with('id')->andReturn(collect([1, 2]));

        $resultadosAlias = Mockery::mock('alias:App\Models\EsaAvaliacoesResultados');
        $resultadosAlias->shouldReceive('where')->once()->with('id_esa_avaliacoes', 20)->andReturnSelf();
        $resultadosAlias->shouldReceive('whereIn')->once()->with('id_aluno', Mockery::type(\Illuminate\Support\Collection::class))->andReturnSelf();
        $resultadosAlias->shouldReceive('exists')->once()->andReturn(false);

        $gboAlias = Mockery::mock('alias:App\Models\EsaAvaliacoesGbo');
        $gboAlias->shouldReceive('join')->once()->andReturnSelf();
        $gboAlias->shouldReceive('where')->once()->with('esa_avaliacoes_indice.id_esa_avaliacoes', 20)->andReturnSelf();
        $gboAlias->shouldReceive('whereIn')->once()->with('esa_avaliacoes_gbo.id_aluno', Mockery::type(\Illuminate\Support\Collection::class))->andReturnSelf();
        $gboAlias->shouldReceive('exists')->once()->andReturn(false);

        $request = Request::create('/gaviao/ajax/gerenciar-avaliacao/rap', 'DELETE', [
            'hash_rap' => encrypt('teste-20-10'),
        ]);

        $controller = new ControllerAvaliacao($request, $ownAuth);
        $response = $controller->removeRap();
        $payload = $response->getData(true);

        $this->assertSame('err', $payload['status']);
        $this->assertContains('Ocorreu um erro ao tentar remover o RAP.', $payload['response']);
    }
}

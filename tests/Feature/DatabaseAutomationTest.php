# tests/Feature/DatabaseAutomationTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class DatabaseAutomationTest extends TestCase
{
    /** @test */
    public function a_view_de_alunos_esa_esta_acessivel_e_filtrando_corretamente()
    {
        // Garante que o ambiente tenha os dados básicos (Ano de formação, etc)
        $this->artisan('db:seed');

        // Insere um aluno com QMS (deve aparecer na view)
        DB::table('alunos')->insert([
            'numero' => 123,
            'nome_completo' => 'ALUNO TESTE VIEW',
            'nome_guerra' => 'TESTE',
            'data_nascimento' => '2000-01-01',
            'data_matricula' => 1,
            'qms_id' => 1, // Not null
            'sexo' => 'M'
        ]);

        $alunoView = DB::table('vw_alunos_esa')->where('numero', 123)->first();

        $this->assertNotNull($alunoView);
        $this->assertEquals('ALUNO TESTE VIEW', $alunoView->nome_completo);
    }

    /** @test */
    public function o_trigger_de_recalculo_do_gbm_funciona_automaticamente()
    {
        $conn = 'mysql_ssaa';

        // 1. Cria Disciplina e Avaliação
        $discId = DB::connection($conn)->table('esa_disciplinas')->insertGetId([
            'id_qms' => 1, 'nome_disciplina' => 'D1', 'nome_disciplina_abrev' => 'D1', 'tfm' => 'N'
        ]);

        $avaId = DB::connection($conn)->table('esa_avaliacoes')->insertGetId([
            'id_esa_disciplinas' => $discId, 'nome_avaliacao' => 'AA', 'chamada' => 1, 'peso' => 1, 'realizacao' => now(), 'gbm' => 0
        ]);

        // 2. Insere Índices (Itens da prova)
        $indiceId = DB::connection($conn)->table('esa_avaliacoes_indice')->insertGetId([
            'id_esa_avaliacoes' => $avaId, 'nr_item' => '1', 'score_total' => 10, 'tipo_questao' => 'N'
        ]);

        DB::connection($conn)->table('esa_avaliacoes_indice')->insert([
            'id_esa_avaliacoes' => $avaId, 'nr_item' => '2', 'score_total' => 15, 'tipo_questao' => 'N'
        ]);

        // 3. Simula um UPDATE (O que dispara o Trigger)
        DB::connection($conn)->table('esa_avaliacoes_indice')->where('id', $indiceId)->update(['score_total' => 20]);

        // 4. Verifica se o GBM da avaliação foi para 35 (20 + 15)
        $avaliacao = DB::connection($conn)->table('esa_avaliacoes')->where('id', $avaId)->first();
        
        $this->assertEquals(35, $avaliacao->gbm);
    }
}
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SsaaSchemaTest extends TestCase
{
    /** @test */
    public function as_tabelas_do_modulo_ssaa_existem_no_banco_correto()
    {
        $connection = 'mysql_ssaa';

        $this->assertTrue(Schema::connection($connection)->hasTable('esa_disciplinas'));
        $this->assertTrue(Schema::connection($connection)->hasTable('esa_avaliacoes'));
        $this->assertTrue(Schema::connection($connection)->hasTable('esa_avaliacoes_indice'));
        $this->assertTrue(Schema::connection($connection)->hasTable('esa_avaliacoes_gbo'));
    }

    /** @test */
    public function o_cascateamento_de_exclusao_esta_funcionando()
    {
        $conn = 'mysql_ssaa';

        // 1. Criar Disciplina
        $discId = DB::connection($conn)->table('esa_disciplinas')->insertGetId([
            'id_qms' => 1,
            'nome_disciplina' => 'TESTE CASCATA',
            'nome_disciplina_abrev' => 'TC',
            'carga_horaria' => 10,
            'tipo_disciplina' => 'C',
            'tfm' => 'N'
        ]);

        // 2. Criar Avaliação vinculada
        $avaId = DB::connection($conn)->table('esa_avaliacoes')->insertGetId([
            'id_esa_disciplinas' => $discId,
            'nome_avaliacao' => 'AA',
            'chamada' => 1,
            'peso' => 1.0,
            'realizacao' => now(),
            'gbm' => 100
        ]);

        // 3. Verificar se existem
        $this->assertDatabaseHas('esa_avaliacoes', ['id' => $avaId], $conn);

        // 4. Deletar Disciplina e verificar se a Avaliação sumiu (Cascade)
        DB::connection($conn)->table('esa_disciplinas')->where('id', $discId)->delete();

        $this->assertDatabaseMissing('esa_avaliacoes', ['id' => $avaId], $conn);
    }
}

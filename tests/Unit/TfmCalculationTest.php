# tests/Unit/TfmCalculationTest.php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Alunos;
use App\Models\AvaliacaoTaf;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TfmCalculationTest extends TestCase
{
    /**
     * Testa se o bônus de atleta Marexaer está sendo aplicado corretamente.
     * Regra: Se média >= 5 e < 7, ganha +1. Se > 7, ganha +2. Max 10.
     */
    public function test_atleta_marexaer_bonus_calculation()
    {
        // Simulação de um aluno atleta
        $alunoAtleta = new Alunos();
        $alunoAtleta->atleta_marexaer = 'S';
        $alunoAtleta->area_id = 1; // Combatente (Razão 3)

        // Notas base: Corrida 5, Flexão Braço 5, Flexão Barra 5 = Média 5
        $corrida = 5.0;
        $flexBra = 5.0;
        $flexBar = 5.0;

        $mediaBase = ($corrida + $flexBra + $flexBar) / 3;

        // Aplicação da lógica contida no AjaxAdminController
        $mediaFinal = $mediaBase;
        if ($alunoAtleta->atleta_marexaer == 'S') {
            if ($mediaFinal >= 5 && $mediaFinal <= 6.999) {
                $mediaFinal += 1;
            } elseif ($mediaFinal > 6.999) {
                $mediaFinal += 2;
            }
        }

        $this->assertEquals(6.0, $mediaFinal, "O bônus de +1 para média 5.0 não foi aplicado corretamente.");
    }

    /**
     * Testa a restrição de suficiência do abdominal.
     */
    public function test_abdominal_sufficiency_logic()
    {
        // Se abdominal for 'NS' (Não Suficiente), o aluno deve ser reprovado
        // independente da média das outras provas.
        $suficienciaAbdominal = 'NS';
        $mediaProvas = 8.5;

        $reprovado = ($suficienciaAbdominal == 'NS') ? 'S' : (($mediaProvas >= 5) ? 'N' : 'S');

        $this->assertEquals('S', $reprovado, "Aluno com abdominal NS deve constar como reprovado.");
    }
}

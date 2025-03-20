<?php

namespace App\Http\Controllers\SSAA\Avaliacao;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Models\AnoFormacao;
use App\Models\Avaliacoes;
use App\Models\EsaAvaliacoes;
use App\Models\EsaAvaliacoesDemonstrativo;
use App\Models\EsaDisciplinas;
use App\Models\EscolhaQMS;
use App\Models\QMS;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ControllerResultados extends Controller
{

    private $_ownauthcontroller = null;
    private $_request = null;

    public function __construct(Request $request, OwnAuthController $ownauthcontroller)
    {
        $this->_ownauthcontroller = $ownauthcontroller;
        $this->_request = $request;
    }

    public function index() {}

    public function show($id) {}

    public function update($id) {}

    public function destroy($id) {}

    public function gerarND()
    {

        if (!$this->_ownauthcontroller->PermissaoCheck(41)) {
            return response()->json(['error' => true, 'message' => '<strong>ATENÇÃO: </strong></p>Usuário sem Permissão</p>']);
        }

        $param = ['AF', 'AF1', 'AF2', 'AR', 'AI', 'AD'];
        $aas = ['AA', 'AA1', 'AA2', 'AA3'];
        $acs = ['AC', 'AC1', 'AC2'];

        $esaDisciplinas = EsaDisciplinas::find($this->_request->disciplinaID);

        $esaAvaliacoes = $esaDisciplinas->esaAvaliacoes->whereNotIn('nome_avaliacao', $param);

        $turmaAlunoAvaliacao = [];

        foreach ($esaAvaliacoes as $esaAvaliacao) {
            foreach ($esaAvaliacao->esaAvaliacoesResultadosOrderByAlunos() as $avaliacaoResultado) {
                $turmaAlunoAvaliacao[$avaliacaoResultado->aluno->turmaEsa->id][$avaliacaoResultado->aluno->id][$avaliacaoResultado->esaAvaliacoes->id] = $avaliacaoResultado;
            }
        }

        ksort($turmaAlunoAvaliacao);

        //coleta os IDs dos alunos
        $alunos_ids = collect($turmaAlunoAvaliacao)->flatMap(function ($item) {
            return array_keys($item);
        })->all();

        //Remove todos os registros da disciplina
        EsaAvaliacoesDemonstrativo::where('id_esa_disciplinas', $esaDisciplinas->id)->whereNotIn('id_aluno', $alunos_ids)->delete();

        foreach ($turmaAlunoAvaliacao as $aluno) {
            foreach ($aluno as $id_aluno => $avaliacoes) {
                $arrayCalc = array('peso' => 0, 'AA' => [], 'AC' => []);
                $avaliacoes_resultados = null;
                foreach ($avaliacoes as $avaliacao) {
                    $arrayCalc[$avaliacao->esaAvaliacoes->nome_avaliacao][] = ($avaliacao->nota * $avaliacao->esaAvaliacoes->peso);
                    $arrayCalc['peso'] = $arrayCalc['peso'] + $avaliacao->esaAvaliacoes->peso;

                    $avaliacoes_resultados['avaliacoes'][] = ['id_esa_avaliacao' => $avaliacao->id_esa_avaliacoes, 'nota' => (float)$avaliacao->nota, 'peso' => (float)$avaliacao->esaAvaliacoes->peso];
                }

                $avaliacoes_resultados['ND'] = (float) number_format((array_sum($arrayCalc['AA']) + array_sum($arrayCalc['AC'])) / $arrayCalc['peso'], 3);

                EsaAvaliacoesDemonstrativo::updateOrCreate(
                    ['id_esa_disciplinas' => $esaDisciplinas->id, 'id_aluno' => $id_aluno],
                    ['avaliacoes_resultados' => json_encode($avaliacoes_resultados), 'id_operador' => session('login.operadorID')]
                );
            }
        }

        EsaAvaliacoes::where([['id_esa_disciplinas', $esaDisciplinas->id], ['nome_avaliacao', $this->_request->nome_avaliacao], ['chamada', $this->_request->chamada]])
            ->update(['retorno_aluno' => ($this->_request->has('ciente') ? 'S' : 'N')]);

        Log::channel('gaviao')
            ->info("Gerou ND da Disciplina ", [
                "id" => $esaDisciplinas->id,
                "nome_disciplina" => $esaDisciplinas->nome_disciplina_abrev,
                'id_operador' => session('login.operadorID')
            ]);

        return response()->json(['success' => true, 'message' => '<strong>ATENÇÃO: </strong></p>Ciente do Aluno Realizado</p>']);
    }

    public static function getAvaliacoesDisciplinas($disciplina_id)
    {

        //Verifica quem está com ND abaixo da média
        $filtro = EsaDisciplinas::find($disciplina_id)->esaAvaliacoesDemonstrativos->filter(function ($esaAvaliacoesDemonstrativo) {
            $avaliacoes_resultados = json_decode($esaAvaliacoesDemonstrativo->avaliacoes_resultados);
            return $avaliacoes_resultados->ND < 5;
        });

        Log::channel('gaviao')
            ->info("Consultou ND de Recuperação. ", [
                'id_operador' => session('login.operadorID')
            ]);

        return $filtro;
    }
}

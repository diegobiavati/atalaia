<?php

namespace App\Http\Controllers\SSAA\Avaliacao;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\SSAA\Calendario\ControllerAvaliacao;
use App\Models\EsaAvaliacoesIndices;
use App\Models\EsaAvaliacoesGbo;
use App\Models\AlunosClassificacao;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Models\Alunos;
use App\Models\EsaAvaliacoesResultados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\EsaAvaliacoes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ControllerLancamentoGBO extends Controller
{
    private $_ownauthcontroller = null;
    private $_request = null;

    private $_urlComboAlunos = '/gaviao/ajax/view-combo-box-alunos-rap/';
    private $_urlNavegacaoItem = '/gaviao/ajax/view-navegacao-item/';
    private $_urlLancamentoGBO = '/gaviao/ajax/view-lancamento-gbo';

    public function __construct(Request $request, OwnAuthController $ownauthcontroller)
    {
        $this->_ownauthcontroller = $ownauthcontroller;
        $this->_request = $request;
        //Log::channel('gaviao')->info("Entrou na Rotina");
    }

    public function index()
    {
        if ($this->_ownauthcontroller->PermissaoCheck([39]) && session()->has('encryptData')) {
            $id_esa_avaliacoes = explode('-', decrypt(session()->get('encryptData')))[3];

            $esaAvaliacoes = EsaAvaliacoes::find($id_esa_avaliacoes);
            $turmas = $esaAvaliacoes->esadisciplinas->qms->consultaTurmas();
            $gbm = ControllerIndiceDificuldades::getGBM()->getData()->resultado_gbm;
            $gbo = 0;

            //Verifica se é Par
            if (($gbm % 2) != 0) {
                return view('ssaa.avaliacao.indice.gbo.erro_gbm')->with('mensagem', 'O GBM [' . $gbm . '] informado não pode ser ímpar');
            }
            $alunos = collect();

            $criptografia = true;

            return view('ssaa.avaliacao.indice.gbo.index', compact('esaAvaliacoes', 'turmas', 'alunos', 'criptografia', 'gbm', 'gbo'))
                ->with('ownauthcontroller', $this->_ownauthcontroller)
                ->with('urlComboAlunos', $this->_urlComboAlunos)
                ->with('urlNavegacaoItem', $this->_urlNavegacaoItem);
        } else {
            return view('ssaa.avaliacao.indice.gbo.erro_gbm')->with('mensagem', 'Usuário sem Permissão');
        }
    }

    public function show($id)
    {
        if ($this->_ownauthcontroller->PermissaoCheck([39]) && session()->has('encryptData')) {
            $id_esa_avaliacoes = explode('-', decrypt(session()->get('encryptData')))[3];

            $esaAvaliacoes = EsaAvaliacoes::find($id_esa_avaliacoes);
            $turmas = $esaAvaliacoes->esadisciplinas->qms->consultaTurmas();
            $gbm = ControllerIndiceDificuldades::getGBM()->getData()->resultado_gbm;
            $gbo = 0;

            //Verifica se é Par
            if (($gbm % 2) != 0) {
                return view('ssaa.avaliacao.indice.gbo.erro_gbm')->with('mensagem', 'O GBM [' . $gbm . '] informado não pode ser ímpar');
            }
            $alunos = collect();

            $criptografia = true;

            return view('ssaa.avaliacao.indice.gbo.index', compact('esaAvaliacoes', 'turmas', 'alunos', 'criptografia', 'gbm', 'gbo'))
                ->with('ownauthcontroller', $this->_ownauthcontroller)
                ->with('urlComboAlunos', $this->_urlComboAlunos)
                ->with('urlNavegacaoItem', $this->_urlNavegacaoItem)
                ->with('edicao', true);
        } else {
            return view('ssaa.avaliacao.indice.gbo.erro_gbm')->with('mensagem', 'Usuário sem Permissão');
        }
    }

    public function update($id)
    {
        $retorno['status'] = 'err';
        $retorno['response'] = 'Usuário sem Permissão Ou a Sessão está expirada.';

        if ($this->_ownauthcontroller->PermissaoCheck([39]) && session()->has('encryptData')) {
            $id_indice = (int)explode('_', decrypt($id))[2];
            $id_aluno = (int)explode('_', decrypt($this->_request->id_aluno))[2];
            $score_vermelho = $this->_request->score_vermelho;
            $gbo_errado = $this->_request->gbo_errado > 0 ? $this->_request->gbo_errado : null;

            $esaAvaliacoesGbo = EsaAvaliacoesGBO::where([['id_esa_avaliacoes_indice', '=', $id_indice], ['id_aluno', '=', $id_aluno]])
                ->firstOrCreate(array('id_esa_avaliacoes_indice' => $id_indice, 'id_aluno' => $id_aluno, 'id_operador' => session('login.operadorID')));

            if (isset($esaAvaliacoesGbo)) {
                if ($score_vermelho > $esaAvaliacoesGbo->esaAvaliacoesIndices->score_total) {
                    $retorno['response'] = 'Score lançado é maior que ' . $esaAvaliacoesGbo->esaAvaliacoesIndices->score_total . '.';
                } elseif ($score_vermelho < 0) {
                    $retorno['response'] = 'Score lançado é menor que 0.';
                } elseif (is_null($score_vermelho)) {
                    $retorno['response'] = 'Informe um Score válido.';
                } else {
                    $esaAvaliacoesGbo->score_vermelho = $score_vermelho;
                    $esaAvaliacoesGbo->id_operador = session('login.operadorID');
                    $esaAvaliacoesGbo->save();

                    EsaAvaliacoesResultados::where([['id_esa_avaliacoes', '=', $esaAvaliacoesGbo->esaAvaliacoesIndices->esaAvaliacoes->id], ['id_aluno', '=', $id_aluno]])
                        ->update(['gbo_aluno' => $gbo_errado]);

                    $retorno['status'] = 'success';
                    $retorno['response'] = 'GBO Registrado.';
                }
            } else {
                $retorno['response'] = 'Entre em contato com o desenvolvedor.';
            }
        }
        return response()->json($retorno);
    }

    public function destroy($id)
    {
        $retorno['status'] = 'err';
        $retorno['response'] = 'Ocorreu um erro.';

        return response()->json($retorno);
    }

    public function viewPaginacaoLancamento()
    {
        if (
            $this->_ownauthcontroller->PermissaoCheck([39])
            && $this->_request->id_aluno
            && session()->has('encryptData')
        ) {
            $id_esa_avaliacoes = explode('-', decrypt(session()->get('encryptData')))[3];
            $id_aluno = explode('_', decrypt($this->_request->id_aluno))[1];
            $requisicao = $this->_request->requisicao ?? null;
            $item = (!is_null($this->_request->item)) ? explode('_', decrypt($this->_request->item))[2] : null;
            $selecionado = null;

            $esaAvaliacoesIndices = new EsaAvaliacoesIndices();

            $esaAvaliacoesIndices = $esaAvaliacoesIndices->getAlunoIndicesItens($id_esa_avaliacoes, $id_aluno)->get();

            for ($i = 0; $i < $esaAvaliacoesIndices->count(); $i++) {
                if (!is_null($item) && $esaAvaliacoesIndices->get($i)->id == $item) {
                    switch ($requisicao) {
                        case 'anterior':
                            $selecionado = ($i - 1);
                            break;
                        case 'proximo':
                            $selecionado = ($i + 1);
                            break;
                    }
                } elseif (is_null($esaAvaliacoesIndices->get($i)->score_vermelho)) {
                    $selecionado = $i;
                }

                if (!is_null($selecionado)) {
                    break;
                }
            }

            $edicao = ($this->_request->edicao == 'true') ? true : false;

            if (!$edicao && is_null($selecionado)) {
                $mensagem = 'Terminou!!!';
                return view('ssaa.avaliacao.indice.gbo.mensagem', compact('mensagem'))->with('gbo', $this->getGBO($id_esa_avaliacoes, $id_aluno)->getData()->resultado_gbo);
            }

            $selecionado = is_null($selecionado) ? 0 : $selecionado;

            if (!$esaAvaliacoesIndices->has($selecionado)) {
                $selecionado--;
            }

            $gbo_errado = EsaAvaliacoesResultados::where([['id_esa_avaliacoes', '=', $id_esa_avaliacoes], ['id_aluno', '=', $id_aluno]])->first()->gbo_aluno ?? 0;

            return view('ssaa.avaliacao.indice.gbo.componente-gbo', compact('esaAvaliacoesIndices', 'selecionado'))
                ->with('id_indice', $esaAvaliacoesIndices->get($selecionado)->id)
                ->with('id_aluno', $id_aluno)
                ->with('urlLancamentoGBO', $this->_urlLancamentoGBO)
                ->with('gbo', $this->getGBO($id_esa_avaliacoes, $id_aluno)->getData()->resultado_gbo)
                ->with('gbo_errado', $gbo_errado);
        }
    }

    public function viewComboBoxAlunosRap($idTurma = null, $edicao = false)
    {
        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        $idEsaAvaliacoes = explode('-', decrypt(session()->get('encryptData')))[3];
        $idTurma = explode('_', decrypt($this->_request->idTurma))[1];

        $esaAvaliacoes = EsaAvaliacoes::find($idEsaAvaliacoes);
        $idAlunosFaltas = [];

        $colecaoFaltas = collect();

        //Avaliação Recuperação...
        if ($esaAvaliacoes->nome_avaliacao == 'AR') {
            $avaliacoesDisciplinasRecuperacao = ControllerResultados::getAvaliacoesDisciplinasRecuperacao($esaAvaliacoes->esadisciplinas->id);

            $filtro = $avaliacoesDisciplinasRecuperacao->filter(function ($esaAvaliacoesDemonstrativo) use ($idTurma) {
                return $esaAvaliacoesDemonstrativo->aluno->turma_esa_id == $idTurma;
            });

            $colecaoFaltas = ControllerAvaliacao::getRecuperacaoAvaliacoes($filtro);
        }

        if ($esaAvaliacoes->chamada == 2) {
            $rapPrimeiraChamada = ControllerAvaliacao::getPrimeiraChamadaAvaliacao($esaAvaliacoes);

            if (count($rapPrimeiraChamada) == 1) {
                $colecaoFaltas = ControllerAvaliacao::getFaltasAvaliacoes($rapPrimeiraChamada[0]);
            } else {
                return view('ajax.erros.view-erro-padrao-centralizado')->with('mensagem', 'Existem avaliações lançadas incorretamente');
            }
        }

        if ($esaAvaliacoes->esadisciplinas->tfm == 'S') {
            if (isset($esaAvaliacoes->esaAvaliacoesRapTfm->first()->alunos_faltas)) {
                $idAlunosFaltas = array_column($esaAvaliacoes->esaAvaliacoesRapTfm->first()->alunos_faltas, 'id_aluno');
            }
        } else {
            if (isset($esaAvaliacoes->esaAvaliacoesRap->where('id_turmas_esa', $idTurma)->first()->alunos_faltas)) {
                $idAlunosFaltas = array_column($esaAvaliacoes->esaAvaliacoesRap->where('id_turmas_esa', $idTurma)
                    ->first()->alunos_faltas, 'id_aluno');
            }
        }

        $alunos = Alunos::join('ssaa.esa_avaliacoes', function ($join) use ($esaAvaliacoes) {
            $join->on(DB::raw($esaAvaliacoes->id), '=', 'ssaa.esa_avaliacoes.id');
        })->where([['atalaia.alunos.turma_esa_id', '=', $idTurma]])
            ->whereNotIn('atalaia.alunos.id', $idAlunosFaltas);

        if ($colecaoFaltas->count() > 0) {
            $alunos->whereIn('atalaia.alunos.id', $colecaoFaltas->where('turma_esa_id', $idTurma)->pluck('id'));
        }

        if (!$edicao) {
            //Listar somente alunos que tem lançamento pendentes;
            $alunos = $this->retornaAlunosLancamentosPendentes($alunos);
        }

        $alunos = $alunos->select('atalaia.alunos.*')->orderBy('atalaia.alunos.numero')->get();

        $criptografia = true;

        return view('ajax.componentes.componenteAlunos', compact('alunos', 'criptografia'))
            ->with('ownauthcontroller', $this->_ownauthcontroller);
    }

    private function retornaAlunosLancamentosPendentes(Builder $builder)
    {
        return $builder->join('ssaa.esa_avaliacoes_indice', function ($join) {
            $join->on('ssaa.esa_avaliacoes.id', '=', 'ssaa.esa_avaliacoes_indice.id_esa_avaliacoes');
        })->leftJoin('ssaa.esa_avaliacoes_gbo', function ($join) {
                $join->on('ssaa.esa_avaliacoes_indice.id', '=', 'ssaa.esa_avaliacoes_gbo.id_esa_avaliacoes_indice');
                $join->on('atalaia.alunos.id', '=', 'ssaa.esa_avaliacoes_gbo.id_aluno');
        })
            //->where([['atalaia.alunos.turma_esa_id', '=', $idTurma]])
            ->whereNull('ssaa.esa_avaliacoes_gbo.score_vermelho')
            //->whereNotIn('atalaia.alunos.id', $idAlunosFaltas)
            //->select('atalaia.alunos.*')
            ->groupBy(['atalaia.alunos.numero', 'atalaia.alunos.nome_guerra']);
        //->orderBy('atalaia.alunos.numero');
    }

    public static function retornaLancamentosAvaliacao(EsaAvaliacoes $esaAvaliacoes)
    {
        //Traz os alunos que tem lançamento completo de GBO
        return EsaAvaliacoesGbo::join('esa_avaliacoes_indice', 'esa_avaliacoes_gbo.id_esa_avaliacoes_indice', 'esa_avaliacoes_indice.id')
            ->join('atalaia.alunos', 'esa_avaliacoes_gbo.id_aluno', 'atalaia.alunos.id')
            ->select(DB::raw('atalaia.alunos.id AS id_aluno, 
                            COUNT(esa_avaliacoes_gbo.id_esa_avaliacoes_indice) AS lancamentos, 
                            SUM(esa_avaliacoes_indice.score_total) AS gbm,
                            SUM(esa_avaliacoes_gbo.score_vermelho) AS gbo_errado,
                            SUM(esa_avaliacoes_indice.score_total) - SUM(esa_avaliacoes_gbo.score_vermelho) AS gbo_certo,
                            ROUND((( SUM(esa_avaliacoes_indice.score_total) - SUM(esa_avaliacoes_gbo.score_vermelho)) / (SUM(esa_avaliacoes_indice.score_total)) * 10), 3) AS nota_aluno'))
            ->where('esa_avaliacoes_indice.id_esa_avaliacoes', $esaAvaliacoes->id)
            ->groupBy('atalaia.alunos.id')
            ->orderBy('atalaia.alunos.numero')
            ->havingRaw('COUNT(esa_avaliacoes_gbo.id_esa_avaliacoes_indice) = (SELECT COUNT(nr_item) FROM esa_avaliacoes_indice
                                        WHERE esa_avaliacoes_indice.id_esa_avaliacoes = ?)', [$esaAvaliacoes->id])->get();
    }

    public static function getGBO($id_esa_avaliacoes, $id_aluno)
    {
        $retorno['success'] = true;

        $gbm = ControllerIndiceDificuldades::getGBM()->getData()->resultado_gbm;

        $esaAvaliacoesIndices = new EsaAvaliacoesIndices();
        $retorno['resultado_gbo'] = ($gbm - $esaAvaliacoesIndices->getAlunoIndicesItens($id_esa_avaliacoes, $id_aluno)->sum('score_vermelho'));

        return response()->json($retorno);
    }
}

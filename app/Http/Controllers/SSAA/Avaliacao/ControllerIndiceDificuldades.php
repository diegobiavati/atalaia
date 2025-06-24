<?php

namespace App\Http\Controllers\SSAA\Avaliacao;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EditableGrid\EditableGrid;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\SSAA\Calendario\ControllerAvaliacao;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Models\AnoFormacao;
use App\Models\EsaAvaliacoes;
use App\Models\EsaAvaliacoesGbo;
use App\Models\EsaAvaliacoesIndices;
use App\Models\EsaDisciplinas;
use App\Models\QMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ControllerIndiceDificuldades extends Controller
{

    private $_ownauthcontroller = null;
    private $_request = null;
    private $_urlIndice = '/gaviao/ajax/indice-dificuldades';
    private $_urlGBM = '/gaviao/ajax/indice-dificuldades/get-gbm/0';
    private $_urlLancamentoGBO = '/gaviao/ajax/view-lancamento-gbo';
    private $_urlIndiceDisciplinas = '/gaviao/ajax/indice-dificuldades/get-disciplinas/';
    private $_urlIndiceDisciplinasProvas = '/gaviao/ajax/indice-dificuldades/get-disciplinas-provas/';

    public function __construct(Request $request, OwnAuthController $ownauthcontroller)
    {
        $this->_ownauthcontroller = $ownauthcontroller;
        $this->_request = $request;
    }

    private function encryptData()
    {

        if (strlen($this->_request->id_prova) > 10) {
            $this->_request->id_prova = explode('-', decrypt($this->_request->id_prova))[3];
        }

        $encrypt = encrypt(date('Y-m-d H:i:s') . '-' . $this->_request->id_prova);
        session()->put('encryptData', $encrypt);
    }

    public function index()
    {
        session()->forget('encryptData');
        return view('ssaa.avaliacao.indice.index')->with('urlIndice', $this->_urlIndice)
            ->with('urlGBM', $this->_urlGBM);
    }

    public function show($id)
    {
        session()->forget('encryptData');
        //Verifica se tem permissão de lançamento de indíces
        if ($this->_ownauthcontroller->PermissaoCheck([38])) {

            $cursos = FuncoesController::retornaCursoPerfilAnoFormacao(AnoFormacao::find($id));

            return view('ssaa.avaliacao.indice.form', compact('cursos'))
                ->with('ownauthcontroller', $this->_ownauthcontroller)
                ->with('urlIndiceDisciplinas', $this->_urlIndiceDisciplinas);
        } else {
            return view('ajax.erros.view-erro-padrao-centralizado')->with('mensagem', 'Usuário sem Permissão');
        }
    }

    public function update($id)
    {
        $retorno['status'] = 'err';
        $retorno['response'] = 'Informe o Nª do Item';

        if ($this->_ownauthcontroller->PermissaoCheck([38]) && session()->has('encryptData')) {

            $id_esa_avaliacoes = explode('-', decrypt(session()->get('encryptData')))[3];

            if (!isset($this->_request->nr_item)) {
                return response()->json($retorno);
            }

            $nr_item = $this->_request->nr_item;

            if ($this->_request->colname == 'nr_item' && isset($this->_request->oldValue)) {
                $nr_item = $this->_request->oldValue;
            }

            $esaAvaliacoesIndices = EsaAvaliacoesIndices::where([['id_esa_avaliacoes', '=', $id_esa_avaliacoes], ['nr_item', '=', $nr_item]])->firstOrCreate(['id_esa_avaliacoes' => $id_esa_avaliacoes], ['nr_item' => $nr_item], ['id_operador' => session('login.operadorID')]);

            if (isset($esaAvaliacoesIndices)) {
                $esaAvaliacoesIndices->{$this->_request->colname} = $this->_request->newvalue;
                $esaAvaliacoesIndices->id_operador = session('login.operadorID');

                $esaAvaliacoesIndices->save();
            }

            $retorno['status'] = 'success';
            $retorno['response'] = 'Registrado.';
            $retorno['identificador'] = $esaAvaliacoesIndices->id_esa_avaliacoes;
        }
        return response()->json($retorno);
    }

    public function destroy($id)
    {
        $retorno['status'] = 'err';
        $retorno['response'] = 'Ocorreu um erro.';

        if ($this->_ownauthcontroller->PermissaoCheck([38])) {
            $id_esa_avaliacoes = explode('-', decrypt($this->_request->id_esa_avaliacoes))[3];

            if (EsaAvaliacoesIndices::where([['id_esa_avaliacoes', '=', $id_esa_avaliacoes], ['nr_item', '=', $this->_request->nr_item]])->delete()) {
                $retorno['status'] = 'success';
                $retorno['response'] = array('mensagem' => 'Índice Removido Com Sucesso.');
            } else {
                $retorno['response'] = 'Ocorreu um erro ao deletar o item ' . $this->_request->nr_item . '.';
            }
        }

        return response()->json($retorno);
    }

    public function carregaDisciplinas()
    {
        session()->forget('encryptData');
        $disciplinas = EsaDisciplinas::where([['id_qms', '=', $this->_request->id_qms]])->get();
        if ($disciplinas->count() > 0) {
            $cursos = FuncoesController::retornaCursoPerfilAnoFormacao(AnoFormacao::find($disciplinas[0]->qms->escolhaQms->anoformacao->id));

            if (isset($this->_request->id_disciplina)) {
                $disciplinaSelecionada = null;
            }

            $cursoSelecionado = $disciplinas[0]->qms;

            //return view('ssaa.avaliacao.indice.form', compact('cursos', 'cursoSelecionado', 'disciplinas', 'disciplinaSelecionada'))
            return view('ssaa.avaliacao.indice.form', compact('cursos', 'cursoSelecionado', 'disciplinas'))
                ->with('ownauthcontroller', $this->_ownauthcontroller)
                ->with('urlIndice', $this->_urlIndice)
                ->with('urlIndiceDisciplinas', $this->_urlIndiceDisciplinas)
                ->with('urlLancamentoGBO', $this->_urlLancamentoGBO)
                ->with('urlIndiceDisciplinasProvas', $this->_urlIndiceDisciplinasProvas);
        } else {
            return $this->show(QMS::find($this->_request->id_qms)->escolhaQms->anoformacao->id);
        }
    }

    public function carregaDisciplinasProvas()
    {
        session()->forget('encryptData');
        $provas = collect();
        $criptografia = false;

        $idDisciplina = $this->_request->id_disciplina;
        if (strlen($idDisciplina) > 10) {
            $idDisciplina = explode('_', decrypt($idDisciplina))[1];
            $criptografia = true;
        }

        $avaliacoes = EsaAvaliacoes::where([['id_esa_disciplinas', '=', $idDisciplina]])->get();

        //$turmas = $avaliacoes->first()->esadisciplinas->qms->consultaTurmas();

        //Só habilita a avaliação se estiver com o RAP de todas as turmas pendentes lançadas
        foreach ($avaliacoes as $avaliacao) {
            if ($avaliacao->esadisciplinas->tfm == 'S') {
                $contador = count($avaliacao->esaAvaliacoesRapTfm);

                if ($contador > 0) {
                    $provas->push($avaliacao);
                }
            } else {
                $contador = count($avaliacao->esaAvaliacoesRap);
                $turmas = $avaliacao->esadisciplinas->qms->consultaTurmas();

                if ($avaliacao->chamada == 2) {
                    $colecaoFaltas = ControllerAvaliacao::getFaltasAvaliacoes(ControllerAvaliacao::getPrimeiraChamadaAvaliacao($avaliacao)[0]);

                    $turmas = $turmas->whereIn('id', $colecaoFaltas->pluck('turma_esa_id'));
                }

                if ($contador > 0 && $contador == count($turmas)) {
                    $provas->push($avaliacao);
                }
            }
        }

        return view('ajax.ssaa.componenteDisciplinasProvas', compact('provas', 'criptografia'));
    }

    public function carregaIndices()
    {

        if (strlen($this->_request->id_prova) > 10) {
            $id_esa_avaliacoes = explode('-', decrypt($this->_request->id_prova))[3];
        } else {
            $id_esa_avaliacoes = $this->_request->id_prova;
        }

        if ($this->_ownauthcontroller->PermissaoCheck([38]) && $id_esa_avaliacoes > 0) {

            $this->encryptData();

            //Verifica se existe lançamento de escore
            $valida = $this->validaNovoItem()->getData()->success;

            $esaAvaliacoes = EsaAvaliacoes::find($id_esa_avaliacoes);
            $avlBasico = ($esaAvaliacoes->avl_1_ano == 'S');

            $esaAvaliacoesIndices = new EsaAvaliacoesIndices();
            $esaAvaliacoesIndices = $esaAvaliacoesIndices->getOrdenadoPorItem($id_esa_avaliacoes)
                ->with('esaAvaliacoes')
                ->get();

            $editableGrid = new EditableGrid();

            $editableGrid->addColumn("id_esa_avaliacoes", "", "string", null, false, null, false, true);
            $editableGrid->addColumn("nr_item", "Nº item", "string", null, $valida);
            $editableGrid->addColumn("score_total", "Score Total", "integer(Score)", null, $valida, null, false);
            if ($avlBasico) {
                $editableGrid->addColumn("assunto_basico", "Disc B/As", "string", null, $valida, null, false);
            }
            $editableGrid->addColumn("tipo_questao", "Tipo Questão", "string", array('BD' => 'Banco de Dados', 'N' => 'Nova'), $valida);
            $editableGrid->addColumn("dificuldade_estimada", "Dif. Estimada", "string", array('F' => 'Fácil', 'M' => 'Médio', 'D' => 'Difícil', 'MD' => 'Muito Difícil'), $valida);
            // action column ("html" type), not editable
            if ($valida) {
                $editableGrid->addColumn("action", "Ações", "html", NULL, false);
            }

            $data = array();
            foreach ($esaAvaliacoesIndices as $indices) {
                $data[] = array(
                    "id_esa_avaliacoes" => encrypt(date('Y-m-d H:i:s') . '-' . $indices->id_esa_avaliacoes),
                    "nr_item" => $indices->nr_item,
                    "score_total" => $indices->score_total,
                    "tipo_questao" => $indices->tipo_questao,
                    "dificuldade_estimada" => $indices->dificuldade_estimada
                );

                if ($avlBasico) {
                    end($data);
                    $data[key($data)]['assunto_basico'] = $indices->assunto_basico;
                }
            }

            //render JSON
            return $editableGrid->renderJSON($data);
        } else {
            return response()->json(array('message', 'Você não tem permissão.'));
        }
    }

    public function validaNovoItem()
    {
        $retorno['success'] = false;
        $retorno['message'] = 'Logue-se no sistema.';

        if (session()->has('encryptData')) {
            $idEsaAvaliacao = explode('-', decrypt(session()->get('encryptData')))[3];

            $esaAvaliacoesGbo = EsaAvaliacoesGbo::join('esa_avaliacoes_indice', 'esa_avaliacoes_gbo.id_esa_avaliacoes_indice', 'esa_avaliacoes_indice.id')
                ->where('esa_avaliacoes_indice.id_esa_avaliacoes', $idEsaAvaliacao)->get();

            if ($esaAvaliacoesGbo->count() == 0) {
                $retorno['success'] = true;
                $retorno['message'] = 'Liberado.';
            } else {
                $retorno['message'] = 'Já existe lançamento(s) de escore para esse processo/prova.';
            }
        }

        return response()->json($retorno);
    }

    public static function getGBM($idAvaliacao = null)
    {
        $retorno['success'] = true;
        $retorno['resultado_gbm'] = 0;

        if ($idAvaliacao) {
            session()->forget('encryptData');
        }

        if (session()->has('encryptData')) {
            $idAvaliacao = explode('-', decrypt(session()->get('encryptData')))[3];
        }

        if ($idAvaliacao) {
            //$retorno['resultado_gbm'] = EsaAvaliacoesIndices::where([['id_esa_avaliacoes', '=', $idAvaliacao]])->sum('score_total');
            $retorno['resultado_gbm'] = EsaAvaliacoes::find($idAvaliacao)->gbm;
        }

        return response()->json($retorno);
    }
}

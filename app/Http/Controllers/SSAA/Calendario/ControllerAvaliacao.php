<?php

namespace App\Http\Controllers\SSAA\Calendario;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Models\AnoFormacao;
use App\Models\EsaAvaliacoes;
use App\Models\EsaDisciplinas;
use App\Models\QMS;
use App\Rules\ESANomeAvaliacoes;
use App\Rules\TipoAvaliacoes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ControllerAvaliacao extends Controller
{

    private $_ownauthcontroller = null;
    private $_request = null;

    public function __construct(Request $request, OwnAuthController $ownauthcontroller)
    {
        $this->_ownauthcontroller = $ownauthcontroller;
        $this->_request = $request;
    }

    private function getAnoFormacao()
    {
        if (!session()->has('_anoFormacao')) {
            return view('ajax.erros.view-erro-padrao-centralizado')->with('mensagem', 'Refaça o login.');
        }

        $decrypt = decrypt(session('_anoFormacao'));

        return AnoFormacao::find(explode('-', $decrypt)[1]);
    }

    public function getComponentAjax()
    {

        $cursoSelecionado = QMS::find($this->_request->id_curso);
        $disciplinas = EsaDisciplinas::where(['id_qms' => $cursoSelecionado->id])->get();

        $esaAvaliacoes = new EsaAvaliacoes;

        return view('ssaa.avaliacao.componenteAjax', compact('disciplinas', 'cursoSelecionado'))
            ->with('tipoAvaliacao', $esaAvaliacoes->getTodosTiposAvaliacoes())
            ->with('chamadas', $esaAvaliacoes->getTodasChamadas())
            ->with('nomeAvaliacoes', $esaAvaliacoes->getTodasAvaliacoes());
    }

    public function index()
    {

        $anoFormacao = $this->getAnoFormacao();

        if (!$anoFormacao instanceof AnoFormacao) {
            return $anoFormacao;
        }

        $cursos = FuncoesController::retornaCursoPerfilAnoFormacao($anoFormacao);

        return view("ssaa.avaliacao.form", compact('cursos', 'anoFormacao'))->with('ownauthcontroller', $this->_ownauthcontroller);
    }

    public function store()
    {
        if ($this->_ownauthcontroller->PermissaoCheck([33])) {
            $retorno['status'] = 'err';
            $retorno['response'] = [];

            $validador = $this->validaRequest();

            if ($validador->fails()) {
                $retorno['response'] = $validador->errors()->all();
            } else {
                $esaAvaliacoes = new EsaAvaliacoes;
                $esaAvaliacoes->fill(
                    [
                        'id_esa_disciplinas' => $this->_request->disciplinaID, 'nome_avaliacao' => $this->_request->nome_avaliacao, 'tipo_avaliacao' => $this->_request->tipo_avaliacao, 'chamada' => $this->_request->chamada, 'peso' => $this->_request->peso, 'proposta' => $this->_request->proposta, 'realizacao' => $this->_request->realizacao
                    ]
                );

                $esaAvaliacoes->save();

                $retorno['status'] = 'success';
                $retorno['response'] = 'Registrado com sucesso.';
            }
        }

        return response()->json($retorno);
    }

    public function show($id)
    {
        if ($this->_ownauthcontroller->PermissaoCheck([33])) {

            $anoFormacao = $this->getAnoFormacao();

            if (!$anoFormacao instanceof AnoFormacao) {
                return $anoFormacao;
            }

            $cursos = FuncoesController::retornaCursoPerfilAnoFormacao($anoFormacao);

            $esaAvaliacoes = EsaAvaliacoes::find($id);
            $cursoSelecionado = $esaAvaliacoes->esadisciplinas->qms;

            $disciplinas = EsaDisciplinas::where(['id_qms' => $cursoSelecionado->id])->get();

            return view('ssaa.avaliacao.form', compact('cursos', 'cursoSelecionado', 'disciplinas', 'esaAvaliacoes'))
                ->with('ownauthcontroller', $this->_ownauthcontroller)
                ->with('tipoAvaliacao', $esaAvaliacoes->getTodosTiposAvaliacoes())
                ->with('chamadas', $esaAvaliacoes->getTodasChamadas())
                ->with('nomeAvaliacoes', $esaAvaliacoes->getTodasAvaliacoes());
        }
    }

    public function update($id)
    {
        $retorno['status'] = 'err';
        $retorno['response'] = [];

        if ($this->_ownauthcontroller->PermissaoCheck([33])) {

            $validador = $this->validaRequest();

            if ($validador->fails()) {
                $retorno['response'] = $validador->errors()->all();
            } else {
                $esaAvaliacoes = EsaAvaliacoes::find($id);

                $esaAvaliacoes->id_esa_disciplinas = $this->_request->disciplinaID;
                $esaAvaliacoes->nome_avaliacao = $this->_request->nome_avaliacao;
                $esaAvaliacoes->tipo_avaliacao = $this->_request->tipo_avaliacao;
                $esaAvaliacoes->chamada = $this->_request->chamada;
                $esaAvaliacoes->peso = $this->_request->peso;
                $esaAvaliacoes->proposta = $this->_request->proposta;
                $esaAvaliacoes->realizacao = $this->_request->realizacao;

                if ($esaAvaliacoes->save()) {
                    $retorno['status'] = 'success';
                    array_push($retorno['response'], 'Avaliação modificada com sucesso.');
                } else {
                    array_push($retorno['response'], 'Ocorreu um erro.');
                }
            }
        }

        return response()->json($retorno);
    }

    public function destroy($id)
    {

        $retorno['status'] = 'err';
        $retorno['response'] = [];

        if ($this->_ownauthcontroller->PermissaoCheck([33])) {

            $esaAvaliacoes = EsaAvaliacoes::find($id);

            if ($esaAvaliacoes->delete()) {
                $retorno['status'] = 'success';
                $retorno['response'] = 'Deletado com sucesso.';
                return response()->json($retorno);
            } else {
                array_push($retorno['response'], 'Ocorreu um erro ao tentar remover a avaliação.');
            }
        } else {
            array_push($retorno['response'], 'Você não tem permissão para esta ação.');
        }

        return response()->json($retorno);
    }

    public function validaRequest()
    {
        return Validator::make(
            $this->_request->all(),
            [
                'disciplinaID' => 'required|numeric|exists:esa_disciplinas,id',
                'nome_avaliacao' => ['required', new ESANomeAvaliacoes],
                'tipo_avaliacao' => ['required', new TipoAvaliacoes],
                'chamada' => 'required|integer',
                'peso' => 'required|integer',
                'proposta' => 'required|date',
                'realizacao' => 'required|date'
            ],
            [
                'disciplinaID.exists' => '<b>Disciplina não cadastrada.</b>',
                'disciplinaID.required' => 'O campo <b>disciplina</b> é obrigatório.',
                'nome_avaliacao.required' => 'O campo <b>avaliação</b> é obrigatória.',
                'tipo_avaliacao.required' => 'O campo <b>tipo de avaliação</b> é obrigatório.',
                'chamada.required' => 'O campo <b>chamada</b> é obrigatória.',
                'peso.required' => 'O campo <b>peso</b> é obrigatório e deve ser um número.',
                'proposta.required' => 'O campo <b>data de proposta</b> é obrigatória.',
                'realizacao.required' => 'O campo <b>data de realização</b> é obrigatória.'
            ]
        );
    }
}

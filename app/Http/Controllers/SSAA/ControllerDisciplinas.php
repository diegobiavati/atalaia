<?php

namespace App\Http\Controllers\SSAA;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Models\AnoFormacao;
use App\Models\EsaDisciplinas;
use App\Models\QMS;
use App\Rules\Uppercase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ControllerDisciplinas extends Controller
{

    private $_ownauthcontroller = null;
    private $_request = null;
    private $_tipo_disciplina = null;

    public function __construct(Request $request, OwnAuthController $ownauthcontroller)
    {
        $this->_ownauthcontroller = $ownauthcontroller;
        $this->_request = $request;

        $this->_tipo_disciplina = (object)[(object)['id' => 'C', 'descricao' => 'Comum'], (object)['id' => 'E', 'descricao' => 'Específicas']];
    }

    public function index()
    {
        if ($this->_request->id_ano_formacao != null) {
            $anoFormacao = AnoFormacao::find($this->_request->id_ano_formacao);
            $cursos = FuncoesController::retornaCursoPerfilAnoFormacao($anoFormacao);

            return view("ssaa.disciplina.disciplinas", compact('cursos'))->with('ownauthcontroller', $this->_ownauthcontroller);
        }

        return view("ssaa.disciplina.index");
    }

    public function load()
    {
        $anoFormacao = AnoFormacao::find($this->_request->id_ano_formacao);
        $cursos = FuncoesController::retornaCursoPerfilAnoFormacao($anoFormacao);

        $cursoSelecionado = $cursos->find($this->_request->id_curso);

        $load = true;

        $disciplinas = EsaDisciplinas::where(['id_qms' => $cursoSelecionado->id])->get();

        return view("ssaa.disciplina.disciplinas", compact('cursos', 'cursoSelecionado', 'load', 'disciplinas'))->with('ownauthcontroller', $this->_ownauthcontroller);
    }

    public function form()
    {

        $cursoSelecionado = QMS::find($this->_request->id_curso);

        $cursos = array($cursoSelecionado);

        return view('ssaa.disciplina.form', compact('cursos', 'cursoSelecionado'))
            ->with('ownauthcontroller', $this->_ownauthcontroller)
            ->with('tipo_disciplina', $this->_tipo_disciplina);
    }

    public function store()
    {

        $retorno['status'] = 'err';
        $retorno['response'] = [];

        $validador = $this->validaRequest();

        if ($validador->fails()) {
            $retorno['response'] = $validador->errors()->all();
        } else {

            EsaDisciplinas::create([
                'id_qms' => $this->_request->qmsID, 'nome_disciplina' => $this->_request->nome_disciplina, 'nome_disciplina_abrev' => $this->_request->nome_disciplina_abrev, 'tipo_disciplina' => $this->_request->tipo_disciplina, 'carga_horaria' => $this->_request->carga_horaria, 'tfm' => ($this->_request->has('tfm') ? 'S' : 'N')
            ]);

            $retorno['status'] = 'success';
            $retorno['response'] = 'Registrado com sucesso.';
        }

        return response()->json($retorno);
    }

    public function destroy($id)
    {

        $retorno['status'] = 'err';
        $retorno['response'] = [];

        if ($this->_ownauthcontroller->PermissaoCheck([34])) {

            $esaDisciplinas = EsaDisciplinas::find($id);

            if ($esaDisciplinas->delete()) {
                $retorno['status'] = 'success';
                $retorno['response'] = 'Deletado com sucesso.';
                return response()->json($retorno);
            } else {
                array_push($retorno['response'], 'Ocorreu um erro ao tentar remover a disciplina.');
            }
        } else {
            array_push($retorno['response'], 'Você não tem permissão para esta ação.');
        }

        return response()->json($retorno);
    }

    public function show($id)
    {
        if ($this->_ownauthcontroller->PermissaoCheck([34])) {

            $esaDisciplinas = EsaDisciplinas::find($id);
            $cursoSelecionado = $esaDisciplinas->qms;

            $cursos = array($cursoSelecionado);

            return view('ssaa.disciplina.form', compact('cursos', 'cursoSelecionado', 'esaDisciplinas'))
                ->with('ownauthcontroller', $this->_ownauthcontroller)
                ->with('tipo_disciplina', $this->_tipo_disciplina);
        }
    }

    public function update($id)
    {
        $retorno['status'] = 'err';
        $retorno['response'] = [];

        if ($this->_ownauthcontroller->PermissaoCheck([34])) {

            $validador = $this->validaRequest();

            if ($validador->fails()) {
                $retorno['response'] = $validador->errors()->all();
            } else {
                $esaDisciplinas = EsaDisciplinas::find($id);

                $esaDisciplinas->nome_disciplina = $this->_request->nome_disciplina;
                $esaDisciplinas->nome_disciplina_abrev = $this->_request->nome_disciplina_abrev;
                $esaDisciplinas->carga_horaria = $this->_request->carga_horaria;
                $esaDisciplinas->tipo_disciplina = $this->_request->tipo_disciplina;
                $esaDisciplinas->tfm = isset($this->_request->tfm) ? 'S' : 'N';

                if ($esaDisciplinas->save()) {
                    $retorno['status'] = 'success';
                    array_push($retorno['response'], 'Disciplina modificada com sucesso.');
                } else {
                    array_push($retorno['response'], 'Ocorreu um erro.');
                }
            }
        }

        return response()->json($retorno);
    }

    public function validaRequest()
    {
        return Validator::make(
            $this->_request->all(),
            [
                'qmsID' => 'required|numeric|exists:qms,id',
                'nome_disciplina' => ['required', new Uppercase],
                'nome_disciplina_abrev' => ['required', new Uppercase],
                'carga_horaria' => 'required|numeric',
                'tipo_disciplina' => 'required'
            ],
            [
                'qmsID.exists' => '<b>Curso não cadastrado.</b>',
                'nome_disciplina.required' => 'O <b>nome completo da disciplina</b> é obrigatório.',
                'nome_disciplina_abrev.required' => 'O <b>nome abreviado da disciplina</b> é obrigatório.',
                'carga_horaria.required' => 'A <b>carga horária</b> é obrigatória.',
                'tipo_disciplina.required' => 'O <b>tipo de avaliação</b> é obrigatório.'
            ]
        );
    }
}

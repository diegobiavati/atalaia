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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ControllerDisciplinas extends Controller
{
    private $_ownauthcontroller = null;
    private $_request = null;

    public function __construct(Request $request, OwnAuthController $ownauthcontroller)
    {
        $this->_ownauthcontroller = $ownauthcontroller;
        $this->_request = $request;
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
        //$anoFormacao = AnoFormacao::find($this->_request->id_ano_formacao);
        //$cursos = FuncoesController::retornaCursoPerfilAnoFormacao($anoFormacao);
        $cursoSelecionado = QMS::find($this->_request->id_curso);
        $cursos = FuncoesController::retornaCursoPerfilAnoFormacao($cursoSelecionado->escolhaqms->anoformacao);

        $load = true;

        $disciplinas = EsaDisciplinas::where(['id_qms' => $cursoSelecionado->id])->get();

        return view("ssaa.disciplina.disciplinas", compact('cursos', 'cursoSelecionado', 'load', 'disciplinas'))->with('ownauthcontroller', $this->_ownauthcontroller);
    }

    public function form()
    {
        $cursoSelecionado = QMS::find($this->_request->id_curso);

        $cursos = array($cursoSelecionado);
        $esaDisciplinas = new EsaDisciplinas();

        return view('ssaa.disciplina.form', compact('cursos', 'cursoSelecionado'))
            ->with('ownauthcontroller', $this->_ownauthcontroller)
            ->with('tipo_disciplina', $esaDisciplinas->getTodosTiposDisciplinas());
    }

    public function showImportar()
    {
        $cursoSelecionado = QMS::find($this->_request->id_curso);

        $cursos = array($cursoSelecionado);

        $query = EsaDisciplinas::join('atalaia.qms', 'esa_disciplinas.id_qms', 'atalaia.qms.id')
            ->join('atalaia.escolha_qms', 'atalaia.qms.escolha_qms_id', 'atalaia.escolha_qms.id')
            ->join('atalaia.ano_formacao', 'atalaia.escolha_qms.ano_formacao_id', 'atalaia.ano_formacao.id')
            ->select(DB::raw('esa_disciplinas.id, esa_disciplinas.id_qms, esa_disciplinas.nome_disciplina, esa_disciplinas.nome_disciplina_abrev
                                , esa_disciplinas.carga_horaria, esa_disciplinas.tipo_disciplina, esa_disciplinas.tfm
                                , atalaia.qms.qms, atalaia.ano_formacao.formacao'))
            ->where('atalaia.qms.qms_matriz_id', $cursoSelecionado->qms_matriz_id)
            ->orderBy('atalaia.ano_formacao.formacao')
            ->orderBy('esa_disciplinas.id');

        $agrupados = (clone $query)->select(
            'atalaia.ano_formacao.formacao',
            DB::raw('COUNT(esa_disciplinas.id) as total_disciplinas')
        )->groupBy('atalaia.ano_formacao.formacao')
            ->orderBy('atalaia.ano_formacao.formacao')
            ->orderBy('esa_disciplinas.id')
            ->get();

        return view('ssaa.disciplina.importar', compact('cursoSelecionado', 'agrupados'))->with('query', $query->get());
    }

    public function Importar()
    {

        $retorno = array('status' => 'err', 'response' => []);

        $disciplinas = EsaDisciplinas::whereIn('id', $this->_request->disciplinas)->get();

        if ($disciplinas->count() > 0) {
            $cursoSelecionado = $this->_request->cursoSelecionado;
            // Filtrando apenas os registros que não existem
            $dadosFormatados = $disciplinas->reject(function ($item) use ($cursoSelecionado) {
                return EsaDisciplinas::where('id_qms', $cursoSelecionado)
                    ->where('nome_disciplina_abrev', $item['nome_disciplina_abrev'])
                    ->exists();
            })->map(function ($item) use ($cursoSelecionado) {
                return [
                    'id_qms' => $cursoSelecionado,
                    'nome_disciplina' => $item['nome_disciplina'],
                    'nome_disciplina_abrev' => $item['nome_disciplina_abrev'],
                    'tipo_disciplina' => $item['tipo_disciplina'],
                    'carga_horaria' => $item['carga_horaria'],
                    'tfm' => $item['tfm'],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            });

            // Inserindo os dados em massa na tabela
            if ($dadosFormatados->isNotEmpty()) {
                EsaDisciplinas::insert($dadosFormatados->toArray());

                $retorno['status'] = 'success';
                array_push($retorno['response'], 'Importados com Sucesso.');
            }
        } else {
            array_push($retorno['response'], 'Ocorreu um Erro!!');
            return response()->json($retorno);
        }

        /*$esaDisciplinas = new EsaDisciplinas;
        $esaDisciplinas->fill(
            [
                'id_qms' => $this->_request->cursoSelecionado,
                'nome_disciplina' => $this->_request->nome_disciplina,
                'nome_disciplina_abrev' => $this->_request->nome_disciplina_abrev,
                'tipo_disciplina' => $this->_request->tipo_disciplina,
                'carga_horaria' => $this->_request->carga_horaria,
                'tfm' => ($this->_request->has('tfm') ? 'S' : 'N')
            ]
        );

        $esaDisciplinas->save();

        $retorno['status'] = 'success';
        $retorno['response'] = 'Registrado com sucesso.';*/

        return response()->json($retorno);
    }

    public function store()
    {

        if ($this->_ownauthcontroller->PermissaoCheck([34])) {
            $retorno['status'] = 'err';
            $retorno['response'] = [];

            $validador = $this->validaRequest();

            if ($validador->fails()) {
                $retorno['response'] = $validador->errors()->all();
            } else {
                $esaDisciplinas = new EsaDisciplinas();
                $esaDisciplinas->fill(
                    [
                        'id_qms' => $this->_request->qmsID,
                        'nome_disciplina' => $this->_request->nome_disciplina,
                        'nome_disciplina_abrev' => $this->_request->nome_disciplina_abrev,
                        'tipo_disciplina' => $this->_request->tipo_disciplina,
                        'carga_horaria' => $this->_request->carga_horaria,
                        'tfm' => ($this->_request->has('tfm') ? 'S' : 'N')
                    ]
                );

                $esaDisciplinas->save();

                $retorno['status'] = 'success';
                $retorno['response'] = 'Registrado com sucesso.';
            }
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
                ->with('tipo_disciplina', $esaDisciplinas->getTodosTiposDisciplinas());
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

    public function getDisciplinas(QMS $qms)
    {
        return EsaDisciplinas::where(['id_qms' => $qms->id])->get();
    }

    public function getComboDisciplinas()
    {
        $criptografia = true;

        return view('ajax.ssaa.componenteDisciplinas', compact('criptografia'))->with('disciplinas', $this->getDisciplinas(Qms::find($this->_request->id_curso)));
    }

    public function validaRequest()
    {
        return Validator::make(
            $this->_request->all(),
            [
                'qmsID' => 'required|numeric|exists:qms,id',
                'nome_disciplina' => ['required', new Uppercase()],
                'nome_disciplina_abrev' => ['required', new Uppercase()],
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

<?php

namespace App\Http\Controllers\Ajax;

/* MODELS */


/* CONTROLLERS */

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Http\OwnClasses\ClassLog;
use App\Models\AnoFormacao;
use App\Models\CapitaniMSAccess;
use App\Models\MSAccess;
use App\Models\QMS;

setlocale(LC_ALL, "pt_BR.utf8");

class AjaxRelatoriosGaviaoController extends Controller
{
    protected $_classLog;
    protected $_ownauthcontroller;
    protected $_request;

    public function __construct(Request $request, ClassLog $classLog, OwnAuthController $ownauthcontroller)
    {
        $this->_request = $request;
        $this->_classLog = $classLog;
        $this->_ownauthcontroller = $ownauthcontroller;
    }

    public function OpcoesRelatoriosDefault(OwnAuthController $ownauthcontroller, Request $request)
    {

        $botaoAnoFormacao = FuncoesController::retornaBotaoAnoFormacao();

        $requisicao = $request->item;

        return view('ajax.relatorios.opcoes-relatorios-geral-aluno', compact('botaoAnoFormacao', 'requisicao'));
    }

    public function CarregaSelectCiaCurso(Request $request)
    {

        $qms = QMS::find($request->qmsID);

        if (isset($qms) && $qms->qms_matriz_id == 1) { //Infantaria
            return view('ajax.componenteSelectCiaInfantaria');
        } else {
            $retorno = 'failure';
        }

        return response()->json($retorno);
    }

    public function DemonstrativoNotas(OwnAuthController $ownauthcontroller, Request $request)
    {

        $idAnoFormacao = $request->id_ano_formacao;

        $cursos = FuncoesController::retornaCursoPerfilAnoFormacao(AnoFormacao::find($idAnoFormacao));

        $urlVisualizacao = 'ajax/relatorios/demonstrativo-notas/';

        return view('ajax.avaliacao.view-demonstrativo-notas', compact('ownauthcontroller', 'cursos', 'urlVisualizacao'));

        /*
        $capitaniMSAccess = CapitaniMSAccess::whereHas('aluno', function ($q) use ($idAnoFormacao) {
            $q->where([['data_matricula', '=', $idAnoFormacao]])->orWhere([['ano_formacao_reintegr_id', '=', $idAnoFormacao]]);
        })->get();

        if (count($capitaniMSAccess) > 0) {
            return view('ajax.avaliacao.view-demonstrativo-notas', compact('ownauthcontroller', 'cursos', 'urlVisualizacao'));
        } else {
            return '<div>Sem Notas de Alunos Nesse Período</div>';
        }*/
    }

    public function analiseResultadoProva()
    {

        $anoFormacao = AnoFormacao::find($this->_request->id_ano_formacao);

        $cursos = FuncoesController::retornaCursoPerfilAnoFormacao($anoFormacao);

        $urlVisualizacao = 'ajax/relatorios/analise-resultado-prova';
        $urlGetDisciplinas = 'ajax/get-combo-disciplinas/';
        $urlGetAvaliacoes = 'ajax/indice-dificuldades/get-disciplinas-provas/';
        $urlValidaVisualizacao = 'ajax/analise-resultado-prova/valida';

        return view('ajax.avaliacao.view-analise-resultado-prova', compact('cursos', 'urlVisualizacao', 'urlGetDisciplinas', 'urlGetAvaliacoes', 'urlValidaVisualizacao'))
            ->with('ownauthcontroller', $this->_ownauthcontroller);
    }

    public function AvaliacaoRecuperacao()
    {
        $anoFormacao = AnoFormacao::find($this->_request->id_ano_formacao);

        $cursos = FuncoesController::retornaCursoPerfilAnoFormacao($anoFormacao);

        $urlVisualizacao = 'ajax/relatorios/avaliacao-recuperacao';
        $urlGetDisciplinas = 'ajax/get-combo-disciplinas/';

        return view('ajax.avaliacao.view-avaliacoes-recuperacao', compact('cursos', 'urlVisualizacao', 'urlGetDisciplinas'))
            ->with('ownauthcontroller', $this->_ownauthcontroller);
    }
}

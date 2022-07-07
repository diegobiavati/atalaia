<?php

namespace App\Http\Controllers\Ajax;

/* MODELS */


/* CONTROLLERS */

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Models\AnoFormacao;
use App\Models\MSAccess;
use App\Models\QMS;

setlocale(LC_ALL, "pt_BR.utf8");

class AjaxRelatoriosGaviaoController extends Controller
{
    public function OpcoesRelatoriosDefault(OwnAuthController $ownauthcontroller, Request $request){

        $botaoAnoFormacao = FuncoesController::retornaBotaoAnoFormacao();
        
        $requisicao = $request->item;

        return view('ajax.relatorios.opcoes-relatorios-geral-aluno', compact('botaoAnoFormacao', 'requisicao'));
    }

    public function CarregaSelectCiaCurso(Request $request){

        $qms = QMS::find($request->qmsID);

        if(isset($qms) && $qms->qms_matriz_id == 1){//Infantaria
            return view('ajax.componenteSelectCiaInfantaria');
        }else{
            $retorno = 'failure';
        }

        return response()->json($retorno);
    }

    public function DemonstrativoNotas(OwnAuthController $ownauthcontroller, Request $request){
       
        $cursos = FuncoesController::retornaCursoPerfilAnoFormacao(AnoFormacao::find($request->id_ano_formacao));
        
        $urlVisualizacao = 'ajax/relatorios/demonstrativo-notas/'.$request->id_ano_formacao.'/';

        return view('ajax.avaliacao.view-demonstrativo-notas', compact('ownauthcontroller', 'cursos', 'urlVisualizacao'));
    }
}

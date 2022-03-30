<?php

namespace App\Http\Controllers\Ajax;

/* MODELS */


/* CONTROLLERS */

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Models\QMS;

setlocale(LC_ALL, "pt_BR.utf8");

class AjaxRelatoriosGaviaoController extends Controller
{
    public function OpcoesRelatoriosDefault(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

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
}

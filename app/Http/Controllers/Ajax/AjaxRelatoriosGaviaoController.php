<?php

namespace App\Http\Controllers\Ajax;

/* MODELS */


/* CONTROLLERS */

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utilitarios\FuncoesController;

setlocale(LC_ALL, "pt_BR.utf8");

class AjaxRelatoriosGaviaoController extends Controller
{
    public function OpcoesRelatoriosDefault(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        $botaoAnoFormacao = FuncoesController::retornaBotaoAnoFormacao();
        
        $requisicao = $request->item;

        return view('ajax.relatorios.opcoes-relatorios-geral-aluno', compact('botaoAnoFormacao', 'requisicao'));
    }
}

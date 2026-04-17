<?php

namespace App\Http\Controllers;

use App\Http\OwnClasses\EscolhaQMSLoader;
use App\Models\AnoFormacao;
use Illuminate\Http\Request;

class EslogController
{
    public function retornaAlunos(Request $request, EscolhaQMSLoader $escolhaQMS)
    {
        $escolhaQMS->ano_formacao = $request->ano_formacao_id;
        $escolhaQMS->escolha_qms_id = $request->escolhaQMS;

        /*$ano_formacao = AnoFormacao::find($request->ano_formacao_id);
        $ano_selecionado = (isset($ano_formacao->formacao)) ? $ano_formacao->formacao : '---';*/

        $escolha = $escolhaQMS->designacaoFinalQMSDetalhadaEslog();

        dd($escolha);
    }
}

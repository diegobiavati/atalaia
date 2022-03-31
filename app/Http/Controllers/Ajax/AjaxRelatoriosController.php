<?php

namespace App\Http\Controllers\Ajax;

/* MODELS */

use App\Models\Alunos;
use App\Models\AlunosClassificacao;
use App\Models\AlunosConselhoEscolar;
use App\Models\AlunosSitDivHistorico;
use App\Models\AnoFormacao;
use App\Models\Areas;
use App\Models\Avaliacoes;
use App\Models\AvaliacoesNotas;
use App\Models\AvaliacaoTaf;
use App\Models\Disciplinas;
use App\Models\EscolhaQMS;
use App\Models\OMCT;
use App\Models\RelatoriosConfiguracoes;
use App\Models\SituacoesDiversas;
use App\Models\ConfDemonstrativos;

/* CONTROLLERS */

use App\Http\Controllers\OwnAuthController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utilitarios\FuncoesController;
use Exception;
use Illuminate\Support\Facades\DB;

setlocale(LC_ALL, "pt_BR.utf8");

class AjaxRelatoriosController extends Controller
{
    public function OpcoesRelatoriosDefault(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        $anos_formacao = AnoFormacao::orderBy('formacao', 'desc')->get();
        if(count($anos_formacao)>0){
            $response_ano_formacao[] = '<div style="text-align: center; margin-top: 52px;">
                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">';
            $i=0;
            foreach($anos_formacao as $ano_formacao){
                $status_active_label = ($i==0)?'active':'';
                $status_checked_input = ($i==0)?'checked':'';
                $response_ano_formacao[] = '<label class="btn btn-secondary '.$status_active_label.'" style="text-align: center;" onclick="$(\'div#relatorios-content\').empty();">
                                                <input type="radio" name="ano_formacao" value="'.$ano_formacao->id.'" '.$status_checked_input.' /> '.$ano_formacao->ano_cfs.'
                                            </label>';
                $i++;
            }
            $response_ano_formacao[] = '    </div>
                                        </div>';
        } else {
            $response_ano_formacao[] = '<div style="text-align: center;">NÃO HÁ ANO DE FORMAÇÃO CADASTRADO NO SISTEMA</div>';
        }
        
        $data_ano_formacao = implode('', $response_ano_formacao);

        if($request->item=='avaliacoes'){

            // VERIFICANDO SE A CONFIGURAÇÃO ESTÁ PERMITINDO A VISUALIZAÇÃO DO DEMSNOTRATIVO

            $rel_conf = RelatoriosConfiguracoes::find(1);

            if($rel_conf->valor==0 && !$ownauthcontroller->PermissaoCheck(1)){
                $demonstrativos_notas = '';
            } else {
                $demonstrativos_notas = '   <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'demonstrativo-notas\');">Demonstrativo de notas <b>(POR UETE)</b></a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'demonstrativo-notas-por-aluno\');">Demonstrativo de notas <b>(POR ALUNO)</b></a></li>';                
            }

            if($ownauthcontroller->PermissaoCheck(1)){
                $pronto_de_faltas = '<li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'pronto-de-faltas\');">Pronto de faltas <b>(RELAÇÃO GERAL)</b></a></li>';
            } else {
                $pronto_de_faltas = '';
            }

            if($ownauthcontroller->PermissaoCheck(1)){
                $pronto_lancamento_notas = '<li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'pronto-lancamento-notas\');">Pronto do lançamento de notas <b>(RELAÇÃO GERAL)</b></a></li>';
            } else {
                $pronto_lancamento_notas = '';
            }
            if($ownauthcontroller->PermissaoCheck(1)){
                $pronto_lancamento_notas_ar = '<li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'pronto-lancamento-notas-ar\');">Pronto do lançamento de notas nas ARs <b>(RELAÇÃO GERAL)</b></a></li>';
            } else {
                $pronto_lancamento_notas_ar = '';
            }

            if($ownauthcontroller->PermissaoCheck(1)){
                $pronto_lancamento_taf = '<li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'pronto-lancamento-taf\');">Pronto do lançamento do TFM <b>(RELAÇÃO GERAL)</b></a></li>';
            } else {
                $pronto_lancamento_taf = '';
            }

            if($ownauthcontroller->PermissaoCheck(1)){
                $pronto_lancamento_taf = '<li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'pronto-lancamento-taf\');">Pronto do lançamento do TFM <b>(RELAÇÃO GERAL)</b></a></li>';
            } else {
                $pronto_lancamento_taf = '';
            }




            $data['modalTitle'] = '<h5><b>AVALIAÇÕES</b></h5>';
            $data['response'] = '  '.$data_ano_formacao.'
                                    <div style="margin: 46px auto; text-align: center; width: 780px;">
                                        <ul class="list-group list-group-flush" style="margin-top: 44px;">
                                            '.$pronto_de_faltas.'
                                            '.$pronto_lancamento_notas.'
                                            '.$pronto_lancamento_notas_ar.'
                                            '.$pronto_lancamento_taf.'
                                            '.$demonstrativos_notas.'
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'resultado-avaliacao-por-nota\');">Resultado de avaliação por média maior ou menor que 5 <b>(POR UETE)</b></a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'recibo-demonstrativo\');">Recibo de entrega do GRAU do aluno <b>(POR UETE)</b></a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'recibo-demonstrativo-por-aluno\');">Recibo de entrega do GRAU do aluno <b>(POR ALUNO)</b></a></li>
                                            </ul>
                                            <div id="relatorios-content" style="margin-top: 24px;"></div>
                                            </div>';
                                            
        } else if($request->item=='alunos'){

            if($ownauthcontroller->PermissaoCheck(1)){
                $classificacao_geral = '<li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'classificacao-final-aluno\');">Classificação FINAL do Baśico CFS</a></li>';
            } else {
                $classificacao_geral = '';
            } 

            if($ownauthcontroller->PermissaoCheck(1)){
                $alunos_aprovados_conselho = '<li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'alunos-conselho-escolar\');">Alunos aprovados em Conselho de Ensino</a></li>';
            } else {
                $alunos_aprovados_conselho = '';
            }            
                                            
            $data['modalTitle'] = '<h5><b>ALUNOS</b></h5>';
            $data['response'] = '  '.$data_ano_formacao.'
                                    <div style="margin: 46px auto; text-align: center; width: 780px;">
                                        <ul class="list-group list-group-flush" style="margin-top: 44px;">
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'relacao-geral-alunos\');">Relação geral de alunos</a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'relacao-atletas-marexaer\');">Relação de atletas MAREXAER</a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'alunos-situacoes-diversas\');">Alunos em situações diversas</a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'alunos-sit-div-hist-escolar\');">Alunos em situações diversas <b>(HISTÓRICO ESCOLAR)</b></a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'alunos-sem-cadastro-telegram\');">Alunos sem registro no TELEGRAM</a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'alunos-em-recuperacao-por-disciplinas\');">Alunos em Recuperação <b>(POR DISCIPINA)</b></a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'alunos-em-recuperacao\');">Alunos em Recuperação <b>(TABELA DE CLASSIFICAÇÃO GERAL)</b></a></li>
                                            '.$alunos_aprovados_conselho.'
                                            '.$classificacao_geral.'
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'ficha-individual-aluno\');">Ficha Individual do Aluno(a)s</a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'view-frad-aluno\');">Ficha Registro para Acompanhamento do Discente <b>(FRAD)</b></a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'view-rod-aluno\');">Relatório de Observação do Discente <b>(ROD)</b></a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'view-ficha-disciplinar\');">Ficha Disciplinar do Aluno(a)s <b>(FATD)</b></a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'view-relacao-punidos\');">Relação de Aluno(a)s Punidos</a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'view-relacao-reprovados\');">Alunos Reprovados <b>(Conselho e Recuperação)</b></a></li>
                                        </ul>
                                        <div id="relatorios-content" style="margin-top: 24px;"></div>
                                    </div>';
        } else if($request->item=='voluntarios-aviacao'){
            $data['modalTitle'] = '<h5><b>VOLUNTÁRIOS PARA AVIAÇÃO</b></h5>';
            $data['response'] = '  '.$data_ano_formacao.'
                                    <div style="margin: 46px auto; text-align: center; width: 780px;">
                                        <ul class="list-group list-group-flush" style="margin-top: 44px;">
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'relatorio-voluntarios-aviacao\');">Voluntários para QMS aviação</a></li>
                                        </ul>
                                        <div id="relatorios-content" style="margin-top: 24px;"></div>
                                    </div>';
        } else if($request->item=='escolha-qms'){

            if($ownauthcontroller->PermissaoCheck(1)){
                $relatoriosdeescolha = '<li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'relatorios-escolha-qms\');">Relatórios de Escolha de QMS</a></li>';
            } else {
                $relatoriosdeescolha = '';
            }             

            $data['modalTitle'] = '<h5><b>ESCOLHA DE QMS</b></h5>';
            $data['response'] = '  '.$data_ano_formacao.'
                                    <div style="margin: 46px auto; text-align: center; width: 780px;">
                                        <ul class="list-group list-group-flush" style="margin-top: 44px;">
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'alunos-sem-escolha-qms\');">Alunos que não fizeram suas escolhas</a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'comprovante-escolha-qms\');">Comprovante de preenchimento <b>(POR UETE)</b></a></li>
                                            '.$relatoriosdeescolha.'
                                        </ul>
                                        <div id="relatorios-content" style="margin-top: 24px;"></div>
                                    </div>';                
        
        
        } else if($request->item=='avaliacoes-ssaa'){


            $data['modalTitle'] = '<h5><b>Dados e Análise Estatística</b></h5>';
            $data['response'] = '  '.$data_ano_formacao.'
                                    <div style="margin: 46px auto; text-align: center; width: 780px;">
                                        <ul class="list-group list-group-flush" style="margin-top: 44px;">
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'pronto-de-faltas\');">Pronto de faltas em avaliações</a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'alunos-em-recuperacao-por-disciplinas\');">Alunos em recuperação <b>(POR DISCIPINA)</b></a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'dados-estatisticos-de-avaliacoes\');">Dados estatísticos de avaliações <b>(GERAL)</b></a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'analise-parcial-provas\');">Análise parcial do resultado de provas</a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'analise-parcial-disciplinas\');">Análise parcial do resultado de disciplinas</a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'analise-parcial-pb\');">Análise parcial do resultado do período básico</a></li>
                                            <!--<li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'resultado-final-pb\');">RFPB</a></li>-->
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'dados-estatistico-gerais\');">Dados Estatísticos Gerais</a></li>
                                        </ul>
                                        <div id="relatorios-content" style="margin-top: 24px;"></div>
                                    </div>';                
        } else if($request->item=='mapa'){


            $data['modalTitle'] = '<h5><b>Mapa de Efetivo</b></h5>';
            $data['response'] = '  '.$data_ano_formacao.'
                                    <div style="margin: 46px auto; text-align: center; width: 780px;">
                                        <ul class="list-group list-group-flush" style="margin-top: 44px;">
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'mapa-efetivo-geral\');">Situação Geral</a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'mapa-efetivo-desligado\');">Efetivo Desligado</b></a></li>
                                            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent(\'mapa-evasao-escolar\');">Evasão Escolar</b></a></li>
                                        </ul>
                                        <div id="relatorios-content" style="margin-top: 24px;"></div>
                                    </div>';                
        }
    
        return $data;

    }

    public function DadosEstatisticosDeAvaliacoes(Request $request){


        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.dados_estatisticos_de_avaliacoes').'" method="get" target="_blank">';

        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';

        $disciplinas = Disciplinas::where('ano_formacao_id', $request->id_ano_formacao)->get(['id']);
        foreach($disciplinas as $disciplina){
            $disciplinasID[] = $disciplina->id;
        }

        $disciplinasID = (isset($disciplinasID))?array_unique($disciplinasID):array();        

        /* SELECIONANDO AS AVALIAÇÕES DAS $disciplinasID COM DATA E HORA MENOR QUE A ATUAL */

        $avaliacoes = Avaliacoes::whereIn('disciplinas_id', $disciplinasID)->where('data', '<', date('Y-m-d'))
                                                                           ->orderBy('disciplinas_id', 'asc')
                                                                           ->orderBy('nome_abrev', 'asc')
                                                                           ->get();

        
        $data[] = '<div style="margin-top: 24px;">';
        $data[] = '<select name="avaliacaoID" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma avaliação</option>';
        foreach($avaliacoes as $avaliacao){
            if($avaliacao->avaliacao_recuperacao==0){
                $data[] = '<option value="'.$avaliacao->id.'">'.$avaliacao->disciplinas->nome_disciplina.' - '.$avaliacao->nome_completo.' ('.$avaliacao->nome_abrev.' - '.$avaliacao->chamada.'ª chamada)</option>' ;
            } else {
                $data[] = '<option value="'.$avaliacao->id.'">'.$avaliacao->disciplinas->nome_disciplina.' - '.$avaliacao->nome_completo.'</option>' ;
            }
        }

        $data[] = '</select>';
        $data[] = '</div>';

        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Analizar resultados</button>
                    </div>';        
        $data[] = '</form>';
        
        return implode('', $data);

    }

    public function AnaliseParcialProvas(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        /* FAZENDO A VERIFICAÇÃO DE PERMISSAO DO USUÁRIO */
        
        if($ownauthcontroller->PermissaoCheck(1)) {
            $omcts = OMCT::get();            
        } else {
            $omcts = OMCT::where('id', session()->get('login.omctID'))->get();            
        }        


        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.analise_parcial_provas').'" method="get" target="_blank">';

        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';

        $disciplinas = Disciplinas::where('ano_formacao_id', $request->id_ano_formacao)->get(['id']);
        foreach($disciplinas as $disciplina){
            $disciplinasID[] = $disciplina->id;
        }

        $disciplinasID = (isset($disciplinasID))?array_unique($disciplinasID):array();        

        /* SELECIONANDO AS AVALIAÇÕES DAS $disciplinasID COM DATA E HORA MENOR QUE A ATUAL */

        $avaliacoes = Avaliacoes::whereIn('disciplinas_id', $disciplinasID)->where('data', '<', date('Y-m-d'))
                                                                           ->orderBy('disciplinas_id', 'asc')
                                                                           ->orderBy('nome_abrev', 'asc')
                                                                           ->get();

        $data[] = '<div style="margin-top: 24px;">';
                                                                   
        $data[] = '<select name="omctID" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma UETE</option>';
                                                                   
        //if($ownauthcontroller->PermissaoCheck(1)){
            $data[] = '<option value="todas_omct">TODAS AS UETE</option>';
        //}

        foreach($omcts as $omct){
        
            if($omct->id!=1){
                $data[] = '<option value="'.$omct->id.'">'.$omct->omct.'</option>' ;
            }
        
        }
                                                                           
        $data[] = '</select>';
        $data[] = '</div>';        
        
        $data[] = '<div style="margin-top: 24px;">';
        $data[] = '<select name="avaliacaoID" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma avaliação</option>';
        foreach($avaliacoes as $avaliacao){
            if($avaliacao->avaliacao_recuperacao==0){
                $data[] = '<option value="'.$avaliacao->id.'">'.$avaliacao->disciplinas->nome_disciplina.' - '.$avaliacao->nome_completo.' ('.$avaliacao->nome_abrev.' - '.$avaliacao->chamada.'ª chamada)</option>' ;
            } else {
                $data[] = '<option value="'.$avaliacao->id.'">'.$avaliacao->disciplinas->nome_disciplina.' - '.$avaliacao->nome_completo.'</option>' ;
            }
        }

        $data[] = '</select>';
        $data[] = '</div>';


        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Visualizar relação</button>
                    </div>';        
        $data[] = '</form>';
        
        return implode('', $data);

    }

    public function AnaliseParcialDisciplinas(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        /* FAZENDO A VERIFICAÇÃO DE PERMISSAO DO USUÁRIO */
                
        if($ownauthcontroller->PermissaoCheck(1)) {
            $uetes = OMCT::get();            
        } else {
            $uetes = OMCT::where('id', session()->get('login.omctID'))->get();            
        } 

        $disciplinas = Disciplinas::where('ano_formacao_id', $request->id_ano_formacao)->get();

        return view('ajax.view-analise-parcial-disciplinas', compact('ownauthcontroller', 'uetes', 'disciplinas'))->with('ano_formacao_id', $request->id_ano_formacao);

    }

    public function AnaliseParcialNPB(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        /* FAZENDO A VERIFICAÇÃO DE PERMISSAO DO USUÁRIO */
        
        if($ownauthcontroller->PermissaoCheck(1)) {
            $omcts = OMCT::get();            
        } else {
            $omcts = OMCT::where('id', session()->get('login.omctID'))->get();            
        }        


        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.analise_parcial_npb').'" method="get" target="_blank">';

        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';

        $data[] = '<div style="margin-top: 24px;">';
                                                                   
        $data[] = '<select name="omctID" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma UETE</option>';
                                                                   
        //if($ownauthcontroller->PermissaoCheck(1)){
            $data[] = '<option value="todas_omct">TODAS AS UETE</option>';
        //}

        foreach($omcts as $omct){
        
            if($omct->id!=1){
                $data[] = '<option value="'.$omct->id.'">'.$omct->omct.'</option>' ;
            }
        
        }
                                                                           
        $data[] = '</select>';
        $data[] = '</div>';        

        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Visualizar relação</button>
                    </div>';        
        $data[] = '</form>';
        
        return implode('', $data);

    }    

    public function AlunosEmRecPorDisciplina(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        // SELECIONANDO AS DISCIPLINAS DO ANO DE FORMAÇÃO SELECIONADO NO CHECKBOX

        $disciplinas = Disciplinas::where('ano_formacao_id',$request->id_ano_formacao)->orderBy('nome_disciplina', 'asc')->get();

        $data[] = ' <div style="color: #DF7401;">
                        <b>ATENÇÃO:</b>
                        <p>
                            Verifique se já encontram-se lançadas todas as notas de avaliações referente a disciplina analisada 
                        </p>
                    </div>';

        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.alunos_em_recuperacao_em_disciplinas').'" method="get" target="_blank">';

        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';

        $data[] = '<select name="disciplina_id" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma disciplina</option>';
        foreach($disciplinas as $disciplina){
            $data[] = '<option value="'.$disciplina->id.'">'.$disciplina->nome_disciplina.'</option>';
        }
        $data[] = '</select>';
        $data[] = '</div>';

        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Visualizar relação</button>
                    </div>';        
        $data[] = '</form>';
        
        return implode('', $data);

    }

    public function RelatoriosEscolhaQMS(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        // SELECIONANDO TODAS AS ESCOLHAS DE QMS DO ANO DE FORMAÇÃO SELECIONADO NO CHECKBOX

        $escolhas_qms = EscolhaQMS::where('ano_formacao_id', $request->id_ano_formacao)->orderBy('data_hora_inicio', 'desc')->get();


        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.relatorios_escolha_qms').'" method="get" target="_blank">';

        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';

        $data[] = '<select name="escolhaQMS" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma escolha de QMS</option>';
        foreach($escolhas_qms as $escolha_qms){
            $data[] = '<option value="'.$escolha_qms->id.'">'.$escolha_qms->identificador.'</option>';
        }
        $data[] = '</select>';

        $data[] = '<div style="margin-top: 24px;">';
        $data[] = '<select name="tipo_relatorio" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma opção</option>';        
        $data[] = '<option value="13">Tabela Geral Combatente/Logistica - Segmento Masculino - (Detalhada)</option>';        
        $data[] = '<option value="14">Tabela Geral Combatente/Logistica - Segmento Feminino - (Detalhada)</option>';        
        /*$data[] = '<option value="15">Tabela Geral Combatente/Logistica - Segmento Masculino - (Detalhada com reprovados)</option>';        
        $data[] = '<option value="16">Tabela Geral Combatente/Logistica - Segmento Feminino - (Detalhada com reprovados)</option>';        
        $data[] = '<option value="1">Tabela Geral Combatente/Logistica - Segmento Masculino</option>';        
        $data[] = '<option value="2">Tabela Geral Combatente/Logistica - Segmento Feminino</option>';        
        $data[] = '<option value="7">Tabela Geral Combatente/Logistica - Segmento Masculino (Prévia com reprovados)</option>';        
        $data[] = '<option value="8">Tabela Geral Combatente/Logistica - Segmento Feminino (Prévia com reprovadas)</option>';        
        $data[] = '<option value="5">Relação de alunos por QMS - Segmento Masculino</option>';        
        $data[] = '<option value="6">Relação de alunos por QMS - Segmento Feminino</option>';        
        $data[] = '<option value="9">Relação de alunos por QMS - Segmento Masculino (Prévia com reprovados)</option>';        
        $data[] = '<option value="10">Relação de alunos por QMS - Segmento Feminino (Prévia com reprovadas)</option>';        
        $data[] = '<option value="3">Aviação - Segmento Masculino</option>';        
        $data[] = '<option value="4">Aviação - Segmento Feminino</option>';        
        $data[] = '<option value="11">Aviação - Segmento Masculino (Prévia com reprovados)</option>';        
        $data[] = '<option value="12">Aviação - Segmento Feminino (Prévia com reprovadas)</option>';*/       
        $data[] = '</select>';
        $data[] = '</div>';

        $data[] = view('relatorios.template-requisicao-html-excel');

        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Visualizar</button>
                    </div>';
        
        $data[] = '</form>';

        return implode('', $data);       

    }

    public function AlunosSemEscolhaQMS(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){
        
        /* FAZENDO A VERIFICAÇÃO DE PERMISSAO DO USUÁRIO */
        
        if($ownauthcontroller->PermissaoCheck(1)) {
            $omcts = OMCT::get();            
        } else {
            $omcts = OMCT::where('id', session()->get('login.omctID'))->get();            
        }

        // SELECIONANDO TODAS AS ESCOLHAS DE QMS DO ANO DE FORMAÇÃO SELECIONADO NO CHECKBOX

        $escolhas_qms = EscolhaQMS::where('ano_formacao_id', $request->id_ano_formacao)->orderBy('data_hora_inicio', 'desc')->get();

        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.alunos_nao_escolheram_qms').'" method="get" target="_blank">';

        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';

        $data[] = '<select name="escolhaQMS" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma escolha de QMS</option>';
        foreach($escolhas_qms as $escolha_qms){
            $data[] = '<option value="'.$escolha_qms->id.'">'.$escolha_qms->identificador.'</option>';
        }
        $data[] = '</select>';

        $data[] = '<div style="margin-top: 24px;">';

        $data[] = '<select name="omctID" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma UETE</option>';

        if($ownauthcontroller->PermissaoCheck(1)){
            $data[] = '<option value="todas_omct">TODAS AS UETE</option>';
        }
        foreach($omcts as $omct){
            if($omct->id!=1){
                $data[] = '<option value="'.$omct->id.'">'.$omct->omct.'</option>' ;
            }
        }
        
        $data[] = '</select>';
        $data[] = '</div>';

        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Gerar relação</button>
                    </div>';
        
        $data[] = '</form>';

        return implode('', $data);
    }

    public function ComprovanteEscolhaQMS(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){
        
        /* FAZENDO A VERIFICAÇÃO DE PERMISSAO DO USUÁRIO */
        
        if($ownauthcontroller->PermissaoCheck(1)) {
            $omcts = OMCT::get();            
        } else {
            $omcts = OMCT::where('id', session()->get('login.omctID'))->get();            
        }

        // SELECIONANDO TODAS AS ESCOLHAS DE QMS DO ANO DE FORMAÇÃO SELECIONADO NO CHECKBOX

        $escolhas_qms = EscolhaQMS::where('ano_formacao_id', $request->id_ano_formacao)->orderBy('data_hora_inicio', 'desc')->get();


        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.comprovante_escolha_qms').'" method="get" target="_blank">';

        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';

        $data[] = '<select name="escolhaQMS" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma escolha de QMS</option>';
        foreach($escolhas_qms as $escolha_qms){
            $data[] = '<option value="'.$escolha_qms->id.'">'.$escolha_qms->identificador.'</option>';
        }
        $data[] = '</select>';

        $data[] = '<div style="margin-top: 24px;">';

        $data[] = '<select name="omctID" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma UETE</option>';

        foreach($omcts as $omct){
            if($omct->id!=1){
                $data[] = '<option value="'.$omct->id.'">'.$omct->omct.'</option>' ;
            }
        }
        
        $data[] = '</select>';
        $data[] = '</div>';

        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Gerar relação</button>
                    </div>';
        
        $data[] = '</form>';

        return implode('', $data);
    }    

    public function AlunosConselhoEscolar(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){
        
        if($ownauthcontroller->PermissaoCheck(1)){
            $data[] = ' <form id="submit-relatorio" action="'.route('relatorios.lista_alunos_conselho').'" method="get" target="_blank">';

            $data[] = ' <input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';
            $data[] = ' <div style="margin-top: 24px;">
                            <button id="submit-relatorio" type="button" class="btn btn-primary" style="" onclick="$(\'form#submit-relatorio\').submit();">Gerar relação</button>
                        </div>';

            $data[] = ' </form>';            

        } else {
            $data[] = 'Não autorizado';
        }
        
        return implode('', $data);
    }

    public function RelacaoAtletasMarexaer(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){
        
        /* FAZENDO A VERIFICAÇÃO DE PERMISSAO DO USUÁRIO */
        
        if($ownauthcontroller->PermissaoCheck(1)) {
            $omcts = OMCT::get();            
        } else {
            $omcts = OMCT::where('id', session()->get('login.omctID'))->get();            
        }

        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.relacao_atletas_marexaer').'" method="get" target="_blank">';

        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';
        $data[] = '<select name="omctID" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma UETE</option>';

        if($ownauthcontroller->PermissaoCheck(1)){
            $data[] = '<option value="todas_omct">TODAS AS UETE</option>';
        }
        foreach($omcts as $omct){
            if($omct->id!=1){
                $data[] = '<option value="'.$omct->id.'">'.$omct->omct.'</option>' ;
            }
        }
        
        $data[] = '</select>';

        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Gerar relação</button>
                    </div>';
        
        $data[] = '</form>';

        return implode('', $data);

    }

    public function ResultadoAvaliacaoPorNota(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        /* FAZENDO A VERIFICAÇÃO DE PERMISSAO DO USUÁRIO */
        
        if($ownauthcontroller->PermissaoCheck(1)) {
            $omcts = OMCT::get();            
        } else {
            $omcts = OMCT::where('id', session()->get('login.omctID'))->get();            
        }

        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.resultado_avaliacao_por_nota').'" method="get" target="_blank">';

        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';

        $data[] = '<select name="omctID" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma UETE</option>';

        foreach($omcts as $omct){
            if($omct->id!=1){
                $data[] = '<option value="'.$omct->id.'">'.$omct->omct.'</option>' ;
            }
        }
        
        $data[] = '</select>';

        /* SELECIONANDO TODAS DISCIPLINAS DO ANO DE FORMAÇÃO SELECIONADO */
        
        $disciplinas = Disciplinas::where('ano_formacao_id', $request->id_ano_formacao)->get(['id']);
        foreach($disciplinas as $disciplina){
            $disciplinasID[] = $disciplina->id;
        }

        $disciplinasID = (isset($disciplinasID))?array_unique($disciplinasID):array();


        /* SELECIONANDO AS AVALIAÇÕES DAS $disciplinasID COM DATA E HORA MENOR QUE A ATUAL */

        $avaliacoes = Avaliacoes::whereIn('disciplinas_id', $disciplinasID)->where('data', '<', date('Y-m-d'))
                                                                           ->orderBy('disciplinas_id', 'asc')
                                                                           ->orderBy('nome_abrev', 'asc')
                                                                           ->get();

        if(count($avaliacoes)>0){
        
            $data[] = '<div style="margin-top: 24px;">';
            $data[] = '<select name="avaliacaoID" class="custom-select required_to_show_button">';
            $data[] = '<option value="0" selected>Selecione uma avaliação</option>';
            foreach($avaliacoes as $avaliacao){
                if($avaliacao->avaliacao_recuperacao==0){
                    $data[] = '<option value="'.$avaliacao->id.'">'.$avaliacao->disciplinas->nome_disciplina.' - '.$avaliacao->nome_completo.' ('.$avaliacao->nome_abrev.' - '.$avaliacao->chamada.' chamada)</option>' ;
                } else {
                    $data[] = '<option value="'.$avaliacao->id.'">'.$avaliacao->disciplinas->nome_disciplina.' - '.$avaliacao->nome_completo.'</option>' ;
                }
            }

            $data[] = '</select>';
            $data[] = '</div>';

        } else {

            $data[] = '<div style="text-align: center;">SEM AVALIAÇÕES PARA EXIBIÇÃO</div>';
        }        
        
        $data[] = '<div style="margin-top: 24px;">';
        $data[] = '<select name="notas_minimas" class="custom-select required_to_show_button">';
        $data[] = '     <option value="0" selected>Selecione uma das opções</option>';
        $data[] = '     <option value="1">Nota maior ou igual a 5,000</option>';
        $data[] = '     <option value="2">Nota menor que 5,000</option>';
        $data[] = '</select>';
        $data[] = '</div>';


        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Gerar relação</button>
                    </div>';
        
        $data[] = '</form>';

        return implode('', $data);

    }

    public function ClassificacaoFinalAluno(OwnAuthController $ownauthcontroller, Request $request) {
        
        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.classificacao_geral').'" method="get" target="_blank">';
        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';
        $data[] = '<div style="margin-top: 24px;">';
        $data[] = '<select name="options_class_geral" class="custom-select required_to_show_button">';
        $data[] = '     <option value="0" selected>Selecione uma das opções</option>';
        $data[] = '     <option value="9">Classificação Geral por área com ND | N1 | Mensão | Classificação</option>';
        $data[] = '     <option value="10">Classificação Geral QMS</option>';
        //$data[] = '     <option value="1">Classificação Geral do PB (Aprovados)</option>';
        //$data[] = '     <option value="2">Classificação Geral do PB (Reprovados inclusive)</option>';
        //$data[] = '     <option value="5">Classificação por Area Masculino e Feminino (Aprovados)</option>';
        //$data[] = '     <option value="8">Classificação por Area Masculino e Feminino (Reprovados inclusive)</option>';
        //$data[] = '     <option value="3">Classificação Segmento Masculino por áera (Aprovados)</option>';
        //$data[] = '     <option value="6">Classificação Segmento Masculino por áera (Reprovados inclusive)</option>';
        //$data[] = '     <option value="4">Classificação Segmento Feminino por áera (Aprovadas)</option>';
        //$data[] = '     <option value="7">Classificação Segmento Feminino por áera (Reprovadas inclusive)</option>';
        $data[] = '</select>';
        $data[] = '</div>';
        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Visualizar</button>
                    </div>';        
        $data[] = '</form>';

        return implode('', $data);

    }

    public function ProntoLancamentoTAF(OwnAuthController $ownauthcontroller, Request $request) {
        
        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.pronto_lancamento_taf').'" method="get" target="_blank">';
        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';
        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" onclick="$(\'form#submit-relatorio\').submit();">Visualizar</button>
                    </div>';        
        $data[] = '</form>';

        return implode('', $data);

    }

    public function AlunosRecuperacao(OwnAuthController $ownauthcontroller, Request $request) {
        
        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.alunos_em_recuperacao').'" method="get" target="_blank">';
        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';
        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" onclick="$(\'form#submit-relatorio\').submit();">Visualizar</button>
                    </div>';        
        $data[] = '</form>';

        return implode('', $data);

    }

    public function RelatorioVoluntariosAviacao(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        /* FAZENDO A VERIFICAÇÃO DE PERMISSAO DO USUÁRIO */
        
        if($ownauthcontroller->PermissaoCheck(1)) {
            $omcts = OMCT::get();            
        } else {
            $omcts = OMCT::where('id', session()->get('login.omctID'))->get();            
        }

        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.relacao_voluntarios_qms_aviacao').'" method="get" target="_blank">';

        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';

        $data[] = '<select name="omctID" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma UETE</option>';
        if($ownauthcontroller->PermissaoCheck(1)){
            $data[] = '<option value="todas_omct">TODAS AS UETE</option>';
        }
        foreach($omcts as $omct){
            if($omct->id!=1){
                $data[] = '<option value="'.$omct->id.'">'.$omct->omct.'</option>' ;
            }
        }
        
        $data[] = '</select>';
        
        $data[] = '<div style="margin-top: 24px;">';
        $data[] = '<select name="filtro_voluntarios" class="custom-select required_to_show_button">';
        $data[] = '     <option value="0" selected>Selecione uma das opções</option>';
        $data[] = '     <option value="1">Voluntários</option>';
        $data[] = '     <option value="2">Selecionados para os Exames Complementares</option>';
        $data[] = '     <option value="3">Aprovados na IS</option>';
        $data[] = '     <option value="4">Aprovados na AVL PSC</option>';
        $data[] = '     <option value="5">Aptos</option>';
        $data[] = '     <option value="6">Inaptos</option>';
        $data[] = '</select>';
        $data[] = '</div>';

        $data[] = ' <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
                        <h4 style="text-align: center; margin-bottom: 12px;">CONTENDO OS ADICIONAIS</h4>';        

        $data[] = ' <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="ordem" name="ordem" value="1" />
                        <label class="custom-control-label" for="ordem">Ordem</label>
                    </div>';                        

        $data[] = '</div>';           

        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Gerar relação</button>
                    </div>';
        
        $data[] = '</form>';           


        return implode('', $data);

    }

    public function RelacaoAlunosSituacaoDiversas(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        /* FAZENDO A VERIFICAÇÃO DE PERMISSAO DO USUÁRIO */
        
        if($ownauthcontroller->PermissaoCheck(1)) {
            $omcts = OMCT::get();            
        } else {
            $omcts = OMCT::where('id', session()->get('login.omctID'))->get();            
        }

        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.relacao_alunos_situacoes_diversas').'" method="get" target="_blank">';
        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';

        $data[] = '<select name="omctID" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma UETE</option>';
        foreach($omcts as $omct){
            if($omct->id!=1){
                $data[] = '<option value="'.$omct->id.'">'.$omct->omct.'</option>' ;
            }
        }

        $data[] = '</select>';

        /* BUSCANDO AS SITUAÇÕES DIVERSAS */

        $situacoes_diversas = SituacoesDiversas::get();

        $data[] = ' <div style="text-align: right; margin-top: 12px;">
                        <a class="marcar no-style" href="javascript: void(0);" onclick="$(\'input.custom-control-input\').prop(\'checked\', true); $(\'a.marcar\').hide(); $(\'a.desmarcar\').show();">
                            Marcar todas
                        </a>
                        <a class="desmarcar no-style"" href="javascript: void(0);" onclick="$(\'input.custom-control-input\').prop(\'checked\', false); $(\'a.marcar\').show(); $(\'a.desmarcar\').hide();" style="display: none;">
                            Desmarcar todas
                        </a>
                    </div>';

        $data[] = ' <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
                        <h4 style="text-align: center; margin-bottom: 12px;">SITUAÇÃO DIVERSA</h4>';

        foreach($situacoes_diversas as $situacao_diversa){
            
            $data[] = ' <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                            <input type="checkbox" class="custom-control-input" id="situacoes_diversas_'.$situacao_diversa->id.'" name="situacoes_diversas[]" value="'.$situacao_diversa->id.'" />
                            <label class="custom-control-label" for="situacoes_diversas_'.$situacao_diversa->id.'">'.$situacao_diversa->situacao.'</label>
                        </div>';
        }

        $data[] = ' <br /><div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="observacoes" name="observacoes" value="1" />
                        <label class="custom-control-label" for="observacoes">Mostrar campo observação</label>
                    </div>';

        $data[] = '</div>';        


        $data[] = '<div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
                        <h4 style="text-align: center; margin-bottom: 12px;">SEGMENTO</h4>
                        <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                            <input type="checkbox" class="custom-control-input segmento" id="segmento_M" name="segmento[]" value="M" />
                            <label class="custom-control-label" for="segmento_M">Masculino</label>
                        </div>
                        <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                            <input type="checkbox" class="custom-control-input segmento" id="segmento_F" name="segmento[]" value="F" />
                            <label class="custom-control-label" for="segmento_F">Feminino</label>
                        </div>
                    </div>';


        /* BUSCANDO AS ÁREAS */

        $areas = Areas::get();

        $data[] = ' <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
                        <h4 style="text-align: center; margin-bottom: 12px;">AREA</h4>';

        foreach($areas as $area){
            
            $data[] = ' <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                            <input type="checkbox" class="custom-control-input" id="area_'.$area->id.'" name="areas[]" value="'.$area->id.'" />
                            <label class="custom-control-label" for="area_'.$area->id.'">'.$area->area.'</label>
                        </div>';
        }

        $data[] = '</div>';

        $data[] = ' <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
                        <h4 style="text-align: center; margin-bottom: 12px;">CONTENDO OS CAMPOS</h4>';

        $data[] = ' <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="numero" name="numero" value="1" />
                        <label class="custom-control-label" for="numero">Número</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="nome_comleto" name="nome_comleto" value="1" />
                        <label class="custom-control-label" for="nome_comleto">Nome completo</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="nome_guerra" name="nome_guerra" value="1" />
                        <label class="custom-control-label" for="nome_guerra">Nome de guerra</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="data_nascimento" name="data_nascimento" value="1" />
                        <label class="custom-control-label" for="data_nascimento">Data de nascimento</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="primeira_data_praca" name="primeira_data_praca" value="1" />
                        <label class="custom-control-label" for="primeira_data_praca">Data de praça inicial</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="turma" name="turma" value="1" />
                        <label class="custom-control-label" for="turma">Turma</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="area" name="area" value="1" />
                        <label class="custom-control-label" for="area">Area</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="sexo" name="sexo" value="1" />
                        <label class="custom-control-label" for="sexo">Segmento</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="email" name="email" value="1" />
                        <label class="custom-control-label" for="email">Email</label>
                    </div>';                        

        $data[] = '</div>';

        $data[] = ' <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
                        <h4 style="text-align: center; margin-bottom: 12px;">CONTENDO OS ADICIONAIS</h4>';        

        $data[] = ' <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="ordem" name="ordem" value="1" />
                        <label class="custom-control-label" for="ordem">Ordem</label>
                    </div>';                        

        $data[] = '</div>';              


        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Gerar relação</button>
                    </div>';
        $data[] = '</form>'; 
        
        return implode('', $data);        
        
    }
    
    public function SituacaoDiversasHistoricoEscolar(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        if($ownauthcontroller->PermissaoCheck(1)){
            $alunos = AlunosSitDivHistorico::orderBy('aluno', 'asc')->get();
        } else {
            $alunos = AlunosSitDivHistorico::orderBy('aluno', 'asc')->where('omct_id', session()->get('login.omctID'))->get();
        }

        $data[] = ' <form id="submit-relatorio" action="'.route('relatorios.visualizar_historico_aluno').'" method="get" target="_blank">';
        $data[] = ' <input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';        

        $data[] = '<input name="alunoHistoricoID" list="alunos_list" class="form-control" onkeyup="if($(this).val().length>0){$(\'button#submit-relatorio\').show();} else {$(\'button#submit-relatorio\').hide();}" autofocus autocomplete="off">';
        $data[] = '<datalist id="alunos_list">';
        foreach($alunos as $aluno){
            $data[] = '<option value="'.$aluno->aluno.'">';
        }

        $data[] = '</datalist>';
        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Visualizar histórico</button>
                    </div>';
        $data[] = '</form>'; 
        
        return implode('', $data);
    }

    public function RelacaoGeralAlunos(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){
        
        /* FAZENDO A VERIFICAÇÃO DE PERMISSAO DO USUÁRIO */
        
        if($ownauthcontroller->PermissaoCheck(1)) {
            $omcts = OMCT::get();            
        } else {
            $omcts = OMCT::where('id', session()->get('login.omctID'))->get();            
        }

        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.relacao_alunos_prontos').'" method="get" target="_blank">';
        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';

        $data[] = '<select name="omctID" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma UETE</option>';
        if($ownauthcontroller->PermissaoCheck(1)) {
            $data[] = '<!--option value="todas_omct">TODAS AS UETE</option-->';            
        }        
        foreach($omcts as $omct){
            if($omct->id!=1){
                $data[] = '<option value="'.$omct->id.'">'.$omct->omct.'</option>' ;
            }
        }

        $data[] = '</select>';

        $data[] = ' <div style="text-align: right; margin-top: 12px;">
        <a class="marcar no-style" href="javascript: void(0);" onclick="$(\'input.custom-control-input\').prop(\'checked\', true); $(\'a.marcar\').hide(); $(\'a.desmarcar\').show();">
            Marcar todas
        </a>
        <a class="desmarcar no-style"" href="javascript: void(0);" onclick="$(\'input.custom-control-input\').prop(\'checked\', false); $(\'a.marcar\').show(); $(\'a.desmarcar\').hide();" style="display: none;">
            Desmarcar todas
        </a>
    </div>';

        $data[] = '<div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
                        <h4 style="text-align: center; margin-bottom: 12px;">SEGMENTO</h4>
                        <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                            <input type="checkbox" class="custom-control-input segmento" id="segmento_M" name="segmento[]" value="M" />
                            <label class="custom-control-label" for="segmento_M">Masculino</label>
                        </div>
                        <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                            <input type="checkbox" class="custom-control-input segmento" id="segmento_F" name="segmento[]" value="F" />
                            <label class="custom-control-label" for="segmento_F">Feminino</label>
                        </div>
                    </div>';


        /* BUSCANDO AS ÁREAS */

        $areas = Areas::get();

        $data[] = ' <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
                        <h4 style="text-align: center; margin-bottom: 12px;">AREA</h4>';

        foreach($areas as $area){
            
            $data[] = ' <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                            <input type="checkbox" class="custom-control-input" id="area_'.$area->id.'" name="areas[]" value="'.$area->id.'" />
                            <label class="custom-control-label" for="area_'.$area->id.'">'.$area->area.'</label>
                        </div>';
        }

        $data[] = '</div>';

        $data[] = ' <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
                        <h4 style="text-align: center; margin-bottom: 12px;">CONTENDO OS CAMPOS</h4>';

        $data[] = ' <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="numero" name="numero" value="1" />
                        <label class="custom-control-label" for="numero">Número</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="nome_comleto" name="nome_comleto" value="1" />
                        <label class="custom-control-label" for="nome_comleto">Nome completo</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="nome_guerra" name="nome_guerra" value="1" />
                        <label class="custom-control-label" for="nome_guerra">Nome de guerra</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="data_nascimento" name="data_nascimento" value="1" />
                        <label class="custom-control-label" for="data_nascimento">Data de nascimento</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="primeira_data_praca" name="primeira_data_praca" value="1" />
                        <label class="custom-control-label" for="primeira_data_praca">Data de praça inicial</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="turma" name="turma" value="1" />
                        <label class="custom-control-label" for="turma">Turma</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="area" name="area" value="1" />
                        <label class="custom-control-label" for="area">Area</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="sexo" name="sexo" value="1" />
                        <label class="custom-control-label" for="sexo">Segmento</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="email" name="email" value="1" />
                        <label class="custom-control-label" for="email">Email</label>
                    </div>';                        

        $data[] = '</div>';

        $data[] = ' <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
                        <h4 style="text-align: center; margin-bottom: 12px;">CONTENDO OS ADICIONAIS</h4>';        

        $data[] = ' <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="ordem" name="ordem" value="1" />
                        <label class="custom-control-label" for="ordem">Ordem</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="alteracao" name="alteracao" value="1" />
                        <label class="custom-control-label" for="alteracao">Alteração</label>
                    </div>

                    <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                        <input type="checkbox" class="custom-control-input" id="ciente" name="ciente" value="1" />
                        <label class="custom-control-label" for="ciente">Ciente</label>
                    </div>';                        

        $data[] = '</div>';        



        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Gerar relação</button>
                    </div>';
        $data[] = '</form>'; 

        return implode('', $data);
    }


    public function ProntoDeFaltas(Request $request){

        /* SELECIONANDO TODAS DISCIPLINAS DO ANO DE FORMAÇÃO SELECIONADO */
        $disciplinas = Disciplinas::where('ano_formacao_id', $request->id_ano_formacao)->get(['id']);
        foreach($disciplinas as $disciplina){
            $disciplinasID[] = $disciplina->id;
        }

        $disciplinasID = (isset($disciplinasID))?array_unique($disciplinasID):array();


        /* SELECIONANDO AS AVALIAÇÕES DAS $disciplinasID COM DATA E HORA MENOR QUE A ATUAL */

        $avaliacoes = Avaliacoes::whereIn('disciplinas_id', $disciplinasID)->where([
            ['data', '<=', date('Y-m-d')],
            //['hora', '<', date('H:i:s')]
        ])->orderBy('disciplinas_id', 'asc')->orderBy('nome_abrev', 'asc')->get();

        if(count($avaliacoes)>0){

            $data[] = '<form id="submit-relatorio" action="'.route('relatorios.pronto_de_faltas').'" method="get" target="_blank">';
            $data[] = '<select name="avaliacaoID" class="custom-select required_to_show_button">';
            $data[] = '<option value="0" selected>Selecione uma avaliação</option>';
            foreach($avaliacoes as $avaliacao){
                if($avaliacao->avaliacao_recuperacao==0){
                    $data[] = '<option value="'.$avaliacao->id.'">'.$avaliacao->disciplinas->nome_disciplina.' - '.$avaliacao->nome_completo.' ('.$avaliacao->nome_abrev.' - '.$avaliacao->chamada.' chamada)</option>' ;
                } else {
                    $data[] = '<option value="'.$avaliacao->id.'">'.$avaliacao->disciplinas->nome_disciplina.' - '.$avaliacao->nome_completo.'</option>' ;
                }
            }
            $data[] = '</select>';
            $data[] = ' <div style="margin-top: 24px;">
                            <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Gerar pronto de faltas</button>
                        </div>';
            $data[] = '</form>';
                        
        } else {
            $data[] = '<div style="text-align: center;">SEM AVALIAÇÕES PARA EXIBIÇÃO DE PRONTO DE FALTAS</div>';
        }

        return implode('', $data);        
    }

    public function ProntoLancamentoNotas(Request $request){

        /* SELECIONANDO TODAS DISCIPLINAS DO ANO DE FORMAÇÃO SELECIONADO */

        $disciplinas = Disciplinas::where('ano_formacao_id', $request->id_ano_formacao)->get(['id']);
        foreach($disciplinas as $disciplina){
            $disciplinasID[] = $disciplina->id;
        }

        $disciplinasID = (isset($disciplinasID))?array_unique($disciplinasID):array();


        /* SELECIONANDO AS AVALIAÇÕES DAS $disciplinasID COM DATA E HORA MENOR QUE A ATUAL */

        $avaliacoes = Avaliacoes::whereIn('disciplinas_id', $disciplinasID)->where('data', '<', date('Y-m-d'))
                                                                           ->where('chamada', 1)
                                                                           ->orderBy('disciplinas_id', 'asc')
                                                                           ->orderBy('nome_abrev', 'asc')
                                                                           ->get();

        if(count($avaliacoes)>0){

            $data[] = '<form id="submit-relatorio" action="'.route('relatorios.pronto_lancamento_notas').'" method="get" target="_blank">';
            $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';
            $data[] = '<select name="avaliacaoID" class="custom-select required_to_show_button">';
            $data[] = '<option value="0" selected>Selecione uma avaliação</option>';
            foreach($avaliacoes as $avaliacao){
                $data[] = '<option value="'.$avaliacao->id.'">'.$avaliacao->disciplinas->nome_disciplina.' - '.$avaliacao->nome_abrev.'</option>' ;
            }
            $data[] = '</select>';
            $data[] = ' <div style="margin-top: 24px;">
                            <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Gerar pronto de lançamento</button>
                        </div>';
            $data[] = '</form>';
                        
        } else {
            $data[] = '<div style="text-align: center;">SEM AVALIAÇÕES PARA EXIBIÇÃO DE PRONTO DE LANÇAMENTO</div>';
        }

        return implode('', $data);        
    }

    public function ProntoLancamentoNotasAR(Request $request){

        /* SELECIONANDO TODAS DISCIPLINAS DO ANO DE FORMAÇÃO SELECIONADO */
        $disciplinas = Disciplinas::where('ano_formacao_id', $request->id_ano_formacao)->get(['id']);
        foreach($disciplinas as $disciplina){
            $disciplinasID[] = $disciplina->id;
        }

        $disciplinasID = (isset($disciplinasID))?array_unique($disciplinasID):array();


        /* SELECIONANDO AS AVALIAÇÕES DAS $disciplinasID COM DATA E HORA MENOR QUE A ATUAL */
        $avaliacoes = Avaliacoes::whereIn('disciplinas_id', $disciplinasID)->where('data', '<', date('Y-m-d'))
                                                                           ->where('avaliacao_recuperacao', 1)
                                                                           ->orderBy('disciplinas_id', 'asc')
                                                                           ->orderBy('nome_abrev', 'asc')
                                                                           ->get();

        if(count($avaliacoes)>0){

            $data[] = '<form id="submit-relatorio" action="'.route('relatorios.pronto_lancamento_notas_ar').'" method="get" target="_blank">';
            $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';
            $data[] = '<select name="avaliacaoID" class="custom-select required_to_show_button">';
            $data[] = '<option value="0" selected>Selecione uma avaliação</option>';
            foreach($avaliacoes as $avaliacao){
                $data[] = '<option value="'.$avaliacao->id.'">'.$avaliacao->disciplinas->nome_disciplina.' - '.$avaliacao->nome_abrev.'</option>' ;
            }
            $data[] = '</select>';
            $data[] = ' <div style="margin-top: 24px;">
                            <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Gerar pronto de lançamento</button>
                        </div>';
            $data[] = '</form>';
                        
        } else {
            $data[] = '<div style="text-align: center;">SEM AVALIAÇÕES PARA EXIBIÇÃO DE PRONTO DE LANÇAMENTO</div>';
        }

        return implode('', $data);        
    }
    
    public function DemonstrativoNotas(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){
        
        /* FAZENDO A VERIFICAÇÃO DE PERMISSAO DO USUÁRIO */

        if($ownauthcontroller->PermissaoCheck(1)) {
            $omcts = OMCT::get();            
        } else {
            $omcts = OMCT::where('id', session()->get('login.omctID'))->get();            
        }

        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.demonstrativo_notas').'" method="get" target="_blank">';
        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';

        $data[] = '<select name="omctID" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma UETE</option>';
        foreach($omcts as $omct){
            if($omct->id!=1){
                $data[] = '<option value="'.$omct->id.'">'.$omct->omct.'</option>' ;
            }
        }
        $data[] = '</select>';
        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Gerar demonstrativos</button>
                    </div>';
        $data[] = '</form>';        

        return implode('', $data);

    }

    public function DemonstrativoNotasPorAluno(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){
        /* FAZENDO A VERIFICAÇÃO DE PERMISSAO DO USUÁRIO */
        if($ownauthcontroller->PermissaoCheck(1)) {
            $omcts_ids = OMCT::get(['id']);            
        } else {
            $omcts_ids = OMCT::where('id', session()->get('login.omctID'))->get(['id']);            
        }

        $omcts_ids = ($omcts_ids)??array();

        $alunos = Alunos::whereIn('omcts_id', $omcts_ids)->where('data_matricula', $request->id_ano_formacao)->orderBy('omcts_id', 'asc')->orderBy('numero', 'asc')->get();

        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.demonstrativo_notas').'" method="get" target="_blank">';
        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';
        $data[] = '<input type="hidden" name="omctID" value="'.session()->get('login.omctID').'">';
        $data[] = ' <div style="margin: 48px auto; width: 80%;">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">SELEÇÃO</th>
                                    <th scope="col">ALUNO(A)</th>
                                </tr>
                            </thead>
                            <tbody>';        
        foreach($alunos as $aluno){

            $data[] = '     <tr onclick="if($(this).find(\'input\').is(\':checked\')) { $(this).find(\'input\').prop(\'checked\', false); } else { $(this).find(\'input\').prop(\'checked\', true); }" style="cursor: pointer;">
                                <td> 
                                    <div class="custom-control custom-checkbox" style="width: 36%; margin: 4px auto;">
                                        <input type="checkbox" class="custom-control-input" id="alunoID_'.$aluno->id.'" name="alunos_ids[]" value="'.$aluno->id.'" />
                                        <label class="custom-control-label" for="alunoID_'.$aluno->id.'"></label>
                                    </div>
                                </td>
                                <td style="text-align: left;">
                                    <b>'.$aluno->numero.' '.$aluno->nome_guerra.'</b><br />
                                    <span style="color: #696969;"><i>'.$aluno->nome_completo.'</i></span><br />
                                    '.$aluno->omct->sigla_omct.'
                                </td>
                            </tr>';
        }

        $data[] = '        </tbody>
                        </table>
                    </div>';

        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" style="" onclick="$(\'form#submit-relatorio\').submit();">Gerar demonstrativos</button>
                    </div>';
        $data[] = '</form>';   
        
        

        return implode('', $data);

    }

    public function ReciboDemonstrativo(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        /* SELECIONANDO TODAS DISCIPLINAS DO ANO DE FORMAÇÃO SELECIONADO */

        $disciplinas = Disciplinas::where('ano_formacao_id', $request->id_ano_formacao)->get(['id']);
        foreach($disciplinas as $disciplina){
            $disciplinasID[] = $disciplina->id;
        }

        $disciplinasID = (isset($disciplinasID))?array_unique($disciplinasID):array();

        /* SELECIONANDO AS AVALIAÇÕES DAS $disciplinasID COM DATA E HORA MAIOR QUE A ATUAL */

        $avaliacoes = Avaliacoes::whereIn('disciplinas_id', $disciplinasID)
                                    ->where('data', '<', date('Y-m-d'))
                                    ->orderBy('disciplinas_id', 'asc')
                                    ->orderBy('data', 'asc')
                                    ->get();

        if(count($avaliacoes)>0){
        
            /* FAZENDO A VERIFICAÇÃO DE PERMISSAO DO USUÁRIO */

            if($ownauthcontroller->PermissaoCheck(1)) {
                $omcts = OMCT::get();            
            } else {
                $omcts = OMCT::where('id', session()->get('login.omctID'))->get();            
            }

            $data[] = '<form id="submit-relatorio" action="'.route('relatorios.lista_assinavel_demosntrativo_notas').'" method="get" target="_blank">';
            $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';
            
            $data[] = '<p><select name="avaliacaoID" class="custom-select required_to_show_button">';
            $data[] = '<option value="0" selected>Selecione uma avaliação</option>';
            foreach($avaliacoes as $avaliacao){
                $data[] = '<option value="'.$avaliacao->id.'">'.$avaliacao->disciplinas->nome_disciplina.' - '.$avaliacao->nome_completo.' ('.$avaliacao->nome_abrev.' - '.$avaliacao->chamada.' chamada)</option>' ;
            }
            $data[] = '</select></p>';       
            
            $data[] = '<select name="omctID" class="custom-select required_to_show_button">';
            $data[] = '<option value="0" selected>Selecione uma UETE</option>';
            foreach($omcts as $omct){
                if($omct->id!=1){
                    $data[] = '<option value="'.$omct->id.'">'.$omct->omct.'</option>' ;
                }
            }
            $data[] = '</select>';
            $data[] = ' <div style="margin-top: 24px;">
                            <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Gerar lista assinável</button>
                        </div>';
            $data[] = '</form>';   
        
        } else {
            $data[] = '<div style="text-align: center;">SEM AVALIAÇÕES PARA EXIBIÇÃO DE PRONTO DE FALTAS</div>';
        }
        

        return implode('', $data);

    }

    public function ReciboDemonstrativoPorAluno(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        /* SELECIONANDO TODAS DISCIPLINAS DO ANO DE FORMAÇÃO SELECIONADO */

        $disciplinas = Disciplinas::where('ano_formacao_id', $request->id_ano_formacao)->get(['id']);
        foreach($disciplinas as $disciplina){
            $disciplinasID[] = $disciplina->id;
        }

        $disciplinasID = (isset($disciplinasID))?array_unique($disciplinasID):array();


        /* SELECIONANDO AS AVALIAÇÕES DAS $disciplinasID COM DATA E HORA MAIOR QUE A ATUAL */

        $avaliacoes = Avaliacoes::whereIn('disciplinas_id', $disciplinasID)
                                    ->where('data', '<', date('Y-m-d'))
                                    ->orderBy('disciplinas_id', 'asc')
                                    ->orderBy('data', 'desc')
                                    ->get();

        if(count($avaliacoes)>0){        
        
            /* FAZENDO A VERIFICAÇÃO DE PERMISSAO DO USUÁRIO */

            if($ownauthcontroller->PermissaoCheck(1)) {
                $omcts = OMCT::get();            
            } else {
                $omcts = OMCT::where('id', session()->get('login.omctID'))->get();            
            }

            $data[] = '<form id="submit-relatorio" action="'.route('relatorios.lista_assinavel_demosntrativo_notas_por_aluno').'" method="get" target="_blank">';
            $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';
            
            $data[] = '<p><select name="avaliacaoID" class="custom-select required_to_show_button" onchange="selectAlunosFizeramAvaliacao(this);">';

            $data[] = '<option value="0" selected>Selecione uma avaliação</option>';
            foreach($avaliacoes as $avaliacao){
                $data[] = '<option value="'.$avaliacao->id.'">'.$avaliacao->disciplinas->nome_disciplina.' - '.$avaliacao->nome_completo.' ('.$avaliacao->nome_abrev.' - '.$avaliacao->chamada.' chamada)</option>' ;
            }
            $data[] = '</select></p>';  

            $data[] = '<div id="lista_complementar"></div>'; 
        
        } else {
            $data[] = '<div style="text-align: center;">SEM AVALIAÇÕES PARA EXIBIÇÃO DE PRONTO DE FALTAS</div>';
        }
        

        return implode('', $data);

    }
    
    public function selectAlunosFizeramAvaliacao(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        //$data['response'] = $request->id;
        
        // SELECIONANDO TODOS OS ALUNOS QUE FIZERAM A AVALIAÇÃO $request->id

        /* FAZENDO A VERIFICAÇÃO DE PERMISSAO DO USUÁRIO */

        if($ownauthcontroller->PermissaoCheck(1)) {
            $omcts = OMCT::get();            
        } else {
            $omcts = OMCT::where('id', session()->get('login.omctID'))->get();            
        }        

        // MONTANDO UMA ARRAY COM A LISTA DE IDS DAS UETEs

        foreach($omcts as $omct){
            $omcts_id[] = $omct->id;
        }

        $omcts_id = ($omcts_id)??array();

        $avaliacoes_notas = AvaliacoesNotas::where('avaliacao_id', $request->id)->get(['alunos_id']);

        foreach($avaliacoes_notas as $aluno){
            $alunos_id[] = $aluno->alunos_id;
        }
        
        $alunos_id = ($alunos_id)??array();
        
        // SELECIONANDO OS ALUNOS

        $alunos = Alunos::whereIn('id', $alunos_id)->whereIn('omcts_id', $omcts_id)->orderBy('omcts_id', 'asc')->orderBy('numero', 'asc')->get();

        if(count($alunos)>0){

            $data[] = ' <div style="margin: 48px auto; width: 80%;">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">SELEÇÃO</th>
                                        <th scope="col">ALUNO(A)</th>
                                    </tr>
                                </thead>
                                <tbody>';
    
            foreach($alunos as $aluno){
    
                $data[] = '     <tr onclick="if($(this).find(\'input\').is(\':checked\')) { $(this).find(\'input\').prop(\'checked\', false); } else { $(this).find(\'input\').prop(\'checked\', true); }" style="cursor: pointer;">
                                    <td> 
                                        <div class="custom-control custom-checkbox" style="width: 36%; margin: 4px auto;">
                                            <input type="checkbox" class="custom-control-input" id="alunoID_'.$aluno->id.'" name="aluno_id[]" value="'.$aluno->id.'" />
                                            <label class="custom-control-label" for="alunoID_'.$aluno->id.'"></label>
                                        </div>
                                    </td>
                                    <td style="text-align: left;">
                                        <b>'.$aluno->numero.' '.$aluno->nome_guerra.'</b><br />
                                        <span style="color: #696969;"><i>'.$aluno->nome_completo.'</i></span><br />
                                        '.$aluno->omct->sigla_omct.'
                                    </td>
                                </tr>';
            }
    
            $data[] = '        </tbody>
                            </table>
                        </div>';

            $data[] = ' <div style="margin-top: 24px;">
                            <button id="submit-relatorio" type="button" class="btn btn-primary" style="" onclick="$(\'form#submit-relatorio\').submit();">Gerar lista assinável</button>
                        </div>';
            $data[] = ' </form>';                          

        } else {
            $data[] = '<div style="text-align: center; margin-top: 24px;">NENHUM ALUNO REALIZOU ESTA AVALIAÇÃO NESTA UETE</div>';            
        }


        $data2['response'] = implode('', $data);                      

        return $data2;
    }

    public function DialogConfiguracoesRelatorio(\App\Http\Controllers\OwnAuthController $ownauthcontroller) {

        if($ownauthcontroller->permissaoCheck(1)){

            $rel_conf = RelatoriosConfiguracoes::find(1);
            
            // SELECIONANDO DISCIPLINAS DO ANO CORRENTE
            
            $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
            $id_ano_corrente = ($ano_corrente->id)??0;
            
            $disciplinas = Disciplinas::where('ano_formacao_id', $id_ano_corrente)->get(['id']);

            // GUARDANDO EM UMA ARRAY TODAS AS AVALIÇÕES CONFIGURADAS

            //ConfDemonstrativos

            $disciplinas_marcadas = ConfDemonstrativos::first();

            if($disciplinas_marcadas->avaliacoes){
                $disciplinas_marcadas = explode(',', $disciplinas_marcadas->avaliacoes);
            } else {
                $disciplinas_marcadas = array(); 
            }

            $get_avaliacoes = Avaliacoes::where('chamada', 1)->whereIn('disciplinas_id', $disciplinas)->orderBy('disciplinas_id', 'asc')->get();
            $avaliacao_array[] = '<div style="margin-top: 12px; border-bottom: 1px solid #ccc; padding: 2px;"> Selecione as avaliações consideradas para classsificação</div>';
            //$avaliacao_array[] = '<input type="checkbox" id="marcar_todas" onclick="$(\'input[name="avaliacoesID"]\').prop(\'checked\', true);" /> <label style="font-size: 12px;" for="marcar_todas">Todas</label>';
            foreach($get_avaliacoes as $avaliacao){
                $status_checked = (in_array($avaliacao->id, $disciplinas_marcadas))? 'checked="checked"':'';
                $avaliacao_array[] = '<input '.$status_checked.' type="checkbox" value="'.$avaliacao->id.'" id="avaliacao-'.$avaliacao->id.'" name="avaliacoesID[]" /> <label style="font-size: 12px;" for="avaliacao-'.$avaliacao->id.'">'.$avaliacao->nome_abrev.' '.$avaliacao->disciplinas->nome_disciplina.'</label>';
            }
            
            //$status_checked = (in_array(99999, $disciplinas_marcadas))? 'checked="checked"':'';            
            //$avaliacao_array[] = '<input '.$status_checked.' type="checkbox" value="99999" id="avaliacao-99999" name="avaliacoesID[]" /> <label style="font-size: 12px;" for="avaliacao-99999">TFM</label>';
 
            $status_checked_input = ($rel_conf->valor==0)?'':'checked';

            $data['header'] = '<i class="ion-ios-gear" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Configurações de relatórios';     
            $data['body'] = '   <form id="configuracoes_relatorios">
                                    <div class="custom-control custom-checkbox" style="margin: 12px;">
                                        <input type="hidden" name="_token" value="'.csrf_token().'">
                                        <input type="checkbox" class="custom-control-input" id="conf_id_'.$rel_conf->id.'" name="conf_id_'.$rel_conf->id.'" value="1" - '.$status_checked_input.' />
                                        <label class="custom-control-label" for="conf_id_'.$rel_conf->id.'">Permitir que as OMCT acessem o demonstrativo de notas</label>
                                        <br />
                                        '.implode('<br />', $avaliacao_array).'
                                    </div>                                       
                                </form>';

            $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Cancelar
                                </button>
                                <button type="button" class="btn btn-primary" onclick="this.disabled=true; configurarRelatorios(this);">
                                    Salvar
                                </button>';
        
            $data['status'] = 'ok';

        } else {
            $data['status'] = 'err';
        }
                    
        return $data;            
        
    }
    
    public function ConfigurarRelatorio(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        if($ownauthcontroller->permissaoCheck(1)){

            // LIMPANDO TODOS DADOS DA TABELA alunos_classificacao

            $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
            $id_ano_corrente = ($ano_corrente->id)??0;

            AlunosClassificacao::whereHas('aluno', function($q) use ($id_ano_corrente) {
                $q->where('data_matricula', '=', $id_ano_corrente);
            })->delete();

            // RESETANDO AUTO INCREMENT
            $max = DB::table('alunos_classificacao')->max('id') + 1;
            DB::statement("ALTER TABLE alunos_classificacao AUTO_INCREMENT=$max;");
            /*DB::statement("SET @count = 0;");
            DB::statement("UPDATE alunos_classificacao SET alunos_classificacao.id = @count:= @count + 1;");
            DB::statement("ALTER TABLE alunos_classificacao AUTO_INCREMENT = 1;");*/
     
            /*
            
            Aqui configuro se o demonstrativo de notas estará disponível para visualização pelas UETEs (tabela relatorios_configuracoes)
            
            */

            if(isset($request->conf_id_1)){
                RelatoriosConfiguracoes::where('id', 1)->update(['valor' => 1]);                
            } else {
                RelatoriosConfiguracoes::where('id', 1)->update(['valor' => 0]);                
            }


            /*
            
                Atualizando tabela conf_demonstrativos com as avaliações que serão consideradas para cálculo da classificação.
                Nota: Na MODAL (dialogo da seleção de avaliações) que serão consideradas para cálculo da classificação, não são 
                selecionadas as avaliações de 2ª chamada.
                No código abaixo, as avaliações de 2ª chamada são adicionadas a array $request->avaliacoesID. Posteriormente são persistidas 
                na tabela conf_demonstrativos somente as de 1º chamada, OU SEJA, todas as avaliações EXCETO as de segunda chamada são adicionadas a tabela.
            
                $avaliacoesIDs = esta variavel do tipo array contém as avaliações e suas respectivas 2ª chamadas.

            */

            
            if($request->avaliacoesID){
                $avaliacoes_2_chamada = Avaliacoes::whereIn('chamada_refer_id', $request->avaliacoesID)->where('chamada', '<>', 1)->get(['id']);
                foreach($avaliacoes_2_chamada as $ava_2_chamada){
                    $array_ava_2_chamada[] = $ava_2_chamada->id;
                }
                $array_ava_2_chamada = ($array_ava_2_chamada)??array();
                ConfDemonstrativos::where('id', 1)->update(['avaliacoes' => implode(',', $request->avaliacoesID)]);
                $avaliacoesIDs = array_merge($request->avaliacoesID, $array_ava_2_chamada);
            } else {
                $avaliacoesIDs = array();
                ConfDemonstrativos::where('id', 1)->update(['avaliacoes' => '']);
            }

            // SELECIONANDO TODAS AS AVALIAÇÕES CONFIGURADAS NA TABELA conf_demonstrativos para se obter a razão de cada disciplina, id e nome.

            $avaliacoes = Avaliacoes::whereIn('id', $avaliacoesIDs)->get();

            // CALCULANDO A RAZÃO DE CADA DISCIPLINA 
            /*
                se dá pela qtde de avaliações de primeira chamada que tem cada disciplina
            */

            foreach($avaliacoes as $avaliacao) {
                if($avaliacao->chamada==1 && $avaliacao->avaliacao_recuperacao==0){
                    $razao[$avaliacao->disciplinas_id][] = 1;                         
                }

                $disciplinasID[] = $avaliacao->disciplinas_id;
                $disciplina_nome[$avaliacao->disciplinas_id] = $avaliacao->disciplinas->nome_disciplina;

            }

            // TRATANDO OS IDs DAS DISCIPLINAS QUE PERTENCEM AS AVALIAÇÕES SELECIONADAS

            if(isset($disciplinasID)){
                if(in_array(99999, $avaliacoesIDs)){
                    $disciplinasID = array_unique(array_merge($disciplinasID, array(99999)));    
                } else {
                    $disciplinasID = array_unique($disciplinasID);
                }
            } else {
                if(in_array(99999, $avaliacoesIDs)){
                    $disciplinasID = array(99999);   
                }               
            }
            
            // SELECIONANDO AS NOTAS DOS ALUNOS NAS AVAIAÇÕES SELECIONADAS (inclusive 2ª chamada)

            //2ºTen João Victor, Alteração no Cálculo da NOTA
            $alunoNota = FuncoesController::recalculaNotaAluno(AvaliacoesNotas::whereIn('avaliacao_id', $avaliacoesIDs)->get());
            //dd($alunoNota[33][3378]);
            $alunosID = $alunoNota['alunosID'];
            //Fim Alteração 2ºTen João Victor
            $alunosID = array_unique($alunosID);

            sort($alunosID);

            if(isset($alunosID) && isset($alunoNota)){
                foreach($disciplinasID as $disciplinas){
                    foreach($alunosID as $alunoID){
                        if(isset($alunoNota[$disciplinas][$alunoID])){
                            if($alunoNota[$disciplinas][$alunoID]['tfm'] == 'S'){
                                $k[$alunoID]['avaliacoes_tfm'][] = $alunoNota[$disciplinas][$alunoID];
                            }
                            
                            $k[$alunoID][] = $alunoNota[$disciplinas][$alunoID];
                        }
                    }
                }
                
                if(isset($k)){
                    
                    foreach($alunosID as $alunoID){
                        $discAprovConselhoEnsino = array();
                        foreach($k[$alunoID] as $key => $z){
                            $reprovado = [];
                            if(is_numeric($key) && $k[$alunoID][$key]['tfm'] == 'N'){
                                $k[$alunoID][$key]['media'] = (isset($z['media'])) ? $z['media'] : 0;

                                //AQUI VERIFICO SE O  ALUNO OBTEVE MEDIA FINAL INFERIOR A 5 NESSA DISCIPLINA
                                if($k[$alunoID][$key]['media']<5 && $k[$alunoID][$key]['tfm'] == 'N'){

                                    $avaliacao_rec_nota = null;
                                    $avaliacao_rec_id = Avaliacoes::where('disciplinas_id', $k[$alunoID][$key]['disciplina_id'])->where('avaliacao_recuperacao', 1)->first();
                                    
                                    if($avaliacao_rec_id){
                                        $avaliacao_rec_nota = AvaliacoesNotas::where('alunos_id', $alunoID)->where('avaliacao_id', $avaliacao_rec_id->id)->first();

                                        if(isset($avaliacao_rec_nota)){
                                            $k[$alunoID][$key]['AR'] = $avaliacao_rec_nota->getNota();
                                            $k[$alunoID][$key]['avaliacoes']['ACR'] = $avaliacao_rec_nota->getNota();
        
                                            //Caso o discente obtenha nota igual ou superior a 5,000, receberá a nota 5,000 como nota final da disciplina respectiva.
                                            //Caso o discente obtenha nota inferior a 5,000, permanecerá com ND obtida antes da ACR como nota final da disciplina respectiva.                                      
                                            if(($k[$alunoID][$key]['AR']) >=5){
                                                $k[$alunoID][$key]['media'] = 5;    
                                            }
                                        }
                                    }
                                    // VERIFICANDO SE O ALUNO ESTÁ EM CONSELHO
                                    $alunos_em_conselho = AlunosConselhoEscolar::where('aluno_id', $alunoID)->where('disciplina_id', $k[$alunoID][$key]['disciplina_id'])->first();
                                    
                                    if($alunos_em_conselho){
                                        $k[$alunoID][$key]['avaliacoes']['CE'] = 'APROVADO';
                                        $discAprovConselhoEnsino[] = $k[$alunoID][$key]['disciplina_id'];
                                    } 

                                    if(is_numeric($k[$alunoID][$key]['media'])){
                                        $mf[] = number_format($k[$alunoID][$key]['media'], '4', '.', '');
                                        $mf_tmp = number_format($k[$alunoID][$key]['media'], '4', '.', '');
                                    }
                                    
                                } else if($k[$alunoID][$key]['tfm'] == 'N'){
                                    $mf_tmp = number_format($z['media'], '3', '.','');
                                    $mf[] = $mf_tmp;
                                }

                                if($k[$alunoID][$key]['tfm'] == 'N' && $mf_tmp<5){
                                    $reprovado[] = 1;
                                    $disciplinas_reprovado_array[] = $k[$alunoID][$key]['disciplina_id'];
                                }else {
                                    $reprovado[] = 0;
                                    $disciplinas_reprovado_array[] = 0;
                                }

                            }else if(!is_numeric($key)){

                                try{
                                    // Faz cálculo da NA do TFM
                                    if($key == 'avaliacoes_tfm'){
                                              
                                        $soma = null;
                                        $soma_avaliacoes = 0;
                                        $colspan_demonstrativo = 1;

                                        $k[$alunoID]['avaliacoes_tfm']['media_tfm'] = 0;
                                        $k[$alunoID]['avaliacoes_tfm']['colspan_demonstrativo'] = 0;
                                        $k[$alunoID]['avaliacoes_tfm']['media_tfm_abdominal'] = 'NS';
                                        $k[$alunoID]['avaliacoes_tfm']['AR_tfm_abdominal'] = false;
                                        $k[$alunoID]['avaliacoes_tfm']['notas_AR'] = null;
                                        $k[$alunoID]['avaliacoes_tfm']['notas_sem_grau_minimo'] = null;

                                        foreach($k[$alunoID][$key] as $key_aval => $avaliacao){

                                            //Valida se é os arrays com as avaliações e não o valor final de média
                                            if(is_numeric($key_aval)){
    
                                                $media_tfm = $k[$alunoID][$key][$key_aval]['media'];
                                                /********************************************************
                                                ***********       Recuperação de TFM       **************                                  
                                                *********************************************************/

                                                $avaliacao_rec_nota = null;
                                                $avaliacao_rec_id = Avaliacoes::where('disciplinas_id', $avaliacao['disciplina_id'])->where('avaliacao_recuperacao', 1)->first();

                                                $colspan_demonstrativo++;

                                                if($avaliacao_rec_id){
                                                    $avaliacao_rec_nota = AvaliacoesNotas::where('alunos_id', $alunoID)->where('avaliacao_id', $avaliacao_rec_id->id)->first();

                                                    if(isset($avaliacao_rec_nota)){
                                                        $colspan_demonstrativo++;
                                                        $k[$alunoID][$key][$key_aval]['AR'] = $avaliacao_rec_nota->getNota();
                                                        $k[$alunoID][$key][$key_aval]['avaliacoes']['ACR'] = $k[$alunoID][$key][$key_aval]['AR'];                                   
                                                        
                                                        //Não soma o abdominal para a ND Final
                                                        if(isset($avaliacao['tfm_abdominal']) && $avaliacao['tfm_abdominal'] == 'N'){
                                                            
                                                            if($k[$alunoID][$key][$key_aval]['AR'] >= 5){
                                                                $k[$alunoID][$key][$key_aval]['media'] = 5;
                                                            }
                                                            $k[$alunoID]['avaliacoes_tfm']['notas_AR'][] = $k[$alunoID][$key][$key_aval]['AR'];
                                                        }
                                                    }
                                                }

                                                // VERIFICANDO SE O ALUNO ESTÁ EM CONSELHO
                                                $alunos_em_conselho = AlunosConselhoEscolar::where('aluno_id', $alunoID)->where('disciplina_id', $k[$alunoID][$key][$key_aval]['disciplina_id'])->first();
                                                
                                                if($alunos_em_conselho){
                                                    $colspan_demonstrativo++;
                                                    $k[$alunoID][$key][$key_aval]['avaliacoes']['CE'] = 'APROVADO';
                                                    $discAprovConselhoEnsino[] = $k[$alunoID][$key][$key_aval]['disciplina_id'];
                                                    $k[$alunoID][$key][$key_aval]['media'] = $media_tfm;
                                                } 

                                                /********************************************************
                                                ***********      Fim Recuperação de TFM     *************
                                                *********************************************************/
 
                                                if(isset($avaliacao['tfm_abdominal']) && $avaliacao['tfm_abdominal'] == 'N'){                                                
                                                    /** Faz a Soma das Médias para Gerar a ND Final **/
                                                    $soma += (0 + $k[$alunoID][$key][$key_aval]['media']);
                                                    $soma_avaliacoes++;    

                                                    if($k[$alunoID][$key][$key_aval]['media'] < 5 
                                                        && !isset($k[$alunoID][$key][$key_aval]['avaliacoes']['CE'])){
                                                        $k[$alunoID]['avaliacoes_tfm']['notas_sem_grau_minimo'][] = $k[$alunoID][$key][$key_aval]['media'];
                                                    }
                                                }else if(isset($avaliacao['tfm_abdominal']) && $avaliacao['tfm_abdominal'] == 'S'){
                                                    foreach($avaliacao['avaliacoes'] as $aval){
                                                        $k[$alunoID]['avaliacoes_tfm']['media_tfm_abdominal'] = $aval->nota;
                                                    }

                                                    //Se Existir Recuperação sobrepoe a nota do abdominal
                                                    if(isset($k[$alunoID][$key][$key_aval]['AR'])){
                                                        $k[$alunoID]['avaliacoes_tfm']['media_tfm_abdominal'] = $k[$alunoID][$key][$key_aval]['AR'];
                                                        $k[$alunoID]['avaliacoes_tfm']['AR_tfm_abdominal'] = true;
                                                    }
                                                }

                                                
                                                if(count($avaliacao['avaliacoes']) > $colspan_demonstrativo){
                                                    $colspan_demonstrativo = count($avaliacao['avaliacoes']);
                                                }
                                                /*Fim Soma das Médias*/                                                    
                                                
                                            }
                                        }

                                        if(!is_null($soma)){
                                            //ND TFM
                                            $k[$alunoID]['avaliacoes_tfm']['media_tfm'] = number_format($soma / $soma_avaliacoes, '3', '.', '');
                                            $mf[] = number_format($k[$alunoID]['avaliacoes_tfm']['media_tfm'], '3', '.', '');
                                            $k[$alunoID]['avaliacoes_tfm']['colspan_demonstrativo'] = ($colspan_demonstrativo + 1);
                                        }

                                        /********************************************************
                                        ***********       Regras de AR de TFM      **************
                                        *********************************************************/

                                        //Regra III
                                        //Aluno não alcance Suficiência na AR de Abdominal e não  tenha alcançado o grau minímo para aprovação em um ou mais teste físicos.
                                        //ND = média aritmética simples das notas das AR abaixo de 5,000 + 4,900 do abdominal supra para o cálculo.
                                        if($k[$alunoID]['avaliacoes_tfm']['AR_tfm_abdominal'] == true 
                                            && $k[$alunoID]['avaliacoes_tfm']['media_tfm_abdominal'] == 'NS' 
                                            && !is_null($k[$alunoID]['avaliacoes_tfm']['notas_sem_grau_minimo'])){
                                             
                                            $notas_ar = [];
                                            foreach($k[$alunoID]['avaliacoes_tfm']['notas_AR'] as $notas){
                                                if($notas < 5){
                                                    $notas_ar[] = $notas;
                                                }
                                            }

                                            $notas_ar[] = 4.900;//Adiciona a Nota do Abdominal
                                            $k[$alunoID]['avaliacoes_tfm']['media_tfm'] = number_format((array_sum($notas_ar) / count($notas_ar)), '3', '.', '');
                                            
                                            array_pop($mf);//Remove a última Média Lançada
                                            $mf[] = number_format($k[$alunoID]['avaliacoes_tfm']['media_tfm'], '3', '.', '');

                                        //Regra II
                                        //Aluno não alcance Suficiência Na AR de Abdominal  e obtenha grau igual ou superior a 5,000 nas demais AR
                                        //ND = 4,900 
                                        }else if($k[$alunoID]['avaliacoes_tfm']['AR_tfm_abdominal'] == true 
                                            && $k[$alunoID]['avaliacoes_tfm']['media_tfm_abdominal'] == 'NS' 
                                            && is_null($k[$alunoID]['avaliacoes_tfm']['notas_sem_grau_minimo'])){

                                            $notas_ar = [];
                                            $notas_ar[] = 4.900;//Adiciona a Nota do Abdominal
                                            $k[$alunoID]['avaliacoes_tfm']['media_tfm'] = number_format(array_sum($notas_ar), '3', '.', '');

                                            array_pop($mf);//Remove a última Média Lançada
                                            $mf[] = number_format($k[$alunoID]['avaliacoes_tfm']['media_tfm'], '3', '.', '');

                                        //Regra I
                                        //Aluno tenha alcançado Suficiência no Abdominal Supra, mas não tenha alcançado o grau mínimo para aprovação em um ou mais teste físicos.
                                        //ND = média aritmética simples das notas das AR dos teste que obeteve abaixo de 5,000.
                                        }else if($k[$alunoID]['avaliacoes_tfm']['media_tfm_abdominal'] == 'S'
                                            && !is_null($k[$alunoID]['avaliacoes_tfm']['notas_sem_grau_minimo'])
                                            && !is_null($k[$alunoID]['avaliacoes_tfm']['notas_AR'])){

                                            $notas_ar = [];
                                            foreach($k[$alunoID]['avaliacoes_tfm']['notas_AR'] as $notas){
                                                if($notas < 5){
                                                    $notas_ar[] = $notas;
                                                }
                                            }

                                            $k[$alunoID]['avaliacoes_tfm']['media_tfm'] = number_format((array_sum($notas_ar) / count($notas_ar)), '3', '.', '');
                                            
                                            array_pop($mf);//Remove a última Média Lançada
                                            $mf[] = number_format($k[$alunoID]['avaliacoes_tfm']['media_tfm'], '3', '.', '');
                                        } 

                                        /********************************************************
                                        ***********       Fim de AR de TFM      **************
                                        *********************************************************/
                                        
                                        /*if($alunoID == 3678){//Aluno 1050 KARINA CARDOSO
                                            dd($k[$alunoID][$key], $soma, $soma_avaliacoes);
                                        }*/
                                        /*if($alunoID == 3290){//Aluno BUROCK 3646 
                                            dd($k[$alunoID][$key], $soma, $soma_avaliacoes);
                                        }*/
                                        /*if($alunoID == 4085){
                                            dd($k[$alunoID][$key], $soma, $soma_avaliacoes);
                                        }*/
                                    }
                                }catch(Exception $ex){
                                    dd($ex, $avaliacao);
                                }
                                
                                if($key == 'media_final'){
                                    arsort($k[$alunoID]);
                                }
                            }
                        }
/*if($alunoID == 4085){//THAYANE (THAYANE DA SILVA DE ALMÊDA)
    dd($k[$alunoID], $mf, $reprovado, $disciplinas_reprovado_array);
}*/
                        if(isset($mf)){
                            
                            if(array_sum($reprovado)>0){
                                $val = false;
                                foreach(array_unique($disciplinas_reprovado_array) as $disciplina){
                                    if($disciplina > 0 && !in_array($disciplina, $discAprovConselhoEnsino)){
                                        $val = true;
                                    }
                                }
                                
                                if($val){
                                    $k[$alunoID]['reprovado'] = 'S';
                                    $disciplinas_reprovado = implode(',', array_unique($disciplinas_reprovado_array));    
                                }else{
                                    $k[$alunoID]['reprovado'] = 'N';
                                    $disciplinas_reprovado = null;    
                                }
                                
                            } else {
                                $k[$alunoID]['reprovado'] = 'N';
                                $disciplinas_reprovado = null;
                            }

                            $k[$alunoID]['media_final'] = number_format((array_sum($mf)) / (count($mf)), '10', '.', '');
/*if($alunoID == 3365){//THAYANE (THAYANE DA SILVA DE ALMÊDA)
    dd($k[$alunoID], $mf, $reprovado, $disciplinas_reprovado_array, $discAprovConselhoEnsino);
}*/        
                            unset($mf);
                            unset($reprovado); 
                            unset($disciplinas_reprovado_array);
                        }
   
                        if(!is_null($alunoID)){

                            $class = new AlunosClassificacao;
                            $class->aluno_id = $alunoID;
                            $class->nota_final = $k[$alunoID]['media_final'];
                            $class->nota_final_arredondada = number_format($k[$alunoID]['media_final'], '3', '.', '');
                            $class->data_demonstrativo = serialize($k[$alunoID]);
                            $class->reprovado = $k[$alunoID]['reprovado'];
                            $class->disciplinas_reprovado = $disciplinas_reprovado;
                            $class->ano_formacao_id = $id_ano_corrente;
                            $class->save();
                        }                        
                    
                        unset($k[$alunoID]);                    
                    }
                   
                    $areas = Areas::get();
                    foreach($areas as $item){
                        $area['M'][$item->id]=0;
                        $area['F'][$item->id]=0;
                        $area['GAAE'][$item->id]=0;
                    } 

                    /*
                    * Faz o Select removendo o 1º GAAE (Saúde e Música)
                    */
                    $set_classif = DB::select("SELECT alunos_classificacao.id, alunos.sexo, alunos_classificacao.reprovado, alunos.area_id
                                    FROM alunos_classificacao
                                    INNER JOIN alunos ON alunos_classificacao.aluno_id=alunos.id 
                                    WHERE alunos.data_matricula = $id_ano_corrente
                                    AND alunos.omcts_id <> 2
                                    ORDER BY nota_final DESC, precedencia DESC");
                    
                    $i=0;
                    
                    foreach($set_classif as $id_tb){
                        $i++;
                        if(is_numeric($id_tb->area_id)){
                            if($id_tb->reprovado=='S'){
                                $classif = 0;
                            } else {
                                $area[$id_tb->sexo][$id_tb->area_id]++;
                                $classif = $area[$id_tb->sexo][$id_tb->area_id];
                            }
                        } else {
                            $classif=0;
                        }
                        $xzy = AlunosClassificacao::find($id_tb->id);
                        $xzy->classificacao_por_area = $classif;
                        $xzy->classificacao = $i;
                        $xzy->save();
                    }
                    /*
                    * Fim sem 1º GAAE
                    */

                    /*
                    * Faz o Select somente com o 1º GAAE (Saúde e Música)
                    */
                    $set_class_gaae = DB::select("SELECT alunos_classificacao.id, alunos.sexo, alunos_classificacao.reprovado, alunos.area_id
                                    FROM alunos_classificacao
                                    INNER JOIN alunos ON alunos_classificacao.aluno_id=alunos.id 
                                    WHERE alunos.data_matricula = $id_ano_corrente
                                    AND alunos.omcts_id = 2
                                    ORDER BY nota_final DESC, precedencia DESC");
                    
                    $i=0;
                    
                    foreach($set_class_gaae as $id_tb){
                        $i++;
                        if(is_numeric($id_tb->area_id)){
                            if($id_tb->reprovado=='S'){
                                $classif = 0;
                            } else {
                                $area['GAAE'][$id_tb->area_id]++;
                                $classif = $area['GAAE'][$id_tb->area_id];
                            }
                        } else {
                            $classif=0;
                        }
                        $xzy = AlunosClassificacao::find($id_tb->id);
                        $xzy->classificacao_por_area = $classif;
                        $xzy->classificacao = $i;
                        $xzy->save();
                    }
                    /*
                    * Fim 1º GAAE
                    */
                    
                }
            }

            $data['status'] = 'ok';    
            $data['response'] = 'Configurações do relatório foram atualizadas com sucesso!';

        } else {
            $data['status'] = 'err';
            $data['response'] = '<strong>ATENÇÃO: </strong>Você não tem permissão para configurar relatórios!';
        }

        return $data;            
    }

    public function AlunosSemCadastroTelegram(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        /* FAZENDO A VERIFICAÇÃO DE PERMISSAO DO USUÁRIO */

        if($ownauthcontroller->PermissaoCheck(1)) {
            $omcts = OMCT::get();            
        } else {
            $omcts = OMCT::where('id', session()->get('login.omctID'))->get();            
        }

        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.alunos_sem_cadastro_telegram').'" method="get" target="_blank">';
        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';

        $data[] = '<select name="omctID" class="custom-select required_to_show_button">';
        $data[] = '<option value="0" selected>Selecione uma UETE</option>';
        foreach($omcts as $omct){
            if($omct->id!=1){
                $data[] = '<option value="'.$omct->id.'">'.$omct->omct.'</option>' ;
            }
        }
        $data[] = '</select>';
        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$(\'form#submit-relatorio\').submit();">Gerar relação</button>
                    </div>';
        $data[] = '</form>'; 

        return implode('', $data);

    }

    public function resultadoFinalPB(Request $request){
        $data[] = '<form id="submit-relatorio" action="'.route('relatorios.relacao_final_periodo_basico').'" method="get" target="_blank">';
        $data[] = '<input type="hidden" name="ano_formacao_id" value="'.$request->id_ano_formacao.'">';
        $data[] = ' <div style="margin-top: 24px;">
                        <button id="submit-relatorio" type="button" class="btn btn-primary" onclick="$(\'form#submit-relatorio\').submit();">Gerar relação</button>
                    </div>';
        $data[] = '</form>'; 

        return implode('', $data);        
    }
}

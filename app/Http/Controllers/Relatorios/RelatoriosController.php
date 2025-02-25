<?php

namespace App\Http\Controllers\Relatorios;

/* MODELS */

//use App\Models\AnoFormacao;
use App\Models\Alunos;
use App\Models\AlunosClassificacao;
use App\Models\AlunosConselhoEscolar;
use App\Models\AlunosSitDivHistorico;
use App\Models\AlunosSitDiv;
use App\Models\AlunosVoluntAv;
use App\Models\AnoFormacao;
use App\Models\Areas;
use App\Models\AvaliacaoTaf;
use App\Models\Avaliacoes;
use App\Models\AvaliacoesNotas;
use App\Models\AvaliacoesProntoFaltas;
use App\Models\AvaliacoesProntoFaltasStatus;
use App\Models\Disciplinas;
use App\Models\EscolhaQMSAlunosOpcoes;
use App\Models\Mencoes;
use App\Models\OMCT;
use App\Models\QMS;
use App\Models\TelegramAlunoAuth;
use App\Models\ConfDemonstrativos;

/* CONTROLLERS */

use App\Http\Controllers\OwnAuthController;

/*

OUTRAS CLASSES

*/

use App\Http\OwnClasses\EscolhaQMSLoader;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Models\EscolhaQMS;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RelatoriosController extends Controller
{

    protected $classLog;
    protected $ownauthcontroller;

    public function __construct(\App\Http\OwnClasses\ClassLog $classLog, OwnAuthController $ownauthcontroller, Request $request){
        $this->classLog = $classLog;
        $this->ownauthcontroller = $ownauthcontroller;
        $this->classLog->ip = $request->ip();  
    }    

    public function LoadHistoricoAluno(OwnAuthController $ownauthcontroller, Request $request){
        $alunos = AlunosSitDivHistorico::where('aluno', $request->alunoHistoricoID)->get();
        $this->classLog->RegistrarLog('Acessou histórico escolar de aluno', auth()->user()->email);
        return view('relatorios.historico-escolar-aluno')->with('alunos', $alunos);         
    }
    
    public function DadosEstatisticosDeAvaliacoes(OwnAuthController $ownauthcontroller, Request $request) {

        // requests
        
        //ano_formacao_id
        //avaliacaoID

        //dd($request->all());

        $ava = Avaliacoes::find($request->avaliacaoID);

        $ano_selecionado = AnoFormacao::find($request->ano_formacao_id);

        $avaliacoes_notas = AvaliacoesNotas::where('avaliacao_id', $request->avaliacaoID)->get();

        $mencoes = Mencoes::orderBy('id', 'desc')->get();
        foreach($avaliacoes_notas as $item){
            foreach($mencoes as $mencao){
                $nota = number_format($item->getNota(), 3, '.', '');
                if($nota>=$mencao->inicio && $nota<=$mencao->fim){
                    $total_mencao[$mencao->mencao][] = 1;
                    break; 
                }
            }
            $total_notas_array[] = 1;
        }

        $total_notas = (isset($total_notas_array))?array_sum($total_notas_array):0;

        if(isset($total_mencao)){
            foreach($mencoes as $mencao){
                if(isset($total_mencao[$mencao->mencao])) {
                    $mencao_qtde[$mencao->mencao] = array_sum($total_mencao[$mencao->mencao]);
                } else {
                    $mencao_qtde[$mencao->mencao] = 0;
                }              
            }
                        
        }
        $this->classLog->RegistrarLog('Acessou dados estatísticos de avaliação', auth()->user()->email);
        return view('relatorios.dados-estatisticos-avaliacoes')->with('mencao_qtde', $mencao_qtde)
                                                               ->with('total_notas', $total_notas)       
                                                               ->with('ano_selecionado', $ano_selecionado)       
                                                               ->with('ava', $ava)       
                                                               ->with('mencoes', $mencoes);       

        
    }


    public function AlunosConselhoEscolar(Request $request) {

        // SELECIONANDO O ANO DE FORMAÇÃO DO RELATÓRIO
        
        $ano_selecionado = AnoFormacao::find($request->ano_formacao_id);
        
        // SELECIONANDO TODOS OS ALUNOS DO ANO DE FORMAÇÃO

        $alunosID = Alunos::where('data_matricula', $request->ano_formacao_id)->get(['id']);

        // SELECIONANDO TODAS AS DISCIPLINAS DO ANO DE FORMAÇÃO

        $disciplinas = Disciplinas::where('ano_formacao_id', $request->ano_formacao_id)->get();
        
        foreach($disciplinas as $disc){
            $disciplina[$disc->id] = array(
                "nome_disciplina" => $disc['nome_disciplina'],
                "nome_disciplina_abrev" => $disc['nome_disciplina_abrev']
            );
        }

        $disciplina[99999] = array(
            //"nome_disciplina" => 'TESTE DE APITIDÃO FÍSICA',
            //"nome_disciplina_abrev" => 'TAF'
            "nome_disciplina" => 'TREINAMENTO FÍSICO MILITAR 1',
            "nome_disciplina_abrev" => 'TFM1'
        );        
        
        // SELECIONANDO TODAS AS UETEs
        $omcts = OMCT::where('id', '<>', 1)->get();

        // SELECIONANDO TODOS OS ALUNOS APROVADOS EM CONSELHO

        $alunos_conselho = AlunosConselhoEscolar::whereIn('aluno_id', $alunosID)->get();

        $this->classLog->RegistrarLog('Acessou lista de alunos em conselho de ensino', auth()->user()->email);
        return view('relatorios.alunos-em-conselho')->with('alunos', $alunos_conselho)
                                                    ->with('ano_selecionado', $ano_selecionado)
                                                    ->with('disciplina', $disciplina)
                                                    ->with('omcts', $omcts);
        
    }

    public function RelatoriosEscolhaQMS (OwnAuthController $ownauthcontroller, Request $request, EscolhaQMSLoader $escolhaQMS) {
        
        $valida = FuncoesController::validaSessao();

        if (isset($valida)) {
            return $valida;
        }

        $relacao = $request->relacao;

        if(!$ownauthcontroller->PermissaoCheck(1) && $request->omctID!=session()->get('login.omctID')){
            
            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';
            
        } else {
            
            $escolhaQMS->ano_formacao = $request->ano_formacao_id;
            $escolhaQMS->escolha_qms_id = $request->escolhaQMS;
    
            $ano_formacao = AnoFormacao::find($request->ano_formacao_id);
            $ano_selecionado = (isset($ano_formacao->formacao))? $ano_formacao->formacao:'---';

            // CASO SEJA ESCOLHA DE QMS AVIAÇÃO MASC OU FEM

            if($request->tipo_relatorio==3 || $request->tipo_relatorio==4){

                $this->classLog->RegistrarLog('Acessou relatório de escolha de QMS AVIAÇÃO', auth()->user()->email);
                $segmento = ($request->tipo_relatorio==3)?'M':'F';
                return view('relatorios.escolha-qms-aviacao')->with('alunos', $escolhaQMS->getAlunosAviacao($segmento))
                                                             ->with('ano_selecionado', $ano_selecionado);
                                                             
            } else if($request->tipo_relatorio==11 || $request->tipo_relatorio==12){
                
                $this->classLog->RegistrarLog('Acessou relatório de escolha de QMS', auth()->user()->email);
                $segmento = ($request->tipo_relatorio==11)?'M':'F';
                return view('relatorios.escolha-qms-aviacao')->with('alunos', $escolhaQMS->getAlunosAviacao($segmento, 'S'))
                                                             ->with('ano_selecionado', $ano_selecionado);
            
            } else if($request->tipo_relatorio==1 || $request->tipo_relatorio==2){
                
                $segmento = ($request->tipo_relatorio==1)?'M':'F';

                if($segmento=='M'){
                    $qms = QMS::where('escolha_qms_id', $request->escolhaQMS)->where('segmento', $segmento)->where('qms_alias', '<>', 'aviacao')->get();
                } else {
                    $qms = QMS::where('escolha_qms_id', $request->escolhaQMS)->where('segmento', $segmento)->where('qms_alias', '<>', 'aviacao_feminino')->get();
                }

                foreach($qms as $item){
                    $qms_id_nome[$item->id] = $item->qms_sigla;
                }


                $this->classLog->RegistrarLog('Acessou relatório de escolha de QMS', auth()->user()->email);
                return view('relatorios.escolha-qms-comb-log')->with('alunos', $escolhaQMS->designacaoFinalQMS($segmento, 'por_area'))
                                                              ->with('total_opcoes', count($qms))
                                                              ->with('qms_id_nome', $qms_id_nome)
                                                              ->with('ano_selecionado', $ano_selecionado);
                

            } else if($request->tipo_relatorio==7 || $request->tipo_relatorio==8){
                
                $segmento = ($request->tipo_relatorio==7)?'M':'F';

                if($segmento=='M'){
                    $qms = QMS::where('escolha_qms_id', $request->escolhaQMS)->where('segmento', $segmento)->where('qms_alias', '<>', 'aviacao')->get();
                } else {
                    $qms = QMS::where('escolha_qms_id', $request->escolhaQMS)->where('segmento', $segmento)->where('qms_alias', '<>', 'aviacao_feminino')->get();
                }

                foreach($qms as $item){
                    $qms_id_nome[$item->id] = $item->qms_sigla;
                }


                $this->classLog->RegistrarLog('Acessou relatório de escolha de QMS', auth()->user()->email);
                return view('relatorios.escolha-qms-comb-log')->with('alunos', $escolhaQMS->designacaoFinalQMS($segmento, 'por_area', 'S'))
                                                              ->with('total_opcoes', count($qms))
                                                              ->with('qms_id_nome', $qms_id_nome)
                                                              ->with('ano_selecionado', $ano_selecionado);
                

            } else if($request->tipo_relatorio==5 || $request->tipo_relatorio==6) {
                
                $segmento = ($request->tipo_relatorio==5)?'M':'F';
                
                if($segmento=='M'){
                    $qms = QMS::where('escolha_qms_id', $request->escolhaQMS)->where('segmento', $segmento)->where('qms_alias', '<>', 'aviacao')->get();
                } else {
                    $qms = QMS::where('escolha_qms_id', $request->escolhaQMS)->where('segmento', $segmento)->where('qms_alias', '<>', 'aviacao_feminino')->get();
                }

                foreach($qms as $item){
                    $qms_id_nome[$item->id] = $item->qms_sigla;
                }

                $this->classLog->RegistrarLog('Acessou relatório de escolha de QMS', auth()->user()->email);
                return view('relatorios.escolha-qms-por-qms')->with('alunos', $escolhaQMS->designacaoFinalQMS($segmento, 'por_qms'))
                                                             ->with('qms_id_nome', $qms_id_nome)
                                                             ->with('qms', $qms)
                                                             ->with('ano_selecionado', $ano_selecionado);


            } else if($request->tipo_relatorio==9 || $request->tipo_relatorio==10) {
                
                $segmento = ($request->tipo_relatorio==9)?'M':'F';
                
                if($segmento=='M'){
                    $qms = QMS::where('escolha_qms_id', $request->escolhaQMS)->where('segmento', $segmento)->where('qms_alias', '<>', 'aviacao')->get();
                } else {
                    $qms = QMS::where('escolha_qms_id', $request->escolhaQMS)->where('segmento', $segmento)->where('qms_alias', '<>', 'aviacao_feminino')->get();
                }

                foreach($qms as $item){
                    $qms_id_nome[$item->id] = $item->qms_sigla;
                }

                $this->classLog->RegistrarLog('Acessou relatório de escolha de QMS', auth()->user()->email);
                return view('relatorios.escolha-qms-por-qms')->with('alunos', $escolhaQMS->designacaoFinalQMS($segmento, 'por_qms', 'S'))
                                                             ->with('qms_id_nome', $qms_id_nome)
                                                             ->with('qms', $qms)
                                                             ->with('ano_selecionado', $ano_selecionado);


            } else if($request->tipo_relatorio==13 || $request->tipo_relatorio==14) {
                
                $segmento = ($request->tipo_relatorio==13)?'M':'F';
                
                $bi_bloqueio = null;
                if($segmento=='M'){
                    $qms = QMS::where('escolha_qms_id', $request->escolhaQMS)->where('segmento', $segmento)->where('qms_alias', '<>', 'aviacao')->get();
                    $bi_bloqueio = EscolhaQMS::select('bi_qms_masculino')->where([['id', '=', $request->escolhaQMS]])->first()->bi_qms_masculino;
                    //$qms = QMS::where('escolha_qms_id', $request->escolhaQMS)->where('segmento', $segmento)->get();
                } else {
                    $qms = QMS::where('escolha_qms_id', $request->escolhaQMS)->where('segmento', $segmento)->where('qms_alias', '<>', 'aviacao_feminino')->get();
                    $bi_bloqueio = EscolhaQMS::select('bi_qms_feminino')->where([['id', '=', $request->escolhaQMS]])->first()->bi_qms_feminino;
                    //$qms = QMS::where('escolha_qms_id', $request->escolhaQMS)->where('segmento', $segmento)->get();
                }
                
                foreach($qms as $item){
                    $qms_id_nome[$item->id] = $item->qms_sigla;
                }

                $rota = 'ajax/aplicar-escolha-qms';
                $rota_bi = 'ajax/aplicar-escolha-qms-bi';

                /*Novo Modo*/
                $designacao = $escolhaQMS->designacaoFinalQMSDetalhada($segmento, 'N');
                //$designacao = $escolhaQMS->designacaoFinalQMSDetalhada($segmento, 'S');

                if(key_exists('aluno', $designacao)){
                    $designacao['total_opcoes'] = count($qms);
                    $designacao['qms_id_nome'] = $qms_id_nome;
                    $designacao['ano_selecionado'] = $ano_selecionado;
                    $designacao['ano_formacao'] = $ano_formacao;
                    $designacao['ownauthcontroller'] = $this->ownauthcontroller;
                    $designacao['segmento'] = $segmento;
                    $designacao['bi_bloqueio'] = $bi_bloqueio;

                    $request->session()->flash('aplicar_qms', serialize($designacao));
                }
                /*Fim novo Modo*/

                $this->classLog->RegistrarLog('Acessou relatório de escolha de QMS', auth()->user()->email);
                return view('relatorios.escolha-qms-comb-log-detalhada')->with('data', $designacao)
                                                                        ->with('total_opcoes', count($qms))
                                                                        ->with('qms_id_nome', $qms_id_nome)
                                                                        ->with('ano_selecionado', $ano_selecionado)
                                                                        ->with('ownauthcontroller', $this->ownauthcontroller)
                                                                        ->with('rota_bi', $rota_bi)
                                                                        ->with('rota', $rota)
                                                                        ->with('relacao', $relacao);


            } else if($request->tipo_relatorio==15 || $request->tipo_relatorio==16) {
                
                $segmento = ($request->tipo_relatorio==15)?'M':'F';
                
                if($segmento=='M'){
                    $qms = QMS::where('escolha_qms_id', $request->escolhaQMS)->where('segmento', $segmento)->where('qms_alias', '<>', 'aviacao')->get();
                } else {
                    $qms = QMS::where('escolha_qms_id', $request->escolhaQMS)->where('segmento', $segmento)->where('qms_alias', '<>', 'aviacao_feminino')->get();
                }

                foreach($qms as $item){
                    $qms_id_nome[$item->id] = $item->qms_sigla;
                }

                //dd($escolhaQMS->designacaoFinalQMSDetalhada($segmento));

                $this->classLog->RegistrarLog('Acessou relatório de escolha de QMS', auth()->user()->email);
                return view('relatorios.escolha-qms-comb-log-detalhada')->with('data', $escolhaQMS->designacaoFinalQMSDetalhada($segmento, 'S'))
                                                                        ->with('total_opcoes', count($qms))
                                                                        ->with('qms_id_nome', $qms_id_nome)
                                                                        ->with('ano_selecionado', $ano_selecionado);


            }
            //$qms = $escolhaQMS->getQMS();
            //dd($escolhaQMS->getClassificacao());
            //dd($escolhaQMS->getAlunosOpcoes([12, 21]));
            //dd($escolhaQMS->getVagasQMSAviacao('M'));
        }

    }

    public function ProntoDeFaltas(Request $request) {
        
        /* SELECIONANDO UETEs */
        $omcts = OMCT::get();
        $status_pronto_faltas = AvaliacoesProntoFaltasStatus::where('avaliacao_id', $request->avaliacaoID)->get(); 
        $pronto_faltas = AvaliacoesProntoFaltas::where('avaliacao_id', $request->avaliacaoID)->get(); 
        $avaliacao = Avaliacoes::find($request->avaliacaoID);
              
        /**
         *  Verifico se é avaliação de 2 chamada.
         *  Caso seja avaliação de segunda chamada, vefifico a avaliação de referencia
         */

        if($avaliacao->chamada==2){
            // AVALIAÇAO DE REFERENCIA
            $ava_ref = Avaliacoes::find($avaliacao->chamada_refer_id);
            $status_pronto_faltas_av_ref = AvaliacoesProntoFaltasStatus::whereNotIn('omcts_id', $status_pronto_faltas->pluck('omcts_id')->toArray())->where(['avaliacao_id' => $ava_ref->id, 'status' => 0])->get();
            $pronto_faltas_av_ref = AvaliacoesProntoFaltas::whereIn('omcts_id', $status_pronto_faltas_av_ref->pluck('omcts_id')->toArray())->where(['avaliacao_id' => $avaliacao->chamada_refer_id])->get();
        }

        $status_pronto_faltas_av_ref = ($status_pronto_faltas_av_ref)??[];
        $pronto_faltas_av_ref = ($pronto_faltas_av_ref)??[];

        $this->classLog->RegistrarLog('Acessou relatório do pronto de faltas', auth()->user()->email);
        return view('relatorios.pronto-de-faltas')->with('omcts', $omcts)
                                                  ->with('status_pronto_faltas', $status_pronto_faltas)
                                                  ->with('avaliacao', $avaliacao)
                                                  ->with('status_pronto_faltas_av_ref', $status_pronto_faltas_av_ref)
                                                  ->with('pronto_faltas_av_ref', $pronto_faltas_av_ref)
                                                  ->with('pronto_faltas', $pronto_faltas);    
    }

    public function AlunosRecuperacao(OwnAuthController $ownauthcontroller, Request $request) {
        
        if(FuncoesController::validaSessao()){
            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';
        }

        // SELECIONANDO O ANO DE FORMAÇÃO DO RELATÓRIO
        $ano_selecionado = AnoFormacao::find($request->ano_formacao_id); 

        $omcts = OMCT::get();
        $disciplinas = Disciplinas::where([['ano_formacao_id', '=', $ano_selecionado->id]])->get();

        foreach($disciplinas as $disciplina){
            $disciplina_array[$disciplina->id] = $disciplina->nome_disciplina_abrev;
        }

        $disciplina_array[99999] = 'TFM 1';
        $disciplina_array[88888] = 'TAF 1 RECUPERAÇÃO';       
        
        if($ownauthcontroller->PermissaoCheck(1)){
            // selecioando todos os alunos do ano de formação selecionado de todas UETEs
            $alunos = Alunos::where('data_matricula', $request->ano_formacao_id)->orderBy('omcts_id', 'asc')->get(['id', 'numero', 'nome_guerra', 'omcts_id', 'turma_id']);
        } else {
            // selecioando todos os alunos do ano de formação selecionado DA UETEs DO OPERADOR
            $alunos = Alunos::where('data_matricula', $request->ano_formacao_id)->where('omcts_id', session()->get('login.omctID'))->get(['id', 'numero', 'nome_guerra', 'omcts_id', 'turma_id']);
        }

        /*if($alunos){
            foreach($alunos as $aluno){
                $alunosIDs[] = $aluno->id;
            }
        }

        if(isset($alunosIDs)){*/
        if(count($alunos) > 0){

            //$alunos_classificacao = AlunosClassificacao::whereIn('aluno_id', $alunosIDs)->where('reprovado', 'S')->get();

            $param['anoFormacao'] = $ano_selecionado;
            $avaliacoes = Avaliacoes::whereHas('disciplinas', function($q) use ($param){
                $q->where([['ano_formacao_id', '=', $param['anoFormacao']->id]]);
            })->get();

            foreach($avaliacoes as $avaliacao){
                $avaliacoesIDs[] = $avaliacao->id;
            }
    
            $avaliacoesIDs = (isset($avaliacoesIDs))?array_unique($avaliacoesIDs):array(0);

            //2ºTen João Victor, Alteração no Cálculo da NOTA
            $aluno_notas = FuncoesController::recalculaNotaAluno(AvaliacoesNotas::whereIn('avaliacao_id', $avaliacoesIDs)->get());
            //Fim Alteração 2ºTen João Victor

            $alunos_recuperacao = array();

            
            foreach($aluno_notas as $key_dsp => $disciplina){
                foreach($disciplina as $key => $aluno){
                    if(($aluno['disciplina_razao'] > 0) && ($aluno['media'] < 5)){
                        
                        $alunos_recuperacao[$key][$key_dsp] = $aluno;
                        $alunos_recuperacao[$key]['aluno'] = $alunos->find($key);
                    }
                }
            }
            
        } else {
            return '<div style="text-align: center;">SEM ALUNOS NO UNIVERSO PARA EXIBIÇÃO</div>';
        }

        $this->classLog->RegistrarLog('Acessou relatório de alunos em recuperação', auth()->user()->email);
        return view('relatorios.alunos-em-recuperacao')//->with('alunos_classificacao', $alunos_classificacao)
                                                       ->with('alunos_recuperacao', $alunos_recuperacao)
                                                       //->with('omcts', $omcts)
                                                       ->with('ano_selecionado', $ano_selecionado);
                                                       //->with('disciplinas', $disciplina_array);

    }

    public function AlunosEmRecPorDisciplina(OwnAuthController $ownauthcontroller, Request $request) {


        if(FuncoesController::validaSessao()){
            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';
        }

        $disciplina = Disciplinas::find($request->disciplina_id);

        $ano_selecionado = AnoFormacao::find($request->ano_formacao_id);

        // SELECIONANDO TODAS AS AVALIAÇÕES DA DISCIPLINA ACIMA SELECIONADA (inclusive 2 chamadas)

        $avaliacoes = Avaliacoes::where('disciplinas_id', $request->disciplina_id)->where('avaliacao_recuperacao', 0)->get();

        foreach($avaliacoes as $avaliacao){
            $avaliacoesIDs[] = $avaliacao->id;
        }

        $avaliacoesIDs = (isset($avaliacoesIDs))?array_unique($avaliacoesIDs):array(0);

        //2ºTen João Victor, Alteração no Cálculo da NOTA
        $aluno_notas = FuncoesController::recalculaNotaAluno(AvaliacoesNotas::whereIn('avaliacao_id', $avaliacoesIDs)->get());
        //Fim Alteração 2ºTen João Victor
        
        if($ownauthcontroller->PermissaoCheck(1) || $ownauthcontroller->PermissaoCheck(20)){
            $alunos = Alunos::where('data_matricula', $request->ano_formacao_id)->get(['id']);
        } else {
            $alunos = Alunos::where('data_matricula', $request->ano_formacao_id)->where('omcts_id', session()->get('login.omctID'))->get(['id']);
        }

        //dd($aluno_notas[48][4406]);
        foreach($alunos as $aluno){
            foreach($aluno_notas as $notas){
                
                if(isset($notas[$aluno->id]) && ($notas[$aluno->id]['disciplina_razao'] > 0)){
                    /*$media = (array_sum($alunos[$aluno->id]['notas'])/$alunos[$aluno->id]['disciplina_razao']);
    
                    if($media<5){
                        $nd_aluno[$aluno->id] = number_format($media, 3, ',', ''); 
                        $alunos_em_recuperacao[] = $aluno->id;
                    }*/
                    
                    try{
                        if($notas[$aluno->id]['tfm'] == 'S' 
                            && $notas[$aluno->id]['tfm_abdominal'] == 'S'){
                                if(isset($notas[$aluno->id]['media_tfm_abdominal'])){
                                    if($notas[$aluno->id]['media_tfm_abdominal'] == 'NS'){
                                        $nd_aluno[$aluno->id] = $notas[$aluno->id]['media_tfm_abdominal'];
                                        $alunos_em_recuperacao[] = $aluno->id;
                                    }
                                }else{
                                    $nd_aluno[$aluno->id] = 'Sem Lançamento de AC';
                                    $alunos_em_recuperacao[] = $aluno->id;
                                }
                        }else{
                            $media = $notas[$aluno->id]['media'];
                            if($media<5){
                                $nd_aluno[$aluno->id] = number_format($media, 3, ',', ''); 
                                $alunos_em_recuperacao[] = $aluno->id;
                            }
                        }
                    }catch (Exception $ex){
                        dd($notas[$aluno->id]);
                    }
                }    
            }
        }

        $nd_aluno = ($nd_aluno)??array(0);
        $alunos_em_recuperacao = ($alunos_em_recuperacao)??array(0);

        //if($ownauthcontroller->PermissaoCheck(1) || $ownauthcontroller->PermissaoCheck(20)){
        if($ownauthcontroller->PermissaoCheck(1)){
            $alunos = Alunos::whereIn('id', $alunos_em_recuperacao)->orderBy('omcts_id', 'asc')->get();
        } else {
            $alunos = Alunos::whereIn('id', $alunos_em_recuperacao)->where('omcts_id', session()->get('login.omctID'))->orderBy('omcts_id', 'asc')->get();
        }
        $this->classLog->RegistrarLog('Acessou lista de alunos em recuperação', auth()->user()->email);
        return view('relatorios.alunos-em-recuperacao-por-disciplina')->with('alunos', $alunos)
                                                                      ->with('ownauthcontroller', $ownauthcontroller)
                                                                      ->with('nd_aluno', $nd_aluno)
                                                                      ->with('ano_selecionado', $ano_selecionado)
                                                                      ->with('disciplina', $disciplina);       
    }

    public function ClassificacaoGeral(OwnAuthController $ownauthcontroller, Request $request){

        if($ownauthcontroller->PermissaoCheck(1)){

            $ano_formacao = AnoFormacao::find($request->ano_formacao_id);
            $ano_selecionado = (isset($ano_formacao->formacao))? $ano_formacao->formacao:'---';

            if($request->options_class_geral == 10){//Classificação Geral por QMS
                $escolhaQMS = EscolhaQMS::where([['ano_formacao_id', '=', $ano_formacao->id]])->first();

                if(isset($escolhaQMS->bi_qms_masculino) && isset($escolhaQMS->bi_qms_feminino)){

                    //Junta os alunos da escolha de QMS...
                    $listaAlunos = FuncoesController::ArrayMergeKeepKeys(unserialize($escolhaQMS->escolha_qms_masculino)['aluno'], unserialize($escolhaQMS->escolha_qms_feminino)['aluno']);
                    $idsAlunos = array_keys($listaAlunos); 

                    //Junta os alunos da Escolha de QMS Aviação...
                    $listaAlunosAviacao = FuncoesController::ArrayMergeKeepKeys(unserialize($escolhaQMS->escolha_qms_masculino)['alunos_aviacao'], unserialize($escolhaQMS->escolha_qms_feminino)['alunos_aviacao']);
                    $idsAlunosAviacao = array_keys($listaAlunosAviacao); 
                    //Fim Junta...

                    $listaAlunos = FuncoesController::ArrayMergeKeepKeys($listaAlunos, $listaAlunosAviacao);
                    $idsAlunos = array_merge($idsAlunos, $idsAlunosAviacao);

                    $alunos = Alunos::where('data_matricula', $ano_formacao->id)
                                    ->whereIn('id', $idsAlunos)
                                    ->with('classificacao')
                                    ->get()
                                    ->sortBy('classificacao.classificacao');

                    $mencoes = Mencoes::get();

                    foreach($alunos as $aluno){
                        if(!isset($listaAlunos[$aluno->id]['qmsdesignda_nome_sigla'])){
                            $listaAlunos[$aluno->id]['qmsdesignda_nome_sigla'] = 'Avi';
                        }

                        if(isset($aluno->classificacao)){
                            $aluno->data_demonstrativo = unserialize($aluno->classificacao->data_demonstrativo);
                            
                            $aluno->mencao = 'Não calculada';
                            foreach($mencoes as $mencao){
                                if($aluno->classificacao->nota_final_arredondada>=$mencao->inicio && $aluno->classificacao->nota_final_arredondada<=$mencao->fim){
                                    $aluno->mencao = $mencao;
                                    break;
                                }
                            }

                            $qmsNomeSigla[] = $listaAlunos[$aluno->id]['qmsdesignda_nome_sigla'];
                            $listaAlunoQms[$listaAlunos[$aluno->id]['qmsdesignda_nome_sigla']][] = $aluno;
                        }
                    }                
                   
                    $qmsNomeSigla = array_unique($qmsNomeSigla);
                    
                    $qmss = QMS::whereHas('escolhaQms', function($q) use ($ano_formacao){
                        $q->where('ano_formacao_id', $ano_formacao->id);
                    })->whereIn('qms_sigla', $qmsNomeSigla)->select('qms', 'qms_sigla')->distinct()->get();
                    
                    $this->classLog->RegistrarLog('Acessou lista de classificação geral por QMS de alunos', auth()->user()->email);
                    return view('relatorios.classificacao-geral-qms', compact('listaAlunoQms', 'qmss', 'ano_selecionado'))
                                    ->with('disciplinas', Disciplinas::where('ano_formacao_id', $ano_formacao->id)->get());
                }else{
                    return '<div style="text-align: center;">ESCOLHA DE QMS NÃO FINALIZADA COM BI DO CMT da ESA</div>';
                }
            }

            // SELECIONANDO DISCIPLINAS

            $disciplinas = Disciplinas::where('ano_formacao_id', $ano_formacao->id)->get();

            // SELECIONANDO TODAS AS AREAS

            $areas = Areas::whereNotIn('id', [4,5])->get();

            $alunos_classificacao = AlunosClassificacao::where('ano_formacao_id', $ano_formacao->id)->orderBy('nota_final', 'desc')->get();

            // VERIFICANDO SE HÁ NOTAS REPETIDAS

            foreach($alunos_classificacao as $nota){
                $notas_array[] = ''.$nota->nota_final.'';
                $notas_data_array[$nota->aluno_id] = array(
                    "NPB" => $nota->nota_final,
                    "data_demonstrativo" => unserialize($nota->data_demonstrativo)
                ); 

                /*if($nota->aluno_id == 3207){
                    dd(unserialize($nota->data_demonstrativo));
                }*/
            }

            if(isset($notas_array)){
                foreach(array_count_values($notas_array) as $key => $val) {
                    if($val>1){
                        $nota_final[str_replace('.', ',', $key)] = $val;
                    }
                }
            } else {
                $nota_final = array();                
            }

            //dd($nota_final);

            $mencoes = Mencoes::get();

            $this->classLog->RegistrarLog('Acessou lista de classificação geral de alunos', auth()->user()->email);
            return view('relatorios.classificacao-geral')->with('alunos_classificacao', $alunos_classificacao)
                                                         ->with('notas_data_array', $notas_data_array)
                                                         ->with('nota_final_empatada', $nota_final)
                                                         ->with('areas', $areas)
                                                         ->with('disciplinas', $disciplinas)
                                                         ->with('mencoes', $mencoes)
                                                         ->with('ano_selecionado', $ano_selecionado);

        } else {
            $this->classLog->RegistrarLog('Teve acesso negado ao tenter acessar a classificação geral de alunos', auth()->user()->email);
            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';
        }

    }

    public function AlunosNaoEscolheramQMS(OwnAuthController $ownauthcontroller, Request $request){

        if(!$ownauthcontroller->PermissaoCheck(1) && $request->omctID!=session()->get('login.omctID')){
            $this->classLog->RegistrarLog('Teve acesso negado ao tenter acessar a lista de alunos que não escolheram QMS', auth()->user()->email);
            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';

        } else {

            $ano_formacao = AnoFormacao::find($request->ano_formacao_id);

            /*
            
                Selecioando todos os alunos que preencheram a escolha de QMS com ID recebido do select name=escolhaQMS e que
                estão com o status de finalizada

            */

            $alunos_que_fizeramID = EscolhaQMSAlunosOpcoes::where('escolha_qms_id', $request->escolhaQMS)->where('finalizada', 'S')->get(['aluno_id']);

            if($request->omctID!='todas_omct'){
                $omct = OMCT::find($request->omctID);
                 $alunos = Alunos::where('data_matricula', $request->ano_formacao_id)
                                    ->where('omcts_id', $request->omctID)
                                    ->where('area_id', 1)
                                    ->whereNotIn('id', $alunos_que_fizeramID)
                                    ->orderBy('omcts_id', 'asc')
                                    ->orderBy('numero', 'asc')
                                    ->get();
                    
            } else {
                $omct = null;
                $alunos = Alunos::where('data_matricula', $request->ano_formacao_id)
                                    ->where('area_id', 1)
                                    ->whereNotIn('id', $alunos_que_fizeramID)
                                    ->orderBy('numero', 'asc')->get();
            }

            $this->classLog->RegistrarLog('Acessou alunos que não escolheram QMS', auth()->user()->email);
            return view('relatorios.alunos-sem-escolha-qms')->with('ano_formacao', $ano_formacao)
                                                                     ->with('ownauthcontroller', $ownauthcontroller)
                                                                     ->with('omct', $omct)
                                                                     ->with('alunos', $alunos);

        }

    }
    
    public function ComprovanteEscolhaQMS(OwnAuthController $ownauthcontroller, Request $request){

        if(!$ownauthcontroller->PermissaoCheck(1) && $request->omctID!=session()->get('login.omctID')){
            $this->classLog->RegistrarLog('Obteve acesso negado ao tentar acessar a lista de comprovamte de escolha de QMS', auth()->user()->email);
            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';

        } else {

            $omct = OMCT::find($request->omctID);

            if($omct){
                $omct_nome = $omct->omct;
            } else {
                $omct_nome = '';
            }

            $ano_formacao = AnoFormacao::find($request->ano_formacao_id);

            // SELECIOANANDO TODOS OS ALUNOS DA UETEs SELECIONADA
            
            $alunos = Alunos::where('omcts_id', $request->omctID)->where('area_id', 1)->where('data_matricula', $request->ano_formacao_id)->orderBy('numero', 'asc')->get();

            foreach($alunos as $aluno){
                $alunosID[] = $aluno->id;
            }

            $alunosID = ($alunosID)??array();

            // SELECIONANDO TODAS AS OPÇÕES DOS ALUNOS $alunosID DA ESCOLHA DE QMS DO ID $request->escolhaQMS
            
            $opcoes_alunos = EscolhaQMSAlunosOpcoes::where('finalizada', 'S')->whereIn('aluno_id', $alunosID)->where('escolha_qms_id', $request->escolhaQMS)->get();
            
            foreach($opcoes_alunos as $opcoes){
                $opcao_aluno[$opcoes->aluno_id] = array("opcoes" => $opcoes->opcoes,
                    "finalizada" => $opcoes->finalizada
                );
            }

            $opcao_aluno = ($opcao_aluno)??array();
            
            // SELECIONANDO AS QMS PARA GUARDAR SEUS NOMES EM UMA ARRAY

            $qms_list = QMS::where('escolha_qms_id', $request->escolhaQMS)->get();

            foreach($qms_list as $qms){
                $qms_data[$qms->id] = $qms->qms;
            }
            $this->classLog->RegistrarLog('Acessou canhoto para assinatura de escolha de QMS', auth()->user()->email);
            return view('relatorios.comprovante-escolha-qms')->with('ano_formacao', $ano_formacao)
                                                                     ->with('ownauthcontroller', $ownauthcontroller)
                                                                     ->with('opcao_aluno', $opcao_aluno)
                                                                     ->with('qms_data', $qms_data)
                                                                     ->with('omct_nome', $omct_nome)
                                                                     ->with('alunos', $alunos);

        }

    }

    public function ProntoLancamentoNotas(Request $request) {
        
        /* SELECIONANDO UETEs */

        $omcts = OMCT::get();

        /* SELECIONANDO AS AVALIAÇÕES DE REFERÊNCIA  */

        $avaliacao_ref = Avaliacoes::where('chamada_refer_id', $request->avaliacaoID)->get();

        foreach($avaliacao_ref as $avaliacao){
            $avaliacoesIDs[] = $avaliacao->id;
        }
        
        $avaliacoesIDs[] = $request->avaliacaoID;

        // SELECIONO TODOS IDs DE ALUNOS QUE POSSUEM NOTA NAS AVALIAÇÕES $avaliacoesIDs[]

        $avaliacoes_notas = AvaliacoesNotas::whereIn('avaliacao_id', $avaliacoesIDs)->get(['alunos_id']);
        foreach($avaliacoes_notas as $aluno){
            if($aluno->alunos_id!=null){
                $alunosIDs[] = $aluno->alunos_id;
            }
        }

        $alunosIDs = ($alunosIDs)??array();

        // SELECIONO OS ALUNOS DO ANO DE FORMAÇÃO QUE NÃO ESTEJAM EM $alunosIDs[] (POIS JÁ POSSUEM NOTA NA AVALIAÇÃO)

        $ano_formacao = AnoFormacao::find($request->ano_formacao_id);

        $alunos = DB::select('SELECT * FROM alunos WHERE data_matricula='.$request->ano_formacao_id.' AND id NOT IN('.implode(',', $alunosIDs).')');
        
        $avaliacao = Avaliacoes::find($request->avaliacaoID);
        $this->classLog->RegistrarLog('Acessou pronto de lançamento de notas', auth()->user()->email);
        return view('relatorios.pronto-lancamento-notas')->with('omcts', $omcts)
                                                         ->with('alunos', $alunos)
                                                         ->with('avaliacao', $avaliacao);
                                                      
    }

    public function ProntoLancamentoNotasAR(OwnAuthController $ownauthcontroller, Request $request) {

        $omcts = OMCT::get();

        $avaliacao_data = Avaliacoes::find($request->avaliacaoID);
        
        $disciplina = Disciplinas::find($avaliacao_data->disciplinas->id);

        // SELECIONANDO TODAS AS AVALIAÇÕES DA DISCIPLINA ACIMA SELECIONADA (inclusive 2 chamadas)
        $avaliacoes = Avaliacoes::where('disciplinas_id', $disciplina->id)->where('avaliacao_recuperacao', 0)->get();

        foreach($avaliacoes as $avaliacao){
            if($avaliacao->chamada==1){
                $disciplina_razao[] = 1; 
            }

            $avaliacoesIDs[] = $avaliacao->id;
        }

        $avaliacoesIDs = (isset($avaliacoesIDs))?array_unique($avaliacoesIDs):array(0);

        //2ºTen João Victor, Alteração no Cálculo da NOTA
        $aluno_notas = FuncoesController::recalculaNotaAluno(AvaliacoesNotas::whereIn('avaliacao_id', $avaliacoesIDs)->get());
        //Fim Alteração 2ºTen João Victor
        if($ownauthcontroller->PermissaoCheck(1)){
            $alunos = Alunos::where('data_matricula', $request->ano_formacao_id)->get(['id']);
        } else {
            $alunos = Alunos::where('data_matricula', $request->ano_formacao_id)->where('omcts_id', session()->get('login.omctID'))->get(['id']);
        }

        foreach($alunos as $aluno){
            foreach($aluno_notas as $notas){
                
                if(isset($notas[$aluno->id]) && ($notas[$aluno->id]['disciplina_razao'] > 0)){
                    
                    $media = $notas[$aluno->id]['media'];
                    if($media<5){
                        $nd_aluno[$aluno->id] = number_format($media, 3, ',', ''); 
                        $alunos_em_recuperacao[] = $aluno->id;
                    }
                }else if(isset($notas[$aluno->id]['tfm']) 
                    && ($notas[$aluno->id]['tfm'] == 'S' && $notas[$aluno->id]['tfm_abdominal'] == 'S')){
                    
                    foreach($notas[$aluno->id]['avaliacoes'] as $aval){
                        
                        if($aval->nota == 'NS'){
                            $alunos_em_recuperacao[] = $aluno->id;
                        }
                    }
                } 
            }
        }

        $nd_aluno = ($nd_aluno)??array(0);
        $alunos_em_recuperacao = ($alunos_em_recuperacao)??array(0);

        /*$razao = (isset($disciplina_razao))?array_sum($disciplina_razao):1;

        // SELECIONANDO TODAS AS NOTAS (avaliacoes_notas) DE TODAS AVALIAÇÕES EM $avaliacoesIDs
        $notas = AvaliacoesNotas::whereIn('avaliacao_id', $avaliacoesIDs)->get();
        
        foreach($notas as $item){
            if(!is_null($item->alunos_id)){
                $aluno_notas[$item->alunos_id][] = $item->getNota();
            }
        }

        foreach($alunos as $aluno){
            if(isset($aluno_notas[$aluno->id])){
                if((array_sum($aluno_notas[$aluno->id])/$razao)<5){
                    $alunos_em_recuperacao[] = $aluno->id;
                }
            }    
        }

        $alunos_em_recuperacao = ($alunos_em_recuperacao)??array(0);*/

        $avaliacao_notas = AvaliacoesNotas::whereIn('alunos_id', $alunos_em_recuperacao)->where('avaliacao_id', $request->avaliacaoID)->get();

        foreach($avaliacao_notas as $item){
            $aluno_fizeram_ar[]=$item->alunos_id;
        }

//dd($aluno_fizeram_ar, $alunos_em_recuperacao, $avaliacao_notas);

        $aluno_fizeram_ar = ($aluno_fizeram_ar)??array(0);

        foreach($alunos_em_recuperacao as $item){
            if(!in_array($item, $aluno_fizeram_ar)){
                $alunosIDs[] = $item;
            }
        }

        $alunosIDs = ($alunosIDs)??array(0);

        $alunos = Alunos::whereIn('id', $alunosIDs)->get();

        //dd($alunos_em_recuperacao);
        
        //dd(array_diff($alunos_em_recuperacao, $aluno_fizeram_ar));
        $this->classLog->RegistrarLog('Acessou pronto de lançamento de notas das avaliações de recuperação', auth()->user()->email);
        return view('relatorios.pronto-lancamento-notas-ar')->with('omcts', $omcts)
                                                         ->with('alunos', $alunos)
                                                         ->with('avaliacao', $avaliacao_data);
                                                      
    }

    public function ProntoLancamentoTAF(Request $request) {
        
        // SELECIONADO OS ALUNOS DO ANO DE FORMAÇÃO $request->ano_formacao_id;
        $avaliacoes_lancadas = AvaliacaoTaf::get();

        foreach($avaliacoes_lancadas as $aluno){
            if(isset($aluno->aluno->data_matricula) && $aluno->aluno->data_matricula==$request->ano_formacao_id){
                $alunosIDs[] = $aluno->aluno_id;
            }
        }

        $alunosIDs = ($alunosIDs)??array();

        // SELECIONO OS ALUNOS DO ANO DE FORMAÇÃO QUE NÃO ESTEJAM EM $alunosIDs[]

        //$alunos = DB::select('SELECT * FROM alunos WHERE data_matricula='.$request->ano_formacao_id.' AND id NOT IN('.implode(',', $alunosIDs).') ORDER BY omcts_id');
        $alunos = Alunos::where('data_matricula', $request->ano_formacao_id)->whereNotIn('id', $alunosIDs)->orderBy('omcts_id', 'asc')->get();

        //dd($alunos);
        $this->classLog->RegistrarLog('Acessou pronto de lançamento do TFM', auth()->user()->email);
        return view('relatorios.pronto-lancamento-taf')->with('alunos', $alunos);
                                                      
    }

    public function RelacaoAtletasMarexaer(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request) {

        if(!$ownauthcontroller->PermissaoCheck(1) && $request->omctID!=session()->get('login.omctID')){

            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';

        } else {

            $ano_formacao = AnoFormacao::find($request->ano_formacao_id);

            if($request->omctID!='todas_omct'){
                $omct = OMCT::find($request->omctID);
                $alunos = Alunos::where('data_matricula', $request->ano_formacao_id)->where('atleta_marexaer', 'S')->where('omcts_id', $request->omctID)->orderBy('omcts_id', 'asc')->orderBy('numero', 'asc')->get();
            } else {
                $omct = null;
                $alunos = Alunos::where('data_matricula', $request->ano_formacao_id)->where('atleta_marexaer', 'S')->orderBy('numero', 'asc')->get();
            }
            $this->classLog->RegistrarLog('Acessou relação de alunos atletas da MAREXAER', auth()->user()->email);
            return view('relatorios.alunos-atletas-marexaer')->with('ano_formacao', $ano_formacao)
                                                                     ->with('ownauthcontroller', $ownauthcontroller)
                                                                     ->with('omct', $omct)
                                                                     ->with('alunos', $alunos);

        }

    }

    public function DemonstrativoNotas(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        if(!$ownauthcontroller->PermissaoCheck(1) && $request->omctID!=session()->get('login.omctID')){

            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';

        } else {

            if(isset($request->alunos_ids) && $request->omctID!=1){
                $alunosID = Alunos::whereIn('id', $request->alunos_ids)->where('omcts_id', $request->omctID)->where('data_matricula', $request->ano_formacao_id)->get(['id']);
            } else if(isset($request->alunos_ids) && $request->omctID==1){
                $alunosID = Alunos::whereIn('id', $request->alunos_ids)->where('data_matricula', $request->ano_formacao_id)->get(['id']);    
            } else if(!isset($request->alunos_ids) && $request->omctID==1){
                $alunosID = Alunos::where('data_matricula', $request->ano_formacao_id)->get(['id']);
            } else {
                $alunosID = Alunos::where('omcts_id', $request->omctID)->where('data_matricula', $request->ano_formacao_id)->get(['id']);
            }

            $alunos_classif = AlunosClassificacao::whereIn('aluno_id', $alunosID)
            ->whereHas('aluno', function($q) use ($request) {
                $q->orderBy('numero', 'asc');
            })->get();

            //dd(unserialize($alunos_classif[0]->data_demonstrativo));
            $mencoes = Mencoes::get();
            $this->classLog->RegistrarLog('Acessou demonstrativo de notas', auth()->user()->email);
            return view('relatorios.demonstrativo-notas')->with('alunos_classif', $alunos_classif)
                                                         ->with('mencoes', $mencoes);        
                                                         //->with('class_por_area_seg', $class_por_area_seg)      
                                                         //->with('class_geral', $class_geral);        
        
        }

    }

    function ListaAssinavelDemonstrativoNotas(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        if(!$ownauthcontroller->PermissaoCheck(1) && $request->omctID!=session()->get('login.omctID')){

            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';

        } else {

            //omctID
            //avaliacaoID

            // PEGANDO OS DADOS DA AVALIAÇÃO

            $avaliacao = Avaliacoes::find($request->avaliacaoID);

            // FAZENDO UMA ARRAY COM OS IDs DOS ALUNOS QUE TEM NOTA NA AVALIAÇÃO

            $alunos = AvaliacoesNotas::where('avaliacao_id', $request->avaliacaoID)->get(['alunos_id']);
            
            foreach ($alunos as $aluno_rows) {
                $array_alunos_id[] = $aluno_rows->alunos_id;
            }
                                   
            $array_alunos_id = ($array_alunos_id)??array();
            
            // FILTRANDO, SELECIONANDO ALUNOS SOMENTE DA UETE SELECIONADA
            
            $alunos = Alunos::where('omcts_id', $request->omctID)->whereIn('id', $array_alunos_id)->orderBy('numero', 'asc')->get();
            
            foreach($alunos as $aluno_rows){
                $turmas[] =  $aluno_rows->turma_id;
            }
            
            $turmas = ($turmas)??array();

            sort($turmas);
            
            $turmas = array_unique($turmas);

            $ano_formacao = AnoFormacao::find($request->ano_formacao_id);
            $this->classLog->RegistrarLog('Acessou lista assinável do demonstrativo de notas', auth()->user()->email);
            return view('relatorios.assinatura-demonstrativo')->with('avaliacao', $avaliacao)
                                                              ->with('ano_formacao', $ano_formacao)
                                                              ->with('turmas', $turmas)
                                                              ->with('alunos', $alunos);

        }        
    }

    function ListaAssinavelDemonstrativoNotasPorAluno(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        /*if(!$ownauthcontroller->PermissaoCheck(1) && $request->omctID!=session()->get('login.omctID')){

            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';

        } else {*/

            //omctID
            //avaliacaoID

            // PEGANDO OS DADOS DA AVALIAÇÃO

            $ano_formacao = AnoFormacao::find($request->ano_formacao_id);

            $avaliacao = Avaliacoes::find($request->avaliacaoID);

            $alunos = Alunos::whereIn('id', $request->aluno_id)->orderBy('omcts_id', 'asc')->orderBy('numero', 'asc')->get();
            $this->classLog->RegistrarLog('Acessou lista assinável do demonstrativo de notas por aluno', auth()->user()->email);
            return view('relatorios.assinatura-demonstrativo-por-aluno')->with('avaliacao', $avaliacao)
                                                                        ->with('ano_formacao', $ano_formacao)
                                                                        ->with('alunos', $alunos);

        /*}  */      
    }

    function RelacaoAlunosProntos(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        if(!$ownauthcontroller->PermissaoCheck(1) && $request->omctID!=session()->get('login.omctID')){

            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';

        } else {

            $ano_formacao = AnoFormacao::find($request->ano_formacao_id);

            if(isset($request->segmento)){
                    $segmento_array = $request->segmento;         
            } else {
                $segmento_array = array('M', 'F');
            }

            if(isset($request->areas)){
                $areas_array = $request->areas;
            } else {
                $areas_array = array();
            }

            $alunos = Alunos::whereIn('sexo', $segmento_array)->whereIn('area_id', $areas_array)->where('omcts_id', $request->omctID)->where('data_matricula', $request->ano_formacao_id)->orderBy('numero', 'asc')->get();
            
            $omct = OMCT::find($request->omctID);
            $this->classLog->RegistrarLog('Acessou lista de alunos prontos', auth()->user()->email);
            return view('relatorios.relacao-alunos-prontos')->with('ano_formacao', $ano_formacao)
                                                            ->with('omct', $omct)
                                                            ->with('alunos', $alunos);

        }        
    }

    function RelacaoAlunosSituacoesDiversas(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        if(!$ownauthcontroller->PermissaoCheck(1) && $request->omctID!=session()->get('login.omctID')){

            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';

        } else {

            $ano_formacao = AnoFormacao::find($request->ano_formacao_id);

            if(isset($request->segmento)){
                    $segmento_array = $request->segmento;         
            } else {
                $segmento_array = array('M', 'F');
            }

            if(isset($request->areas)){
                $areas_array = $request->areas;
            } else {
                $areas_array = array();
            }

            if(isset($request->situacoes_diversas)){
                $situacoes_array = $request->situacoes_diversas;
            } else {
                $situacoes_array = array();
            }

            $alunos = AlunosSitDiv::whereIn('sexo', $segmento_array)->whereIn('area_id', $areas_array)->whereIn('situacoes_diversas_id', $situacoes_array)->where('omcts_id', $request->omctID)->where('data_matricula', $request->ano_formacao_id)->orderBy('numero', 'asc')->get();
            
            $omct = OMCT::find($request->omctID);
            $this->classLog->RegistrarLog('Acessou lista de alunos em situações diversas', auth()->user()->email);
            return view('relatorios.relacao-alunos-situacoes-diversas')->with('ano_formacao', $ano_formacao)
                                                                       ->with('omct', $omct)
                                                                       ->with('alunos', $alunos);

        }        
    }

    function RelacaoVoluntariosQMSAviacao(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){

        if(!$ownauthcontroller->PermissaoCheck(1) && $request->omctID!=session()->get('login.omctID')){

            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';

        } else {

            $ano_formacao = AnoFormacao::find($request->ano_formacao_id);
            $omct = (($request->omctID!='todas_omct') ? OMCT::find($request->omctID) : null);
            
            $param['anoCorrente'] = $ano_formacao;
            $param['uete'] = $omct;

            /* SELECIONANDO TODOS OS VOLUNTÁRIOS NA TABELA alunos_voluntarios_aviacao */
            $alunos_voluntarios_aviacao = AlunosVoluntAv::whereHas('aluno', function ($q) use ($param) {
                
                if($param['uete'] != null){
                    $where = [['data_matricula', '=', $param['anoCorrente']->id], ['omcts_id', '=', $param['uete']->id]];
                }else{
                    $where = [['data_matricula', '=', $param['anoCorrente']->id]];
                }
                $q->where($where);
            });

            switch($request->filtro_voluntarios){
                case 1:
                    $filtro_voluntario = 'VOLUNTÁRIOS';
                    $alunos_voluntarios_aviacao = $alunos_voluntarios_aviacao->get();
                break;
                case 2:
                    $filtro_voluntario = 'SELECIONADOS PARA OS EXAMES COMPLEMENTARES';
                    $alunos_voluntarios_aviacao = $alunos_voluntarios_aviacao->where('selecionado_exame', 'S')->get();
                break;
                case 3:
                    $filtro_voluntario = 'APROVADOS NA IS';
                    $alunos_voluntarios_aviacao = $alunos_voluntarios_aviacao->where('apto_is', 'S')->get();
                break;
                case 4:
                    $filtro_voluntario = 'APROVADOS NA AVL PSC';
                    $alunos_voluntarios_aviacao = $alunos_voluntarios_aviacao->where('apto_avi', 'S')->get();
                break;
                case 5:
                    $filtro_voluntario = 'APTOS';
                    $alunos_voluntarios_aviacao = $alunos_voluntarios_aviacao->where('apto', 1)->get();
                break;
                case 6:
                    $filtro_voluntario = 'INAPTOS';
                    $alunos_voluntarios_aviacao = $alunos_voluntarios_aviacao->where('apto', 0)->get();
                break;
            }
            
            $this->classLog->RegistrarLog('Acessou lista de alunos voluntários para QMS aviação', auth()->user()->email);
            return view('relatorios.relacao-voluntarios-qms-aviacao')->with('ano_formacao', $ano_formacao)
                                                                     ->with('ownauthcontroller', $ownauthcontroller)
                                                                     ->with('omct', $omct)
                                                                     ->with('filtro_voluntario', $filtro_voluntario)
                                                                     ->with('alunos_voluntarios_aviacao', $alunos_voluntarios_aviacao);

        }        
    }

    public function RelacaoAlunosSemCadastroTelegram(Request $request) {
        
        $omct = OMCT::find($request->omctID);
        $ano_formacao = AnoFormacao::find($request->ano_formacao_id);

        // SELECIONAR TODOS OS ALUNOS QUE NÃO TEM chat_id NO TELEGRAM

        $alunos = TelegramAlunoAuth::whereNull('chat_id')->get();

        foreach($alunos as $aluno){
            $aluno_omct = ($aluno->aluno->omcts_id)??0;
            if($aluno_omct==$omct->id && $aluno->aluno->data_matricula==$ano_formacao->id) {
                $alunosIDs[] = $aluno->aluno_id;
            }
        }

        $alunosIDs = ($alunosIDs)??array();

        $alunos = Alunos::whereIn('id', $alunosIDs)->orderBy('turma_id', 'asc')->orderBy('numero', 'asc')->get();
        $this->classLog->RegistrarLog('Acessou lista de alunos sem cadastro no TELEGRAM', auth()->user()->email);
        return view('relatorios.alunos-sem-cadastro-telegram')->with('omct', $omct)
                                                              ->with('alunos', $alunos)
                                                              ->with('ano_formacao', $ano_formacao);
    }

    public function relacaoFinalPeriodoBasico(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request) {
        
        $mencao = new Mencoes;
        $ano_corrente_data = AnoFormacao::find($request->ano_formacao_id);
        $ano_corrente = ($ano_corrente_data->id)??0;
        if(!$ownauthcontroller->PermissaoCheck(23)){
            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';
        } else {
            $rfpb = \App\Models\AlunosClassificacao::join('alunos', 'alunos_classificacao.aluno_id', '=', 'alunos.id')->where([
                ['ano_formacao_id', '=', $request->ano_formacao_id],
                ['reprovado', '=', 'N']
                ])->orderBy('classificacao', 'asc')->get();

            if(count($rfpb)==0){
                return '<div style="text-align: center;">RELAÇÃO NÃO DISPONÍVEL PARA O ANO DE FORMAÇÃO '.$ano_corrente_data->formacao.'</div>';
            } else {

                $data[] = '<div style="text-align: center; margin-top: 44px;">
                            <h3>RELAÇÃO FINAL DO PERÍODO BÁSICO ANO DE FORMAÇÃO '.$ano_corrente_data->formacao.'</h3>
                            </div>';
                $data[] = '<table style="border-collapse: collapse; border: 1px solid #696969; text-align: center; margin: 0 auto; margin-top: 44px;">
                                <tr style="border: 1px solid #696969; font-weight: bold; background-color: #f2f2f2;">
                                    <td style="border: 1px solid #696969; padding: 12px;">
                                    Número
                                    </td>
                                    <td style="border: 1px solid #696969; padding: 12px;">
                                    ARMT
                                    </td>
                                    <td style="border: 1px solid #696969; padding: 12px;">
                                    IIB
                                    </td>
                                    <td style="border: 1px solid #696969; padding: 12px;">
                                    TOPO
                                    </td>
                                    <td style="border: 1px solid #696969; padding: 12px;">
                                    PTRL
                                    </td>
                                    <td style="border: 1px solid #696969; padding: 12px;">
                                    IG
                                    </td>
                                    <td style="border: 1px solid #696969; padding: 12px;">
                                    GLO
                                    </td>
                                    <td style="border: 1px solid #696969; padding: 12px;">
                                    LM
                                    </td>
                                    <td style="border: 1px solid #696969; padding: 12px;">
                                    ETI
                                    </td>
                                    <td style="border: 1px solid #696969; padding: 12px;">
                                    TFM
                                    </td>
                                    <td style="border: 1px solid #696969; padding: 12px;">
                                    N1
                                    </td>
                                    <td style="border: 1px solid #696969; padding: 12px;">
                                    N1 Arred.
                                    </td>
                                    <td style="border: 1px solid #696969; padding: 12px;">
                                    MENÇÃO
                                    </td>
                                    <td style="border: 1px solid #696969; padding: 12px;">
                                    CLASS
                                    </td>
                                </tr>';
                foreach($rfpb as $item){
                    $data_demonstrativo = unserialize($item->data_demonstrativo);
                    
                    $data[] = '<tr style="border: 1px solid #696969; padding: 12px;">
                                <td>
                                    '.$item->numero.'
                                </td>
                                <td style="border: 1px solid #696969; padding: 12px;">
                                    '.$data_demonstrativo[0]['media'].'
                                </td>
                                <td style="border: 1px solid #696969; padding: 12px;">
                                    '. ((key_exists(1, $data_demonstrativo)) ? $data_demonstrativo[1]['media'] : '') .'
                                </td>
                                <td style="border: 1px solid #696969; padding: 12px;">
                                    '. ((key_exists(2, $data_demonstrativo)) ? $data_demonstrativo[2]['media'] : '') .'
                                </td>
                                <td style="border: 1px solid #696969; padding: 12px;">
                                    '. ((key_exists(3, $data_demonstrativo)) ? $data_demonstrativo[3]['media'] : '') .'
                                </td>
                                <td style="border: 1px solid #696969; padding: 12px;">
                                    '. ((key_exists(4, $data_demonstrativo)) ? $data_demonstrativo[4]['media'] : '') .'
                                </td>
                                <td style="border: 1px solid #696969; padding: 12px;">
                                    '. ((key_exists(5, $data_demonstrativo)) ? $data_demonstrativo[5]['media'] : '') .'
                                </td>
                                <td style="border: 1px solid #696969; padding: 12px;">
                                    '. ((key_exists(6, $data_demonstrativo)) ? $data_demonstrativo[6]['media'] : '') .'
                                </td>
                                <td style="border: 1px solid #696969; padding: 12px;">
                                    '. ((key_exists(7, $data_demonstrativo)) ? $data_demonstrativo[7]['media'] : '') .'
                                </td>
                                <td style="border: 1px solid #696969; padding: 12px;">
                                    '. ((key_exists(8, $data_demonstrativo)) ? $data_demonstrativo[8]['media'] : '') .'
                                </td>
                                <td style="border: 1px solid #696969; padding: 12px;">
                                    '.$item->nota_final.'
                                </td>
                                <td style="border: 1px solid #696969; padding: 12px;">
                                    '.$item->nota_final_arredondada.'
                                </td>
                                <td style="border: 1px solid #696969; padding: 12px;">
                                    '.$mencao->getMencao($item->nota_final).'
                                </td>
                                <td style="border: 1px solid #696969; padding: 12px;">
                                    '.$item->classificacao.'
                                </td>

                                </tr>';
                }
                $data[] = '</table>';
            }
            return implode('', $data);
        }
        
    }

    public function ResultadoAvaliacaoPorNota(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request) {

        if(!$ownauthcontroller->PermissaoCheck(1) && $request->omctID!=session()->get('login.omctID')){
            $this->classLog->RegistrarLog('Teve acesso negado ao tentar acessar o resultado da avaliação por nota, maior ou menor que 5', auth()->user()->email);
            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';

        } else {
            
            $avaliacao = Avaliacoes::find($request->avaliacaoID);
        
            $omct = OMCT::find($request->omctID);

            $ano_formacao = AnoFormacao::find($request->ano_formacao_id);

            //$request->avaliacaoID

            //$request->notas_minimas

            $alunos_notas = AvaliacoesNotas::where('avaliacao_id', $request->avaliacaoID)->get();

            // SELECIONANDO TODOS OS ALUNOS COM AS NOTAS (notas_minimas)

            foreach($alunos_notas as $aluno_nota){
                $aluno_omct = ($aluno_nota->aluno->omcts_id)??0;
                if($request->notas_minimas==1){ // NOTA >= 5
                    if($aluno_nota->getNota()>=5 && $aluno_omct==$request->omctID){
                        $alunosIds[] = $aluno_nota->alunos_id;
                        $alunoNota[$aluno_nota->alunos_id] = $aluno_nota->getNota();
                    }
                } else if($request->notas_minimas==2){
                    if($aluno_nota->getNota()<5 && $aluno_omct==$request->omctID){
                        $alunosIds[] = $aluno_nota->alunos_id;
                        $alunoNota[$aluno_nota->alunos_id] = $aluno_nota->getNota();
                    }
                }
            }

            $alunosIds = ($alunosIds)??array();
            
            $alunoNota = ($alunoNota)??array();

            // SELECIONANDO SOMENTE O $alunosIds[]

            $alunos = Alunos::whereIn('id', $alunosIds)->get();
            $this->classLog->RegistrarLog('Acessou resultado da avaliação por nota, maior ou menor que 5', auth()->user()->email);
            return view('relatorios.resultado-avaliacao-por-nota')->with('omct', $omct)
                                                                  ->with('alunos', $alunos)
                                                                  ->with('avaliacao', $avaliacao)
                                                                  ->with('alunoNota', $alunoNota)
                                                                  ->with('ano_formacao', $ano_formacao);

        }
                                                                        
    }

    public function RelatorioDisciplinasDiplomaUete(Request $request){

       $anoFormacao = AnoFormacao::find($request->ano_formacao_id);
       $disciplinas = Disciplinas::where([['ano_formacao_id', '=', $anoFormacao->id]])->get();

       return view('ajax.relatorios.listagem-disciplinas-diploma-uete', compact('anoFormacao', 'disciplinas')); 
    }

}

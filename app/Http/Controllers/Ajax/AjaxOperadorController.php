<?php

namespace App\Http\Controllers\Ajax;

//use Request;

use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

/* MODELS */

use App\Models\Alunos;
use App\Models\AnoFormacao;
use App\Models\Avaliacoes;
use App\Models\AvaliacoesNotas;
use App\Models\AvaliacoesProntoFaltas;
use App\Models\AvaliacoesProntoFaltasStatus;
use App\Models\Disciplinas;
use App\Models\Operadores;
use App\Models\LancamentoFo;


use App\Http\OwnClasses\ClassLog;

setlocale(LC_ALL, "pt_BR.utf8");
//date_default_timezone_set('America/Sao_Paulo');

class AjaxOperadorController extends Controller
{
    protected $classLog;

    public function __construct(ClassLog $classLog){
        $this->classLog = $classLog;
        $classLog->ip=$_SERVER['REMOTE_ADDR'];      
    }

    public function VisaoGeralOMCT(\App\Http\Controllers\OwnAuthController $ownauthcontroller){
        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $ano_formacao = ($ano_corrente->id)??0;
        $disciplinas = Disciplinas::where('ano_formacao_id', '=', $ano_formacao)->get();
        $alunos['total_alunos'] = Alunos::where('sexo', '=', 'M')->where('omcts_id', session()->get('login.omctID'))->where('data_matricula', '=', $ano_formacao)->count();
        $alunos['total_alunas'] = Alunos::where('sexo', '=', 'F')->where('omcts_id', session()->get('login.omctID'))->where('data_matricula', '=', $ano_formacao)->count();
        $alunos['total'] = (($alunos['total_alunos'] + $alunos['total_alunas'])==0)?1:$alunos['total_alunos'] + $alunos['total_alunas'];
        $alunos['porcentagem_alunos'] = floor(($alunos['total_alunos']*100)/$alunos['total']);
        $alunos['porcentagem_alunas'] = ceil(($alunos['total_alunas']*100)/$alunos['total']);
        $alunos['list'] = Alunos::get();
        foreach($disciplinas as $disciplina){
            $disciplinas_id[] = $disciplina->id;    
        }
        $disciplinas_id = ($disciplinas_id)??array(0);
        $avaliacoes = Avaliacoes::whereIn('disciplinas_id', $disciplinas_id)->where('data', '>', date('Y-m-d'))->orderBy('data', 'asc')->get();
        
        $param = $ano_formacao;
        $lancamentoFo = LancamentoFo::whereHas('aluno', function($query) use($param){
            $query->where(['data_matricula' => $param, 'omcts_id' => session()->get('login.omctID')]);
        })->get();

        $fatd = LancamentoFo::whereHas('aluno', function($query) use($param){
            $query->where(['data_matricula' => $param, 'omcts_id' => session()->get('login.omctID')]);
        })->whereHas('fatdLancada', function($query) {
            $query->where(['justificado' => null]);
        })->get();

        $operadores = Operadores::find(session()->get('login.operadorID'));
        $funcaoOperador = explode(',', $operadores->id_funcao_operador);

        if($ownauthcontroller->PermissaoCheck(18)){
            $omct_id = session()->get('login.omctID');
            return view('ajax.visao-geral-omct')->with('total_operadores', Operadores::count())
                                                ->with('ano_corrente', $ano_corrente)
                                                ->with('disciplinas', $disciplinas)
                                                ->with('avaliacoes', $avaliacoes)
                                                ->with('ownauthcontroller', $ownauthcontroller)
                                                ->with('alunos', $alunos)
                                                ->with('lancamentoFo', $lancamentoFo)
                                                ->with('fatd', $fatd)
                                                ->with('funcaoOperador', $funcaoOperador);
        }
    }

    function DialogProntoFaltas(Request $request) {

        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();

        /* VERIFICANDO SE A AVALIAÇÃO É 2ª CHAMADA */

        $chamadas = Avaliacoes::find($request->id);

        if($chamadas->chamada>1){

            // SE FOR 2ª CHAMADA DEVO PEGAR A AVALIAÇÃO DE REFERENCIA E VER SE OS PRONTO DE FALTAS JÁ FORAM ENVIADOS

            // AVALIAÇÃO DE REFERÊNCIA 

            $avaliacao_referencia = $chamadas->chamada_refer_id;

            $pronto_faltas_status = AvaliacoesProntoFaltasStatus::where('avaliacao_id', $avaliacao_referencia)->where('omcts_id', session()->get('login.omctID'))->get();

            if(count($pronto_faltas_status)>0){

                if($chamadas->chamada==2){
                    
                    // RELACIONANDO OS MILITARES QUE FALTARAM A 1ª CHAMADA E QUE DEVEM FAZER A SEGUNDA
                    
                    $query = DB::select('SELECT * FROM avaliacoes_pronto_faltas WHERE avaliacao_id=? AND omcts_id=?', [$avaliacao_referencia, session()->get('login.omctID')]);
                    foreach($query as $data_alunos){
                        $alunos_faltosos[] = $data_alunos->aluno_id;
                    }

                    $alunos_faltosos = ($alunos_faltosos)??array();

                    //$alunos = Alunos::where('omcts_id', session()->get('login.omctID'))->whereIn('id', $alunos_faltosos)->orderBy('sexo', 'desc')->get();                    
                    $alunos = Alunos::where([
                        'omcts_id' => session()->get('login.omctID'),
                        'data_matricula' => $ano_corrente->id
                    ])->whereIn('id', $alunos_faltosos)->orderBy('sexo', 'desc')->get();                    
                    
                }
                
            } else {
                $data[] = '<div style="color: #B40404; text-align:center; margin: 32px;">O pronto de faltas da avaliação de referência não foi enviado!</div>';
            }
            
        } else if($chamadas->chamada==0 && $chamadas->avaliacao_recuperacao==1){
            // VERIFICO OS ALUNOS QUE FICARAM COM MÉDIA < 5,00 NA DISCIPLINA
            
            // ID DA DISCIPLINAS
            
            $disciplina_id = $chamadas->disciplinas_id;
            
            // SELECIONANDO TODAS AVALIAÇÕES DA DISCIPLINA ($disciplina_id) EXCETO A DE RECUPERAÇÃO
            
            $avaliacoes = Avaliacoes::where('disciplinas_id', $disciplina_id)->where('avaliacao_recuperacao', '<>', 1)->get(['id', 'chamada', 'peso']);
            
            foreach($avaliacoes as $avaliacao){
                
                // FAZENDO UM ARRAY COM TODAS AVALIAÇÕES DA DISCIPLINA EXCETO A AVALIAÇÃO DE RECUPERAÇÃO 
                
                $avaliacoes_array[] = $avaliacao->id;
                
                // FAZENDO UM ARRAY COM TODAS AS AVALIAÇÕES DE PRIMERA CHAMADA (O array_sum DESSA ARRAY SERÁ O TOTAL DE AVALIAÇÕES => RAZÃO DA DIVISÃO PARA CÁLCULO DA MÉDIA DA NOTA )
                
                $razao[] = ($avaliacao->chamada==1)?1:0;


            }
            
            // TOTAL DE AVALIAÇÕES DA DISCIPLINA É O TOTAL DE AVALIAÇÕES DE PRIMEIRA CHAMADA
            $razao = array_sum($razao);
            
            $avaliacoes_notas = AvaliacoesNotas::whereIn('avaliacao_id', $avaliacoes_array)->get();
            foreach($avaliacoes_notas as $avaliacao_nota){
                $aluno_array[$avaliacao_nota->alunos_id][] = $avaliacao_nota->getNota();
            }

            
            
            // APOS O LOOP ACIMA TENHO OS RESULTADOS:
            
            // $aluno_array[numero_do_aluno][$key] = Nota da prova
            
            // para saber a nota de cada aluno fazemos o select na tabela alunos
            
            //$alunos = Alunos::where('omcts_id', session()->get('login.omctID'))->get();
            $alunos = Alunos::where([
                'omcts_id' => session()->get('login.omctID'),
                'data_matricula' => $ano_corrente->id
            ])->get();
            foreach($alunos as $aluno){
                if(isset($aluno_array[$aluno->id])){
                    $media = array_sum($aluno_array[$aluno->id])/$razao;

                    if($media<5){
                        $alunosID_recuperacao[] = $aluno->id;
                    }
                }
            }
                
            // ID DOS ALUNOS QUE PERDERAM MÉDIA!
            
            // $alunosID_recuperacao
            
            $alunosID_recuperacao = ($alunosID_recuperacao)??array();
            
            //$alunos = Alunos::where('omcts_id', session()->get('login.omctID'))->whereIn('id', $alunosID_recuperacao)->orderBy('sexo', 'desc')->get();                    
            $alunos = Alunos::where([
                'omcts_id' => session()->get('login.omctID'),
                'data_matricula' => $ano_corrente->id
            ])->whereIn('id', $alunosID_recuperacao)->orderBy('sexo', 'desc')->get();                    
            
        } else {
            //$alunos = Alunos::where('omcts_id', session()->get('login.omctID'))->orderBy('sexo', 'desc')->get();
            $alunos = Alunos::where([
                'omcts_id' => session()->get('login.omctID'),
                'data_matricula' => $ano_corrente->id
            ])->orderBy('sexo', 'desc')->get();                       
        }


        if(isset($alunos)){
            if(count($alunos)>0){

                $data[] = ' <div style="margin-top: 64px; text-align: center; color: #696969;">
                                <h5>PRONTO DE FALTAS<h5>
                                <p>Selecione SFC os alunos que faltaram a esta avaliação</p>
                            </div>';
        
                $data[] = ' <div style="margin: 64px auto; width: 50%;">
                                <form id="ponto-de-faltas">
                                <input type="hidden" name="_token" value="'.csrf_token().'">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">SELEÇÃO</th>
                                            <th scope="col">ALUNO(A)</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
        
                foreach($alunos as $aluno){
                    $data[] = ' <tr onclick="if($(this).find(\'input\').is(\':checked\')) { $(this).find(\'input\').prop(\'checked\', false); } else { $(this).find(\'input\').prop(\'checked\', true); }" style="cursor: pointer;">
                                    <td>
                                        <div class="custom-control custom-checkbox" style="width: 36%; margin: 4px auto;">
                                            <input type="checkbox" class="custom-control-input" id="alunoID_'.$aluno->id.'" name="alunos_faltas[]" value="'.$aluno->id.'" />
                                            <label class="custom-control-label" for="alunoID_'.$aluno->id.'"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <b>'.$aluno->numero.' '.$aluno->nome_guerra.'</b><br />
                                        <span style="color: #696969;"><i>'.$aluno->nome_completo.'</i></span>
                                    </td>
                                </tr>';
                }                            
        
                $data[] = '         </tbody>
                                </table>
                                </form>';
        
                $data[] = ' <div style="margin: 32px auto; width: 50%; text-align: center; ">
                                <button type="button" class="btn btn-success" onclick="enviarProntoFaltas(this, '.$request->id.');">Enviar pronto de faltas</button>
                            </div>';              

            } else {

                $data[] = '<div style="color: #B40404; text-align:center; margin: 32px;">Sem alunos previstos para esta chamada!</div>';

            }
        }
                    
        $data['response'] = implode('', $data);

        return $data;
    }

    public function EnviarProntoFaltas(Request $request){

        if(AvaliacoesProntoFaltasStatus::where([
            ["avaliacao_id", '=', $request->id],
            ["omcts_id", '=', session()->get('login.omctID')],
        ])->first()){

        //if(AvaliacoesProntoFaltasStatus::where('avaliacao_id', $request->id)->where('omcts_id', session()->get('login.omctID'))->count()>0){

            $data['response'] = '<span class="badge badge-secondary">Pronto de faltas JÁ enviado</span>';
            $this->classLog->RegistrarLog('Enviou pronto de faltas pela segunda vez', auth()->user()->email);
            return $data;
        } else {
            $avaliacoes_status = new AvaliacoesProntoFaltasStatus;
            $avaliacoes_status->avaliacao_id = $request->id;
            $avaliacoes_status->omcts_id = session()->get('login.omctID');
            
            $avaliacoes_status->status = ($request->alunos_faltas)?0:1;
            $avaliacoes_status->save();
    
            if($request->alunos_faltas){
               foreach($request->alunos_faltas as $falta){
                   $avaliacoes_faltas = new AvaliacoesProntoFaltas;
                   $avaliacoes_faltas->avaliacao_id = $request->id;
                   $avaliacoes_faltas->omcts_id = session()->get('login.omctID');
                   $avaliacoes_faltas->aluno_id = $falta;
                   $avaliacoes_faltas->save();
               }
            }
    
            $data['response'] = '<span class="badge badge-secondary">Pronto de faltas enviado</span>';
            $this->classLog->RegistrarLog('Enviou pronto de faltas', auth()->user()->email);
            return $data;
        }
    }

    public function DialogLancarGraus(Request $request){
        
        // AVALIÇÃO id $request->id

        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $id_ano_corrente = ($ano_corrente->id)??0;

        /* PEGANDO OS DADOS DA AVALIAÇÃO */

        $avaliacao = Avaliacoes::find($request->id);

        // VERIFICANDO SE JÁ FOI DADO O PRONTO DE FALTAS 

        if(AvaliacoesProntoFaltasStatus::where('avaliacao_id', $request->id)->where('omcts_id', session()->get('login.omctID'))->count()>0){
            
            // BUSCANDO RELAÇÃO DE ALUNOS QUE FALTARAM A PROVA
    
            $alunos_faltas = AvaliacoesProntoFaltas::where('avaliacao_id', $request->id)->where('omcts_id', session()->get('login.omctID'))->get();
            foreach($alunos_faltas as $falta){
                $faltas[] = $falta->aluno_id;
            }
    
            /* GUARDANDO NUM ARRAY AS NOTAS NA AVALIAÇÃO */
    
            $avaliacoes_notas = AvaliacoesNotas::where('avaliacao_id', $request->id)->get();
            foreach($avaliacoes_notas as $notas){
                $nota[$notas->alunos_id] = $notas->gbo;
            }
    
            $faltas = ($faltas)??array();
    
            // RELAÇÃO DE ALUNOS
    
            /* CASO SEJA 1ª CHAMADA */
    
            if($avaliacao->chamada==1){
                
                $alunos = Alunos::where('omcts_id', session()->get('login.omctID'))->where('data_matricula', $id_ano_corrente)->whereNotIn('id', $faltas)->orderBy('numero', 'asc')->orderBy('sexo', 'desc')->get();
                
            } else if($avaliacao->chamada==0 && $avaliacao->avaliacao_recuperacao==1){

                // VERIFICO OS ALUNOS QUE FICARAM COM MÉDIA < 5,00 NA DISCIPLINA
                
                // ID DA DISCIPLINAS
                
                $disciplina_id = $avaliacao->disciplinas_id;
                
                // SELECIONANDO TODAS AVALIAÇÕES DA DISCIPLINA ($disciplina_id) EXCETO A DE RECUPERAÇÃO
                
                $avaliacoes2 = Avaliacoes::where('disciplinas_id', $disciplina_id)->where('avaliacao_recuperacao', '<>', 1)->get(['id', 'chamada']);
                
                foreach($avaliacoes2 as $avaliacao2){
                    
                    // FAZENDO UM ARRAY COM TODAS AVALIAÇÕES DA DISCIPLINA EXCETO A AVALIAÇÃO DE RECUPERAÇÃO 
                    
                    $avaliacoes_array[] = $avaliacao2->id;
                    
                    // FAZENDO UM ARRAY COM TODAS AS AVALIAÇÕES DE PRIMERA CHAMADA (O array_sum DESSA ARRAY SERÁ O TOTAL DE AVALIAÇÕES => RAZÃO DA DIVISÃO PARA CÁLCULO DA MÉDIA DA NOTA )
                    
                    $razao[] = ($avaliacao2->chamada==1)?1:0;
                    
                }
                
                // TOTAL DE AVALIAÇÕES DA DISCIPLINA É O TOTAL DE AVALIAÇÕES DE PRIMEIRA CHAMADA
                
                $razao = array_sum($razao);
                
                $avaliacoes_notas = AvaliacoesNotas::whereIn('avaliacao_id', $avaliacoes_array)->get();
                foreach($avaliacoes_notas as $avaliacao_nota){
                    $aluno_array[$avaliacao_nota->alunos_id][] = $avaliacao_nota->getNota();
                }
                
                // APOS O LOOP ACIMA TENHO OS RESULTADOS:
                
                // $aluno_array[numero_do_aluno][$key] = Nota da prova
                
                // para saber a nota de cada aluno fazemos o select na tabela alunos
                
                $alunos = Alunos::where('omcts_id', session()->get('login.omctID'))->get();
                foreach($alunos as $aluno){
                    if(isset($aluno_array[$aluno->id])){
                        $media = array_sum($aluno_array[$aluno->id])/$razao;
                        if($media<5){
                            $alunosID_recuperacao[] = $aluno->id;
                        }
                    }
                }
                    
                // ID DOS ALUNOS QUE PERDERAM MÉDIA!
                
                // $alunosID_recuperacao
                
                $alunosID_recuperacao = ($alunosID_recuperacao)??array();

                $array_whereIn_IDs = array_diff($alunosID_recuperacao, $faltas);
                
                $alunos = Alunos::where('omcts_id', session()->get('login.omctID'))->whereIn('id', $array_whereIn_IDs)->orderBy('sexo', 'desc')->get();    
            
            } else {
    
            /* CASO SEJA 2ª CHAMADA */
    
                /* VERIFICANDO QUAL FOI A AVALIAÇÃO DE REFERÊNCIA */
    
                $avaliacao_referencia = $avaliacao->chamada_refer_id;
    
                /* SELECIONANDO OS ALUNOS QUE FALTARAM A CHAMADA DE REFERENCIA */
    
                $query = DB::select('SELECT * FROM avaliacoes_pronto_faltas WHERE avaliacao_id=? AND omcts_id=?', [$avaliacao_referencia, session()->get('login.omctID')]);
                foreach($query as $data_alunos){
                    $alunos_faltosos[] = $data_alunos->aluno_id;
                }
    
                $alunos_faltosos = ($alunos_faltosos)??array(); 
                
                $alunos = Alunos::where('omcts_id', session()->get('login.omctID'))->where('data_matricula', $id_ano_corrente)->whereIn('id', $alunos_faltosos)->orderBy('numero', 'asc')->orderBy('sexo', 'desc')->get();
    
            }
    
            if(count($alunos)>0){
    
                $total_alunos = count($alunos);
    
                $resul[] = ' <div style="margin-top: 64px; text-align: center; color: #696969;">
                                <h5>LANÇAMENTO DO GRAU ESCOLAR INDIVIDUAL<h5>
                            </div>';
        
                            $resul[] = ' <div style="margin: 64px auto; width: 50%;">
                                <form id="ponto-de-faltas">
                                <input type="hidden" name="_token" value="'.csrf_token().'">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ALUNO(A)</th>
                                            <th style="text-align: center; width: 150px;">GBO</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
        
                $total_notas_lancadas=0;
                foreach($alunos as $aluno){
                    $gbo = (isset($nota[$aluno->id]))?$nota[$aluno->id]:'';
                    if(isset($nota[$aluno->id])){
                        $td_content_nota_aluno = '  <div style="width: 100%; margin: 0 auto;" data-toggle="tooltip" data-placement="right" title="Clique aqui para retificação do grau">
                                                        <div style="float: left;">
                                                            <span><span style="color: #0B3B0B;"><b>GBO:</span></b> <span style="font-family: Tahoma;">'.$gbo.'</span></span><br />
                                                            <span><span style="color: #0B3B0B;"><b>NOTA:</span></b> <span style="font-family: Tahoma;">'.$avaliacao->getNota($gbo).'</span></span>                                                
                                                        </div>
                                                        <div style="float: right; margin: 8px 10px 0 0;">
                                                            <a href="javascript: void(0);" class="no-style" onclick="editarRegistroGrauAluno('.$aluno->id.', '.$request->id.');">    
                                                                <i class="ion-android-create" style="color: #0B610B; font-size: 18px;"></i>
                                                            </a>
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>';
                        $total_notas_lancadas++;                
                    } else {
                        $td_content_nota_aluno = '  <div style="text-align: center;">
                                                        <input type="text" name="nota_aluno_id_'.$aluno->id.'" value="" style="width: 38px; border: 1px solid #ccc; padding: 3px 4px; text-align: right; margin-top: -10px;" onkeyup="toogleConfirmGrau(this);" autocomplete="off" maxlength="3" /><br />
                                                        <a href="javascript: void(0);" class="badge badge-success" onclick="registrarGrauAluno('.$aluno->id.', '.$request->id.');" style="display: none; margin-top: 8px;">Confirmar</a>
                                                        <span class="badge badge-secondary" style="margin-top: 8px;">Confirma</span>
                                                    </div>';
                    }
                    $resul[] = ' <tr onclick="$(this).find(\'input\').focus();" style="cursor: pointer;">
                                    <td style="text-align: left;">
                                        <b>'.$aluno->numero.' '.$aluno->nome_guerra.'</b><br />
                                        <span style="color: #696969;"><i>'.$aluno->nome_completo.'</i></span>
                                    </td>
                                    <td id="col_aluno_'.$aluno->id.'" style="text-align: left;">
                                        '.$td_content_nota_aluno.'
                                    </td>
                                </tr>';
                }
        
                $total_notas_lancadas = ($total_notas_lancadas)??0;
        
                $data['porcento_preenchido'] = (floor(($total_notas_lancadas*100)/$total_alunos)>100)?100:floor(($total_notas_lancadas*100)/$total_alunos);
        
                $resul[] = '         </tbody>
                                </table>
                                </form>';
        
                $resul[] = ' <div id="status_top">
                                <strong>Porcentagem preenchida:</strong><br />
                                <div id="porcentagem-preenchimento" style="padding: 3px; border: 1px solid #ccc; margin-top: 4px;">
                                    <div style="width: '.$data['porcento_preenchido'].'%; background-color: #0B6121; text-align: center; font-size: 12px; color: #D8F6CE;">'.$data['porcento_preenchido'].'%</div>
                                </div>
                            </div>';                
            } else {
                $resul[] = '<div style="color: #B40404; text-align:center; margin: 32px;">Sem alunos previstos para lançamento de notas!</div>';
            }
            
        } else {
            $resul[] = '<div style="color: #B40404; text-align:center; margin: 32px;">É preciso enviar primeiro o pronto de faltas para então larçar o grau escolar referente a avaliação.</div>';
        }



        $data['response'] = implode('', $resul);
        return $data;
    }

    public function RegistrarGrauAluno(Request $request, \App\Http\Controllers\OwnAuthController $ownauthcontroller){
        
        if($request->ajax()){
            $this->classLog->RegistrarLog('Registrou grau de aluno', auth()->user()->email);
            if($ownauthcontroller->PermissaoCheck([1,3])){

                /* PEGANDO OS DADOS DA AVALIAÇÃO */

                $avaliacao = Avaliacoes::find($request->avaliacaoID);

                /* VERIFICANDO SE A UETE DO ALUNO É A MESMA DO OPERADOR */

                $aluno = Alunos::find($request->id);
                $omct_id = session()->get('login.omctID');
                if($aluno->omcts_id==$omct_id || $ownauthcontroller->PermissaoCheck(1)){
                    AvaliacoesNotas::where('alunos_id', $request->id)->where('avaliacao_id', $request->avaliacaoID)->delete();
                    $avaliacoes_notas = new AvaliacoesNotas;
                    $avaliacoes_notas->alunos_id = $request->id;
                    $avaliacoes_notas->avaliacao_id = $request->avaliacaoID;
                    $avaliacoes_notas->gbo = $request->gbo;
                    if($request->gbo<=$avaliacao->gbm){

                        if($avaliacoes_notas->save()){

                            /* CALCULANDO PORCENTAGEM PREENCHIDA */
                            
                            $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
                            $id_ano_corrente = ($ano_corrente->id)??0;

                            $alunos_faltas = AvaliacoesProntoFaltas::where('avaliacao_id', $request->avaliacaoID)->where('omcts_id', session()->get('login.omctID'))->get(['id']);
                            foreach($alunos_faltas as $falta){
                                $faltas[] = $falta->id;
                            }
                    
                            $faltas = ($faltas)??array();                            

                            $alunos = Alunos::where('omcts_id', session()->get('login.omctID'))->where('data_matricula', $id_ano_corrente)->whereNotIn('id', $faltas)->get(['id']);
                            $total_alunos = count($alunos);
                            foreach($alunos as $aluno){
                                $alunos_id_array[] = $aluno->id;
                            }

                            $total_notas_lancadas = AvaliacoesNotas::whereIn('alunos_id', $alunos_id_array)->where('avaliacao_id', $request->avaliacaoID)->count();

                            $data['status'] = 'ok';
                            $data['porcento_preenchido'] = (floor(($total_notas_lancadas*100)/$total_alunos)>100)?100:floor(($total_notas_lancadas*100)/$total_alunos);
                            $data['response'] = '   <div style="width: 100%; margin: 0 auto;" data-toggle="tooltip" data-placement="right" title="Clique aqui para retificação do grau">
                                                        <div style="float: left;">
                                                            <span><span style="color: #0B3B0B;"><b>GBO:</span></b> <span style="font-family: Tahoma;">'.$request->gbo.'</span></span><br />
                                                            <span><span style="color: #0B3B0B;"><b>NOTA:</span></b> <span style="font-family: Tahoma;">'.$avaliacao->getNota($request->gbo).'</span></span>                                                
                                                        </div>
                                                        <div style="float: right; margin: 8px 10px 0 0;">
                                                            <a href="javascript: void(0);" class="no-style" onclick="editarRegistroGrauAluno('.$request->id.', '.$request->avaliacaoID.');">    
                                                                <i class="ion-android-create" style="color: #0B610B; font-size: 18px;"></i>
                                                            </a>
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>';
                                                
                        } else {
                            $data['status'] = 'err';
                            $data['response'] = '   <div style="padding: 12px 18px; margin-top: 10px; text-transform: uppercase;">
                                                        <b>Houve um erro e o grau não foi registrado!</b>
                                                    </div>
                                                    <div style="padding: 8px 24px; text-align: right;">
                                                        <a href="javascript: void(0)" class="no-style" data-dismiss="modal" style="color: #2E64FE; font-size: 12px;">
                                                            <b>OK</b>
                                                        </a>                                                    
                                                    </div>';
                        }
                    
                    } else {
                        $data['status'] = 'err';
                        $data['response'] = '   <div style="padding: 12px 18px; margin-top: 10px; text-transform: uppercase;">
                                                    <b>O GBO NÃO DEVE SER MAIOR QUE O ATUAL GBM ('.$avaliacao->gbm.')</b>
                                                </div>
                                                <div style="padding: 8px 24px; text-align: right;">
                                                    <a href="javascript: void(0)" class="no-style" data-dismiss="modal" style="color: #2E64FE; font-size: 12px;">
                                                        <b>OK</b>
                                                    </a>                                                    
                                                </div>';                        
                    }
                } else {
                    $data['status'] = 'err';
                    $data['response'] = '   <div style="padding: 12px 18px; margin-top: 10px; text-transform: uppercase;">
                                                <b>É preciso pertencer a mesma UETE do aluno para lançar seus graus</b>
                                            </div>
                                            <div style="padding: 8px 24px; text-align: right;">
                                                <a href="javascript: void(0)" class="no-style" data-dismiss="modal" style="color: #2E64FE; font-size: 12px;">
                                                    <b>OK</b>
                                                </a>                                                    
                                            </div>';
                }

            } else {
                $data['status'] = 'err';
                $data['response'] = '   <div style="padding: 12px 18px; margin-top: 10px; text-transform: uppercase;">
                                            <b>Você não tem permissão para fazer lançamento de notas de alunos</b>
                                        </div>
                                        <div style="padding: 8px 24px; text-align: right;">
                                            <a href="javascript: void(0)" class="no-style" data-dismiss="modal" style="color: #2E64FE; font-size: 12px;">
                                                <b>OK</b>
                                            </a>                                                    
                                        </div>';                
            }    
        } else {
            $data = true;
        }
    

        /* CAUCULAR PORCENTAGEM E EXCLUIR */

        return $data;
    
    }

    public function EditarRegistroGrauAluno(Request $request, \App\Http\Controllers\OwnAuthController $ownauthcontroller){
        
        $aluno = Alunos::find($request->id);
        $omct_id = session()->get('login.omctID');
        if($aluno->omcts_id==$omct_id || $ownauthcontroller->PermissaoCheck(1)){
            AvaliacoesNotas::where('alunos_id', $request->id)->where('avaliacao_id', $request->avaliacaoID)->delete();
            
            /* CALCULANDO PORCENTAGEM PREENCHIDA */
                            
            $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
            $id_ano_corrente = ($ano_corrente->id)??0;

            $alunos_faltas = AvaliacoesProntoFaltas::where('avaliacao_id', $request->avaliacaoID)->where('omcts_id', session()->get('login.omctID'))->get(['id']);
            foreach($alunos_faltas as $falta){
                $faltas[] = $falta->id;
            }
            
            $faltas = ($faltas)??array();                            

            $alunos = Alunos::where('omcts_id', session()->get('login.omctID'))->where('data_matricula', $id_ano_corrente)->whereNotIn('id', $faltas)->get(['id']);
            $total_alunos = count($alunos);
            foreach($alunos as $aluno){
                $alunos_id_array[] = $aluno->id;
            }

            $total_notas_lancadas = AvaliacoesNotas::whereIn('alunos_id', $alunos_id_array)->count();

            $data['status'] = 'ok';
            $data['porcento_preenchido'] = (floor(($total_notas_lancadas*100)/$total_alunos)>100)?100:floor(($total_notas_lancadas*100)/$total_alunos);
            //$data['porcento_preenchido'] = ceil(($total_notas_lancadas*100)/$total_alunos);
            $data['response'] = '<div style="text-align: center;">
                                    <input type="text" name="nota_aluno_id_'.$request->id.'" value="" style="width: 38px; border: 1px solid #ccc; padding: 3px 4px; text-align: right; margin-top: -10px;" onkeyup="toogleConfirmGrau(this);" autocomplete="off" maxlength="3" /><br />
                                    <a href="javascript: void(0);" class="badge badge-success" onclick="registrarGrauAluno('.$request->id.', '.$request->avaliacaoID.');" style="display: none; margin-top: 8px;">Confirmar</a>
                                    <span class="badge badge-secondary" style="margin-top: 8px;">Confirma</span>
                                </div>';
            
        }
        $this->classLog->RegistrarLog('Editou grau de aluno', auth()->user()->email);
        return $data;
    
    }

}

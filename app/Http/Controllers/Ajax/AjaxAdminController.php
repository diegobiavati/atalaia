<?php

namespace App\Http\Controllers\Ajax;

//use Request;
use Illuminate\Http\Request;
use App\User;
use App\Http\Requests\OperadoresRequest;
use App\Http\Requests\DisciplinasRequest;
use App\Http\Requests\AvaliacoesRequest;
use App\Http\Requests\AtualizaMeuPerfilRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use Illuminate\Support\Facades\DB;

/* MODELS */

use App\Models\Alunos;
use App\Models\AlunosClassificacao;
use App\Models\AlunosConselhoEscolar;
use App\Models\AlunosVoluntAv;
use App\Models\AlunosSitDiv;
use App\Models\AlunosSitDivHistorico;
use App\Models\Areas;
use App\Models\AnoFormacao;
use App\Models\AvaliacaoTaf;
use App\Models\Avaliacoes;
use App\Models\AvaliacoesNotas;
use App\Models\AvaliacoesProntoFaltasStatus;
use App\Models\ConfLancaTaf;
use App\Models\Disciplinas;
use App\Models\EscolhaQMS;
use App\Models\EscolhaAviacaoStatus;
use App\Models\Imagens;
use App\Models\Instrumentos;
use App\Models\OMCT;
use App\Models\Operadores;
use App\Models\OperadoresTipo;
use App\Models\Permissoes;
use App\Models\PostoGrad;
use App\Models\QMS;
use App\Models\QMSMatriz;
use App\Models\SituacoesDiversas;
use App\Models\TafAbdominal;
use App\Models\TafBonusAtletas;
use App\Models\TafConfiguracoes;
use App\Models\TafCorrida;
use App\Models\TafFlexaoBarra;
use App\Models\TafFlexaoBraco;
use App\Models\TafPortarias;
use App\Models\TurmasPB;
use App\Models\UsersTopicMQTT;

use App\Mail\BemVindo;
use App\Http\OwnClasses\OwnValidator;
use App\Http\OwnClasses\phpMQTT;
use App\Http\OwnClasses\ClassLog;
use App\Models\AvaliacoesProntoFaltas;
use App\Models\Comportamento;
use App\Models\Enquadramentos;
use Exception;

setlocale(LC_ALL, "pt_BR.utf8");
//date_default_timezone_set('America/Sao_Paulo');

class AjaxAdminController extends Controller
{
    protected $lancarTaf;
    protected $classLog;
    public $user_info;
    protected $ownauthcontroller;

    public function __construct(Request $request, AvaliacaoTaf $avaliacaTaf, ClassLog $classLog, OwnAuthController $ownauthcontroller)
    {
        $this->lancarTaf = $avaliacaTaf;
        $this->request = $request;
        $this->classLog = $classLog;
        $classLog->ip = $_SERVER['REMOTE_ADDR'];
        $this->ownauthcontroller = $ownauthcontroller;
    }

    public function GerenciarOperadores(\App\Http\Controllers\OwnAuthController $ownauthcontroller)
    {

        $operadores = Operadores::orderBy('omcts_id', 'asc')->orderBy('postograd_id', 'asc')->get();
        $funcoesOperadores = OperadoresTipo::get();
        foreach ($funcoesOperadores as $funcao) {
            $data[$funcao->id] = $funcao->funcao_abrev;
        }

        $this->classLog->RegistrarLog('Acesso ao menu para gerenciamento de operadores', auth()->user()->email);
        return view('ajax.gerenciar-operadores')->with('operadores', $operadores)->with('data', $data)->with('ownauthcontroller', $ownauthcontroller);
    }

    public function ConselhoEscolar()
    {
        $this->classLog->RegistrarLog('Acesso ao menu de conselho de ensino', auth()->user()->email);
        return view('ajax.conselho-escolar');
    }

    public function DialogConselhoEscolar(Request $request)
    {

        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $id_ano_corrente = ($ano_corrente->id) ?? 0;
        $disciplinas = Disciplinas::where('ano_formacao_id', $id_ano_corrente)->get();
        $options_disciplinas[] = '<select class="custom-select" name="disciplinas_id" onchange="carregaNotaAlunoDisciplina(this);" style="width: 300px;">';
        $options_disciplinas[] = '<option value="0">Selecione uma disciplina</option>';
        foreach ($disciplinas as $disciplina) {
            $options_disciplinas[] = '<option value="' . $disciplina->id . '">' . $disciplina->nome_disciplina . '</option>';
        }
        $options_disciplinas[] = '<option value="99999">TESTE DE APTIDÃO FÍSICA</option>';
        $options_disciplinas[] = '</select>';
        $alunos = Alunos::where('data_matricula', $id_ano_corrente)->where('nome_completo', 'LIKE', '%' . $request->aluno . '%')->orWhere('numero', $request->aluno)->get();
        foreach ($alunos as $aluno) {

            $data[] = ' <div id="box-aluno-conselho_' . $aluno->id . '" class="box-aluno-conselho" style="padding: 6px; border-bottom: 1px solid #ccc">
                            <a href="javascript: void(0);" onclick="loadDialogAlConselho(this, ' . $aluno->id . ');" class="no-style" >Al ' . $aluno->numero . ' ' . $aluno->nome_guerra . ' (' . $aluno->nome_completo . ')</a>
                            <div id="box2-aluno-conselho_' . $aluno->id . '" style="display: none; text-align: left;">
                                Al ' . $aluno->numero . ' ' . $aluno->nome_guerra . ' <span style="color: #696969; size: 10px;"><i>(' . $aluno->omct->sigla_omct . ')</i></span><br /> 
                                <div style="margin-top: 8px;">' . implode('', $options_disciplinas) . '</div>
                                <input type="hidden" name="aluno_id" value="' . $aluno->id . '" />
                            </div>
                        </div>';
        }

        $data = ($data) ?? array('Não localizado');

        return implode('', $data);
    }

    public function CarregaNotaAlunoDisciplina(Request $request)
    {

        // SELECIONANDO A A TABELA alunos_em_conselho_escolar E VERIFICANDO SUA SITUAÇÃO

        $alunos_em_conselho = AlunosConselhoEscolar::where('aluno_id', $request->aluno)->where('disciplina_id', $request->disciplina)->first();

        // SELECIONANDO TODAS AVALIAÇÕES REALIZADAS NA DISCILINA

        $avaliacoes_taf = AvaliacaoTaf::where('aluno_id', $request->aluno)->first();

        if ($request->disciplina == 99999 && $avaliacoes_taf) {
            $notas_array['CORRIDA'] = $avaliacoes_taf->corrida_nota;
            $notas_array['FLEXÃO DE BRAÇO'] = $avaliacoes_taf->flexao_braco_nota;
            $notas_array['FLEXÃO NA BARRA'] = $avaliacoes_taf->flexao_barra_nota;
            $notas_array['ABDOMINAL'] = $avaliacoes_taf->abdominal_suficiencia;
            $notas_array['ATLETA'] = $avaliacoes_taf->aluno->atleta_marexaer;
            $notas_array['MEDIA'] = $avaliacoes_taf->media;
            $media = number_format($avaliacoes_taf->media, '3', '.', '');

            if(isset($avaliacoes_taf->reprovado_recuperacao)){
                if($avaliacoes_taf->reprovado_recuperacao == 'N'){
                    $media = $avaliacoes_taf->media_recuperacao;
                }else{
                    $notas_array_recuperacao["AR"] = $avaliacoes_taf->media_recuperacao;
                }
            }
        } else {
            $avaliacoes = Avaliacoes::where('disciplinas_id', $request->disciplina)->get(['id']);

            $avaliacoes_notas = FuncoesController::recalculaNotaAluno(AvaliacoesNotas::whereIn('avaliacao_id', $avaliacoes)->where('alunos_id', $request->aluno)->get());
            //Original Julião
            //$avaliacoes_notas = AvaliacoesNotas::whereIn('avaliacao_id', $avaliacoes)->where('alunos_id', $request->aluno)->get();

            /*foreach ($avaliacoes_notas as $notas) {
                if ($notas->avaliacao->chamada == 1) {
                    $razao[] = $notas->avaliacao->peso;
                }

                if ($notas->avaliacao->avaliacao_recuperacao == 0) {
                    // ARRAY DAS NOTAS
                    $notas_array[$notas->avaliacao->nome_abrev] = $notas->getNota();
                } else {
                    $notas_array_recuperacao["AR"] = $notas->getNota();
                }
            }

            if (isset($notas_array) && isset($razao)) {
                $media = number_format(array_sum($notas_array) / array_sum($razao), '3', '.', '');
            } else {
                $media = 0.000;
            }*/
            foreach ($avaliacoes_notas as $key => $info) {
                if($key != 'alunosID'){
                    foreach ($info as $notas) {
                        foreach($notas['avaliacoes'] as $avaliacao){
                        
                            if($avaliacao->nome_abrev == 'AR'){
                                $notas_array_recuperacao["AR"] = $avaliacao->nota;
                            }else{    
                                $notas_array[$avaliacao->nome_abrev] = $avaliacao->nota;
                            }
                        }

                        $media = number_format($notas['media'], '3', '.', '');
                    }    
                }
            }

            if (!isset($notas_array) && !isset($razao)) {
                $media = 0.000;
            }
        }
        $nd_recuperada = 0;
//dd($notas_array, array_sum($razao), array_sum($notas_array) / array_sum($razao));
        if (($media < 5 && isset($notas_array)) || ($request->disciplina == 99999 && (isset($avaliacoes_taf) && ($avaliacoes_taf->reprovado == 'S' && $avaliacoes_taf->reprovado_recuperacao == 'S') ))) {
            
            if (isset($notas_array_recuperacao["AR"])) {
                $nd_recuperada = $notas_array_recuperacao["AR"];
                if ($nd_recuperada > 5) {
                    $nd_recuperada = '5.000';
                }else{
                    $nd_recuperada = $media;
                }
            }
            /*if (isset($notas_array_recuperacao["AR"])) {
                $nd_recuperada = number_format(($media + $notas_array_recuperacao["AR"]) / 2, '3', '.', '');
                if ($nd_recuperada < $media) {
                    $nd_recuperada = $media;
                } else if ($nd_recuperada > 5) {
                    $nd_recuperada = '5.000';
                }


            } else {
                $nd_recuperada = $media;
            }*/

            $data[] = '<table style="border: 1px solid #000; border-collapse: collapse; margin: 0 auto; width: 80%;">';
            $data[] = '<tr>';

            foreach ($notas_array as $key => $ava_nota) {
                $data[] = '<td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>' . $key . '</b></td>';
            }

            $data[] = '<td style="border: 1px solid #000; padding: 6px; text-align: center;"><span style="color: #B40404;"><b>ND</b></span></td>';

            if (isset($notas_array_recuperacao["AR"])) {
                $data[] = '<td style="border: 1px solid #000; padding: 6px; text-align: center;"><span style="color: #FF8000;"><b>AR</b></span></td>';
                $data[] = '<td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ND Recuperada</b></td>';
            }

            if ($alunos_em_conselho) {
                $data[] = '<td style="border: 1px solid #000; padding: 6px; text-align: center;"><span style="color: #0B3B0B;"><b>CONSELHO DE ENSINO</b></span></td>';
                $data[] = '<td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ND FINAL</b></td>';
            }

            $data[] = '</tr>';
            $data[] = '<tr>';
            foreach ($notas_array as $key => $ava_nota) {
                $data[] = '<td style="border: 1px solid #000; padding: 6px; text-align: center;">' . $ava_nota . '</td>';
            }

            $data[] = '<td style="border: 1px solid #000; padding: 6px; text-align: center;"><span style="color: #B40404;">' . $media . '</span></td>';

            if (isset($notas_array_recuperacao["AR"])) {
                $data[] = '<td style="border: 1px solid #000; padding: 6px; text-align: center;"><span style="color: #FF8000;">' . $notas_array_recuperacao["AR"] . '</span></td>';
                $data[] = '<td style="border: 1px solid #000; padding: 6px; text-align: center;">' . $nd_recuperada . '</td>';
            }

            if ($alunos_em_conselho) {
                $data[] = '<td style="border: 1px solid #000; padding: 6px; text-align: center;"><span style="color: #0B3B0B;"> + ' . $alunos_em_conselho->acrescimo . '</span></td>';
                $data[] = '<td style="border: 1px solid #000; padding: 6px; text-align: center;">5.000</td>';
            }

            $data[] = '</tr>';
            $data[] = '</table>';

            if ($nd_recuperada < 5 || ($request->disciplina == 99999 && (isset($avaliacoes_taf) && $avaliacoes_taf->reprovado == 'S'))) {

                if ($request->disciplina == 99999 && (isset($avaliacoes_taf) && $avaliacoes_taf->reprovado == 'S')) {
                    if ($alunos_em_conselho) {
                        $data[] = '<div style="text-align: center; margin-top: 18px;">
                                        Reverter a concessão de aprovação do aluno pelo conselho de ensino na disciplina TFM
                                        <div style="margin-top: 22px;">
                                            <button type="button" class="btn btn-danger" onclick="removerConcessaoConselho(' . $request->aluno . ', ' . $request->disciplina . ');">
                                                Reverter concessão
                                            </button>
                                        </div>  
                                    </div>';
                    } else {
                        $data[] = '<div style="text-align: center; margin-top: 18px;">
                                        Conceder a aprovação desse aluno no TFM por conselho de ensino
                                        <div style="margin-top: 22px;">
                                            <button type="button" class="btn btn-primary" onclick="AdicionarConcessaoConselho(' . $request->aluno . ', ' . $request->disciplina . ', \'' . (5 - $nd_recuperada) . '\');">
                                                Conceder
                                            </button>
                                        </div> 
                                    </div>';
                    }
                } else {
                    if ($alunos_em_conselho) {
                        $data[] = '<div style="text-align: center; margin-top: 18px;">
                                        Este aluno recebeu <span style="color: #FF8000;"><b>' . $alunos_em_conselho->acrescimo . '</b></span> em sua média.
                                        <div style="margin-top: 22px;">
                                            <button type="button" class="btn btn-danger" onclick="removerConcessaoConselho(' . $request->aluno . ', ' . $request->disciplina . ');">
                                                Reverter concessão
                                            </button>
                                        </div>  
                                    </div>';
                    } else {
                        $data[] = '<div style="text-align: center; margin-top: 18px;">
                                        Conceder a este aluno o acréscimo de <span style="color: #FF8000;"><b>' . (5 - $nd_recuperada) . '</b></span> em sua nota final.
                                        <div style="margin-top: 22px;">
                                            <button type="button" class="btn btn-primary" onclick="AdicionarConcessaoConselho(' . $request->aluno . ', ' . $request->disciplina . ', \'' . (5 - $nd_recuperada) . '\');">
                                                Conceder
                                            </button>
                                        </div> 
                                    </div>';
                    }
                }
            } else {
                $data[] = ' <div style="text-align: center; margin-top: 18px;">
                                Aluno não se enquadra para conselho de ensino nesta disciplina<br />
                                <span style="color: #122A0A;"><b>Média:</b> ' . $nd_recuperada . '</span>
                            </div>';
            }
        } else {

            $data[] = ' <div style="text-align: center;">
                            Aluno não se enquadra para conselho de ensino ou não possui notas lançadas nesta disciplina<br />
                            <span style="color: #122A0A;"><b>Média:</b> ' . $media . '</span>
                        </div>';
        }

        return implode('', $data);
    }

    public function AdicionarConcessaoConselho(Request $request, \App\Http\Controllers\OwnAuthController $ownauthcontroller)
    {
        $this->classLog->RegistrarLog('Adicionou aluno em conselho de ensino', auth()->user()->email);
        if ($ownauthcontroller->PermissaoCheck(1)) {
            //{aluno}/{disciplina}/{acrescimo}
            $alunos_em_conselho = new AlunosConselhoEscolar;
            $alunos_em_conselho->aluno_id = $request->aluno;
            $alunos_em_conselho->disciplina_id = $request->disciplina;
            $alunos_em_conselho->acrescimo = str_replace(',', '.', $request->acrescimo);
            if ($alunos_em_conselho->save()) {
                $data = 'ok';
            } else {
                $data = 'err';
            }
        } else {
            $data = 'err2';
        }
    }

    public function RemoverConcessaoConselho(Request $request, \App\Http\Controllers\OwnAuthController $ownauthcontroller)
    {
        $this->classLog->RegistrarLog('Removeu aluno de conselho de ensino', auth()->user()->email);
        if ($ownauthcontroller->PermissaoCheck(1)) {
            $alunos_em_conselho = AlunosConselhoEscolar::where('aluno_id', $request->aluno)->where('disciplina_id', $request->disciplina)->first();
            if ($alunos_em_conselho->delete()) {
                $data = 'ok';
            } else {
                $data = 'err';
            }
        } else {
            $data = 'err2';
        }
    }

    public function GerenciarDisciplinas()
    {
        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $id_ano_corrente = ($ano_corrente->id) ?? 0;
        $anos_formacao = AnoFormacao::get();
        $disciplinas = Disciplinas::where('ano_formacao_id', '=', $id_ano_corrente)->orderBy('id', 'desc')->get();
        return view('ajax.gerenciar-disciplinas')->with('disciplinas', $disciplinas)->with('anos_formacao', $anos_formacao)->with('ano_corrente', $ano_corrente);
    }

    public function Avaliacoes(\App\Http\Controllers\OwnAuthController $ownauthcontroller)
    {

        if ($this->ownauthcontroller->PermissaoCheck(1)) {
            $uetes = OMCT::where('id', '<>', 1)->get(); //Remove a ESA
        } else {
            $uetes = OMCT::where('id', session()->get('login.omctID'))->get();
        }

        $confTaf = ConfLancaTaf::first();

        $confLancaTaf = array(
            "data_inicio" => strftime('%d/%m/%Y', strtotime($confTaf->inicio)),
            "hora_inicio" => strftime('%H:%M', strtotime($confTaf->inicio)),
            "data_fim" => strftime('%d/%m/%Y', strtotime($confTaf->termino)),
            "hora_fim" => strftime('%H:%M', strtotime($confTaf->termino)),
        );

        $avaliacoes_status = AvaliacoesProntoFaltasStatus::where('omcts_id', session()->get('login.omctID'))->get();
        /* RELAÇÃO(ARRAY) DAS AVALIAÇÕES QUE JÁ FORAM DADO O PRONTO DE FALTAS RELATIVO A UETE DO MILITAR */
        foreach ($avaliacoes_status as $status) {
            $avaliacoes_status_array[] = $status->avaliacao_id;
        }

        $avaliacoes_status_array = ($avaliacoes_status_array) ?? array();

        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $id_ano_corrente = ($ano_corrente->id) ?? 0;
        $disciplinas = Disciplinas::orderBy('id', 'desc')->where('ano_formacao_id', '=', $id_ano_corrente)->get();
        foreach ($disciplinas as $disciplina) {
            $array_disciplina_id[] = $disciplina->id;
        }

        $array_disciplina_id = (isset($array_disciplina_id)) ? $array_disciplina_id : array();

        $avaliacoes = Avaliacoes::whereIn('disciplinas_id', $array_disciplina_id)->get();

        /* GRAVANDO O NOME DA AVALIAÇÃO PELO SEU ÍNDICE(id) */

        foreach ($avaliacoes as $avaliacao) {
            $avaliacao_nome_por_indice[$avaliacao->id] = $avaliacao->nome_completo;
        }

        $avaliacao_nome_por_indice = ($avaliacao_nome_por_indice) ?? array();

        return view('ajax.avaliacoes')->with('avaliacoes', $avaliacoes)
            ->with('disciplinas', $disciplinas)
            ->with('ano_corrente', $ano_corrente)
            ->with('ownauthcontroller', $ownauthcontroller)
            ->with('avaliacoes_status_array', $avaliacoes_status_array)
            ->with('confLancaTaf', $confLancaTaf)
            ->with('avaliacao_nome_por_indice', $avaliacao_nome_por_indice)
            ->with('uetes', $uetes);
    }

    public function LancarTafAluno(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request)
    {

        $confTaf = ConfLancaTaf::first();

        if ($confTaf->inicio < date('Y-m-d H:i:s') && $confTaf->termino > date('Y-m-d H:i:s')) {
            $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
            $id_ano_corrente = ($ano_corrente->id) ?? 0;

            $recuperacao = false;
            //Faz as modificações para lançar a recuperação
            if (strstr($request->path(), 'lancar-taf-aluno-recuperacao')) {
                //Deve listar somente os alunos que está com média abaixo de 5.

                $recuperacao = true;
                $param['ano_corrente'] = $ano_corrente;

                $avaliacaoTafNotaAluno = AvaliacaoTaf::whereHas('aluno', function ($q) use ($param) {
                    $q->where('data_matricula', $param['ano_corrente']->id)
                        ->where('omcts_id', session()->get('login.omctID'))
                        ->orderBy('numero', 'asc');
                })->where('reprovado', '=', 'S')->get();

                foreach ($avaliacaoTafNotaAluno as $avaliacao) {
                    $alunos_array[] = $avaliacao->aluno;
                }

                $alunos = collect($alunos_array);
            } else {
                $alunos = Alunos::where('data_matricula', $id_ano_corrente)->where('omcts_id', session()->get('login.omctID'))->orderBy('numero', 'asc')->get();
                foreach ($alunos as $alunoId) {
                    $alunosID[] = $alunoId->id;
                }

                $avaliacaoTafNotaAluno = AvaliacaoTaf::whereIn('aluno_id', $alunosID)->get();
            }

            foreach ($avaliacaoTafNotaAluno as $dataAvalicao) {

                $dataAvalicaoTaf[$dataAvalicao->aluno_id] = array(
                    'corrida_nota' => $dataAvalicao->corrida_nota,
                    'flexao_braco_nota' => $dataAvalicao->flexao_braco_nota,
                    'flexao_barra_nota' => $dataAvalicao->flexao_barra_nota,
                    'abdominal_suficiencia' => $dataAvalicao->abdominal_suficiencia,
                    'media' => number_format($dataAvalicao->media, '3', '.', ''),

                    'corrida_nota_recuperacao' => $dataAvalicao->corrida_nota_recuperacao,
                    'flexao_braco_nota_recuperacao' => $dataAvalicao->flexao_braco_nota_recuperacao,
                    'flexao_barra_nota_recuperacao' => $dataAvalicao->flexao_barra_nota_recuperacao,
                    'abdominal_suficiencia_recuperacao' => $dataAvalicao->abdominal_suficiencia_recuperacao,
                    'media_recuperacao' => number_format((($dataAvalicao->media_recuperacao > 0) ? $dataAvalicao->media_recuperacao : 0), '3', '.', '')
                );
            }

            return view('ajax.lancar-taf-aluno', compact('alunos', 'dataAvalicaoTaf', 'recuperacao'));
        } else {
            return '<div class="box-registro-not-found">
                        <i class="ion-social-snapchat-outline" style="font-size: 32px"></i><br />
                        <span style="color: brown">Fora do período para lançamento do TFM do aluno</span>
                    </div>';
        }
    }

    public function GravarTafAluno(Request $request)
    {

        // CALCULANDO A RAZÃO

        $alunos = Alunos::find($request->id);

        // SER FOR DA ÁREA (BANDA/MÚSICA)

        if ($alunos->area_id == 3) {
            $razao = 2;
        } else {
            $razao = 3;
        }

        $corrida = (is_numeric(str_replace(',', '.', $request->corrida_nota)) && $request->corrida_nota <= 10) ? str_replace(',', '.', $request->corrida_nota) : 0;
        $flex_bra = (is_numeric(str_replace(',', '.', $request->flex_bra_nota)) && $request->flex_bra_nota <= 10) ? str_replace(',', '.', $request->flex_bra_nota) : 0;
        $flex_barr = (is_numeric(str_replace(',', '.', $request->flex_barr_nota)) && $request->flex_barr_nota <= 10) ? str_replace(',', '.', $request->flex_barr_nota) : 0;

        // VERIFICANDO SE É ATLETA

        /*
        
            CASO SEJA ATLETA APLICA A REGRA DE ACRESCIMO DE PONTOS 

        */

        $media = (($corrida + $flex_bra + $flex_barr) / $razao);

        if ($alunos->atleta_marexaer == 'S') {
            if ($media >= 5 && $media <= 6.999) {
                $media = $media + 1;
            } else if ($media > 6.999) {
                $media = $media + 2;
            }

            $media = ($media > 10) ? 10 : $media;
        }

        if ($request->suficiencia_abdominal == 'NS') {
            $reprovado = 'S';
        } else if ($request->suficiencia_abdominal == 'S' && $media >= 5) {
            $reprovado = 'N';
        } else {
            $reprovado = 'S';
        }


        $media_tela = number_format($media, '3', ',', '');
        $media_banco = number_format($media, '3', '.', '');

        $this->lancarTaf->aluno_id = $request->id;
        $this->lancarTaf->corrida_nota = $corrida;
        $this->lancarTaf->flexao_braco_nota = $flex_bra;
        $this->lancarTaf->flexao_barra_nota = $flex_barr;
        $this->lancarTaf->abdominal_suficiencia = $request->suficiencia_abdominal;
        $this->lancarTaf->media = $media_banco;
        $this->lancarTaf->reprovado = $reprovado;

        if ($this->lancarTaf->where('aluno_id', $request->id)->first()) {
            $this->lancarTaf->where('aluno_id', $request->id)->update([
                'corrida_nota' => str_replace(',', '.', $corrida),
                'flexao_braco_nota' => str_replace(',', '.', $flex_bra),
                'flexao_barra_nota' => str_replace(',', '.', $flex_barr),
                'abdominal_suficiencia' => $request->suficiencia_abdominal,
                'media' => $media_banco,
                'reprovado' => $reprovado
            ]);

            $data['status'] = 'ok';
        } else {
            if ($this->lancarTaf->save()) {
                $data['status'] = 'ok';
            } else {
                $data['status'] = 'err';
            }
        }

        $data['media'] = $media_tela;
        $this->classLog->RegistrarLog('Lançou TFM de aluno', auth()->user()->email);
        return $data;
    }

    public function GravarTafRecuperacaoAluno(Request $request)
    {
        //CALCULANDO A RAZÃO
        $alunos = Alunos::find($request->id);

        //SER FOR DA ÁREA (BANDA/MÚSICA)
        if ($alunos->area_id == 3) {
            $razao = 2;
        } else {
            $razao = 3;
        }

        $corrida = (is_numeric(str_replace(',', '.', $request->corrida_nota_recuperacao)) && $request->corrida_nota_recuperacao <= 10) ? str_replace(',', '.', $request->corrida_nota_recuperacao) : 0;
        $flex_bra = (is_numeric(str_replace(',', '.', $request->flexao_braco_nota_recuperacao)) && $request->flexao_braco_nota_recuperacao <= 10) ? str_replace(',', '.', $request->flexao_braco_nota_recuperacao) : 0;
        $flex_barr = (is_numeric(str_replace(',', '.', $request->flexao_barra_nota_recuperacao)) && $request->flexao_barra_nota_recuperacao <= 10) ? str_replace(',', '.', $request->flexao_barra_nota_recuperacao) : 0;

        $abdominal = $request->suficiencia_abdominal_recuperacao;
        //VERIFICANDO SE É ATLETA
        //CASO SEJA ATLETA APLICA A REGRA DE ACRESCIMO DE PONTOS 

        $media = (($corrida + $flex_bra + $flex_barr) / $razao);
        
        if ($alunos->atleta_marexaer == 'S') {
            if ($media >= 5 && $media <= 6.999) {
                $media = $media + 1;
            } else if ($media > 6.999) {
                $media = $media + 2;
            }

            $media = ($media > 10) ? 10 : $media;
        }

        if((!isset($abdominal)) && $media >= 5){
            $reprovado = 'N';
        }else if ($abdominal == 'NS') {
            $reprovado = 'S';
        } else if ($abdominal == 'S' && $media >= 5) {
            $reprovado = 'N';
        } else {
            $reprovado = 'S';
        }
        
        if ($media >= 5) {
            $media = 5;
        }else {
            $array_notas = array($corrida, $flex_bra, $flex_barr);

            for($i=count($array_notas);$i>0;$i++){
                $media = max($array_notas);
                if($media < 5){
                    break;
                }else{
                    $key = array_search($media, $array_notas);
                    unset($array_notas[$key]);
                }
            }
        }
        
        $media_tela = number_format($media, '3', ',', '');
        $media_banco = number_format($media, '3', '.', '');

        $this->lancarTaf->aluno_id = $request->id;
        $this->lancarTaf->corrida_nota_recuperacao = $corrida;
        $this->lancarTaf->flexao_braco_nota_recuperacao = $flex_bra;
        $this->lancarTaf->flexao_barra_nota_recuperacao = $flex_barr;
        $this->lancarTaf->abdominal_suficiencia_recuperacao = $abdominal;
        $this->lancarTaf->media_recuperacao = $media_banco;
        $this->lancarTaf->reprovado = $reprovado;

        if ($this->lancarTaf->where('aluno_id', $request->id)->first()) {
            $this->lancarTaf->where('aluno_id', $request->id)->update([
                'corrida_nota_recuperacao' => str_replace(',', '.', $corrida),
                'flexao_braco_nota_recuperacao' => str_replace(',', '.', $flex_bra),
                'flexao_barra_nota_recuperacao' => str_replace(',', '.', $flex_barr),
                'abdominal_suficiencia_recuperacao' => $abdominal,
                'media_recuperacao' => $media_banco,
                'reprovado_recuperacao' => $reprovado
            ]);

            $data['status'] = 'ok';
        } else {
            if ($this->lancarTaf->save()) {
                $data['status'] = 'ok';
            } else {
                $data['status'] = 'err';
            }
        }

        $data['media'] = $media_tela;
        $this->classLog->RegistrarLog('Lançou TFM de Recuperação de aluno ' . $request->id, auth()->user()->email);
        return $data;
    }

    public function FaleComAluno()
    {
        return view('ajax.fale-com-aluno');
    }

    public function EscolhadeQMS()
    {

        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $id_ano_corrente = ($ano_corrente->id) ?? 0;

        $total_geral_alunos = Alunos::where('data_matricula', $id_ano_corrente)->where('sexo', 'M')->where('area_id', 1)->get(['id']);
        $total_geral_alunas = Alunos::where('data_matricula', $id_ano_corrente)->where('sexo', 'F')->where('area_id', 1)->get(['id']);

        foreach ($total_geral_alunos as $item) {
            $alunosIds[] = $item->id;
        }

        foreach ($total_geral_alunas as $item) {
            $alunasIds[] = $item->id;
        }

        $alunosIDs = array_merge($alunosIds, $alunasIds);

        $alunos_classificacao = AlunosClassificacao::whereIn('aluno_id', $alunosIDs)->where('ano_formacao_id', $id_ano_corrente)->where('reprovado', 'N')->get();

        if ($alunos_classificacao) {
            foreach ($alunos_classificacao as $item) {
                if (isset($item->aluno->sexo) && $item->aluno->sexo == 'M') {
                    $total_alunos_aptos_array[] = 1;
                }
                if (isset($item->aluno->sexo) && $item->aluno->sexo == 'F') {
                    $total_alunas_aptas_array[] = 1;
                }
            }
        }


        $total_alunos_aptos = (isset($total_alunos_aptos_array)) ? array_sum($total_alunos_aptos_array) : '---';
        $total_alunas_aptas = (isset($total_alunas_aptas_array)) ? array_sum($total_alunas_aptas_array) : '---';

        $escolhas_qms = EscolhaQMS::where('ano_formacao_id', '=', $id_ano_corrente)->orderBy('id', 'desc')->get();
        $vagas = QMS::get();
        return view('ajax.escolha-de-qms')->with('ano_corrente', $ano_corrente)
            ->with('escolhas_qms', $escolhas_qms)
            ->with('vagas', $vagas)
            ->with('total_geral_alunos', count($total_geral_alunos))
            ->with('total_geral_alunas', count($total_geral_alunas))
            ->with('total_alunos_aptos', $total_alunos_aptos)
            ->with('total_alunas_aptas', $total_alunas_aptas);
    }

    public function DialogAdicionarPeriodoEscolhaQMS(Request $request)
    {

        $qms_matriz = QMSMatriz::orderBy('id', 'asc')->get();

        /* SELECIONANDO TODOS ALUNOS EM SITUAÇÃO DE APROVADO DISPONÍVEIS Comb/Log/Av (Area 1) */

        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $id_ano_corrente = ($ano_corrente->id) ?? 0;

        $alunos_id = Alunos::where('sexo', 'M')->where('area_id', 1)->where('data_matricula', $id_ano_corrente)->get(['id']);

        //$alunos = AlunosClassificacao::whereIn('aluno_id', $alunos_id)->where('reprovado', 'N')->count();
        $alunos = AlunosClassificacao::whereIn('aluno_id', $alunos_id)->count();

        $qms_list[] = '<h5 style="text-align: center; color: #08298A;"><b>Segmento masculino</b></h5>';
        foreach ($qms_matriz as $qms) {
            if ($qms->segmento == 'M') {
                $vagas = floor(($alunos * $qms->vagas) / 100);
                $vagas_sum[] = $vagas;
                $qms_list[] = ' <div style="padding: 2px 6px; border-bottom: 1px solid #ccc; cursor: pointer;" title="O sistema calculou o total de ' . $vagas . ' vagas que correspondem a QMS ' . $qms->qms . ' (' . $qms->vagas . '% ). Clique para alterar o total de vagas para esta QMS." onclick="$(this).find(\'input\').select();">
                                    <div style="float: left;">' . $qms->qms . '</div>
                                    <div style="float: right;">
                                        <input type="text" value="' . $vagas . '" name="' . $qms->qms_alias . '" style="width: 40px; border: none; text-align: right;" />
                                    </div>
                                    <div class="clear"></div>
                                </div>';
            }
        }

        $qms_list[] = '<div style="text-align: right; padding: 2px 6px;"><p><i>Combatente/Logística-Técnica/Aviação</i></p><b>Total de alunos para escolha:</b> ' . $alunos . '<br /><b>Total de vagas:</b> ' . array_sum($vagas_sum) . '</div>';
        unset($vagas_sum);

        /* SELECIONANDO TODOS ALUNOS EM SITUAÇÃO DE APROVADO DISPONÍVEIS Comb/Log/Av (Area 1) */

        $alunas_id = Alunos::where('sexo', 'F')->where('area_id', 1)->where('data_matricula', $id_ano_corrente)->get(['id']);

        //$alunas = AlunosClassificacao::whereIn('aluno_id', $alunas_id)->where('reprovado', 'N')->count();
        $alunas = AlunosClassificacao::whereIn('aluno_id', $alunas_id)->count();

        $qms_list[] = '<br /><h5 style="text-align: center; color: #FE2E64;"><b>Segmento feminino</b></h5>';
        foreach ($qms_matriz as $qms) {
            if ($qms->segmento == 'F') {
                $vagas = floor(($alunas * $qms->vagas) / 100);
                $vagas_sum[] = $vagas;
                $qms_list[] = ' <div style="padding: 2px 6px; border-bottom: 1px solid #ccc; cursor: pointer;" title="O sistema calculou o total de ' . $vagas . ' vagas que correspondem a ' . $qms->vagas . '% das vagas destinadas a QMS ' . $qms->qms . '. Clique para alterar o total de vagas para esta QMS." onclick="$(this).find(\'input\').select();">
                                    <div style="float: left;">' . $qms->qms . '</div>
                                    <div style="float: right;">
                                        <input type="text" value="' . $vagas . '" name="' . $qms->qms_alias . '" style="width: 40px; border: none; text-align: right;" />
                                    </div>
                                    <div class="clear"></div>
                                </div>';
            }
        }

        $qms_list[] = '<div style="text-align: right; padding: 2px 6px;"><p><i>Logística-Técnica/Aviação</i></p><b>Total de alunas para escolha:</b> ' . $alunas . '<br /><b>Total de vagas:</b> ' . array_sum($vagas_sum) . '</div><br />';
        unset($vagas_sum);

        $data['header'] = '<i class="ion-plus" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Adicionar período de escolha de QMS';
        $data['body'] = '   <div class="alert alert-danger errors-adicionar-escolha-qms" role="alert"></div>
                                    <div id="carouselExampleControls" class="carousel slide">
                                        <div class="carousel-inner">
                                        <form id="adicionar_periodo_escolha_qms">
                                            <div class="carousel-item active">     
                                                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                                                    <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                                        <div style="float: left;">
                                                            <i class="ion-android-create" style="font-size: 24px; color: #696969;"></i>
                                                        </div>
                                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                            <input class="no-style" style="width: 100%;" name="identificador" type="text" value="" maxlength="100" autocomplete="off" placeholder="Identificador. Ex: Escolha formativa" />
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>

                                                    <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                                        <div style="float: left;">
                                                            <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                                        </div>
                                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                            <input class="no-style data_mask" style="width: 100%;" name="data_inicio" type="text" value="" maxlength="10" autocomplete="off" placeholder="Informe a data de abertura (DD/MM/AAAA)" />
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>
                                                    <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                                        <div style="float: left;">
                                                            <i class="ion-ios-time" style="font-size: 24px; color: #696969;"></i>
                                                        </div>
                                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                            <input class="no-style minuto_mask" style="width: 100%;" name="hora_inicio" type="text" value="" maxlength="5" autocomplete="off" placeholder="Informe a hora de abertura (HH:MM)" />
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>
                                                    <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                                        <div style="float: left;">
                                                            <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                                        </div>
                                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                            <input class="no-style data_mask" style="width: 100%;" name="data_fim" type="text" value="" maxlength="10" autocomplete="off" placeholder="Informe a data de fechamento (DD/MM/AAAA)" />
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>
                                                    <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                                        <div style="float: left;">
                                                            <i class="ion-ios-time" style="font-size: 24px; color: #696969;"></i>
                                                        </div>
                                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                            <input class="no-style minuto_mask" style="width: 100%;" name="hora_fim" type="text" value="" maxlength="5" autocomplete="off" placeholder="Informe a hora de fechamento (HH:MM)" />
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>                                                                                                                                                                                                                
                                                    
                                                    <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                                        <div style="float: left; margin-top: 6px;">
                                                            <i class="ion-chatbubble-working" style="font-size: 24px; color: #696969;"></i>
                                                        </div>
                                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                            <textarea class="form-control" id="observacao" name="observacao" rows="3" placeholder="Observações pertinentes ao período"></textarea>
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>
                                                
                                            </div>
                                            <div class="carousel-item">
                                                <div style="width: 80%; margin: 0 auto;">
                                                    <h4 style="text-align: center;">Distribuição de vagas</h4><br />
                                                    <div style="width: 90%; margin: 0 auto;">
                                                        ' . implode('<br />',  $qms_list) . '
                                                    </div>
                                                </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>';

        $data['footer'] = ' <a class="btn btn-info" class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next" onclick="toogleContent(this, \'Configurar vagas &raquo;\', \'&laquo; Voltar\');">Configurar vagas &raquo;</a>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="AdicionarPeriodoEscolhaQMS();">
                                Salvar
                            </button>                            
                            <script>
                                $(\'.minuto_mask\').mask(\'00:00\');
                                $(\'.data_mask\').mask(\'00/00/0000\');
                            </script>';
        return $data;
    }

    public function DialogEditarPeriodoEscolhaQMS(Request $request)
    {

        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $id_ano_corrente = ($ano_corrente->id) ?? 0;

        $escolhaQMS = EscolhaQMS::find($request->id);
        $data_inicio = strftime('%d/%m/%Y', strtotime($escolhaQMS->data_hora_inicio));
        $data_fim = strftime('%d/%m/%Y', strtotime($escolhaQMS->data_hora_fim));
        $hora_inicio = strftime('%H:%M', strtotime($escolhaQMS->data_hora_inicio));
        $hora_fim = strftime('%H:%M', strtotime($escolhaQMS->data_hora_fim));

        $qms_matriz = QMS::where('escolha_qms_id', $request->id)->orderBy('id', 'asc')->get();

        /* SELECIONANDO TODOS ALUNOS EM SITUAÇÃO DE APROVADO DISPONÍVEIS Comb/Log/Av (Area 1) */

        $alunos_id = Alunos::where('sexo', 'M')->where('area_id', 1)->where('data_matricula', $id_ano_corrente)->get(['id']);

        //$alunos = AlunosClassificacao::whereIn('aluno_id', $alunos_id)->where('reprovado', 'N')->count();
        $alunos = AlunosClassificacao::whereIn('aluno_id', $alunos_id)->count();

        $qms_list[] = '<h5 style="text-align: center; color: #08298A;"><b>Segmento masculino</b></h5>';
        foreach ($qms_matriz as $qms) {
            if ($qms->segmento == 'M') {
                $vagas = $qms->vagas;
                $vagas_sum[] = $vagas;
                $qms_list[] = ' <div style="padding: 2px 6px; border-bottom: 1px solid #ccc; cursor: pointer;" title="Clique para alterar o total de vagas para esta QMS." onclick="$(this).find(\'input\').select();">
                                    <div style="float: left;">' . $qms->qms . '</div>
                                    <div style="float: right;">
                                        <input type="text" value="' . $vagas . '" name="' . $qms->qms_alias . '" style="width: 40px; border: none; text-align: right;" />
                                    </div>
                                    <div class="clear"></div>
                                </div>';
            }
        }

        $qms_list[] = '<div style="text-align: right; padding: 2px 6px;"><p><i>Combatente/Logística-Técnica/Aviação</i></p><b>Total de alunos aptos para escolha:</b> ' . $alunos . '<br /><b>Total de vagas:</b> ' . array_sum($vagas_sum) . '</div>';
        unset($vagas_sum);

        /* SELECIONANDO TODOS ALUNOS EM SITUAÇÃO DE APROVADO DISPONÍVEIS Comb/Log/Av (Area 1) */

        $alunas_id = Alunos::where('sexo', 'F')->where('area_id', 1)->where('data_matricula', $id_ano_corrente)->get(['id']);

        //$alunas = AlunosClassificacao::whereIn('aluno_id', $alunas_id)->where('reprovado', 'N')->count();
        $alunas = AlunosClassificacao::whereIn('aluno_id', $alunas_id)->count();

        $qms_list[] = '<br /><h5 style="text-align: center; color: #FE2E64;"><b>Segmento feminino</b></h5>';
        foreach ($qms_matriz as $qms) {
            if ($qms->segmento == 'F') {
                $vagas = $qms->vagas;
                $vagas_sum[] = $vagas;
                $qms_list[] = ' <div style="padding: 2px 6px; border-bottom: 1px solid #ccc; cursor: pointer;" title="Clique para alterar o total de vagas para esta QMS." onclick="$(this).find(\'input\').select();">
                                    <div style="float: left;">' . $qms->qms . '</div>
                                    <div style="float: right;">
                                        <input type="text" value="' . $vagas . '" name="' . $qms->qms_alias . '" style="width: 40px; border: none; text-align: right;" autocomplete="off" />
                                    </div>
                                    <div class="clear"></div>
                                </div>';
            }
        }

        $qms_list[] = '<div style="text-align: right; padding: 2px 6px;"><p><i>Logística-Técnica/Aviação</i></p><b>Total de alunas aptas para escolha:</b> ' . $alunas . '<br /><b>Total de vagas:</b> ' . array_sum($vagas_sum) . '</div><br />';
        unset($vagas_sum);

        $data['header'] = '<i class="ion-android-create" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Editar período de escolha de QMS';
        $data['body'] = '   <div class="alert alert-danger errors-adicionar-escolha-qms" role="alert"></div>
                                    <div id="carouselExampleControls" class="carousel slide">
                                        <div class="carousel-inner">
                                        <form id="editar_periodo_escolha_qms">
                                            <div class="carousel-item active">     
                                                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                                                    <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Identificador">
                                                        <div style="float: left;">
                                                            <i class="ion-android-create" style="font-size: 24px; color: #696969;"></i>
                                                        </div>
                                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                            <input class="no-style" style="width: 100%;" name="identificador" type="text" value="' . $escolhaQMS->identificador . '" maxlength="100" autocomplete="off" placeholder="Identificador. Ex: Escolha formativa" />
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>

                                                    <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Data de início">
                                                        <div style="float: left;">
                                                            <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                                        </div>
                                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                            <input class="no-style data_mask" style="width: 100%;" name="data_inicio" type="text" value="' . $data_inicio . '" maxlength="10" autocomplete="off" placeholder="Informe a data de abertura (DD/MM/AAAA)" />
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>
                                                    <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Hora de início">
                                                        <div style="float: left;">
                                                            <i class="ion-ios-time" style="font-size: 24px; color: #696969;"></i>
                                                        </div>
                                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                            <input class="no-style minuto_mask" style="width: 100%;" name="hora_inicio" type="text" value="' . $hora_inicio . '" maxlength="5" autocomplete="off" placeholder="Informe a hora de abertura (HH:MM)" />
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>
                                                    <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Data de fechamento">
                                                        <div style="float: left;">
                                                            <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                                        </div>
                                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                            <input class="no-style data_mask" style="width: 100%;" name="data_fim" type="text" value="' . $data_fim . '" maxlength="10" autocomplete="off" placeholder="Informe a data de fechamento (DD/MM/AAAA)" />
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>
                                                    <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Hora para fechamento">
                                                        <div style="float: left;">
                                                            <i class="ion-ios-time" style="font-size: 24px; color: #696969;"></i>
                                                        </div>
                                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                            <input class="no-style minuto_mask" style="width: 100%;" name="hora_fim" type="text" value="' . $hora_fim . '" maxlength="5" autocomplete="off" placeholder="Informe a hora de fechamento (HH:MM)" />
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>                                                                                                                                                                                                                
                                                    
                                                    <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Observações pertinentes">
                                                        <div style="float: left; margin-top: 6px;">
                                                            <i class="ion-chatbubble-working" style="font-size: 24px; color: #696969;"></i>
                                                        </div>
                                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                            <textarea class="form-control" id="observacao" name="observacao" rows="3" placeholder="Observações pertinentes ao período">' . $escolhaQMS->observacao . '</textarea>
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>
                                                
                                            </div>
                                            <div class="carousel-item">
                                                <div style="width: 80%; margin: 0 auto;">
                                                    <h4 style="text-align: center;">Distribuição de vagas</h4><br />
                                                    <div style="width: 90%; margin: 0 auto;">
                                                        ' . implode('<br />',  $qms_list) . '
                                                    </div>
                                                </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>';

        $data['footer'] = ' <a class="btn btn-info" class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next" onclick="toogleContent(this, \'Configurar vagas &raquo;\', \'&laquo; Voltar\');">Configurar vagas &raquo;</a>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="AtualizarPeriodoEscolhaQMS(' . $request->id . ');">
                                Salvar
                            </button>                            
                            <script>
                                $(\'.minuto_mask\').mask(\'00:00\');
                                $(\'.data_mask\').mask(\'00/00/0000\');
                                $(function () {
                                    $(\'[data-toggle="tooltip"]\').tooltip();
                                });                               
                            </script>';
        return $data;
    }

    public function AtualizarPeriodoEscolhaQMS(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request)
    {

        @list($dia_i, $mes_i, $ano_i) = @explode('/', $request->data_inicio);
        @list($dia_f, $mes_f, $ano_f) = @explode('/', $request->data_fim);

        $request->hora_inicio = ($request->hora_inicio == '') ? '23:59' : $request->hora_inicio;
        $request->hora_fim = ($request->hora_fim == '') ? '23:59' : $request->hora_fim;

        /*

            A escolha de QMS criada, não devará ter datas conflitantes. Por isso eu consulto a tabela verificando essa situação.

        */

        //$escolhas_registradas = EscolhaQMS::where('data_hora_fim', '>', $ano_i.'-'.$mes_i.'-'.$dia_i.' '.$request->hora_inicio)->orWhere('data_hora_inicio', '<', $ano_f.'-'.$mes_f.'-'.$dia_f.' '.$request->hora_fim)->count();
        $escolhas_resgistradas = DB::select("SELECT id FROM escolha_qms WHERE 'data_hora_fim'>'" . $ano_i . '-' . $mes_i . '-' . $dia_i . ' ' . $request->hora_inicio . ':00' . "' OR 'data_hora_inicio'<'" . $ano_f . '-' . $mes_f . '-' . $dia_f . ' ' . $request->hora_fim . ':00' . "'");

        if (strlen($request->identificador) < 2) {
            $data['status'] = 'err2';
            $data['erroPreencimento'] = 'Identificador possui nonme muito curto. Informe um identificador válido.';
        } else if (!@checkdate($mes_i, $dia_i, $ano_i)) {
            $data['status'] = 'err2';
            $data['erroPreencimento'] = 'Data de início invalida';
        } else if (!@checkdate($mes_f, $dia_f, $ano_f)) {
            $data['status'] = 'err2';
            $data['erroPreencimento'] = 'Data final invalida';
        } else if (!strtotime($request->hora_inicio)) {
            $data['status'] = 'err2';
            $data['erroPreencimento'] = 'Formato da hora inicial inválido.';
        } else if (!strtotime($request->hora_fim)) {
            $data['status'] = 'err2';
            $data['erroPreencimento'] = 'Formato da hora final inválido.';
        } else if (strtotime($ano_i . '-' . $mes_i . '-' . $dia_i . ' ' . $request->hora_inicio) >= strtotime($ano_f . '-' . $mes_f . '-' . $dia_f . ' ' . $request->hora_fim)) {
            $data['status'] = 'err2';
            $data['erroPreencimento'] = 'O início do período de escolha deve ser menor que a data/hora de término.';
        } else if (isset($escolhas_registradas) && count($escolhas_registradas) > 1) {
            $data['status'] = 'err2';
            $data['erroPreencimento'] = 'O período de escolha informado não deve coincidir com nenhum outro período, portanto deverá ser único.';
        } else {

            $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
            $escolha_qms = EscolhaQMS::find($request->id);
            $escolha_qms->identificador = $request->identificador;
            $escolha_qms->data_hora_inicio = $ano_i . '/' . $mes_i . '/' . $dia_i . ':' . $request->hora_inicio;
            $escolha_qms->data_hora_fim = $ano_f . '/' . $mes_f . '/' . $dia_f . ':' . $request->hora_fim;
            $escolha_qms->observacao = ($escolha_qms->observacao == '') ? '' : $escolha_qms->observacao;
            if ($escolha_qms->save()) {

                /* ATUALIZANDO AS VAGAS NA TABELA qms */

                $table_qms = QMS::where('escolha_qms_id', $request->id)->get();

                foreach ($table_qms as $qms) {

                    \App\Models\QMS::where('qms_alias', $qms->qms_alias)->where('escolha_qms_id', $request->id)->update(['vagas' => $request[$qms->qms_alias]]);
                }

                $data['status'] = 'ok';
            } else {
                $data['status'] = 'err';
            }
        }
        $this->classLog->RegistrarLog('Atualizou período de escolha de QMS', auth()->user()->email);
        return $data;
    }

    public function RemoverEscolhaQMS(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request)
    {
        if ($ownauthcontroller->PermissaoCheck(17)) {
            $escolhaQMS = EscolhaQMS::find($request->id);
            if ($escolhaQMS->delete()) {
                $data['status'] = 'ok';
            } else {
                $data['status'] = 'err';
                $data['response'] = 'Houve um erro interno ao tentar excluir este período de escolha.';
            }
        } else {
            $data['status'] = 'err1';
            $data['response'] = 'Você não tem permissão para executar esta operação.';
        }
        $this->classLog->RegistrarLog('Removeu período de escolha de QMS', auth()->user()->email);
        return $data;
    }

    public function AdicionarPeriodoEscolhaQMS(Request $request)
    {


        @list($dia_i, $mes_i, $ano_i) = @explode('/', $request->data_inicio);
        @list($dia_f, $mes_f, $ano_f) = @explode('/', $request->data_fim);

        $request->hora_inicio = ($request->hora_inicio == '') ? '23:59' : $request->hora_inicio;
        $request->hora_fim = ($request->hora_fim == '') ? '23:59' : $request->hora_fim;

        /*

            A escolha de QMS criada, não devará ter datas conflitantes. Por isso eu consulto a tabela verificando essa situação.

        */

        //$escolhas_registradas = EscolhaQMS::where('data_hora_fim', '>', $ano_i.'-'.$mes_i.'-'.$dia_i.' '.$request->hora_inicio)->orWhere('data_hora_inicio', '<', $ano_f.'-'.$mes_f.'-'.$dia_f.' '.$request->hora_fim)->count();
        $escolhas_resgistradas = DB::select("SELECT id FROM escolha_qms WHERE 'data_hora_fim'>'" . $ano_i . '-' . $mes_i . '-' . $dia_i . ' ' . $request->hora_inicio . ':00' . "' OR 'data_hora_inicio'<'" . $ano_f . '-' . $mes_f . '-' . $dia_f . ' ' . $request->hora_fim . ':00' . "'");


        if (strlen($request->identificador) < 2) {
            $data['status'] = 'err2';
            $data['erroPreencimento'] = 'Identificador possui nonme muito curto. Informe um identificador válido.';
        } else if (!@checkdate($mes_i, $dia_i, $ano_i)) {
            $data['status'] = 'err2';
            $data['erroPreencimento'] = 'Data de início invalida';
        } else if (!@checkdate($mes_f, $dia_f, $ano_f)) {
            $data['status'] = 'err2';
            $data['erroPreencimento'] = 'Data final invalida';
        } else if (!strtotime($request->hora_inicio)) {
            $data['status'] = 'err2';
            $data['erroPreencimento'] = 'Formato da hora inicial inválido.';
        } else if (!strtotime($request->hora_fim)) {
            $data['status'] = 'err2';
            $data['erroPreencimento'] = 'Formato da hora final inválido.';
        } else if (strtotime($ano_i . '-' . $mes_i . '-' . $dia_i . ' ' . $request->hora_inicio) >= strtotime($ano_f . '-' . $mes_f . '-' . $dia_f . ' ' . $request->hora_fim)) {
            $data['status'] = 'err2';
            $data['erroPreencimento'] = 'O início do período de escolha deve ser menor que a data/hora de término.';
        } else if (isset($escolhas_registradas) && count($escolhas_registradas) > 0) {
            $data['status'] = 'err2';
            $data['erroPreencimento'] = 'O período de escolha informado não deve coincidir com nenhum outro período, portanto deverá ser único.';
        } else {

            $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
            $escolha_qms = new EscolhaQMS;
            $escolha_qms->identificador = $request->identificador;
            $escolha_qms->ano_formacao_id = $ano_corrente->id;
            $escolha_qms->data_hora_inicio = $ano_i . '/' . $mes_i . '/' . $dia_i . ':' . $request->hora_inicio;
            $escolha_qms->data_hora_fim = $ano_f . '/' . $mes_f . '/' . $dia_f . ':' . $request->hora_fim;
            $escolha_qms->observacao = ($escolha_qms->observacao == '') ? '' : $escolha_qms->observacao;
            if ($escolha_qms->save()) {

                /* OS DADOS DA QMS MATRIZ DEVEM SER PEGOS AUTOMATICAMENTE */

                $qms_matriz = QMSMatriz::orderBy('id', 'asc')->get();

                foreach ($qms_matriz as $qms) {
                    $qms_instance = new QMS;
                    $qms_instance->qms = $qms->qms;
                    $qms_instance->qms_sigla = $qms->qms_sigla;
                    $qms_instance->segmento = $qms->segmento;
                    $qms_instance->img = $qms->img;
                    $qms_instance->vagas = $request[$qms->qms_alias];
                    $qms_instance->qms_alias = $qms->qms_alias;
                    $qms_instance->escolha_qms_id = $escolha_qms->id;
                    if (!$qms_instance->save()) {
                        $erro_table_qms[] = 'err';
                    }
                }

                if (isset($erro_table_qms)) {
                    $data['status'] = 'err1';
                    // REMOVENDO INSERSÕES PROVAVELMENTE CRIADAS
                    QMS::where('escolha_qms_id', '=', $escolha_qms->id)->delete();
                    // REMOVENDO INSERSAO EscolhaQMS
                    EscolhaQMS::destroy($escolha_qms->id);
                } else {
                    $data['status'] = 'ok';
                }
            } else {
                $data['status'] = 'err';
            }
        }
        $this->classLog->RegistrarLog('Adicionou período de escolha de QMS', auth()->user()->email);
        return $data;
    }

    public function VoluntariosParaAviacao()
    {
        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $escolha_aviacao_status = EscolhaAviacaoStatus::where('ano_formacao_id', $ano_corrente->id)->firstOrCreate(['ano_formacao_id' => $ano_corrente->id]);

        return view('ajax.voluntarios-para-aviacao')->with('ano_corrente', $ano_corrente)->with('escolha_aviacao_status', $escolha_aviacao_status)->with('ownauthcontroller', $this->ownauthcontroller);
    }

    public function MarcarVoluntarioAviacao()
    {
        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $escolha_aviacao_status = EscolhaAviacaoStatus::where('ano_formacao_id', $ano_corrente->id)->first();
        
        if ($escolha_aviacao_status->status == 0) {
            $escolha_aviacao_status->status = 1;
        } else {
            $escolha_aviacao_status->status = 0;
        }

        if ($escolha_aviacao_status->save()) {
            $data['status'] = 'ok';
        } else {
            $data['status'] = 'err';
        }
        $this->classLog->RegistrarLog('Marcou voluntário para aviação', auth()->user()->email);
        return $data;
    }

    function DialogRelacaoVoluntariosAviacao(Request $request)
    {

        /* DEVEM SER APRESENTADOS NESTA LISTA SOMENTE OS ALUNOS DO ANO CORRENTE */

        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();

        $alunos = Alunos::join('alunos_voluntarios_aviacao', 'alunos.id', '=', 'alunos_voluntarios_aviacao.alunos_id')
            ->where([['data_matricula', '=', $ano_corrente->id], ['selecionado_exame', '=', 'S']])
            ->orderBy('omcts_id', 'asc')
            ->orderBy('sexo', 'asc')
            ->get();

        $data[] = ' <div style="margin-top: 12px; text-align: center; color: #696969;">
                                            <div class="alert status-aptos-aviacao" role="alert" style="width: 70%; margin: 20px auto;"></div>
                                            <h5>APTOS PARA QMS AVIAÇAO<h5>
                                            <p>Selecione alunos aptos nos exames complementares</p>
                                        </div>';

        if (count($alunos) == 0) {

            $data[] = ' <div class="box-registro-not-found" style="margin-top: 44px;">
                            <i class="ion-social-snapchat-outline" style="font-size: 32px"></i><br />
                                <span style="color: brown">Sem registro de alunos voluntários</span>
                        </div>';
        } else {

            $data[] = ' <div style="margin: 48px auto; width: 60%;">
                            <form id="relacao-aptos-aviacao">
                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                            <!--div style="text-align: right; color: #696969; font-size: 12px"><i>Listando: ' . count($alunos) . ' alunos</i></div-->
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col" style="text-align: center;">Ordem</th>
                                        <th scope="col" style="text-align: center;">IS</th>
                                        <th scope="col" style="text-align: center;">AVI</th>
                                        <th scope="col">ALUNO(A)</th>
                                    </tr>
                                </thead>
                                <tbody>';

            $i = 1;
            
                foreach ($alunos as $aluno) {
                    $status_checked_aptos_is = ($aluno->apto_is == 'N') ? '' : 'checked';
                    $status_checked_aptos_avi = ($aluno->apto_avi == 'N') ? '' : 'checked';
                         
                    $data[] = ' <tr style="cursor: pointer;">
                                    <td style="text-align: center; vertical-align: middle;">' . $i . '</td>
                                    <td>
                                        <div class="custom-control custom-checkbox" style="width: 36%; margin: 4px auto;">
                                            <input type="checkbox" class="custom-control-input" id="alunos_id_aptos_is_' . $aluno->id . '" name="alunos_aptos_is[]" value="' . $aluno->id . '" ' . $status_checked_aptos_is . ' />
                                            <label class="custom-control-label" for="alunos_id_aptos_is_' . $aluno->id . '"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-checkbox" style="width: 36%; margin: 4px auto;">
                                            <input type="checkbox" class="custom-control-input" id="alunos_id_aptos_avi_' . $aluno->id . '" name="alunos_aptos_avi[]" value="' . $aluno->id . '" ' . $status_checked_aptos_avi . ' />
                                            <label class="custom-control-label" for="alunos_id_aptos_avi_' . $aluno->id . '"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <b>' . $aluno->numero . ' ' . $aluno->nome_guerra . '</b><br />
                                        <span style="color: #696969;"><i>' . $aluno->nome_completo . '</i></span><br />
                                        ' . $aluno->omct->sigla_omct . '
                                    </td>
                                </tr>';
                           
                    $i++;
                }
            
            
            $data[] = '         </tbody>
                            </table>
                            </form>
                        </div>';

            $data[] = ' <div style="margin: 32px auto; width: 50%; text-align: center; ">
                            <button type="button" class="btn btn-success" onclick="enviarRelacaoAptosAviacao(this);">Gravar registro</button>
                        </div>';
        }



        $data['response'] = implode('', $data);
        return $data;
    }

    public function AtualizarRelacaoAptosAviacao(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request)
    {

        if ($ownauthcontroller->PermissaoCheck(19) && $request->ajax()) {

            /* SETO TODOS PARA INÁPTO E POSTERIORMENTE SETO PARA ÁPTO OS SELECIONADOS*/

            /* DEVEM SER APRESENTADOS NESTA LISTA SOMENTE OS ALUNOS DO ANO CORRENTE */

            $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();

            $param['anoCorrente'] = $ano_corrente;

            /* RESETANDO TODOS DO ANO CORRENTE */
            $alunosAviacao = AlunosVoluntAv::whereHas('aluno', function ($q) use ($param) {
                $q->where([['data_matricula', '=', $param['anoCorrente']->id]]);
            })->where([['selecionado_exame', '=', 'S']]);

            $alunosAviacao->update(['apto_is' => 'N', 'apto_avi' => 'N']);


            $alunosAviacao->whereIn('id', $request->alunos_aptos_is)->update(['apto_is' => 'S']);
            $alunosAviacao->whereIn('id', $request->alunos_aptos_avi)->update(['apto_avi' => 'S']);


            /*$alunos = Alunos::join('alunos_voluntarios_aviacao', 'alunos.id', '=', 'alunos_voluntarios_aviacao.alunos_id')
                ->where('data_matricula', $ano_corrente->id)
                ->get(['alunos_voluntarios_aviacao.id']);

            foreach ($alunos as $aluno) {
                $alunos_ids[] = $aluno->id;
            }

            

            if (isset($alunos_ids)) {
                $update = AlunosVoluntAv::whereIn('id', $alunos_ids)->update(['apto' => 0]);
            }

            if (isset($request->alunos_aptos)) {
                $update = AlunosVoluntAv::whereIn('id', $request->alunos_aptos)->update(['apto' => 1]);
            }*/

            $data['status'] = 'ok';
            $data['response'] = 'Relação de alunos aptos atualizada com sucesso';
        } else {
            $data['status'] = 'err';
            $data['response'] = '<strong>ATENÇÃO:</strong> Houve um erro. Por favor repita a operação mais tarde';
        }
        $this->classLog->RegistrarLog('Atualizou relação de aptos para aviação', auth()->user()->email);
        return $data;
    }

    public function VisaoGeral()
    {
        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $ano_formacao = ($ano_corrente->id) ?? 0;
        $disciplinas = Disciplinas::where('ano_formacao_id', '=', $ano_formacao)->get();
        $alunos['total_alunos'] = Alunos::where('sexo', '=', 'M')->where('data_matricula', '=', $ano_formacao)->count();
        $alunos['total_alunas'] = Alunos::where('sexo', '=', 'F')->where('data_matricula', '=', $ano_formacao)->count();
        $alunos['total'] = (($alunos['total_alunos'] + $alunos['total_alunas']) == 0) ? 1 : $alunos['total_alunos'] + $alunos['total_alunas'];
        $alunos['porcentagem_alunos'] = floor(($alunos['total_alunos'] * 100) / $alunos['total']);
        $alunos['porcentagem_alunas'] = ceil(($alunos['total_alunas'] * 100) / $alunos['total']);
        $alunos['list'] = Alunos::get();
        foreach ($disciplinas as $disciplina) {
            $disciplinas_id[] = $disciplina->id;
        }
        $disciplinas_id = ($disciplinas_id) ?? array(0);
        $avaliacoes = Avaliacoes::whereIn('disciplinas_id', $disciplinas_id)->where('data', '>', date('Y-m-d'))->orderBy('data', 'asc')->get();
        return  view('ajax.visao-geral')->with('total_operadores', Operadores::count())
            ->with('ano_corrente', $ano_corrente)
            ->with('disciplinas', $disciplinas)
            ->with('avaliacoes', $avaliacoes)
            ->with('alunos', $alunos);
    }

    public function Relatorios(\App\Http\Controllers\OwnAuthController $ownauthcontroller)
    {
        return view('ajax.relatorios')->with('ownauthcontroller', $ownauthcontroller);
    }

    public function AnosDeFormacao(Request $request)
    {
        $anos_formacao = AnoFormacao::orderBy('formacao', 'desc')->get();
        return view('ajax.anos-de-formacao')->with('anos_formacao', $anos_formacao);
    }

    public function PrecedenciaDesempate(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request)
    {

        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();

        $notas_empatadas = DB::select("SELECT nota_final, COUNT(nota_final) FROM alunos_classificacao WHERE ano_formacao_id=" . $ano_corrente->id . " GROUP BY nota_final HAVING COUNT(nota_final)>1");

        foreach ($notas_empatadas as $item) {
            $empatesNotas[] = str_replace(',', '.', $item->nota_final);
            //$empatesNotas[] = $item->nota_final;
        }

        $empatesNotas = ($empatesNotas) ?? array(null);

        foreach ($empatesNotas as $item) {
            $alunosIDs[] = AlunosClassificacao::where('nota_final', 'LIKE', $item)->get(['aluno_id']);
        }

        $alunosIDs = ($alunosIDs) ?? array(null);

        foreach ($alunosIDs as $item) {
            foreach ($item as $consulta) {
                $ids[] = $consulta->aluno_id;
            }
        }

        $ids = ($ids) ?? array(null);

        $classificacao = AlunosClassificacao::whereIn('aluno_id', $ids)->orderBy('nota_final', 'asc')->orderBy('classificacao', 'asc')->get();

        if ($ownauthcontroller->PermissaoCheck(1)) {
            return view('ajax.precedencia-desempate')->with('classificacao', $classificacao);
        } else {
            return 'Não autorizado!';
        }
    }

    public function AlterarPrecedencia(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request)
    {
        //aluno_id}/{precedencia
        $this->classLog->RegistrarLog('Altereou precedência para desempate', auth()->user()->email);
        if ($ownauthcontroller->PermissaoCheck(1)) {
            $aluno = Alunos::find($request->aluno_id);
            if (is_numeric($request->precedencia)) {
                $aluno->precedencia = $request->precedencia;
                if ($aluno->save()) {
                    $data = $aluno->precedencia;
                } else {
                    $data = 0;
                }
            }

            $data = ($data) ?? 0;

            return $data;
        } else {
            return 0;
        }
    }

    public function RemoverAnoFormacao(Request $request)
    {
        $id = $request->id;
        if (AnoFormacao::destroy($id)) {
            $data['status'] = 'ok';
        } else {
            $data['status'] = 'err';
        }
        $this->classLog->RegistrarLog('Removeu ano de formação', auth()->user()->email);
        return $data;
    }

    public function RemoverDisciplina(Request $request)
    {
        $id = $request->id;
        if (Disciplinas::destroy($id)) {
            $data['status'] = 'ok';
        } else {
            $data['status'] = 'err';
        }
        $this->classLog->RegistrarLog('Removeu disciplina', auth()->user()->email);
        return $data;
    }

    public function DialogAdicionarAnoFormacao()
    {

        $data['header'] = '<i class="ion-ios-calendar-outline" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Novo ano de formação';
        $data['body'] = '   <div class="alert alert-danger errors-cadastro-operador" role="alert"></div>
                                    <form id="adicionar_ano_formacao">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-calendar" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="formacao" type="text" pattern="[0-9]{4}" value="" maxlength="4" autocomplete="off" placeholder="Ano de formação (AAAA). Ex 2020" />
                                            </div>
                                            <div class="clear"></div>                                         
                                        </div>                                        
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="data_matricula" type="text" value="" maxlength="10" autocomplete="off" placeholder="Data de matrúcla (DD/MM/AAAA)." />
                                            </div>
                                            <div class="clear"></div>                                            
                                        </div>                                        
                                    </form>';

        $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="AdicionarAnoFormacao();">
                                Salvar
                            </button>
                            <script>
                                $(\'input[name="data_matricula"]\').mask(\'00/00/0000\');
                            </script>';
        return $data;
    }

    public function AdicionarAnoFormacao(Request $request)
    {
        $ano_formacao = new AnoFormacao;
        $ano_formacao->formacao = $request->formacao;

        list($dia, $mes, $ano) = explode('/', $request->data_matricula);

        $dia = ($dia) ?? 0;
        $mes = ($mes) ?? 0;
        $ano = ($ano) ?? 0;

        if (!is_numeric($request->formacao) || $request->formacao < (date('Y') - 5)) {
            $data['status'] = 'err';
        } else if (!checkdate($mes, $dia, $ano)) {
            $data['status'] = 'err';
        } else {
            $ano_formacao->data_matricula = $ano . '-' . $mes . '-' . $dia;
            if ($ano_formacao->save()) {
                $data['status'] = 'ok';
            }
        }
        $this->classLog->RegistrarLog('Adicionou ano de formação', auth()->user()->email);
        return $data;
    }

    public function AtualizarAnoFormacao(Request $request)
    {
        $ano_formacao = AnoFormacao::find($request->id);

        list($dia, $mes, $ano) = explode('/', $request->data_matricula);

        $dia = ($dia) ?? 0;
        $mes = ($mes) ?? 0;
        $ano = ($ano) ?? 0;

        if (!checkdate($mes, $dia, $ano)) {
            $data['status'] = 'err';
        } else {
            $ano_formacao->data_matricula = $ano . '-' . $mes . '-' . $dia;
            if ($ano_formacao->save()) {
                $data['status'] = 'ok';
                $data['content'] = '(Data matrícula: ' . $dia . '/' . $mes . '/' . $ano . ')';
            } else {
                $data['status'] = 'err';
            }
        }
        $this->classLog->RegistrarLog('Atualizou ano de formação', auth()->user()->email);
        return $data;
    }

    public function DialogEditarAnoFromacao(Request $request)
    {
        $ano_formacao = AnoFormacao::find($request->id);
        $data['header'] = '<i class="ion-ios-calendar-outline" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Editar ano de formação';
        $data['body'] = '   <form id="atualizar_ano_formacao">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">                                     
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <p>Informe a nova data de matrícula</p>
                                            <div style="float: left;">
                                                <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="data_matricula" type="text" value="' . strftime('%d/%m/%Y', strtotime($ano_formacao->data_matricula)) . '" maxlength="10" autocomplete="off" placeholder="Data de matrúcla (DD/MM/AAAA)." />
                                            </div>
                                            <div class="clear"></div>                                            
                                        </div>                                        
                                    </form>';

        $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="AtualizarAnoFormacao(' . $request->id . ');">
                                Salvar
                            </button>
                            <script>
                                $(\'input[name="data_matricula"]\').mask(\'00/00/0000\');
                            </script>';
        return $data;
    }

    public function DialogAdicionarAvaliacao()
    {

        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $disciplinas = Disciplinas::orderBy('id', 'desc')->where('ano_formacao_id', '=', $ano_corrente->id)->get();
        $options_disciplinas[] = '<option value="0">Selecione uma disciplina</option>';
        foreach ($disciplinas as $disciplina) {
            $options_disciplinas[] = '<option value="' . $disciplina->id . '">' . $disciplina->nome_disciplina . '</option>';
        }

        $data['header'] = '<i class="ion-ios-compose" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Adicionar avaliação';
        $data['body'] = '   <div class="alert alert-danger errors-adicionar-avaliacoes" role="alert"></div>
                                    <form id="adicionar_avaliacao">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left; margin-top: 6px;">
                                                <i class="ion-ios-bookmarks" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <select class="custom-select" name="disciplinas_id" onchange="loadDialogChamadas(this);">' . implode('', $options_disciplinas) . '</select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-compose" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="nome_completo" type="text" value="" maxlength="100" autocomplete="off" placeholder="Nome completo da avaliação" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                         
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-compose-outline" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="nome_abrev" type="text" value="" maxlength="50" autocomplete="off" placeholder="Nome abreviado. Ex AC1" />
                                            </div>
                                            <div class="clear"></div>
                                        </div> 
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style data_mask" style="width: 100%;" name="data_prova" type="text" value="" maxlength="10" autocomplete="off" placeholder="Data da prova" />
                                            </div>
                                            <div class="clear"></div>
                                        </div> 
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-clock" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style minuto_mask" style="width: 100%;" name="hora_prova" type="text" value="" maxlength="5" autocomplete="off" placeholder="Hora da prova" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-android-done" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="gbm" type="text" value="" maxlength="4" autocomplete="off" placeholder="GBM" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-flask" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="peso" type="text" value="" maxlength="5" autocomplete="off" placeholder="Peso" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Informe o prazo em dias que a UETE terá para informar o resultado obtido na avaliação">
                                            <div style="float: left;">
                                                <i class="ion-compass" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="prazo_nota" type="text" value="" maxlength="5" autocomplete="off" placeholder="Prazo para envio de resultado (Nº dias)" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                        
                                        <div class="box-dialog-chamadas"></div>

                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left; margin-top: 6px;">
                                                <i class="ion-chatbubble-working" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <textarea class="form-control" id="observacao" name="observacao" rows="3" placeholder="Observações pertinentes a avaliação"></textarea>
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                        

                                    </form>';

        $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="AdicionarAvaliacao();">
                                Salvar
                            </button>                            
                            <script>
                                $(\'.minuto_mask\').mask(\'00:00\');
                                $(\'.data_mask\').mask(\'00/00/0000\');
                                $(function () {
                                    $(\'[data-toggle="tooltip"]\').tooltip();
                                });
                            </script>                            ';
        return $data;
    }

    public function DialogAdicionarAvaliacaoRec()
    {
        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $disciplinas = Disciplinas::orderBy('id', 'desc')->where('ano_formacao_id', '=', $ano_corrente->id)->get();
        $options_disciplinas[] = '<option value="0">Selecione uma disciplina</option>';
        foreach ($disciplinas as $disciplina) {
            $options_disciplinas[] = '<option value="' . $disciplina->id . '">' . $disciplina->nome_disciplina . '</option>';
        }

        $data['header'] = '<i class="ion-loop" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Adicionar avaliação de recuperação';
        $data['body'] = '   <div class="alert alert-danger errors-adicionar-avaliacoes" role="alert"></div>
                                    <form id="adicionar_avaliacao_recuperacao">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left; margin-top: 6px;">
                                                <i class="ion-ios-bookmarks" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <select class="custom-select" name="disciplinas_id">' . implode('', $options_disciplinas) . '</select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style data_mask" style="width: 100%;" name="data_prova" type="text" value="" maxlength="10" autocomplete="off" placeholder="Data da prova" />
                                            </div>
                                            <div class="clear"></div>
                                        </div> 
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-clock" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style minuto_mask" style="width: 100%;" name="hora_prova" type="text" value="" maxlength="5" autocomplete="off" placeholder="Hora da prova" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                         
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-android-done" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="gbm" type="text" value="" maxlength="4" autocomplete="off" placeholder="GBM" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Informe o prazo em dias que a UETE terá para informar o resultado obtido na avaliação">
                                            <div style="float: left;">
                                                <i class="ion-compass" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="prazo_nota" type="text" value="" maxlength="5" autocomplete="off" placeholder="Prazo para envio de resultado (Nº dias)" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                        
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left; margin-top: 6px;">
                                                <i class="ion-chatbubble-working" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <textarea class="form-control" id="observacao" name="observacao" rows="3" placeholder="Observações pertinentes a avaliação"></textarea>
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                        

                                    </form>';

        $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="AdicionarAvaliacaoRec();">
                                Salvar
                            </button>                            
                            <script>
                                $(\'.minuto_mask\').mask(\'00:00\');
                                $(\'.data_mask\').mask(\'00/00/0000\');
                                $(function () {
                                    $(\'[data-toggle="tooltip"]\').tooltip();
                                });
                            </script>';
        return $data;
    }

    public function AdicionarAvaliacao(AvaliacoesRequest $request)
    {
        $peso = ($request->peso == '') ? 1 : $request->peso;
        $avaliacao = new Avaliacoes;
        $avaliacao->disciplinas_id = $request->disciplinas_id;
        $avaliacao->nome_completo = $request->nome_completo;
        $avaliacao->nome_abrev = $request->nome_abrev;
        $avaliacao->gbm = $request->gbm;
        $avaliacao->peso = $peso;
        $avaliacao->prazo_nota = $request->prazo_nota;
        $avaliacao->observacao = (empty($request->observacao)) ? '' : $request->observacao;
        $avaliacao->chamada = $request->chamada ?? 1;
        $avaliacao->chamada_refer_id = $request->chamada_refer_id ?? 0;
        $avaliacao->avaliacao_recuperacao = 0;

        if (is_array(@explode('/', $request->data_prova)) && strtotime($request->hora_prova)) {
            $data_prova = explode('/', $request->data_prova);
            $data_hora_prova = $data_prova[2] . '-' . $data_prova[1] . '-' . $data_prova[0] . ' ' . $request->hora_prova . ':00';
            //if(strtotime($data_hora_prova) < (time() - $request->prazo_nota*3600*24)){
            //$data['data_prova'] = 'err';
            //} else if(strtotime($data_hora_prova) > strtotime("+ 365 days ", time())){
            if (strtotime($data_hora_prova) > strtotime("+ 365 days ", time())) {
                $data['data_prova'] = 'err1';
            } else {
                $data['data_prova'] = 'ok';
                $avaliacao->data = $data_prova[2] . '-' . $data_prova[1] . '-' . $data_prova[0];
                $avaliacao->hora = $request->hora_prova . ':00';
            }
        } else {
            $data['data_prova'] = 'err2';
        }

        if ($data['data_prova'] == 'ok') {

            /*
                se for 2 ou 3 chamada
            */

            if (isset($request->chamada) && $request->chamada >= 2) {
                if ($request->chamada_refer_id == 0) {
                    $data['err_chamada'] = 'err';
                } else {
                    $data['err_chamada'] = 'ok';
                }
            } else {
                $data['err_chamada'] = 'ok';
            }

            if ($data['err_chamada'] == 'ok') {
                if ($avaliacao->save()) {
                    $data['status'] = 'ok';
                    $data['disciplinaID'] = $avaliacao->disciplinas_id;
                } else {
                    $data['status'] = 'err';
                }
            }
        }
        $this->classLog->RegistrarLog('Adicionou avaliação', auth()->user()->email);
        return $data;
    }

    public function AdicionarAvaliacaoRecuperacao(Request $request)
    {
        $avaliacao = new Avaliacoes;
        $avaliacao->disciplinas_id = $request->disciplinas_id;
        $avaliacao->nome_completo = 'Avaliação de Recuperação';
        $avaliacao->nome_abrev = 'AR';
        $avaliacao->gbm = $request->gbm;
        $avaliacao->prazo_nota = $request->prazo_nota;
        $avaliacao->observacao = (empty($request->observacao)) ? '' : $request->observacao;
        $avaliacao->chamada = 0;
        $avaliacao->chamada_refer_id = $request->chamada_refer_id ?? 0;
        $avaliacao->avaliacao_recuperacao = 1;

        if (is_array(@explode('/', $request->data_prova)) && strtotime($request->hora_prova)) {
            $data_prova = explode('/', $request->data_prova);
            $data_hora_prova = $data_prova[2] . '-' . $data_prova[1] . '-' . $data_prova[0] . ' ' . $request->hora_prova . ':00';
            //if(strtotime($data_hora_prova)< (time() - $request->prazo_nota*3600*24)){
            //$data['data_prova'] = 'err';
            //} else if(strtotime($data_hora_prova) > strtotime("+ 365 days ", time())){
            if (strtotime($data_hora_prova) > strtotime("+ 365 days ", time())) {
                $data['data_prova'] = 'err1';
            } else {
                $data['data_prova'] = 'ok';
                $avaliacao->data = $data_prova[2] . '-' . $data_prova[1] . '-' . $data_prova[0];
                $avaliacao->hora = $request->hora_prova . ':00';
            }
        } else {
            $data['data_prova'] = 'err2';
        }

        if ($data['data_prova'] == 'ok') {
            if ($avaliacao->save()) {
                $data['status'] = 'ok';
                $data['disciplinaID'] = $avaliacao->disciplinas_id;
            } else {
                $data['status'] = 'err';
            }
        }
        $this->classLog->RegistrarLog('Adicionou avaliação de recuperação', auth()->user()->email);
        return $data;
    }

    function dialogEditarAvaliacao(Request $request)
    {
        $avaliacao = Avaliacoes::find($request->id);
        list($ano, $mes, $dia) = explode('-', $avaliacao->data);
        $data_prova = $dia . '/' . $mes . '/' . $ano;
        /* aqui existem 2 situações: ou é avaliações convencionais ou avaliação de recuperação */

        /*
        
            CASO SEJA AVALIAÇÃO CONVENCIONAL

        */

        if ($avaliacao->avaliacao_recuperacao == 0) {

            $data['header'] = '<i class="ion-ios-compose" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Editar avaliação';
            $data['body'] = '   <div class="alert alert-danger errors-editar-avaliacoes" role="alert"></div>
                                    <form id="editar_avaliacao">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Nome completo da avaliação">
                                            <div style="float: left;">
                                                <i class="ion-ios-compose" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="nome_completo" type="text" value="' . $avaliacao->nome_completo . '" maxlength="100" autocomplete="off" placeholder="Nome completo da avaliação" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                         
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Informe o nome abreviado da avaliação Ex: AC1">
                                            <div style="float: left;">
                                                <i class="ion-ios-compose-outline" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="nome_abrev" type="text" value="' . $avaliacao->nome_abrev . '" maxlength="50" autocomplete="off" placeholder="Nome abreviado. Ex AC1" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Data de aplicação da prova">
                                            <div style="float: left;">
                                                <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style data_mask" style="width: 100%;" name="data_prova" type="text" value="' . $data_prova . '" maxlength="10" autocomplete="off" placeholder="Data da prova" />
                                            </div>
                                            <div class="clear"></div>
                                        </div> 
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Hora que deve ser aplicada a prova">
                                            <div style="float: left;">
                                                <i class="ion-clock" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style minuto_mask" style="width: 100%;" name="hora_prova" type="text" value="' . $avaliacao->hora . '" maxlength="5" autocomplete="off" placeholder="Hora da prova" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                         
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="GBM da avaliação">
                                            <div style="float: left;">
                                                <i class="ion-android-done" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="gbm" type="text" value="' . $avaliacao->gbm . '" maxlength="4" autocomplete="off" placeholder="GBM" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Informe o prazo em dias que a UETE terá para informar o resultado obtido na avaliação">
                                            <div style="float: left;">
                                                <i class="ion-compass" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="prazo_nota" type="text" value="' . $avaliacao->prazo_nota . '" maxlength="5" autocomplete="off" placeholder="Prazo para envio de resultado (Nº dias)" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                            
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Peso da avaliação">
                                            <div style="float: left;">
                                                <i class="ion-ios-flask" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="peso" type="text" value="' . $avaliacao->peso . '" maxlength="5" autocomplete="off" placeholder="Peso" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        
                                        <div class="box-dialog-chamadas"></div>

                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left; margin-top: 6px;">
                                                <i class="ion-chatbubble-working" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <textarea class="form-control" id="observacao" name="observacao" rows="3" placeholder="Observações pertinentes a avaliação">' . $avaliacao->observacao . '</textarea>
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                        

                                    </form>';

            $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        Cancelar
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="EditarAvaliacao(' . $avaliacao->id . ');">
                                        Salvar
                                    </button>                            
                                    <script>
                                        $(\'.minuto_mask\').mask(\'00:00\');
                                        $(\'.data_mask\').mask(\'00/00/0000\');
                                        $(function () {
                                            $(\'[data-toggle="tooltip"]\').tooltip();
                                        });
                                    </script>';
            return $data;

            /*
        
            CASO SEJA AVALIAÇÃO RECUPERAÇÃO

        */
        } else {
            $data['header'] = '<i class="ion-loop" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Editar avaliação de recuperação';
            $data['body'] = '   <div class="alert alert-danger errors-editar-avaliacoes" role="alert"></div>
                                    <form id="editar_avaliacao">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <input name="nome_completo" type="hidden" value="' . $avaliacao->nome_completo . '" />
                                        <input name="nome_abrev" type="hidden" value="' . $avaliacao->nome_abrev . '" />
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style data_mask" style="width: 100%;" name="data_prova" type="text" value="' . $data_prova . '" maxlength="10" autocomplete="off" placeholder="Data da prova" />
                                            </div>
                                            <div class="clear"></div>
                                        </div> 
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-clock" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style minuto_mask" style="width: 100%;" name="hora_prova" type="text" value="' . $avaliacao->hora . '" maxlength="5" autocomplete="off" placeholder="Hora da prova" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                         
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-android-done" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="gbm" type="text" value="' . $avaliacao->gbm . '" maxlength="4" autocomplete="off" placeholder="GBM" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Informe o prazo em dias que a UETE terá para informar o resultado obtido na avaliação">
                                            <div style="float: left;">
                                                <i class="ion-compass" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="prazo_nota" type="text" value="' . $avaliacao->prazo_nota . '" maxlength="5" autocomplete="off" placeholder="Prazo para envio de resultado (Nº dias)" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                        
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left; margin-top: 6px;">
                                                <i class="ion-chatbubble-working" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <textarea class="form-control" id="observacao" name="observacao" rows="3" placeholder="Observações pertinentes a avaliação">' . $avaliacao->observacao . '</textarea>
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                        

                                    </form>';

            $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        Cancelar
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="EditarAvaliacao(' . $avaliacao->id . ');">
                                        Salvar
                                    </button>                            
                                    <script>
                                        $(\'.minuto_mask\').mask(\'00:00\');
                                        $(\'.data_mask\').mask(\'00/00/0000\');
                                        $(function () {
                                            $(\'[data-toggle="tooltip"]\').tooltip();
                                        });
                                    </script>';
            return $data;
        }
    }

    public function EditarAvaliacao(AvaliacoesRequest $request)
    {
        $id = $request->id;
        $avaliacao = Avaliacoes::find($id);
        $peso = ($request->peso == '') ? 1 : $request->peso;
        if ($avaliacao->avaliacao_recuperacao == 0) {
            $avaliacao->nome_completo = $request->nome_completo;
            $avaliacao->nome_abrev = $request->nome_abrev;
            $avaliacao->gbm = $request->gbm;
            $avaliacao->peso = $peso;
            $avaliacao->prazo_nota = $request->prazo_nota;
            $avaliacao->observacao = (empty($request->observacao)) ? '' : $request->observacao;
        } else {
            $avaliacao->nome_completo = $request->nome_completo;
            $avaliacao->nome_abrev = $request->nome_abrev;
            $avaliacao->gbm = $request->gbm;
            $avaliacao->prazo_nota = $request->prazo_nota;
            $avaliacao->observacao = (empty($request->observacao)) ? '' : $request->observacao;
        }

        if (is_array(@explode('/', $request->data_prova)) && strtotime($request->hora_prova)) {
            $data_prova = explode('/', $request->data_prova);
            $data_hora_prova = $data_prova[2] . '-' . $data_prova[1] . '-' . $data_prova[0] . ' ' . $request->hora_prova . ':00';
            //if(strtotime($data_hora_prova)<(time() - $request->prazo_nota*3600*24)){
            //$data['data_prova'] = 'err';
            //} else if(strtotime($data_hora_prova) > strtotime("+ 365 days ", time())){
            if (strtotime($data_hora_prova) > strtotime("+ 365 days ", time())) {
                $data['data_prova'] = 'err1';
            } else {
                $data['data_prova'] = 'ok';
                $avaliacao->data = $data_prova[2] . '-' . $data_prova[1] . '-' . $data_prova[0];
                $avaliacao->hora = $request->hora_prova . ':00';
            }
        } else {
            $data['data_prova'] = 'err2';
        }

        if ($data['data_prova'] == 'ok') {
            if ($avaliacao->save()) {
                $data['status'] = 'ok';
                $data['disciplinaID'] = $avaliacao->disciplinas_id;
            } else {
                $data['status'] = 'err';
            }
        }

        $this->classLog->RegistrarLog('Editou avaliação', auth()->user()->email);
        return $data;
    }

    public function RemoverAvaliacao(Request $request)
    {
        $id = $request->id;
        $avaliacoes_2chamada = Avaliacoes::where('chamada_refer_id', '=', $id)->get(['id']);
        $avaliacoes_disciplina = Avaliacoes::find($id);
        if (count($avaliacoes_2chamada) > 0) {
            $data['status'] = 'err';
        } else {
            if (Avaliacoes::destroy($id)) {
                $data['disciplinaID'] = $avaliacoes_disciplina->disciplinas_id;
                $data['status'] = 'ok';
            } else {
                $data['status'] = 'err_interno';
            }
        }
        $this->classLog->RegistrarLog('Removeu avaliação', auth()->user()->email);
        return $data;
    }

    public function DialogChamadas(Request $request)
    {
        $id = $request->id;
        $avaliacoes = Avaliacoes::where('disciplinas_id', '=', $id)->where('chamada', '=', 1)->get();
        if (count($avaliacoes) > 0) {
            $options_avaliacoes[] = '<option value="0">Selecione a avaliação de referência</option>';
            foreach ($avaliacoes as $avaliacao) {
                $options_avaliacoes[] = '<option value="' . $avaliacao->id . '">' . $avaliacao->nome_completo . '</option>';
            }
            $data['result'] = ' <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                    <div style="float: left; margin-top: 6px;">
                                        <i class="ion-ios-clock" style="font-size: 24px; color: #696969;"></i>
                                    </div>
                                    <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                        <select class="custom-select" name="chamada" onchange="showDialogReferenciaAvaliacao(this);">
                                            <option value="1">1ª Chamada</option>
                                            <option value="2">2ª Chamada</option>
                                            <!--option value="3">3ª Chamada</option-->
                                        </select>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="dialog-referencia-avaliacao" style="margin: 14px auto; width: 70%; max-width: 380px; display: none;">
                                    <div style="float: left; margin-top: 6px;">
                                        <i class="ion-ios-compose" style="font-size: 24px; color: #696969;"></i>
                                    </div>
                                    <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                        <select class="custom-select" name="chamada_refer_id">' . implode('', $options_avaliacoes) . '</select>
                                    </div>
                                    <div class="clear"></div>
                                </div>';
        } else {

            $data['result'] = ' <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                    <div style="float: left; margin-top: 6px;">
                                        <i class="ion-ios-clock" style="font-size: 24px; color: #696969;"></i>
                                    </div>
                                    <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                        <select class="custom-select" name="chamada" onchange="showDialogReferenciaAvaliacao(this);">
                                            <option value="1">1ª Chamada</option>
                                        </select>
                                    </div>
                                    <div class="clear"></div>
                                </div>';
        }

        return $data;
    }

    public function DialogPeriodoLancamentoTAF()
    {

        $confTaf = ConfLancaTaf::first();

        $data_inicio = strftime('%d/%m/%Y', strtotime($confTaf->inicio));
        $hora_inicio = strftime('%H:%M', strtotime($confTaf->inicio));
        $data_fim = strftime('%d/%m/%Y', strtotime($confTaf->termino));
        $hora_fim = strftime('%H:%M', strtotime($confTaf->termino));

        $data['status'] = 'ok';
        $data['header'] = '<i class="ion-android-walk" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Configurar período de lançamento do TFM';
        $data['body'] = '   <div class="alert alert-danger errors-adicionar-disciplinas" role="alert"></div>
                                    <form id="editar_periodo_lanca_taf">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style data_mask" style="width: 100%;" name="data_inicio" type="text" value="' . $data_inicio . '" maxlength="10" autocomplete="off" placeholder="Data de início (DD/MM/AAAA)" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-time" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style minuto_mask" style="width: 100%;" name="hora_inicio" type="text" value="' . $hora_inicio . '" maxlength="5" autocomplete="off" placeholder="Hora início (HH:MM)" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style data_mask" style="width: 100%;" name="data_fim" type="text" value="' . $data_fim . '" maxlength="10" autocomplete="off" placeholder="Data fim (DD/MM/AAAA)" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-time" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style minuto_mask" style="width: 100%;" name="hora_fim" type="text" value="' . $hora_fim . '" maxlength="5" autocomplete="off" placeholder="Hora fim (HH:MM)" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                                                                  
                                    </form>';

        $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="AtualizarPeriodoLancamentoTAF();">
                                Atualizar
                            </button>
                            <script>
                                $(\'.minuto_mask\').mask(\'00:00\');
                                $(\'.data_mask\').mask(\'00/00/0000\');
                            </script>                            ';
        return $data;
    }

    public function AtualizarPeriodoLancamentoTAF(Request $request)
    {
        $confTaf = ConfLancaTaf::first();
        @list($dia, $mes, $ano) = @explode('/', $request->data_inicio);
        $data_hora_inicio = $ano . '-' . $mes . '-' . $dia . ' ' . $request->hora_inicio . ':00';
        @list($dia, $mes, $ano) = @explode('/', $request->data_fim);
        $data_hora_fim = $ano . '-' . $mes . '-' . $dia . ' ' . $request->hora_fim . ':00';
        $confTaf->inicio = $data_hora_inicio;
        $confTaf->termino = $data_hora_fim;
        if ($confTaf->save()) {
            $data['status'] = 'ok';
        } else {
            $data['status'] = 'err';
        }
        $this->classLog->RegistrarLog('Atualizou período de lançamento do TFM', auth()->user()->email);
        return $data;
    }


    public function DialogAdicionarDisciplina()
    {

        $data['header'] = '<i class="ion-ios-bookmarks" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Adicionar disciplina ao sistema';
        $data['body'] = '   <div class="alert alert-danger errors-adicionar-disciplinas" role="alert"></div>
                                    <form id="adicionar_disciplina">
                                        <input type="hidden" name="ano_formacao_id" value="' . AnoFormacao::orderBy('formacao', 'desc')->first()->id . '">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-compose" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="nome_disciplina" type="text" value="" maxlength="100" autocomplete="off" placeholder="Nome completo da disciplina" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                        
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-compose-outline" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="nome_disciplina_abrev" type="text" value="" maxlength="50" autocomplete="off" placeholder="Nome abreviado" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-flask" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="peso" type="text" value="" maxlength="5" autocomplete="off" placeholder="Peso" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                                                                
                                    </form>';

        $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="AdicionarDisciplina();">
                                Salvar
                            </button>';
        return $data;
    }

    public function DialogImportarDisciplina()
    {

        $ano_formacao_max = AnoFormacao::orderBy('formacao', 'desc')->first();
        $anos_formacao = AnoFormacao::where('id', '<>', $ano_formacao_max->id)->get();
        $disciplinas = Disciplinas::where('ano_formacao_id', '<>', $ano_formacao_max->id)->get();

        $i = 0;

        foreach ($anos_formacao as $ano) {
            $carousel_active_class = ($i == 0) ? 'active' : '';
            $slide[$ano->id][] = '  <div class="carousel-item ' . $carousel_active_class . '">
                                        <div style="width: 80%; margin: 0 auto;">
                                            <h4 style="text-align: center;">Disciplinas de ' . $ano->formacao . '</h4><br />';
            foreach ($disciplinas as $disciplina) {
                if ($ano->id == $disciplina->ano_formacao_id) {
                    $slide[$ano->id][] = '  <div class="custom-control custom-checkbox" style="width: 36%; margin: 4px auto;">
                                                <input type="checkbox" class="custom-control-input" id="' . $disciplina->id . '" name="disciplinas[]" value="' . $disciplina->id . '" />
                                                <label class="custom-control-label" for="' . $disciplina->id . '">' . $disciplina->nome_disciplina . '</label>
                                            </div>';
                }
            }
            $slide[$ano->id][] = '      </div>
                                    </div><br />';

            $i++;
        }

        if (isset($slide)) {
            foreach ($slide as $return_slide_array) {
                foreach ($return_slide_array as $return_slide) {
                    $slides[] = $return_slide;
                }
            }
        }

        if ($i == 0) {
            $slides[] = '<div style="text-align: center;">Não há disciplinas a importar</div>';
        }

        if ($i > 1) {
            $button = ' <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                            <i class="ion-chevron-left" style="color: #696969;"></i>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                            <i class="ion-chevron-right" style="color: #696969;"></i>
                            <span class="sr-only">Next</span>
                        </a>';
        } else {
            $button = '';
        }

        $data['header'] = '<i class="ion-android-arrow-down" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Importar disciplinas';
        $data['body'] = '   <div class="alert alert-danger errors-importar-disciplinas" role="alert"></div>
                                    <form id="importar_disciplinas">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <div id="carouselExampleControls" class="carousel slide">
                                            <div class="carousel-inner">
                                                ' . implode('', $slides) . '
                                            </div>
                                            ' . $button . '
                                        </div>
                                    </form>';

        if ($i > 0) {
            $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Cancelar
                                </button>
                                <button type="button" class="btn btn-primary" onclick="ImportarDisciplinas();">
                                    Importar seleção
                                </button>';
        } else {
            $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Fechar
                                </button>';
        }
        return $data;
    }

    public function ImportarDisciplinas(Request $request)
    {
        $ano_formacao_max = AnoFormacao::orderBy('formacao', 'desc')->first();
        if (isset($request->disciplinas) && is_array($request->disciplinas)) {
            foreach ($request->disciplinas as $disciplina_id) {
                // PEGANDO OS DADOS DA DISCIPLINA NA VEZ DO LOOP
                $disciplinas_selecionadas[] = $disciplina_id;
            }

            $disciplinas = Disciplinas::find($disciplinas_selecionadas);
            foreach ($disciplinas as $disciplinas_insert) {
                $disciplina = new Disciplinas;
                $disciplina->nome_disciplina = $disciplinas_insert->nome_disciplina;
                $disciplina->nome_disciplina_abrev = $disciplinas_insert->nome_disciplina_abrev;
                $disciplina->ano_formacao_id = $ano_formacao_max->id;
                $disciplina->save();
            }

            $data['status'] = 'ok';
            $this->classLog->RegistrarLog('Importou disciplinas', auth()->user()->email);
            return $data;
        }
    }

    public function DialogEditarDisciplina(Request $request)
    {

        $disciplina = Disciplinas::find($request->id);

        $data['header'] = '<i class="ion-android-create" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Editar disciplina';
        $data['body'] = '   <div class="alert alert-danger errors-adicionar-disciplinas" role="alert"></div>
                                    <form id="editar_disciplina">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-compose" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="nome_disciplina" type="text" value="' . $disciplina->nome_disciplina . '" maxlength="100" autocomplete="off" placeholder="Nome completo da disciplina" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                        
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-compose-outline" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="nome_disciplina_abrev" type="text" value="' . $disciplina->nome_disciplina_abrev . '" maxlength="50" autocomplete="off" placeholder="Nome abreviado" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-flask" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="peso" type="text" value="' . $disciplina->peso . '" maxlength="5" autocomplete="off" placeholder="Peso" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                                                                
                                    </form>';

        $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="AtualizarDisciplina(' . $request->id . ');">
                                Salvar
                            </button>';
        return $data;
    }

    public function AdicionarDisciplina(DisciplinasRequest $request)
    {
        $peso = ($request->peso == '') ? 1 : $request->peso;
        $disciplina = new Disciplinas;
        $disciplina->nome_disciplina = $request->nome_disciplina;
        $disciplina->nome_disciplina_abrev = $request->nome_disciplina_abrev;
        $disciplina->ano_formacao_id = $request->ano_formacao_id;
        $disciplina->peso = $peso;

        if ($disciplina->save()) {
            $data['status'] = 'ok';
        } else {
            $data['status'] = 'err';
        }
        $this->classLog->RegistrarLog('Adicionou disciplina', auth()->user()->email);
        return $data;
    }

    public function AtualizarDisciplina(DisciplinasRequest $request)
    {
        $peso = ($request->peso == '') ? 1 : $request->peso;
        $disciplina = Disciplinas::find($request->id);
        $disciplina->nome_disciplina = $request->nome_disciplina;
        $disciplina->nome_disciplina_abrev = $request->nome_disciplina_abrev;
        $disciplina->peso = $peso;

        if ($disciplina->save()) {
            $data['status'] = 'ok';
        } else {
            $data['status'] = 'err';
        }
        $this->classLog->RegistrarLog('Atualizou disciplina', auth()->user()->email);
        return $data;
    }

    public function DialogEditarOperador(Request $request)
    {

        $operador = Operadores::find($request->route('id'));
        $user = User::where('email', '=', $operador->email)->first();

        foreach (explode(',', $operador->id_funcao_operador) as $funcao) {
            $funcao_operador[] = $funcao;
        }

        $operadores_tipo = OperadoresTipo::get();
        foreach ($operadores_tipo as $tipo) {
            $attr_checked = (in_array($tipo->id, $funcao_operador)) ? 'checked' : '';
            $tipos[] = '<div class="custom-control custom-checkbox">
                            <input type="checkbox" value="' . $tipo->id . '" name="tipo_operador_check[]" class="custom-control-input" id="tipo_operador_check_' . $tipo->id . '" ' . $attr_checked . '>
                            <label class="custom-control-label" for="tipo_operador_check_' . $tipo->id . '">' . $tipo->funcao . '</label>
                        </div>';
        }

        $omcts = OMCT::get();
        foreach ($omcts as $omct) {
            $attr_selected = ($operador->omcts_id == $omct->id) ? 'selected' : '';
            $options_omcts[] = '<option value="' . $omct->id . '" ' . $attr_selected . '>' . $omct->omct . '</option>';
        }

        $postograds = PostoGrad::get();
        foreach ($postograds as $postograd) {
            $attr_selected = ($operador->postograd_id == $postograd->id) ? 'selected' : '';
            $options_postograd[] = '<option value="' . $postograd->id . '" ' . $attr_selected . '>' . $postograd->postograd . '</option>';
        }

        $style_display = ($user->imagens_id != 1 && $user->imagens_id != null) ? 'display: block;' : 'display: none;';

        $user_img_id = ($user->imagens_id == null) ? 1 : $user->imagens_id;

        $imagem_id = Imagens::find($user_img_id);


        $data['header'] = '<i class="ion-android-create" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Editar operador';
        $data['body'] = '   <div class="alert alert-danger errors-cadastro-operador erro-upload" role="alert"></div>                
                                    <form id="img_perfil" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <input type="file" name="imagem" onchange="submitForm(\'img_perfil\', \'/ajax/upload/img-perfil/operador/' . $request->id . '\');" style="display: none;" />
                                    </form>            
                                    <form id="atualiza_dados_operador">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <div style="display: grid; grid-template-columns: 0.8fr 2.2fr; margin: 0 auto; width: 80%; max-width: 380px; border-bottom: 1px solid #ccc; padding: 0 0 20px 6px;">
                                            <div>
                                                <div class="imagem_perfil" style="background: url(\'' . $imagem_id->imagem . '\') no-repeat center center; background-size: cover;">
                                                    <div>
                                                        <a class="no-style open_input_file" href="javascript: void(0);">
                                                            <span style="color: #696969;"><i class="ion-ios-camera"></i></span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div padding: 6px;">
                                                <p><input class="line-bottom" type="text" name="nome" value="' . $operador->nome . '" autocomplete="off" style="width: 100%;" placeholder="Nome completo" /></p>
                                                <p><input class="line-bottom" type="text" name="nome_guerra" value="' . $operador->nome_guerra . '" autocomplete="off" style="width: 100%;" placeholder="Nome de guerra" /></p>
                                            </div>
 
                                        </div>                                           

                                        <div id="box-dialog-remover-imagem-perfil" style="font-size: 12px; left: 60%; margin-top: -24px; position: absolute; ' . $style_display . '">
                                            <a href="javascript: void(0);" class="no-style" onclick="removerImgPerfil(' . $user->id . ');">
                                                <i class="ion-android-delete" style="font-size: 16px;"></i>
                                                    Remover imagem                                               
                                            </a>
                                        </div>
                                        
                                        <div style="margin: 24px auto; width: 80%; max-width: 380px;">
                                            <div class="progress" style="margin-top: 36px; display: none;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        
                                        <div style="margin: 24px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left; margin-top: 3px;">
                                                <i class="ion-ios-pricetag" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float:right; width: 93%;">
                                            <select class="custom-select" name="postograd_id">' . implode('', $options_postograd) . '</select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                     
                                        <div style="margin: 18px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left; margin-top: 3px;">
                                                <i class="ion-ios-home" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float:right; width: 93%;">
                                                <select class="custom-select" name="omcts_id">' . implode('', $options_omcts) . '</select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-key" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="resetar_senha" class="custom-control-input" id="customCheck1">
                                                    <label class="custom-control-label" for="customCheck1">Resetar senha</label><br />
                                                    <span style="font-size: 12px; color: #FE642E;">Marcando a opção acima, a senha será alterada para o prefixo do email até o arroba</span>
                                                </div>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-bookmark" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" name="idt_militar" value="' . $operador->idt_militar . '" autocomplete="off" placeholder="Identidade Militar" style="width: 100%;" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-bookmark" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" name="idt_militar_o_exp" value="' . $operador->idt_militar_o_exp . '" autocomplete="off" placeholder="Org Expd (Idt Mil)" style="width: 100%;" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-telephone" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style telefone_mask" name="tel_pronto_atendimento" type="text" pattern="[0-9]{11}" value="' . $operador->tel_pronto_atendimento . '" maxlength="14" autocomplete="off" placeholder="Telefone com DDD" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-email" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" name="email" type="text" value="' . $operador->email . '" maxlength="60" autocomplete="off" placeholder="Email" style="width: 100%;" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                          
                                        
                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                        <div style="float: left;">
                                            <i class="ion-locked" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                            <div style="margin: 2px 0 16px 0;">Tipo de conta do operador</div>
                                            ' . implode('<br />', $tipos) . '
                                        </div>
                                        <div class="clear"></div>
                                        </div>
                                        <script>
                                            $(document).ready(function() {
                                                $(\'.telefone_mask\').mask(\'(00)00000-0000\');
                                            });
                                        </script>
                                    </form>';

        $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="AtualizarOperador(' . $request->id . ');">
                                Salvar
                            </button>';
        return $data;
    }

    public function DialogEditarMeuPerfil(Request $request)
    {

        $user = User::find(auth()->id());
        $operador = Operadores::where('email', '=', $user->email)->first();

        $postograds = PostoGrad::get();
        foreach ($postograds as $postograd) {
            $attr_selected = ($operador->postograd_id == $postograd->id) ? 'selected' : '';
            $options_postograd[] = '<option value="' . $postograd->id . '" ' . $attr_selected . '>' . $postograd->postograd . '</option>';
        }

        if (!$imagem_id = Imagens::find($user->imagens_id)) {
            $imagem_id = Imagens::find(1);
        }

        $style_display = ($user->imagens_id != 1 && $user->imagens_id != null) ? 'display: block;' : 'display: none;';
        //$style_display = ($user->imagens_id!=1)?'display: block;':'display: none;';

        $data['header'] = '<i class="ion-android-create" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Editar dados do perfil';
        $data['body'] = '   <div class="alert alert-danger errors-meu-cadastro erro-upload" role="alert"></div>              
                                    
                                    <form id="img_perfil" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <input type="file" name="imagem" onchange="submitForm(\'img_perfil\', \'/ajax/upload/img-perfil/minha-imagem/' . auth()->id() . '\');" style="display: none;" />
                                    </form>

                                    <form id="atualizar_meu_perfil">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <div style="margin: 18px auto; width: 80%; max-width: 380px; padding: 0 6px 12px 6px;">
                                            <div style="width: 100px; margin: 0 auto;">
                                                <div class="imagem_perfil" style="background: url(\'' . $imagem_id->imagem . '\') no-repeat center center; background-size: cover;">
                                                    <div>
                                                        <a class="no-style open_input_file" href="javascript: void(0);">
                                                            <span style="color: #696969;"><i class="ion-ios-camera"></i></span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div id="box-dialog-remover-imagem-perfil" style="font-size: 12px; left: 64%; position: absolute; ' . $style_display . '">
                                                    <a href="javascript: void(0);" class="no-style" onclick="removerImgPerfil(' . auth()->id() . ');">
                                                        <i class="ion-android-delete" style="font-size: 16px;"></i>
                                                            Remover imagem                                               
                                                    </a>
                                                </div>
                                            </div>
  
                                            <div class="progress" style="margin-top: 36px; display: none;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <div style="margin: 24px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left; margin-top: 3px;">
                                                <i class="ion-ios-pricetag" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float:right; width: 93%;">
                                            <select class="custom-select" name="postograd_id">' . implode('', $options_postograd) . '</select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-telephone" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style telefone_mask" name="tel_pronto_atendimento" type="text" pattern="[0-9]{11}" value="' . $operador->tel_pronto_atendimento . '" maxlength="11" autocomplete="off" placeholder="Telefone com DDD" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                            <div class="custom-control custom-checkbox" style="margin-top: 20px;">
                                                <input id="customCheck1" name="alterar_senha" type="checkbox" value="0" class="custom-control-input" onclick="toogleContent2(this, \'div#box-alterar-minha-senha\');" />
                                                <label class="custom-control-label" for="customCheck1">Alterar senha</label>
                                            </div>
                                        </div>
                                        <div id="box-alterar-minha-senha" style="display: none;">
                                            <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                                <div style="float: left;">
                                                    <i class="ion-key" style="font-size: 24px; color: #696969;"></i>
                                                </div>
                                                <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                    <input class="no-style" name="senha" type="password" value="" maxlength="30" autocomplete="off" placeholder="Senha" />
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                                <div style="float: left;">
                                                    <i class="ion-key" style="font-size: 24px; color: #696969;"></i>
                                                </div>
                                                <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                    <input class="no-style" name="confirma_senha" type="password" value="" maxlength="30" autocomplete="off" placeholder="Confirmação de senha" />
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                        <script>
                                            $(document).ready(function() {
                                                $(\'.telefone_mask\').mask(\'(00)00000-0000\');
                                            });
                                        </script>                                                                                
                                    </form>';

        $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="AtualizarMeuPerfil();">
                                Salvar
                            </button>';
        return $data;
    }

    public function AtualizarMeuPerfil(AtualizaMeuPerfilRequest $request)
    {

        $user = User::find(auth()->id());
        $operador = Operadores::where('email', '=', $user->email)->first();
        $operador->tel_pronto_atendimento = $request->tel_pronto_atendimento;
        $operador->postograd_id = $request->postograd_id;

        //$postoGrad_atualizado = $operador->postograd->postograd_abrev;

        if ($request->senha == $request->confirma_senha && $request->senha != '') {
            $status = OwnValidator::ValidarPW($request->senha);

            if ($status != 'ok') {
                $data['status'] = 'err';
            } else {
                if (isset($request->alterar_senha)) {
                    $user->password = bcrypt($request->senha);
                }
                if ($operador->save() && $user->save()) {
                    $data['status'] = 'ok';
                    $data['postoGrad'] = $operador->postograd->postograd_abrev;
                } else {
                    $data['status'] = 'err1';
                }
            }
        } else {
            if ($operador->save()) {
                $data['status'] = 'ok';
                $data['postoGrad'] = $operador->postograd->postograd_abrev;
            } else {
                $data['status'] = 'err1';
            }
        }
        $this->classLog->RegistrarLog('Atualizou o próprio perfil', auth()->user()->email);

        return $data;
    }

    public function DialogAdicionarOperador(Request $request)
    {


        $operadores_tipo = OperadoresTipo::get();
        foreach ($operadores_tipo as $tipo) {
            $tipos[] = '<div class="custom-control custom-checkbox">
                            <input type="checkbox" value="' . $tipo->id . '" name="tipo_operador_check[]" class="custom-control-input" id="tipo_operador_check_' . $tipo->id . '" />
                            <label class="custom-control-label" for="tipo_operador_check_' . $tipo->id . '">' . $tipo->funcao . '</label>
                        </div>';
        }

        /* LOOP QUE BUSCA AS OMCTS */

        $options_omcts[] = '<option value="0">Informe a UETE</option>';
        $omcts = OMCT::get();
        foreach ($omcts as $omct) {
            $options_omcts[] = '<option value="' . $omct->id . '">' . $omct->omct . '</option>';
        }

        /* LOOP QUE BUSCA O POSTO E GRADUAÇÕES */

        $options_postograd[] = '<option value="0">Informe o posto ou graduação</option>';
        $postograds = PostoGrad::get();
        foreach ($postograds as $postograd) {
            $options_postograd[] = '<option value="' . $postograd->id . '">' . $postograd->postograd . '</option>';
        }

        $data['header'] = '<i class="ion-android-person-add" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Adicionar operador';
        $data['body'] = '   <div class="alert alert-danger errors-cadastro-operador" role="alert"></div>
                                    <form id="adicionar_dados_operador">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-android-person" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" name="nome" type="text" autocomplete="off" placeholder="Nome completo" style="width: 100%;" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-android-contact" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" name="nome_guerra" autocomplete="off" placeholder="Nome de Guerra" style="width: 100%;" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>  
                                        
                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-bookmark" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 15px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" name="idt_militar" autocomplete="off" placeholder="Identidade Militar" style="width: 100%;" />
                                            </div>
                                            <div class="clear"></div>
                                        </div> 

                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-bookmark" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 15px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" name="idt_militar_o_exp" autocomplete="off" placeholder="Org Expd (Idt Mil)" style="width: 100%;" />
                                            </div>
                                            <div class="clear"></div>
                                        </div> 

                                        <div style="margin: 24px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left; margin-top: 3px;">
                                                <i class="ion-ios-pricetag" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float:right; width: 93%;">
                                            <select class="custom-select" name="postograd_id">' . implode('', $options_postograd) . '</select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div style="margin: 18px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left; margin-top: 3px;">
                                                <i class="ion-ios-home" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float:right; width: 93%;">
                                                <select class="custom-select" name="omcts_id">' . implode('', $options_omcts) . '</select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-telephone" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style telefone_mask" name="tel_pronto_atendimento" type="text" pattern="[0-9]{11}" maxlength="16" autocomplete="off" placeholder="Telefone com DDD" style="width: 100%;" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-email" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" name="email" type="text" maxlength="60" autocomplete="off" placeholder="Email" style="width: 100%;" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                        
                                        
                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                        <div style="float: left;">
                                            <i class="ion-locked" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                            <div style="margin: 2px 0 16px 0;">Tipo de conta do operador</div>
                                            ' . implode('<br />', $tipos) . '
                                        </div>
                                        <div class="clear"></div>
                                        </div>
                                        <script>
                                            $(document).ready(function() {
                                                $(\'.telefone_mask\').mask(\'(00)00000-0000\');
                                            });
                                        </script>
                                    </form>';

        $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="AdicionarOperador();">
                                Salvar
                            </button>';
        return $data;
    }

    public function DialogAdicionarPortaria()
    {

        $data['header'] = '<i class="ion-arrow-graph-up-right" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Adicionar portaria';
        $data['body'] = '   <div class="alert alert-danger errors-adicionar-portaria" role="alert"></div>
                                    <form id="adicionar_portaria">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-compose" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="nome_portaria" type="text" value="" maxlength="100" autocomplete="off" placeholder="Nome completo da portaria" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                                                            
                                    </form>';

        $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="AdicionarPortaria();">
                                Salvar
                            </button>';
        return $data;
    }

    public function DialogEditarNomePortaria(Request $request)
    {
        $portaria = TafPortarias::find($request->id);
        $data['header'] = '<i class="ion-android-create" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Renomear portaria';
        $data['body'] = '   <div class="alert alert-danger errors-adicionar-portaria" role="alert"></div>
                                    <form id="renomear_portaria">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-compose" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" style="width: 100%;" name="nome_portaria" type="text" value="' . $portaria->nome_portaria . '" maxlength="100" autocomplete="off" placeholder="Nome completo da portaria" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                                                            
                                    </form>';

        $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="renomearPortaria(' . $request->id . ');">
                                Salvar
                            </button>';
        return $data;
    }

    public function RenomearPortaria(Request $request)
    {
        $portaria = TafPortarias::find($request->id);
        $portaria->nome_portaria = $request->nome_portaria;
        if (strlen($request->nome_portaria) < 3) {
            $data['status'] = 'err1';
        } else {
            //if($portaria->update(['nome_portaria'=>$request->nome_portaria])){
            if ($portaria->save()) {
                $data['status'] = 'ok';
                $data['contentOption'] = $portaria->nome_portaria;
            } else {
                $data['status'] = 'err';
            }
        }

        return $data;
    }

    public function IndicesTafAtletas()
    {
        $portarias = TafPortarias::get();
        return view('ajax.indices-taf-atletas')->with('portarias', $portarias);
    }

    public function AdicionarPortaria(Request $request)
    {
        $portaria = new TafPortarias;
        $portaria_conf = new TafConfiguracoes;
        //$portaria_conf->suficiencia_corrida = 0; 
        //$portaria_conf->suficiencia_flexao_braco = 0; 
        //$portaria_conf->suficiencia_flexao_barra = 0; 
        //$portaria_conf->suficiencia_abdominal = 0; 
        if (strlen($request->nome_portaria) < 3) {
            $data['status'] = 'err1';
        } else {
            $portaria->nome_portaria = $request->nome_portaria;
            if ($portaria->save()) {
                $portaria_conf->taf_portarias_id = $portaria->id;
                if ($portaria_conf->save()) {
                    $data['status'] = 'ok';
                } else {
                    $data['status'] = 'err';
                }
            } else {
                $data['status'] = 'err';
            }
        }

        return $data;
    }

    public function LoadContentPortaria(Request $request)
    {

        /* CORRIDA */

        $corrida = TafCorrida::where('taf_portarias_id', '=', $request->id)->orderBy('tempo_inicial', 'asc')->get();
        $data_corrida[] = '<form id="data-insert-indices-corrida">
                            <table class="table table-hover indices-corrida">
                                <thead>
                                    <tr>
                                        <th scope="col">TEMPO INICIAL</th>
                                        <th scope="col">TEMPO FINAL</th>
                                        <th scope="col">NOTA</th>
                                        <th scope="col">AÇÃO</th>
                                    </tr>
                                </thead>
                                <tbody>';

        $data_corrida[] = '         <input type="hidden" name="taf_portarias_id" value="' . $request->id . '" />
                                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                                    <td>
                                        <div style="float: left;">
                                            <i class="ion-ios-time-outline" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 90%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                            <input class="no-style minuto_mask" style="width: 100%;" name="tempo_inicial" type="text" value="" maxlength="5" autocomplete="off" placeholder="Informe o tempo inicial" />
                                        </div>
                                        <div class="clear"></div>
                                    </td>
                                    <td>
                                        <div style="float: left;">
                                            <i class="ion-ios-time" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 90%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                            <input class="no-style minuto_mask" style="width: 100%;" name="tempo_final" type="text" value="" maxlength="5" autocomplete="off" placeholder="Informe o tempo final" />
                                        </div>
                                        <div class="clear"></div>
                                    </td>
                                    <td>
                                        <div style="float: left;">
                                            <i class="ion-ribbon-a" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 90%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                            <input class="no-style" style="width: 100%;" name="grau" type="text" value="" maxlength="5" autocomplete="off" placeholder="Informe o grau" />
                                        </div>
                                        <div class="clear"></div>
                                    </td>
                                        <td class="botao-action-table">
                                        <button type="button" class="btn btn-success" onclick="incluirIndiceCorrida();">Incluir</button>
                                    </td>';

        foreach ($corrida as $c) {
            $data_corrida[] = ' <tr  id="tr_indice_corrida_' . $c->id . '">
                                        <td>' . strftime('%H:%M', strtotime($c->tempo_inicial)) . '</td>
                                        <td>' . strftime('%H:%M', strtotime($c->tempo_final)) . '</td>
                                        <td>' . $c->grau . '</td>
                                        <td class="botao-action-table">
                                            <a href="javascript: void(0);" class="no-style" onclick="dialogRemoverIndiceCorrida(' . $c->id . ');" title="Remover este índice"><i class="ion-android-delete"></i></a>
                                        </td>
                                    </tr>';
        }
        $data_corrida[] = '         </tbody>
                                    </table>
                                </form>';

        /* FLEXÃO DE BRAÇO */

        $flexbra = TafFlexaoBraco::where('taf_portarias_id', '=', $request->id)->orderBy('valor_inicial', 'asc')->get();
        $data_flexbra[] = '<form id="data-insert-indices-flexbra">
                            <table class="table table-hover indices-flexao-braco">
                                <thead>
                                    <tr>
                                        <th scope="col">VALOR INICIAL</th>
                                        <th scope="col">VALOR FINAL FINAL</th>
                                        <th scope="col">NOTA</th>
                                        <th scope="col">AÇÃO</th>
                                    </tr>
                                </thead>
                                <tbody>';

        $data_flexbra[] = '         <input type="hidden" name="taf_portarias_id" value="' . $request->id . '" />
                                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                                    <td>
                                        <div style="float: left;">
                                            <i class="ion-stats-bars" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 90%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                            <input class="no-style" style="width: 100%;" name="valor_inicial" type="text" value="" maxlength="5" autocomplete="off" placeholder="Informe o valor inicial" />
                                        </div>
                                        <div class="clear"></div>
                                    </td>
                                    <td>
                                        <div style="float: left;">
                                            <i class="ion-connection-bars" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 90%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                            <input class="no-style" style="width: 100%;" name="valor_final" type="text" value="" maxlength="5" autocomplete="off" placeholder="Informe o valor final" />
                                        </div>
                                        <div class="clear"></div>
                                    </td>
                                    <td>
                                        <div style="float: left;">
                                            <i class="ion-ribbon-a" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 90%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                            <input class="no-style" style="width: 100%;" name="grau" type="text" value="" maxlength="5" autocomplete="off" placeholder="Informe o grau" />
                                        </div>
                                        <div class="clear"></div>
                                    </td>
                                        <td class="botao-action-table">
                                        <button type="button" class="btn btn-success" onclick="incluirIndiceFlexBra();">Incluir</button>
                                    </td>';

        foreach ($flexbra as $c) {
            $data_flexbra[] = ' <tr  id="tr_indice_flexbra_' . $c->id . '">
                                        <td>' . $c->valor_inicial . '</td>
                                        <td>' . $c->valor_final . '</td>
                                        <td>' . $c->grau . '</td>
                                        <td class="botao-action-table">
                                            <a href="javascript: void(0);" class="no-style" onclick="dialogRemoverIndiceFlexBra(' . $c->id . ');" title="Remover este índice"><i class="ion-android-delete"></i></a>
                                        </td>
                                    </tr>';
        }
        $data_flexbra[] = '         </tbody>
                                    </table>
                                </form>';

        /* FLEXÃO NA BARRA */

        $flexbar = TafFlexaoBarra::where('taf_portarias_id', '=', $request->id)->orderBy('valor_inicial', 'asc')->get();
        $data_flexbar[] = '<form id="data-insert-indices-flexbar">
                            <table class="table table-hover indices-flexao-barra">
                                <thead>
                                    <tr>
                                        <th scope="col">VALOR INICIAL</th>
                                        <th scope="col">VALOR FINAL FINAL</th>
                                        <th scope="col">NOTA</th>
                                        <th scope="col">AÇÃO</th>
                                    </tr>
                                </thead>
                                <tbody>';

        $data_flexbar[] = '         <input type="hidden" name="taf_portarias_id" value="' . $request->id . '" />
                                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                                    <td>
                                        <div style="float: left;">
                                            <i class="ion-stats-bars" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 90%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                            <input class="no-style" style="width: 100%;" name="valor_inicial" type="text" value="" maxlength="5" autocomplete="off" placeholder="Informe o valor inicial" />
                                        </div>
                                        <div class="clear"></div>
                                    </td>
                                    <td>
                                        <div style="float: left;">
                                            <i class="ion-connection-bars" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 90%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                            <input class="no-style" style="width: 100%;" name="valor_final" type="text" value="" maxlength="5" autocomplete="off" placeholder="Informe o valor final" />
                                        </div>
                                        <div class="clear"></div>
                                    </td>
                                    <td>
                                        <div style="float: left;">
                                            <i class="ion-ribbon-a" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 90%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                            <input class="no-style" style="width: 100%;" name="grau" type="text" value="" maxlength="5" autocomplete="off" placeholder="Informe o grau" />
                                        </div>
                                        <div class="clear"></div>
                                    </td>
                                        <td class="botao-action-table">
                                        <button type="button" class="btn btn-success" onclick="incluirIndiceFlexBar();">Incluir</button>
                                    </td>';

        foreach ($flexbar as $c) {
            $data_flexbar[] = ' <tr  id="tr_indice_flexbar_' . $c->id . '">
                                        <td>' . $c->valor_inicial . '</td>
                                        <td>' . $c->valor_final . '</td>
                                        <td>' . $c->grau . '</td>
                                        <td class="botao-action-table">
                                            <a href="javascript: void(0);" class="no-style" onclick="dialogRemoverIndiceFlexBar(' . $c->id . ');" title="Remover este índice"><i class="ion-android-delete"></i></a>
                                        </td>
                                    </tr>';
        }
        $data_flexbar[] = '         </tbody>
                                    </table>
                                </form>';


        /* ABDOMINAL */

        $abdominal = TafAbdominal::where('taf_portarias_id', '=', $request->id)->orderBy('valor_inicial', 'asc')->get();
        $data_abdomin[] = '<form id="data-insert-indices-abdomin">
                            <table class="table table-hover indices-abdominal">
                                <thead>
                                    <tr>
                                        <th scope="col">VALOR INICIAL</th>
                                        <th scope="col">VALOR FINAL FINAL</th>
                                        <th scope="col">NOTA</th>
                                        <th scope="col">AÇÃO</th>
                                    </tr>
                                </thead>
                                <tbody>';

        $data_abdomin[] = '         <input type="hidden" name="taf_portarias_id" value="' . $request->id . '" />
                                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                                    <td>
                                        <div style="float: left;">
                                            <i class="ion-stats-bars" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 90%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                            <input class="no-style" style="width: 100%;" name="valor_inicial" type="text" value="" maxlength="5" autocomplete="off" placeholder="Informe o valor inicial" />
                                        </div>
                                        <div class="clear"></div>
                                    </td>
                                    <td>
                                        <div style="float: left;">
                                            <i class="ion-connection-bars" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 90%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                            <input class="no-style" style="width: 100%;" name="valor_final" type="text" value="" maxlength="5" autocomplete="off" placeholder="Informe o valor final" />
                                        </div>
                                        <div class="clear"></div>
                                    </td>
                                    <td>
                                        <div style="float: left;">
                                            <i class="ion-ribbon-a" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 90%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                            <input class="no-style" style="width: 100%;" name="grau" type="text" value="" maxlength="5" autocomplete="off" placeholder="Informe o grau" />
                                        </div>
                                        <div class="clear"></div>
                                    </td>
                                        <td class="botao-action-table">
                                        <button type="button" class="btn btn-success" onclick="incluirIndiceAbdominal();">Incluir</button>
                                    </td>';

        foreach ($abdominal as $c) {
            $data_abdomin[] = ' <tr  id="tr_indice_abdominal_' . $c->id . '">
                                        <td>' . $c->valor_inicial . '</td>
                                        <td>' . $c->valor_final . '</td>
                                        <td>' . $c->grau . '</td>
                                        <td class="botao-action-table">
                                            <a href="javascript: void(0);" class="no-style" onclick="dialogRemoverIndiceAbdominal(' . $c->id . ');" title="Remover este índice"><i class="ion-android-delete"></i></a>
                                        </td>
                                    </tr>';
        }
        $data_abdomin[] = '         </tbody>
                                    </table>
                                </form>';

        /* BONUS PARA ATLETAS */

        $bonus_atletas = TafBonusAtletas::where('taf_portarias_id', '=', $request->id)->orderBy('valor_min', 'asc')->get();
        $data_bonatle[] = '<form id="data-insert-bonus-atletas">
                            <table class="table table-hover indices-bonus-atletas">
                                <thead>
                                    <tr>
                                        <th scope="col">NOTA MÍNIMA</th>
                                        <th scope="col">NOTA MÁXIMA</th>
                                        <th scope="col">PONTUAÇÃO EXTRA</th>
                                        <th scope="col">AÇÃO</th>
                                    </tr>
                                </thead>
                                <tbody>';

        $data_bonatle[] = '         <input type="hidden" name="taf_portarias_id" value="' . $request->id . '" />
                                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                                    <td>
                                        <div style="float: left;">
                                            <i class="ion-stats-bars" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 90%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                            <input class="no-style" style="width: 100%;" name="valor_min" type="text" value="" maxlength="5" autocomplete="off" placeholder="Nota mínima que faz jus" />
                                        </div>
                                        <div class="clear"></div>
                                    </td>
                                    <td>
                                        <div style="float: left;">
                                            <i class="ion-connection-bars" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 90%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                            <input class="no-style" style="width: 100%;" name="valor_max" type="text" value="" maxlength="5" autocomplete="off" placeholder="Limite que faz jus" />
                                        </div>
                                        <div class="clear"></div>
                                    </td>
                                    <td>
                                        <div style="float: left;">
                                            <i class="ion-ribbon-a" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: right; border-bottom: 1px solid #ccc; width: 90%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                            <input class="no-style" style="width: 100%;" name="bonus" type="text" value="" maxlength="5" autocomplete="off" placeholder="Informe grau extra" />
                                        </div>
                                        <div class="clear"></div>
                                    </td>
                                        <td class="botao-action-table">
                                        <button type="button" class="btn btn-success" onclick="incluirBonusAtleta();">Incluir</button>
                                    </td>';

        foreach ($bonus_atletas as $c) {
            $data_bonatle[] = ' <tr  id="tr_indice_bonus_atletas_' . $c->id . '">
                                        <td>' . $c->valor_min . '</td>
                                        <td>' . $c->valor_max . '</td>
                                        <td>' . $c->bonus . '</td>
                                        <td class="botao-action-table">
                                            <a href="javascript: void(0);" class="no-style" onclick="dialogRemoverBonusAtleta(' . $c->id . ');" title="Remover esta regra de bonificação"><i class="ion-android-delete"></i></a>
                                        </td>
                                    </tr>';
        }
        $data_bonatle[] = '         </tbody>
                                    </table>
                                </form>';

        /* OPÇÕES E OPÇÕES DE SUFICIÊNCIA */

        $taf_config = TafConfiguracoes::where('taf_portarias_id', '=', $request->id)->first();

        $status_check_corrida = ($taf_config->suficiencia_corrida == 0) ? '' : '<i style="color: #0B6121; font-size: 22px; margin-top: 8px;" class="ion-android-done"></i>';
        $status_check_flexbra = ($taf_config->suficiencia_flexao_braco == 0) ? '' : '<i style="color: #0B6121; font-size: 22px; margin-top: 8px;" class="ion-android-done"></i>';
        $status_check_flexbar = ($taf_config->suficiencia_flexao_barra == 0) ? '' : '<i style="color: #0B6121; font-size: 22px; margin-top: 8px;" class="ion-android-done"></i>';
        $status_check_abdomin = ($taf_config->suficiencia_abdominal == 0) ? '' : '<i style="color: #0B6121; font-size: 22px; margin-top: 8px;" class="ion-android-done"></i>';
        $status_check_exercicio_corrida = ($taf_config->avaliado_corrida == 0) ? '' : '<i style="color: #0B6121; font-size: 22px; margin-top: 8px;" class="ion-android-done"></i>';
        $status_check_exercicio_flexbra = ($taf_config->avaliado_flexao_braco == 0) ? '' : '<i style="color: #0B6121; font-size: 22px; margin-top: 8px;" class="ion-android-done"></i>';
        $status_check_exercicio_flexbar = ($taf_config->avaliado_flexao_barra == 0) ? '' : '<i style="color: #0B6121; font-size: 22px; margin-top: 8px;" class="ion-android-done"></i>';
        $status_check_exercicio_abdomin = ($taf_config->avaliado_abdominal == 0) ? '' : '<i style="color: #0B6121; font-size: 22px; margin-top: 8px;" class="ion-android-done"></i>';

        /* OPÇÕES DE SEGMENTO */

        $status_check_masculino = ($taf_config->universo_segmento == 'M' || $taf_config->universo_segmento == 'MF') ? '<i style="color: #0B6121; font-size: 22px; margin-top: 8px;" class="ion-android-done"></i>' : '';
        $status_check_feminino = ($taf_config->universo_segmento == 'F' || $taf_config->universo_segmento == 'MF') ? '<i style="color: #0B6121; font-size: 22px; margin-top: 8px;" class="ion-android-done"></i>' : '';

        /* SELECIONANDO ÁRERAS */

        $areas = Areas::get();
        $areas_array = explode(',', $taf_config->universo_area);
        foreach ($areas as $area) {

            /* VERIFICANDO SE ESTA CHECKED */

            $status_checked = (in_array($area->id, $areas_array)) ? '<i style="color: #0B6121; font-size: 22px; margin-top: 8px;" class="ion-android-done"></i>' : '';

            if (strlen($area->area) > 14) {
                $label_area = '<span style="font-size: 10px;">' . substr($area->area, 0, 28) . '...</span>';
            } else {
                $label_area = $area->area . '<br />&nbsp;';
            }
            $universo[] = ' <div class="box-taf-opcoes">
                                    <a href="javascript: void(0);" onclick="marcarUniverso(this, \'area\', ' . $area->id . ', ' . $request->id . ');" class="no-style" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="Clique aqui para que a portaria seja aplicada a militares da área ' . $area->area . '">
                                        <div style="border-radius: 50%; border: 1px solid #0B6121; width: 36px; height: 36px; margin-left: 27px; margin-bottom: 8px;">' . $status_checked . '</div>
                                        ' . $label_area . '
                                    </a>
                                </div>';
        }

        $anos_de_formacao = AnoFormacao::get();
        foreach ($anos_de_formacao as $ano_de_formacao) {
            //$status_check_ano_formacao = ($taf_config->ano_formacao_id==0)?'':'<i style="color: #0B6121; font-size: 22px; margin-top: 8px;" class="ion-android-done"></i>'; 
            if ($taf_config->ano_formacao_id == $ano_de_formacao->id) {
                $status_check_ano_formacao = '<i style="color: #0B6121; font-size: 22px; margin-top: 8px;" class="ion-android-done"></i>';
            } else {
                $status_check_ano_formacao = '';
            }
            $ano_formacao[] = ' <div class="box-taf-opcoes">
                                        <a href="javascript: void(0);" onclick="marcarUniverso(this, \'ano-formacao\', ' . $ano_de_formacao->id . ', ' . $request->id . ');" class="no-style" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="Clique aqui para que esta portaria seja utilizada no ano de formação ' . $ano_de_formacao->formacao . '">
                                            <div id="anoFormacaoID_' . $ano_de_formacao->id . '" class="anoFormacaoID" style="border-radius: 7px; border: 1px solid #0B6121; width: 36px; height: 36px; margin-left: 27px; margin-bottom: 8px;">' . $status_check_ano_formacao . '</div>
                                            ' . $ano_de_formacao->formacao . '
                                        </a>
                                    </div>';
        }

        $data_opcoes[] = '      <div style="border-bottom: 1px solid #E6E6E6; padding-bottom: 24px;">
                                        <div class="alert status-opcoes-sufuciencia-portaria" role="alert"></div>

                                        <div style="text-align: center; margin-top: 40px; color: #696969;">
                                            <h5>UNIVERSO DE ALUNOS AVALIADOS NA PORTARIA</h5>
                                        </div>
                                        <div style="text-align: center; width: 70%; max-width: 800px; margin: 24px auto;">
                                            ' . implode('', $universo) . '
                                        </div>

                                        <div style="text-align: center; width: 70%; max-width: 800px; margin: 24px auto;">
                                            <div class="box-taf-opcoes">
                                                <a href="javascript: void(0);" onclick="marcarUniverso(this, \'segmento\', \'M\', ' . $request->id . ');" class="no-style" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="Clique aqui para que a portaria seja aplicada a militares do segmento masculino">
                                                    <div style="border-radius: 50%; border: 1px solid #0B6121; width: 36px; height: 36px; margin-left: 27px; margin-bottom: 8px;">' . $status_check_masculino . '</div>
                                                    Masculino
                                                </a>
                                            </div><div class="box-taf-opcoes">
                                                <a href="javascript: void(0);" onclick="marcarUniverso(this, \'segmento\', \'F\', ' . $request->id . ');" class="no-style" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="Clique aqui para que a portaria seja aplicada a militares do segmento feminino">
                                                    <div style="border-radius: 50%; border: 1px solid #0B6121; width: 36px; height: 36px; margin-left: 27px; margin-bottom: 8px;">' . $status_check_feminino . '</div>
                                                    Feminino
                                                </a>
                                            </div>
                                        </div>

                                        <div style="text-align: center; margin-top: 40px; color: #696969;">
                                            <h5>SERÁ AVALIADO NO UNIVERSO</h5>
                                        </div>
                                        <div style="text-align: center; width: 70%; max-width: 800px; margin: 24px auto;">
                                            <div class="box-taf-opcoes">
                                                <a href="javascript: void(0);" onclick="marcarExercicioAvaliado(this, \'avaliado_corrida\', ' . $request->id . ');" class="no-style" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="Clique aqui se a CORRIDA for um exercício avaliado no universo selecionado acima">
                                                    <div style="border-radius: 50%; border: 1px solid #0B6121; width: 36px; height: 36px; margin-left: 27px; margin-bottom: 8px;">' . $status_check_exercicio_corrida . '</div>
                                                    Corrida
                                                </a>
                                            </div><div class="box-taf-opcoes">
                                                <a href="javascript: void(0);" onclick="marcarExercicioAvaliado(this, \'avaliado_flexao_braco\', ' . $request->id . ');" class="no-style" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="Clique aqui se a FLEXÃO DE BRAÇO for um exercício avaliado no universo selecionado acima">
                                                    <div style="border-radius: 50%; border: 1px solid #0B6121; width: 36px; height: 36px; margin-left: 27px; margin-bottom: 8px;">' . $status_check_exercicio_flexbra . '</div>
                                                    Flexão de braço
                                                </a>
                                            </div><div class="box-taf-opcoes">
                                                <a href="javascript: void(0);" onclick="marcarExercicioAvaliado(this, \'avaliado_flexao_barra\', ' . $request->id . ');" class="no-style"  data-trigger="hover" data-toggle="popover" data-placement="top" data-content="Clique aqui se a FLEXÃO NA BARRA for um exercício avaliado no universo selecionado acima">
                                                    <div style="border-radius: 50%; border: 1px solid #0B6121; width: 36px; height: 36px; margin-left: 27px; margin-bottom: 8px;">' . $status_check_exercicio_flexbar . '</div>
                                                    Flexão na barra                                    
                                                </a>
                                            </div><div class="box-taf-opcoes">
                                                <a href="javascript: void(0);" onclick="marcarExercicioAvaliado(this, \'avaliado_abdominal\', ' . $request->id . ');" class="no-style" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="Clique aqui se o ABDOMINAL for um exercício avaliado no universo selecionado acima">
                                                    <div style="border-radius: 50%; border: 1px solid #0B6121; width: 36px; height: 36px; margin-left: 27px; margin-bottom: 8px;">' . $status_check_exercicio_abdomin . '</div>
                                                    Abdominal                                    
                                                </a>
                                            </div>
                                        </div>

                                        <div style="text-align: center; margin-top: 40px; color: #696969;">
                                            <h5>AVALIAÇÃO POR SUFICIÊNCIA</h5>
                                        </div>
                                        <div style="text-align: center; width: 70%; max-width: 800px; margin: 24px auto;">
                                            <div class="box-taf-opcoes">
                                                <a href="javascript: void(0);" onclick="marcarSuficiencia(this, \'suficiencia_corrida\', ' . $request->id . ');" class="no-style" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="Clique aqui se quiser configurar a CORRIDA como suficiência">
                                                    <div style="border-radius: 50%; border: 1px solid #0B6121; width: 36px; height: 36px; margin-left: 27px; margin-bottom: 8px;">' . $status_check_corrida . '</div>
                                                    Corrida
                                                </a>
                                            </div><div class="box-taf-opcoes">
                                                <a href="javascript: void(0);" onclick="marcarSuficiencia(this, \'suficiencia_flexao_braco\', ' . $request->id . ');" class="no-style" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="Clique aqui se quiser configurar a FLEXÃO DE BRAÇO como suficiência">
                                                    <div style="border-radius: 50%; border: 1px solid #0B6121; width: 36px; height: 36px; margin-left: 27px; margin-bottom: 8px;">' . $status_check_flexbra . '</div>
                                                    Flexão de braço
                                                </a>
                                            </div><div class="box-taf-opcoes">
                                                <a href="javascript: void(0);" onclick="marcarSuficiencia(this, \'suficiencia_flexao_barra\', ' . $request->id . ');" class="no-style"  data-trigger="hover" data-toggle="popover" data-placement="top" data-content="Clique aqui se quiser configurar a FLEXÃO NA BARRA como suficiência">
                                                    <div style="border-radius: 50%; border: 1px solid #0B6121; width: 36px; height: 36px; margin-left: 27px; margin-bottom: 8px;">' . $status_check_flexbar . '</div>
                                                    Flexão na barra                                    
                                                </a>
                                            </div><div class="box-taf-opcoes">
                                                <a href="javascript: void(0);" onclick="marcarSuficiencia(this, \'suficiencia_abdominal\', ' . $request->id . ');" class="no-style" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="Clique aqui se quiser configurar o ABDOMINAL como suficiência">
                                                    <div style="border-radius: 50%; border: 1px solid #0B6121; width: 36px; height: 36px; margin-left: 27px; margin-bottom: 8px;">' . $status_check_abdomin . '</div>
                                                    Abdominal                                    
                                                </a>
                                            </div>
                                        </div>
                                        <div style="text-align: center; margin-top: 40px; color: #696969;">
                                            <h5>ANO DE FORMAÇÃO ENQUADRADO NA PORTARIA</h5>
                                        </div>
                                        <div style="text-align: center; width: 70%; max-width: 800px; margin: 24px auto;">
                                            ' . implode('', $ano_formacao) . '
                                        </div>                                        
                                    </div>';

        $data_opcoes[] = '  <div class="alert status-opcoes-portaria" role="alert" style="margin: 18px auto;"></div>
                                <div style="text-align: center; margin: 40px; color: #696969;">
                                    <h5>OPÇÕES DA PORTARIA</h5>    
                                </div>
                                <div style="text-align: center; width: 70%; max-width: 800px; margin: 24px auto;">
                                <div class="box-taf-opcoes">
                                    <a href="javascript: void(0);" onclick="clonarPortaria(this, ' . $request->id . ')" class="no-style" data-trigger="hover" data-toggle="popover" data-placement="top" title="Clonar Portaria" data-content="<p style=\'margin-top: 14px;\'><strong>Importante!</strong></p>É aconselhável clonar a Portaria e seus respectivos registros caso seja necessária a atualização de índices do TAF. A atualização de índices em uma Portaria já vinculada ocasionará em alteração de notas já computadas.">
                                        <div style="border-radius: 50%; border: 1px solid #000000; width: 56px; height: 56px; margin-left: 17px; margin-bottom: 8px;">
                                            <img src="/images/clone.png" style="width: 30px; margin-top: 16px;" />
                                        </div>
                                        Clonar
                                    </a>
                                </div><div class="box-taf-opcoes">
                                    <a href="javascript: void(0);" onclick="dialogEditarNomePortaria(' . $request->id . ');" class="no-style">
                                        <div style="border-radius: 50%; border: 1px solid #0B2161; width: 56px; height: 56px; margin-left: 17px; margin-bottom: 8px; padding-top: 8px;">
                                            <i class="ion-android-create" style="font-size: 26px; color: #0B2161;"></i>
                                        </div>
                                        Renomear
                                    </a>
                                </div><div class="box-taf-opcoes">
                                    <a href="' . route('exportar-modo-impressao.portaria',  $request->id) . '" title="Imprimir tabela de índices" class="no-style" target="_blank">
                                        <div style="border-radius: 50%; border: 1px solid #DF7401; width: 56px; height: 56px; margin-left: 17px; margin-bottom: 8px; padding-top: 8px;">
                                            <i class="ion-android-print" style="font-size: 26px; color: #DF7401;"></i>
                                        </div>
                                        Imprimir                                    
                                    </a>
                                </div><div class="box-taf-opcoes">
                                    <a href="javascript: void(0);" onclick="dialogRemoverPortaria(this, ' . $request->id . ')" class="no-style">
                                        <div style="border-radius: 50%; border: 1px solid #B40404; width: 56px; height: 56px; margin-left: 17px; margin-bottom: 8px; padding-top: 8px;">
                                            <i class="ion-android-delete" style="font-size: 26px; color: #B40404"></i>
                                        </div>
                                        Remover                                    
                                    </a>
                                </div>
                            </div>';

        $data_opcoes[] = '  <script>
                                    $(function () {
                                        $(\'[data-toggle="popover"]\').popover({
                                            html: true,
                                        });
                                    });
                                </script>';

        //$configuracoes = TafConfiguracoes::where('taf_portarias_id', '=', $request->id)->get();

        if ($request->id != 0) {
            $data['result'] = ' <div style="margin-top: 48px;">
                                    <nav class="nav-justified">
                                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                            <a class="nav-item nav-link active" data-toggle="tab" href="#nav-corrida" role="tab" aria-controls="nav-home" aria-selected="true">Corrida</a>
                                            <a class="nav-item nav-link" data-toggle="tab" href="#nav-flexao_braco" role="tab" aria-controls="nav-profile" aria-selected="false">Flexão de braço</a>
                                            <a class="nav-item nav-link" data-toggle="tab" href="#nav-flexao_barra" role="tab" aria-controls="nav-contact" aria-selected="false">Flexão na barra</a>
                                            <a class="nav-item nav-link" data-toggle="tab" href="#nav-abdominal" role="tab" aria-controls="nav-contact" aria-selected="false">Abdominal</a>
                                            <a class="nav-item nav-link" data-toggle="tab" href="#nav-atletas" role="tab" aria-controls="nav-contact" aria-selected="false">Bônus atletas</a>
                                            <a class="nav-item nav-link" data-toggle="tab" href="#nav-configuracao" role="tab" aria-controls="nav-contact" aria-selected="false"><i class="ion-gear-b"></i> Opções</a>
                                        </div>
                                    </nav>
                                    <div class="tab-content" id="nav-tabContent">
                                        <div class="tab-pane fade show active" id="nav-corrida" role="tabpanel" aria-labelledby="nav-home-tab">
                                            ' . implode('', $data_corrida) . '
                                        </div>
                                        <div class="tab-pane fade" id="nav-flexao_braco" role="tabpanel" aria-labelledby="nav-profile-tab">
                                            ' . implode('', $data_flexbra) . '
                                        </div>
                                        <div class="tab-pane fade" id="nav-flexao_barra" role="tabpanel" aria-labelledby="nav-contact-tab">
                                            ' . implode('', $data_flexbar) . '
                                        </div>
                                        <div class="tab-pane fade" id="nav-abdominal" role="tabpanel" aria-labelledby="nav-contact-tab">
                                            ' . implode('', $data_abdomin) . '
                                        </div>
                                        <div class="tab-pane fade" id="nav-atletas" role="tabpanel" aria-labelledby="nav-contact-tab">
                                            ' . implode('', $data_bonatle) . '
                                        </div>
                                        <div class="tab-pane fade" id="nav-configuracao" role="tabpanel" aria-labelledby="nav-contact-tab">
                                            ' . implode('', $data_opcoes) . '
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    $(\'.minuto_mask\').mask(\'00:00\');
                                </script>';
        } else {
            $data['result'] = '';
        }

        return $data;
    }

    public function MarcarSuficiencia(Request $request)
    {

        $taf_config = TafConfiguracoes::where('taf_portarias_id', '=', $request->id)->first();

        if ($taf_config[$request->modo] == 0) {
            $taf_config[$request->modo] = 1;
            if ($taf_config->save()) {
                $data['status'] = 'ok';
                $data['changestatusCheckTo'] = 'on';
            } else {
                $data['status'] = 'err';
            }
        } else {
            $taf_config[$request->modo] = 0;
            if ($taf_config->save()) {
                $data['status'] = 'ok';
                $data['changestatusCheckTo'] = 'off';
            } else {
                $data['status'] = 'err';
            }
        }

        return $data;
    }

    public function MarcarExercicioAvaliado(Request $request)
    {

        $taf_config = TafConfiguracoes::where('taf_portarias_id', '=', $request->id)->first();

        if ($taf_config[$request->modo] == 0) {
            $taf_config[$request->modo] = 1;
            if ($taf_config->save()) {
                $data['status'] = 'ok';
                $data['changestatusCheckTo'] = 'on';
            } else {
                $data['status'] = 'err';
            }
        } else {
            $taf_config[$request->modo] = 0;
            if ($taf_config->save()) {
                $data['status'] = 'ok';
                $data['changestatusCheckTo'] = 'off';
            } else {
                $data['status'] = 'err';
            }
        }

        return $data;
    }

    public function MarcarUniverso(Request $request)
    {

        $taf_config = TafConfiguracoes::where('taf_portarias_id', '=', $request->id)->first();

        if ($request->modo == 'area') {
            if ($taf_config->universo_area != '') {
                $universo_area_array = explode(',', $taf_config->universo_area);
                if (in_array($request->val, $universo_area_array)) {
                    $universo_area = implode(',', array_diff($universo_area_array, array($request->val)));
                    $data['changestatusCheckTo'] = 'off';
                } else {
                    $universo_area = implode(',', array_merge($universo_area_array, array($request->val)));
                    $data['changestatusCheckTo'] = 'on';
                }
            } else {
                $universo_area = $request->val;
                $data['changestatusCheckTo'] = 'on';
            }

            $taf_config = TafConfiguracoes::where('taf_portarias_id', $request->id)->update(['universo_area' => $universo_area]);
            $data['status'] = 'ok';
        } else if ($request->modo == 'segmento') {
            if ($taf_config->universo_segmento != '') {
                if ($taf_config->universo_segmento == 'M') {
                    if ($request->val == 'M') {
                        $universo_segmento = '';
                        $data['changestatusCheckTo'] = 'off';
                    } else if ($request->val == 'F') {
                        $universo_segmento = 'MF';
                        $data['changestatusCheckTo'] = 'on';
                    }
                }

                if ($taf_config->universo_segmento == 'F') {
                    if ($request->val == 'F') {
                        $universo_segmento = '';
                        $data['changestatusCheckTo'] = 'off';
                    } else if ($request->val == 'M') {
                        $universo_segmento = 'MF';
                        $data['changestatusCheckTo'] = 'on';
                    }
                }

                if ($taf_config->universo_segmento == 'MF') {
                    if ($request->val == 'F') {
                        $data['changestatusCheckTo'] = 'off';
                        $universo_segmento = 'M';
                    } else if ($request->val == 'M') {
                        $data['changestatusCheckTo'] = 'off';
                        $universo_segmento = 'F';
                    }
                }

                $taf_config = TafConfiguracoes::where('taf_portarias_id', $request->id)->update(['universo_segmento' => $universo_segmento]);
                $data['status'] = 'ok';
            } else {

                if ($request->val == 'F') {
                    $universo_segmento = 'F';
                    $data['changestatusCheckTo'] = 'on';
                } else if ($request->val == 'M') {
                    $universo_segmento = 'M';
                    $data['changestatusCheckTo'] = 'on';
                }

                $taf_config = TafConfiguracoes::where('taf_portarias_id', $request->id)->update(['universo_segmento' => $universo_segmento]);
                $data['status'] = 'ok';
            }
        } else if ($request->modo == 'ano-formacao') {

            $data['status'] = 'ok';

            if ($taf_config->ano_formacao_id != $request->val) {
                $val = $request->val;
                $data['changestatusCheckTo'] = 'on';
            } else {
                $val = null;
                $data['changestatusCheckTo'] = 'off';
            }

            $taf_config = TafConfiguracoes::where('taf_portarias_id', $request->id)->update(['ano_formacao_id' => $val]);
        }

        /*         if($taf_config[$request->modo]==0){
            $taf_config[$request->modo]=1;
            if($taf_config->save()){
                $data['status'] = 'ok';
                $data['changestatusCheckTo'] = 'on';
            } else {
                $data['status'] = 'err';
            }
        } else {
            $taf_config[$request->modo]=0;
            if($taf_config->save()){
                $data['status'] = 'ok';
                $data['changestatusCheckTo'] = 'off';
            } else {
                $data['status'] = 'err';
            }
        } */

        return $data;
    }

    public function RemoverPortaria(Request $request)
    {
        if (TafPortarias::destroy($request->id)) {
            $data['status'] = 'ok';
        } else {
            $data['status'] = 'err';
        }
        return $data;
    }

    public function ClonarPortaria(Request $request)
    {

        $portaria = TafPortarias::find($request->id);
        $nome_clone = $portaria->nome_portaria . '_Clonada_' . date('d-m-Y_H_i_s');

        $portaria_conf = TafConfiguracoes::where('taf_portarias_id', '=', $request->id)->first();

        $portaria_clone_conf = new TafConfiguracoes;

        $portaria_clone_conf->suficiencia_corrida = $portaria_conf->suficiencia_corrida;
        $portaria_clone_conf->suficiencia_flexao_braco = $portaria_conf->suficiencia_flexao_braco;
        $portaria_clone_conf->suficiencia_flexao_barra = $portaria_conf->suficiencia_flexao_barra;
        $portaria_clone_conf->suficiencia_abdominal = $portaria_conf->suficiencia_abdominal;
        $portaria_clone_conf->avaliado_corrida = $portaria_conf->avaliado_corrida;
        $portaria_clone_conf->avaliado_flexao_braco = $portaria_conf->avaliado_flexao_braco;
        $portaria_clone_conf->avaliado_flexao_barra = $portaria_conf->avaliado_flexao_barra;
        $portaria_clone_conf->avaliado_abdominal = $portaria_conf->avaliado_abdominal;
        $portaria_clone_conf->universo_area = $portaria_conf->universo_area;
        $portaria_clone_conf->universo_segmento = $portaria_conf->universo_segmento;

        $portaria = new TafPortarias;

        $portaria->nome_portaria = $nome_clone;

        if ($portaria->save()) {

            /* TABELA CORRIDA */

            $reg_a_clonar = TafCorrida::where('taf_portarias_id', '=', $request->id)->get();

            foreach ($reg_a_clonar as $reg) {
                $corrida = new TafCorrida;
                $corrida->tempo_inicial = $reg->tempo_inicial;
                $corrida->tempo_final = $reg->tempo_final;
                $corrida->grau = $reg->grau;
                $corrida->taf_portarias_id = $portaria->id;
                $corrida->save();
            }

            /* TABELA FLEXÃO BRAÇO */

            $reg_a_clonar = TafFlexaoBraco::where('taf_portarias_id', '=', $request->id)->get();

            foreach ($reg_a_clonar as $reg) {
                $flexbra = new TafFlexaoBraco;
                $flexbra->valor_inicial = $reg->valor_inicial;
                $flexbra->valor_final = $reg->valor_final;
                $flexbra->grau = $reg->grau;
                $flexbra->taf_portarias_id = $portaria->id;
                $flexbra->save();
            }

            /* TABELA FLEXÃO BARRA */

            $reg_a_clonar = TafFlexaoBarra::where('taf_portarias_id', '=', $request->id)->get();

            foreach ($reg_a_clonar as $reg) {
                $flexbar = new TafFlexaoBarra;
                $flexbar->valor_inicial = $reg->valor_inicial;
                $flexbar->valor_final = $reg->valor_final;
                $flexbar->grau = $reg->grau;
                $flexbar->taf_portarias_id = $portaria->id;
                $flexbar->save();
            }

            /* ABDOMINAL */

            $reg_a_clonar = TafAbdominal::where('taf_portarias_id', '=', $request->id)->get();

            foreach ($reg_a_clonar as $reg) {
                $abdomin = new TafAbdominal;
                $abdomin->valor_inicial = $reg->valor_inicial;
                $abdomin->valor_final = $reg->valor_final;
                $abdomin->grau = $reg->grau;
                $abdomin->taf_portarias_id = $portaria->id;
                $abdomin->save();
            }

            /* BONUS ATLETAS */

            $reg_a_clonar = TafBonusAtletas::where('taf_portarias_id', '=', $request->id)->get();

            foreach ($reg_a_clonar as $reg) {
                $bonus_atle = new TafBonusAtletas;
                $bonus_atle->valor_min = $reg->valor_min;
                $bonus_atle->valor_max = $reg->valor_max;
                $bonus_atle->bonus = $reg->bonus;
                $bonus_atle->taf_portarias_id = $portaria->id;
                $bonus_atle->save();
            }

            $portaria_clone_conf->taf_portarias_id = $portaria->id;

            if ($portaria_clone_conf->save()) {
                $data['status'] = 'ok';
                $data['contentOption'] = '<option value="' . $portaria->id . '">' . $portaria->nome_portaria . '</option>';
            }
        } else {
            $data['status'] = 'err';
        }

        return $data;
    }

    public function incluirIndiceCorrida(Request $request)
    {

        $taf_corrida = new TafCorrida;
        $taf_corrida->tempo_inicial = $request->tempo_inicial;
        $taf_corrida->tempo_final = $request->tempo_final;
        $taf_corrida->grau = $request->grau;
        $taf_corrida->taf_portarias_id = $request->taf_portarias_id;

        if (!strtotime($request->tempo_inicial)) {
            $erro[] = 'tempo inicial inválido';
        }

        if (!strtotime($request->tempo_final)) {
            $erro[] = 'tempo final inválido';
        }

        if ((strtotime($request->tempo_inicial) && strtotime($request->tempo_final)) && strtotime($request->tempo_inicial) > strtotime($request->tempo_final)) {
            $erro[] = 'tempo final não deve ser menor que o tempo inicial';
        }

        if (!is_numeric($request->grau) || $request->grau > 10) {
            $erro[] = 'grau deve ser informado no formato numérico sendo 10 (dez) o valor máximo';
        }

        if (isset($erro)) {
            $data['status'] = 'err';
            $data['erros'] = implode(', ', $erro);
        } else {
            if ($taf_corrida->save()) {
                $data['status'] = 'ok';
                $data['content'] = '    <tr id="tr_indice_corrida_' . $taf_corrida->id . '">
                                            <td>' . strftime('%H:%M', strtotime($taf_corrida->tempo_inicial)) . '</td>
                                            <td>' . strftime('%H:%M', strtotime($taf_corrida->tempo_final)) . '</td>
                                            <td>' . $taf_corrida->grau . '</td>
                                            <td class="botao-action-table">
                                                <a href="javascript: void(0);" class="no-style" onclick="dialogRemoverIndiceCorrida(' . $taf_corrida->id . ');" title="Remover este índice"><i class="ion-android-delete"></i></a>
                                            </td>
                                        </tr>';
            } else {
                $data['status'] = 'err1';
                $data['erros'] = 'Não foi possivel fazer o registro do índice na tabela. ';
            }
        }

        return $data;
    }

    public function RemoverIndiceCorrida(Request $request)
    {
        $id = $request->id;
        if (TafCorrida::destroy($id)) {
            $data['status'] = 'ok';
        } else {
            $data['status'] = 'err';
        }

        return $data;
    }

    public function incluirIndiceFlexBra(Request $request)
    {

        $taf_flexbra = new TafFlexaoBraco;
        $taf_flexbra->valor_inicial = $request->valor_inicial;
        $taf_flexbra->valor_final = $request->valor_final;
        $taf_flexbra->grau = $request->grau;
        $taf_flexbra->taf_portarias_id = $request->taf_portarias_id;

        if (!is_numeric($request->valor_inicial)) {
            $erro[] = 'valor inicial inválido';
        }

        if (!is_numeric($request->valor_final)) {
            $erro[] = 'valor final inválido';
        }

        if ($request->valor_inicial > $request->valor_final) {
            $erro[] = 'valor final não deve ser menor que o valor inicial';
        }

        if (!is_numeric($request->grau) || $request->grau > 10) {
            $erro[] = 'grau deve ser informado no formato numérico sendo 10 (dez) o valor máximo';
        }

        if (isset($erro)) {
            $data['status'] = 'err';
            $data['erros'] = implode(', ', $erro);
        } else {
            if ($taf_flexbra->save()) {
                $data['status'] = 'ok';
                $data['content'] = '    <tr id="tr_indice_flexbra_' . $taf_flexbra->id . '">
                                            <td>' . $taf_flexbra->valor_inicial . '</td>
                                            <td>' . $taf_flexbra->valor_final . '</td>
                                            <td>' . $taf_flexbra->grau . '</td>
                                            <td class="botao-action-table">
                                                <a href="javascript: void(0);" class="no-style" onclick="dialogRemoverIndiceFlexBra(' . $taf_flexbra->id . ');" title="Remover este índice"><i class="ion-android-delete"></i></a>
                                            </td>
                                        </tr>';
            } else {
                $data['status'] = 'err1';
                $data['erros'] = 'Não foi possivel fazer o registro do índice na tabela. ';
            }
        }

        return $data;
    }

    public function RemoverIndiceFlexBra(Request $request)
    {
        $id = $request->id;
        if (TafFlexaoBraco::destroy($id)) {
            $data['status'] = 'ok';
        } else {
            $data['status'] = 'err';
        }

        return $data;
    }

    public function incluirIndiceFlexBar(Request $request)
    {

        $taf_flexbar = new TafFlexaoBarra;
        $taf_flexbar->valor_inicial = $request->valor_inicial;
        $taf_flexbar->valor_final = $request->valor_final;
        $taf_flexbar->grau = $request->grau;
        $taf_flexbar->taf_portarias_id = $request->taf_portarias_id;

        if (!is_numeric($request->valor_inicial)) {
            $erro[] = 'valor inicial inválido';
        }

        if (!is_numeric($request->valor_final)) {
            $erro[] = 'valor final inválido';
        }

        if ($request->valor_inicial > $request->valor_final) {
            $erro[] = 'valor final não deve ser menor que o valor inicial';
        }

        if (!is_numeric($request->grau) || $request->grau > 10) {
            $erro[] = 'grau deve ser informado no formato numérico sendo 10 (dez) o valor máximo';
        }

        if (isset($erro)) {
            $data['status'] = 'err';
            $data['erros'] = implode(', ', $erro);
        } else {
            if ($taf_flexbar->save()) {
                $data['status'] = 'ok';
                $data['content'] = '    <tr id="tr_indice_flexbar_' . $taf_flexbar->id . '">
                                            <td>' . $taf_flexbar->valor_inicial . '</td>
                                            <td>' . $taf_flexbar->valor_final . '</td>
                                            <td>' . $taf_flexbar->grau . '</td>
                                            <td class="botao-action-table">
                                                <a href="javascript: void(0);" class="no-style" onclick="dialogRemoverIndiceFlexBar(' . $taf_flexbar->id . ');" title="Remover este índice"><i class="ion-android-delete"></i></a>
                                            </td>
                                        </tr>';
            } else {
                $data['status'] = 'err1';
                $data['erros'] = 'Não foi possivel fazer o registro do índice na tabela. ';
            }
        }

        return $data;
    }

    public function RemoverIndiceFlexBar(Request $request)
    {
        $id = $request->id;
        if (TafFlexaoBarra::destroy($id)) {
            $data['status'] = 'ok';
        } else {
            $data['status'] = 'err';
        }

        return $data;
    }

    public function incluirIndiceAbdomin(Request $request)
    {

        $taf_abdominal = new TafAbdominal;
        $taf_abdominal->valor_inicial = $request->valor_inicial;
        $taf_abdominal->valor_final = $request->valor_final;
        $taf_abdominal->grau = $request->grau;
        $taf_abdominal->taf_portarias_id = $request->taf_portarias_id;

        if (!is_numeric($request->valor_inicial)) {
            $erro[] = 'valor inicial inválido';
        }

        if (!is_numeric($request->valor_final)) {
            $erro[] = 'valor final inválido';
        }

        if ($request->valor_inicial > $request->valor_final) {
            $erro[] = 'valor final não deve ser menor que o valor inicial';
        }

        if (!is_numeric($request->grau) || $request->grau > 10) {
            $erro[] = 'grau deve ser informado no formato numérico sendo 10 (dez) o valor máximo';
        }

        if (isset($erro)) {
            $data['status'] = 'err';
            $data['erros'] = implode(', ', $erro);
        } else {
            if ($taf_abdominal->save()) {
                $data['status'] = 'ok';
                $data['content'] = '    <tr id="tr_indice_abdominal_' . $taf_abdominal->id . '">
                                            <td>' . $taf_abdominal->valor_inicial . '</td>
                                            <td>' . $taf_abdominal->valor_final . '</td>
                                            <td>' . $taf_abdominal->grau . '</td>
                                            <td class="botao-action-table">
                                                <a href="javascript: void(0);" class="no-style" onclick="dialogRemoverIndiceAbdominal(' . $taf_abdominal->id . ');" title="Remover este índice"><i class="ion-android-delete"></i></a>
                                            </td>
                                        </tr>';
            } else {
                $data['status'] = 'err1';
                $data['erros'] = 'Não foi possivel fazer o registro do índice na tabela. ';
            }
        }

        return $data;
    }

    public function RemoverIndiceAbdomin(Request $request)
    {
        $id = $request->id;
        if (TafAbdominal::destroy($id)) {
            $data['status'] = 'ok';
        } else {
            $data['status'] = 'err';
        }

        return $data;
    }

    public function incluirBonusAtletas(Request $request)
    {

        $taf_bonus_atletas = new TafBonusAtletas;
        $taf_bonus_atletas->valor_min = $request->valor_min;
        $taf_bonus_atletas->valor_max = $request->valor_max;
        $taf_bonus_atletas->bonus = $request->bonus;
        $taf_bonus_atletas->taf_portarias_id = $request->taf_portarias_id;

        if (!is_numeric($request->valor_min)) {
            $erro[] = 'valor inicial inválido';
        }

        if (!is_numeric($request->valor_max)) {
            $erro[] = 'valor final inválido';
        }

        if ($request->valor_min > $request->valor_max) {
            $erro[] = 'valor final não deve ser menor que o valor inicial';
        }

        if (!is_numeric($request->bonus) || $request->bonus > 10) {
            $erro[] = 'bônus deve ser informado no formato numérico sendo 10 (dez) o valor máximo';
        }

        if (isset($erro)) {
            $data['status'] = 'err';
            $data['erros'] = implode(', ', $erro);
        } else {
            if ($taf_bonus_atletas->save()) {
                $data['status'] = 'ok';
                $data['content'] = '    <tr id="tr_indice_bonus_atletas_' . $taf_bonus_atletas->id . '">
                                            <td>' . $taf_bonus_atletas->valor_min . '</td>
                                            <td>' . $taf_bonus_atletas->valor_max . '</td>
                                            <td>' . $taf_bonus_atletas->bonus . '</td>
                                            <td class="botao-action-table">
                                                <a href="javascript: void(0);" class="no-style" onclick="dialogRemoverBonusAtleta(' . $taf_bonus_atletas->id . ');" title="Remover esta regra de bonificação"><i class="ion-android-delete"></i></a>
                                            </td>
                                        </tr>';
            } else {
                $data['status'] = 'err1';
                $data['erros'] = 'Não foi possivel fazer o registro desta regra de bonificação.';
            }
        }

        return $data;
    }

    public function RemoverBonusAtletas(Request $request)
    {
        $id = $request->id;
        if (TafBonusAtletas::destroy($id)) {
            $data['status'] = 'ok';
        } else {
            $data['status'] = 'err';
        }

        return $data;
    }

    public function RemoverOperador(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request)
    {

        /* REMOVENDO DA TABELA operadores */

        if ($ownauthcontroller->PermissaoCheck(1)) {

            $id = $request->route('id');
            $operadores = Operadores::find($id);
            if ($operadores->usuario->imagens_id) {
                $id_img = $operadores->usuario->imagens_id;
                $imagem = Imagens::find($id_img);
                if ($id_img <> 1) {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $imagem->imagem);
                    Imagens::destroy($id_img);
                }
            }
            $email = $operadores->email;
            $operadores->delete();

            /* REMOVENDO DA TABELA user */

            User::where('email', '=', $email)->delete();

            $data['total_op'] = Operadores::count();
        }

        $data['total_op'] = ($data['total_op']) ?? 0;
        $this->classLog->RegistrarLog('Removeu um operador', auth()->user()->email);
        return $data;
    }

    public function RemoverAluno(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request)
    {

        /* REMOVENDO DA TABELA ALUNOS */

        if ($ownauthcontroller->PermissaoCheck(1)) {

            $id = $request->id;
            $aluno = Alunos::find($id);
            if ($aluno->user->imagens_id) {
                $id_img = $aluno->user->imagens_id;
                $imagem = Imagens::find($id_img);
                unlink($_SERVER["DOCUMENT_ROOT"] . $imagem->imagem);
                Imagens::destroy($id_img);
            }
            $email = $aluno->email;
            $aluno->delete();

            /* REMOVENDO DA TABELA user */

            User::where('email', '=', $email)->delete();

            $data['response'] = 'ok';
        }

        $data['response'] = ($data['response']) ?? 'err';
        $this->classLog->RegistrarLog('Removeu um aluno', auth()->user()->email);
        return $data;
    }

    public function AdicionarOperador(OperadoresRequest $request)
    {
        /* CADASTRANDO OPERADOR NA TABELA USERS */

        $user = new User;
        $user->email = $request->email;
        $user->password = bcrypt(uniqid());
        //$user->password = bcrypt($request->email);

        $operador = new Operadores;
        $operador->nome = mb_strtoupper($request->nome, 'UTF-8');
        $operador->nome_guerra = mb_strtoupper($request->nome_guerra, 'UTF-8');
        $operador->idt_militar = $request->idt_militar;
        $operador->idt_militar_o_exp = $request->idt_militar_o_exp;
        $operador->postograd_id = $request->postograd_id;
        $operador->omcts_id = $request->omcts_id;
        $operador->tel_pronto_atendimento = $request->tel_pronto_atendimento;
        $operador->email = $request->email;
        if ($request->tipo_operador_check) {
            $operador->id_funcao_operador = implode(',', $request->tipo_operador_check);
        } else {
            $operador->id_funcao_operador = '';
        }

        try {
            if ($operador->save() && $user->save()) {
                $data['status'] = 'ok';
            } else {
                $data['status'] = 'err';
            }
            $this->classLog->RegistrarLog('Adicionou um operador', auth()->user()->email);
        } catch (Exception $e) {
            $this->classLog->RegistrarLog('TESTE', 'teste');
        }

        return $data;
    }

    public function AtualizarOperador(OperadoresRequest $request)
    {

        $operador = Operadores::find($request->id);

        $email = $operador->email;

        $operador->nome = $request->nome;
        $operador->nome_guerra = $request->nome_guerra;
        $operador->idt_militar = $request->idt_militar;
        $operador->idt_militar_o_exp = $request->idt_militar_o_exp;
        $operador->omcts_id = $request->omcts_id;
        $operador->postograd_id = $request->postograd_id;
        $operador->tel_pronto_atendimento = $request->tel_pronto_atendimento;
        $operador->email = $request->email;
        if ($request->tipo_operador_check) {
            $operador->id_funcao_operador = implode(',', $request->tipo_operador_check);
        } else {
            $operador->id_funcao_operador = '';
        }

        if ($operador->save() && $email != $request->email) {
            User::where('email', '=', $email)->update(['email' => $request->email]);
        }

        if (isset($request->resetar_senha)) {
            $user = User::where('email', '=', $operador->email)->first();
            $senha = explode('@', $operador->email);
            $user->update(['password' => bcrypt($senha[0])]);
        }

        /* FUNÇÃO POR EXTENSO */

        $funcoesOperadores = OperadoresTipo::get(['id', 'funcao_abrev']);
        foreach ($funcoesOperadores as $funcao) {
            $data_op_funcao[$funcao->id] = $funcao->funcao_abrev;
        }

        if (is_array(explode(',', $operador->id_funcao_operador))) {
            foreach (explode(',', $operador->id_funcao_operador) as $funcao) {
                $funcao_extenso[] = (isset($data_op_funcao[$funcao])) ? $data_op_funcao[$funcao] : '<span style="color: #DF0101;">NÃO CONFIGURADA</span>';
            }
        }

        // $data['content_tr_eq0'] = $operador->postograd->postograd_abrev;
        $data['content_tr_eq0'] = '<b>' . $operador->postograd->postograd_abrev . ' ' . $operador->nome_guerra . '</b><br /><span style="font-size: 11px;"><i>' . $operador->nome . '</i></span>';
        $data['content_tr_eq1'] = implode(', ', $funcao_extenso);
        $data['content_tr_eq2'] = $operador->omcts->sigla_omct;
        $data['content_tr_eq3'] = $operador->tel_pronto_atendimento;
        $data['content_tr_eq4'] = $operador->email;
        $data['nomeGuerra'] = $operador->nome_guerra;
        $data['content_1'] = $operador->postograd->postograd_abrev;
        $data['typeUser'] = ($request->id == auth()->id()) ? 1 : 0;

        $this->classLog->RegistrarLog('Atualizou operador', auth()->user()->email);
        return $data;
    }

    public function RemoverImagemPerfil(Request $request)
    {
        $user = User::find($request->id);
        $id_img_antiga = $user->imagens_id;

        /* atualizando valor da img */

        $user->imagens_id = 1;
        $user->save();
        $img = Imagens::find($id_img_antiga);
        unlink($_SERVER["DOCUMENT_ROOT"] . $img->imagem);
        $img->delete();
        $img = Imagens::find(1);
        $data['src_image'] = $img->imagem;
        $data['typeUser'] = ($request->id == auth()->id()) ? 1 : 0;
        $data['status'] = 'ok';
        $this->classLog->RegistrarLog('Removeu imagem do perfil', auth()->user()->email);
        return $data;
    }

    public function DialogInfoUser(Request $request)
    {

        if ($request->tipo == 'operador') {
            $usr = User::find($request->id);
            $operador = Operadores::where('email', '=', $usr->email)->first();
            $funcoesOperadores = OperadoresTipo::get();
            foreach ($funcoesOperadores as $funcao) {
                $data[$funcao->id] = $funcao->funcao_abrev;
            }
            if ($operador->id_funcao_operador != '' && is_array(explode(',', $operador->id_funcao_operador))) {
                foreach (explode(',', $operador->id_funcao_operador) as $funcao) {
                    $funcoes[] = $data[$funcao];
                }
            } else {
                $funcoes[] = '<span id="funcao_user" style="color: #DF0101; font-size: 12px;">ESTE USUÁRIO NÃO POSSUI FUNÇÃO CONFIGURADA</span>';
            }

            unset($data);

            if ($usr->imagens_id == null) {
                $img_perfil = Imagens::find(1);
            } else {
                $img_perfil = Imagens::find($usr->imagens_id);
            }

            $imgPerfil = (isset($img_perfil->imagem)) ? $img_perfil->imagem : '';

            $data['body'] = '   <div style="position: absolute; top: 5px; left: 95%">
                                    <a class="no-style" href="javascript: void(0);" data-dismiss="modal"><i class="ion-close" style="font-size: 20px; color: #fff;"></i></a>
                                </div>
                                <div style="background:rgb(44,62,80); color: #fff;">
                                    <div style="padding: 22px;">
                                        <div style="float: left; width: 130px;">
                                            <div class="imagem_perfil_info" style="background: url(\'' . $imgPerfil . '\') no-repeat center center; background-size: cover;"></div>                                        
                                        </div>
                                        <div style="margin-top: 18px;">
                                            <h5>' . $operador->nome . '</h5>
                                            <h5>' . $operador->postograd->postograd . ' ' . $operador->nome_guerra . '</h5>
                                            <h5>' . $operador->omcts->omct . '</h5>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div>
                                    <div style="width: 60%; margin: 18px auto; border-bottom: 1px solid #ccc; padding: 8px 6px;">
                                        <i class="ion-ios-telephone" style="font-size: 24px; color: #696969; vertical-align: -6px;"></i> ' . $operador->tel_pronto_atendimento . '
                                    </div>
                                    <div style="width: 60%; margin: 18px auto; border-bottom: 1px solid #ccc; padding: 8px 6px;">
                                        <i class="ion-email" style="font-size: 24px; color: #696969; vertical-align: -6px;"></i> ' . $operador->email . '
                                    </div>
                                    <div style="width: 60%; margin: 18px auto; border-bottom: 1px solid #ccc; padding: 8px 6px;">
                                        <i class="ion-locked" style="font-size: 24px; color: #696969; vertical-align: -2px;"></i> Funções no sistema<br /> ' . implode(', ', $funcoes) . '
                                    </div>
                                </div><br />';
        } else if ($request->tipo == 'aluno') {

            $usr = User::find($request->id);
            $aluno = Alunos::where('email', $usr->email)->first();
            $turma = ($aluno->turma->turma) ?? 'Não informada';
            if ($aluno->data_nascimento) {
                list($ano, $mes, $dia) = explode('-', $aluno->data_nascimento);
                $data_nascimento = $dia . '/' . $mes . '/' . $ano;
            } else {
                $data_nascimento = 'Não informada';
            }

            if ($aluno->primeira_data_praca) {
                list($ano, $mes, $dia) = explode('-', $aluno->primeira_data_praca);
                $data_praca = $dia . '/' . $mes . '/' . $ano;
            } else {
                $data_praca = 'Não informada';
            }
            // BACKGROUND COLOR POR SEGMENTO

            $style_back_ground = ($aluno->sexo == 'M') ? 'background-color: #0B6138; color: #fff;' : 'background-color: #F8E0EC; color: #696969;';
            $style_ion_close = ($aluno->sexo == 'M') ? 'color: #ffffff;' : 'color: #696969;';

            if ($usr->imagens_id == null) {
                $img_perfil = Imagens::find(1);
            } else {
                $img_perfil = Imagens::find($usr->imagens_id);
            }

            $data['body'] = '   <div style="position: absolute; top: 5px; left: 95%">
                                    <a class="no-style" href="javascript: void(0);" data-dismiss="modal"><i class="ion-close" style="font-size: 20px; ' . $style_ion_close . '"></i></a>
                                </div>
                                <div style="' . $style_back_ground . '">
                                    <div style="padding: 22px;">
                                        <div style="float: left; width: 130px;">
                                            <div class="imagem_perfil_info" style="background: url(\'' . $img_perfil->imagem . '\') no-repeat center center; background-size: cover;"></div>                                        
                                        </div>
                                        <div style="margin-top: 18px;">
                                            <h5>AL ' . $aluno->numero . ' ' . $aluno->nome_guerra . '</h5>
                                            <h6>' . $aluno->nome_completo . '</h6>
                                            <h6>' . $aluno->omct->omct . '</h6>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div>
                                    <div style="width: 60%; margin: 18px auto; border-bottom: 1px solid #ccc; padding: 8px 6px;">
                                        <i class="ion-easel" style="font-size: 24px; color: #696969; vertical-align: -4px; margin-right: 6px;"></i> <b>Turma</b><br />' . $turma . '
                                    </div>
                                    <div style="width: 60%; margin: 18px auto; border-bottom: 1px solid #ccc; padding: 8px 6px;">
                                        <i class="ion-pinpoint" style="font-size: 24px; color: #696969; vertical-align: -4px; margin-right: 6px;;"></i> <b>Área</b><br />' . $aluno->area->area . '
                                    </div>
                                    <div style="width: 60%; margin: 18px auto; border-bottom: 1px solid #ccc; padding: 8px 6px;">
                                        <i class="ion-calendar" style="font-size: 24px; color: #696969; vertical-align: -4px; margin-right: 6px;"></i> <b>Data de praça anterior</b><br />' . $data_praca . '
                                    </div>
                                    <div style="width: 60%; margin: 18px auto; border-bottom: 1px solid #ccc; padding: 8px 6px;">
                                        <i class="ion-ios-star" style="font-size: 24px; color: #696969; vertical-align: -2px; margin-right: 6px;"></i> <b>Data de nascimento</b><br />' . $data_nascimento . '
                                    </div>
                                    <div style="width: 60%; margin: 18px auto; border-bottom: 1px solid #ccc; padding: 8px 6px;">
                                        <i class="ion-email" style="font-size: 24px; color: #696969; vertical-align: -4px; margin-right: 6px;"></i> <b>Email</b><br />' . $aluno->email . '
                                    </div>
                                </div><br />';
        }

        return $data;
    }

    public function GerenciarAlunos(\App\Http\Controllers\OwnAuthController $ownauthcontroller)
    {

        $anos_formacao = AnoFormacao::get();

        /* SELECIONANDO OMCTS */

        $ano_corrente_data = AnoFormacao::orderBy('formacao', 'desc')->first();
        $ano_corrente = ($ano_corrente_data->id) ?? 0;

        $areas = Areas::get();

        if ($ownauthcontroller->PermissaoCheck(1)) {
            $omcts = OMCT::where('id', '<>', 1)->get();
        } else {
            $omcts = OMCT::where('id', session()->get('login.omctID'))->get();
        }
        foreach ($omcts as $omct) {
            $total_sit_div = AlunosSitDiv::where('omcts_id', '=', $omct->id)->where('data_matricula', '=', $ano_corrente)->count();
            $alunos_por_omct = Alunos::where('omcts_id', '=', $omct->id)->where('data_matricula', '=', $ano_corrente)->get();
            $total_alunos = Alunos::where('omcts_id', '=', $omct->id)->where('data_matricula', '=', $ano_corrente)->where('sexo', 'M')->count();
            $total_alunas = Alunos::where('omcts_id', '=', $omct->id)->where('data_matricula', '=', $ano_corrente)->where('sexo', 'F')->count();

            /* CONSULTA DE QUEM TEM PENDÊNCIAS DE CADASTRO */


            $cadastro_al_pendente = DB::select("SELECT * FROM alunos WHERE (omcts_id=" . $omct->id . " AND data_matricula=" . $ano_corrente . ") 
                                                                           AND (email IS NULL OR turma_id IS NULL OR area_id IS NULL)");

            $omct_data[$omct->id] = array(
                "total_sit_div" => $total_sit_div,
                "total_masculino" => $total_alunos,
                "total_feminino" => $total_alunas,
                "total" => count($alunos_por_omct),
                "lista" => $alunos_por_omct,
                "total_cadastro_al_pendente" => count($cadastro_al_pendente),
                "cadastro_al_pendente" => $cadastro_al_pendente
            );
        }

        //$alunos['list'] = Alunos::get();
        $alunos['list'] = Alunos::carregaAlunosVsAlunosSitDiv();

        return view('ajax.alunos')->with('ownauthcontroller', $ownauthcontroller)
            ->with('alunos', $alunos)
            ->with('omcts', $omcts)
            ->with('areas', $areas)
            ->with('anos_formacao', $anos_formacao)
            ->with('ano_corrente_formacao', $ano_corrente_data->formacao)
            ->with('omct_data', $omct_data);
    }

    public function DialogImplantarAluno(\App\Http\Controllers\OwnAuthController $ownauthcontroller)
    {

        /* LOOP QUE BUSCA AS OMCTS */

        $options_omcts[] = '<option value="0">Informe a UETE</option>';
        $omcts = OMCT::where('id', '<>', 1)->get();
        foreach ($omcts as $omct) {
            if ($ownauthcontroller->PermissaoCheck(1)) {
                $options_omcts[] = '<option value="' . $omct->id . '">' . $omct->omct . '</option>';
            } else if (session()->get('login.omctID') == $omct->id) {
                $options_omcts[] = '<option value="' . $omct->id . '">' . $omct->omct . '</option>';
            }
        }

        /* LOOP QUE BUSCA AS ÁREAS */

        $options_areas[] = '<option value="0">Selecione a área do aluno</option>';
        $areas = Areas::get();
        foreach ($areas as $area) {
            $options_areas[] = '<option value="' . $area->id . '">' . $area->area . '</option>';
        }

        /* LOOP QUE BUSCA OS INSTRUMENTO */

        $options_instrumentos[] = '<option value="0">Selecione um instrumento</option>';
        $instrumentos = Instrumentos::get();
        foreach ($instrumentos as $instrmento) {
            $options_instrumentos[] = '<option value="' . $instrmento->id . '">' . $instrmento->instrumento . '</option>';
        }

        // TURMAS

        $options_turmas[] = '<option value="0">Selecione uma turma</option>';
        $turmas = TurmasPB::get();
        foreach ($turmas as $turma) {
            $options_turmas[] = '<option value="' . $turma->id . '">' . $turma->turma . '</option>';
        }

        $data['header'] = '<i class="ion-person-add" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Implantar aluno';
        $data['body'] = '   <div class="alert alert-danger errors-implantar-aluno" role="alert"></div>
                                    <form id="implantar_aluno">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-pricetag" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" name="numero" type="text" autocomplete="off" maxlength="5" placeholder="Número do aluno" style="width: 100%;" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-android-person" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" name="nome_completo" type="text" autocomplete="off" placeholder="Nome completo" style="width: 100%;" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>
    
                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-android-contact" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" name="nome_guerra" autocomplete="off" placeholder="Nome de guerra" style="width: 100%;" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div style="margin: 18px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left; margin-top: 3px;">
                                                <i class="ion-compose" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float:right; width: 93%;">
                                                <select class="custom-select" name="sexo">
                                                    <option value="0">Selecione o segmento</option>
                                                    <option value="M">Masculino</option>
                                                    <option value="F">Feminino</option>
                                                </select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                        

                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-ios-star" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style data_mask" style="width: 100%;" name="data_nascimento" type="text" value="" maxlength="10" autocomplete="off" placeholder="Data de nascimento (DD/MM/AAAA)" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Atleta da Marexaer">
                                            <div style="float: left;">
                                                <i class="ion-android-walk" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <select class="custom-select" name="atleta_marexaer">
                                                    <option value="S">Sim</option>
                                                    <option value="N" selected>Não</option>
                                                </select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                                                                
                                        <div style="margin: 18px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left; margin-top: 3px;">
                                                <i class="ion-ios-home" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float:right; width: 93%;">
                                                <select class="custom-select" name="omcts_id">' . implode('', $options_omcts) . '</select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div style="margin: 18px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left; margin-top: 3px;">
                                            <i class="ion-ios-book" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float:right; width: 93%;">
                                                <select class="custom-select" name="turma">' . implode('', $options_turmas) . '</select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                           

                                        <div style="margin: 18px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left; margin-top: 3px;">
                                                <i class="ion-pinpoint" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float:right; width: 93%;">
                                                <select class="custom-select" name="area" onchange="if($(this).val()==3){ $(\'select#instrumento\').prop(\'disabled\', false); } else { $(\'select#instrumento\').prop(\'disabled\', true);}">' . implode('', $options_areas) . '</select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div style="margin: 18px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left; margin-top: 3px;">
                                                <i class="ion-music-note" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float:right; width: 93%;">
                                                <select class="custom-select" id="instrumento" name="instrumento" disabled>' . implode('', $options_instrumentos) . '</select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style data_mask" style="width: 100%;" name="data_praca" type="text" value="" maxlength="10" autocomplete="off" placeholder="Data de praça anterior SFC (DD/MM/AAAA)" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>                                        

    
                                        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                                            <div style="float: left;">
                                                <i class="ion-email" style="font-size: 24px; color: #696969;"></i>
                                            </div>
                                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                <input class="no-style" name="email" type="text" maxlength="60" autocomplete="off" placeholder="Email" style="width: 100%;" />
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </form>';

        $data['footer'] = '
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="ImplantarAluno();">
                                Salvar
                            </button>                            
                            <script>
                                $(\'.data_mask\').mask(\'00/00/0000\');
                            </script>';
        return $data;
    }

    public function ImplantarAluno(Request $request)
    {

        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $id_ano_corrente = ($ano_corrente->id) ?? 0;

        @list($dia_n, $mes_n, $ano_n) = @explode('/', $request->data_nascimento);
        @list($dia_p, $mes_p, $ano_p) = @explode('/', $request->data_praca);

        if ($request->numero != '' && !is_numeric($request->numero)) {
            $error[] = 'Se preenchido, campo número deverá conter somente numeros.';
        }

        if (strlen($request->nome_completo) < 5) {
            $error[] = 'Nome parece muito curto. Informe o nome completo.';
        }

        if (strlen($request->nome_guerra) < 2) {
            $error[] = 'Nome de guerra inválido.';
        }

        if ($request->sexo != 'M' && $request->sexo != 'F') {
            $error[] = 'Selecione um segmento.';
        }

        if (isset($request->omcts_id) && $request->omcts_id == 0) {
            $error[] = 'Informe a omct designada';
        }

        if ($request->turma == 0) {
            $turma_request = null;
        } else {
            $turma_request = $request->turma;
        }

        if (!@checkdate($mes_n, $dia_n, $ano_n)) {
            $error[] = 'Data de nascimento inválida';
        } else {
            $data_nascimento = $ano_n . '-' . $mes_n . '-' . $dia_n;
        }

        if (!@checkdate($mes_p, $dia_p, $ano_p)) {
            $data_praca = null;
        } else {
            $data_praca = $ano_p . '-' . $mes_p . '-' . $dia_p;
        }

        if ($request->area == 0) {
            $error[] = 'Selecione a área.';
        }

        if ($request->area == 3 && $request->instrumento == 0) {
            $error[] = 'Alunos da área de música devem possuir um instrumento';
        }

        if (OwnValidator::ValidarEmail($request->email)) {
            $user = User::where('email', $request->email)->first();
            if (!isset($user->email)) {
                $email = $request->email;
            } else {
                $error[] = 'Este email já se encontra cadastrado.';
            }
        } else {
            $error[] = 'E-mail informado é inválido.';
        }

        if (isset($error)) {
            $data['status'] = 'err';
            $data['response'] = implode('<br />', $error);
        } else {
            $aluno = new Alunos;
            $aluno->numero = $request->numero;
            $aluno->nome_completo = $request->nome_completo;
            $aluno->nome_guerra = $request->nome_guerra;
            $aluno->sexo = $request->sexo;
            $aluno->omcts_id = $request->omcts_id;
            $aluno->turma_id = $turma_request;
            $aluno->data_nascimento = $data_nascimento;
            $aluno->atleta_marexaer = $request->atleta_marexaer;
            $aluno->primeira_data_praca = $data_praca;
            $aluno->data_matricula = $id_ano_corrente;
            $aluno->area_id = $request->area;
            $aluno->instrumento_id = $request->instrumento;
            $aluno->email = $email;

            $user = new User;
            $user->email = $email;
            $user->password = bcrypt(uniqid());


            if ($aluno->save() && $user->save()) {
                $data['status'] = 'ok';
            } else {
                $data['status'] = 'err';
                $data['response'] = 'Não foi possível implatar o aluno.';
            }
        }
        $this->classLog->RegistrarLog('Implantou aluno no sistema', auth()->user()->email);
        return $data;
    }

    public function DialogEditarCadastroAluno(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request)
    {
        //dd(base64_decode($request->pesquisa));
        if ($request->tipo == 'byName') {
            $aluno = Alunos::where('nome_completo', $request->pesquisa)->first();
        } else if (($request->tipo = 'byID')) {
            $aluno = Alunos::find($request->pesquisa);
        }

        if (isset($aluno->id)) {

            /* LOOP QUE BUSCA AS OMCTS */

            $options_omcts[] = '<option value="0">Informe a UETE</option>';
            $omcts = OMCT::where('id', '<>', 1)->get();
            foreach ($omcts as $omct) {
                $attr_selected = ($aluno->omcts_id == $omct->id) ? 'selected' : '';
                if ($ownauthcontroller->PermissaoCheck(1)) {
                    $options_omcts[] = '<option value="' . $omct->id . '" ' . $attr_selected . '>' . $omct->omct . '</option>';
                } else if (session()->get('login.omctID') == $omct->id) {
                    $options_omcts[] = '<option value="' . $omct->id . '" ' . $attr_selected . '>' . $omct->omct . '</option>';
                }
            }

            // SEGMENTO 

            $attr_selected_m = ($aluno->sexo == 'M') ? 'selected' : '';
            $attr_selected_f = ($aluno->sexo == 'F') ? 'selected' : '';
            $options_segemento[] = '<option value="0">Selecione o segmento</option>';
            $options_segemento[] = '<option value="M" ' . $attr_selected_m . '>Masculino</option>';
            $options_segemento[] = '<option value="F" ' . $attr_selected_f . '>Feminino</option>';

            // TURMAS

            $options_turmas[] = '<option value="0">Selecione uma turma</option>';
            $turmas = TurmasPB::get();
            foreach ($turmas as $turma) {
                $attr_selected = ($aluno->turma_id == $turma->id) ? 'selected' : '';
                $options_turmas[] = '<option value="' . $turma->id . '" ' . $attr_selected . '>' . $turma->turma . '</option>';
            }


            /* LOOP QUE BUSCA AS ÁREAS */

            $options_areas[] = '<option value="0">Selecione a área do aluno</option>';
            $areas = Areas::get();
            foreach ($areas as $area) {
                $attr_selected = ($aluno->area_id == $area->id) ? 'selected' : '';
                $options_areas[] = '<option value="' . $area->id . '" ' . $attr_selected . '>' . $area->area . '</option>';
            }

            /* LOOP QUE BUSCA OS INSTRUMENTO */

            $options_instrumentos[] = '<option value="0">Selecione um instrumento</option>';
            $instrumentos = Instrumentos::get();
            foreach ($instrumentos as $instrmento) {
                $attr_selected = ($aluno->instrumento_id == $instrmento->id) ? 'selected' : '';
                $options_instrumentos[] = '<option value="' . $instrmento->id . '" ' . $attr_selected . '>' . $instrmento->instrumento . '</option>';
            }

            $attr_disabled_instrumento = ($aluno->area_id != 3) ? 'disabled' : '';

            @list($ano_n, $mes_n, $dia_n) = @explode('-', $aluno->data_nascimento);
            @list($ano_p, $mes_p, $dia_p) = @explode('-', $aluno->primeira_data_praca);

            if (!@checkdate($mes_n, $dia_n, $ano_n)) {
                $data_nascimento = '';
            } else {
                $data_nascimento = $dia_n . '/' . $mes_n . '/' . $ano_n;
            }

            if (!@checkdate($mes_p, $dia_p, $ano_p)) {
                $data_praca = '';
            } else {
                $data_praca = $dia_p . '/' . $mes_p . '/' . $ano_p;
            }

            if ($aluno->atleta_marexaer == 'S') {
                $option_atleta_marexaer = '<option value="S" selected>Sim</option><option value="N">Não</option>';
            } else {
                $option_atleta_marexaer = '<option value="S">Sim</option><option value="N" selected>Não</option>';
            }


            $data['header'] = '<i class="ion-android-create" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Editar cadastro do aluno';
            $data['body'] = '   <div class="alert alert-danger errors-editar-cadastro-aluno" role="alert"></div>
                                        <form id="atualizar_cadastro_aluno">
                                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                                            <div style="margin: 14px auto; width: 80%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Número do aluno">
                                                <div style="float: left;">
                                                    <i class="ion-pricetag" style="font-size: 24px; color: #696969;"></i>
                                                </div>
                                                <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                    <input class="no-style" value="' . $aluno->numero . '" name="numero" type="text" autocomplete="off" maxlength="5" placeholder="Número do aluno" style="width: 100%;" />
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div style="margin: 14px auto; width: 80%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Nome completo">
                                                <div style="float: left;">
                                                    <i class="ion-android-person" style="font-size: 24px; color: #696969;"></i>
                                                </div>
                                                <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                    <input class="no-style" value="' . $aluno->nome_completo . '" name="nome_completo" type="text" autocomplete="off" placeholder="Nome completo" style="width: 100%;" />
                                                </div>
                                                <div class="clear"></div>
                                            </div>
        
                                            <div style="margin: 14px auto; width: 80%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Nome de guerra">
                                                <div style="float: left;">
                                                    <i class="ion-android-contact" style="font-size: 24px; color: #696969;"></i>
                                                </div>
                                                <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                    <input class="no-style" value="' . $aluno->nome_guerra . '" name="nome_guerra" autocomplete="off" placeholder="Nome de guerra" style="width: 100%;" />
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div style="margin: 18px auto; width: 80%; max-width: 380px;">
                                                <div style="float: left; margin-top: 3px;">
                                                    <i class="ion-compose" style="font-size: 24px; color: #696969;"></i>
                                                </div>
                                                <div style="float:right; width: 93%;">
                                                    <select class="custom-select" name="sexo">' . implode('', $options_segemento) . '</select>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div style="margin: 18px auto; width: 80%; max-width: 380px;">
                                                <div style="float: left; margin-top: 3px;">
                                                <i class="ion-ios-book" style="font-size: 24px; color: #696969;"></i>
                                                </div>
                                                <div style="float:right; width: 93%;">
                                                    <select class="custom-select" name="turma">' . implode('', $options_turmas) . '</select>
                                                </div>
                                                <div class="clear"></div>
                                            </div>                                        

                                            <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Data de nascimento">
                                                <div style="float: left;">
                                                    <i class="ion-ios-star" style="font-size: 24px; color: #696969;"></i>
                                                </div>
                                                <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                    <input class="no-style data_mask" value="' . $data_nascimento . '" style="width: 100%;" name="data_nascimento" type="text" value="" maxlength="10" autocomplete="off" placeholder="Data de nascimento (DD/MM/AAAA)" />
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Atleta da Marexaer">
                                                <div style="float: left;">
                                                    <i class="ion-android-walk" style="font-size: 24px; color: #696969;"></i>
                                                </div>
                                                <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                    <select class="custom-select" name="atleta_marexaer">
                                                        ' . $option_atleta_marexaer . '
                                                    </select>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                                                                    
                                            <div style="margin: 18px auto; width: 80%; max-width: 380px;">
                                                <div style="float: left; margin-top: 3px;">
                                                    <i class="ion-ios-home" style="font-size: 24px; color: #696969;"></i>
                                                </div>
                                                <div style="float:right; width: 93%;">
                                                    <select class="custom-select" name="omcts_id">' . implode('', $options_omcts) . '</select>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div style="margin: 18px auto; width: 80%; max-width: 380px;">
                                                <div style="float: left; margin-top: 3px;">
                                                    <i class="ion-pinpoint" style="font-size: 24px; color: #696969;"></i>
                                                </div>
                                                <div style="float:right; width: 93%;">
                                                    <select class="custom-select" name="area" onchange="if($(this).val()==3){ $(\'select#instrumento\').prop(\'disabled\', false); } else { $(\'select#instrumento\').prop(\'disabled\', true);}">' . implode('', $options_areas) . '</select>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div style="margin: 18px auto; width: 80%; max-width: 380px;">
                                                <div style="float: left; margin-top: 3px;">
                                                    <i class="ion-music-note" style="font-size: 24px; color: #696969;"></i>
                                                </div>
                                                <div style="float:right; width: 93%;">
                                                    <select class="custom-select" id="instrumento" name="instrumento" ' . $attr_disabled_instrumento . '>' . implode('', $options_instrumentos) . '</select>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Data de praça anterior SFC">
                                                <div style="float: left;">
                                                    <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                                </div>
                                                <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                    <input class="no-style data_mask" value="' . $data_praca . '"  style="width: 100%;" name="data_praca" type="text" value="" maxlength="10" autocomplete="off" placeholder="Data de praça anterior SFC (DD/MM/AAAA)" />
                                                </div>
                                                <div class="clear"></div>
                                            </div>
        
                                            <div style="margin: 14px auto; width: 80%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Email">
                                                <div style="float: left;">
                                                    <i class="ion-email" style="font-size: 24px; color: #696969;"></i>
                                                </div>
                                                <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                                    <input class="no-style" value="' . $aluno->email . '" name="email" type="text" maxlength="60" autocomplete="off" placeholder="Email" style="width: 100%;" />
                                                </div>
                                                <div class="clear"></div>
                                            </div>';

            $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Cancelar
                                </button>
                                <button type="button" class="btn btn-primary" onclick="AtualizarCadastroAluno(this, ' . $aluno->id . ');">
                                    Atualizar
                                </button>                            
                                <script>
                                    $(\'.data_mask\').mask(\'00/00/0000\');
                                    $(function () {
                                        $(\'[data-toggle="tooltip"]\').tooltip();
                                      });
                                </script>';
        } else {
            $data['header'] = '<i class="ion-person-add" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> EDITAR CADASTRO DO ALUNO';
            $data['body'] = '<div style="padding: 16px; text-align: center;">CADASTRO NÃO LOCALIZADO</div>';
            $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Fechar
                                </button>';
        }

        return $data;
    }

    public function AtualizarCadastroAluno(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request)
    {

        @list($dia_n, $mes_n, $ano_n) = @explode('/', $request->data_nascimento);
        @list($dia_p, $mes_p, $ano_p) = @explode('/', $request->data_praca);

        if ($request->numero != '' && !is_numeric($request->numero)) {
            $error[] = 'Se preenchido, campo número deverá conter somente numeros.';
        }

        if (strlen($request->nome_completo) < 5) {
            $error[] = 'Nome parece muito curto. Informe o nome completo.';
        }

        if (strlen($request->nome_guerra) < 2) {
            $error[] = 'Nome de guerra inválido.';
        }

        if ($request->sexo != 'M' && $request->sexo != 'F') {
            $error[] = 'Selecione um segmento.';
        }

        if (isset($request->omcts_id) && $request->omcts_id == 0) {
            $error[] = 'Informe a omct designada';
        }

        if ($request->turma == 0) {
            $turma_request = null;
        } else {
            $turma_request = $request->turma;
        }

        if (!@checkdate($mes_n, $dia_n, $ano_n)) {
            $error[] = 'Data de nascimento inválida';
        } else {
            $data_nascimento = $ano_n . '-' . $mes_n . '-' . $dia_n;
        }

        if (!@checkdate($mes_p, $dia_p, $ano_p)) {
            $data_praca = null;
        } else {
            $data_praca = $ano_p . '-' . $mes_p . '-' . $dia_p;
        }

        if ($request->area == 0) {
            $error[] = 'Selecione a área.';
        }

        if ($request->area == 3 && $request->instrumento == 0) {
            $error[] = 'Alunos da área de música devem possuir um instrumento';
        }

        if (OwnValidator::ValidarEmail($request->email)) {
            $email = $request->email;
        } else {
            $error[] = 'E-mail informado é inváido.';
        }

        if (isset($error)) {
            $data['status'] = 'err';
            $data['response'] = implode('<br />', $error);
        } else {

            $aluno = Alunos::find($request->id);

            $user = User::where('email', $aluno->email)->first();

            $aluno->numero = $request->numero;
            $aluno->nome_completo = $request->nome_completo;
            $aluno->nome_guerra = $request->nome_guerra;
            $aluno->sexo = $request->sexo;
            $aluno->atleta_marexaer = $request->atleta_marexaer;
            $aluno->omcts_id = $request->omcts_id;
            $aluno->turma_id = $turma_request;
            $aluno->data_nascimento = $data_nascimento;
            $aluno->primeira_data_praca = $data_praca;
            $aluno->area_id = $request->area;
            $aluno->instrumento_id = $request->instrumento;
            $aluno->email = $email;

            $user->email = $email;

            // RECALCULANDO A NOTA DO TFM CASO ESTA JA ESTJA LANÇADA

            $avaliacoaTaf_update = AvaliacaoTaf::where('aluno_id', $request->id)->first();

            if ($avaliacoaTaf_update) {

                // SER FOR DA ÁREA (BANDA/MÚSICA)

                if ($avaliacoaTaf_update->aluno->area_id == 3) {
                    $razao = 2;
                } else {
                    $razao = 3;
                }

                // VERIFICANDO SE É ATLETA

                /*
                
                    CASO SEJA ATLETA APLICA A REGRA DE ACRESCIMO DE PONTOS 

                */

                $media = (($avaliacoaTaf_update->corrida_nota + $avaliacoaTaf_update->flexao_braco_nota + $avaliacoaTaf_update->flexao_barra_nota) / $razao);

                if ($aluno->atleta_marexaer == 'S') {
                    if ($media >= 5 && $media <= 6.999) {
                        $media = $media + 1;
                    } else if ($media > 6.999) {
                        $media = $media + 2;
                    }

                    $media = ($media > 10) ? 10 : $media;
                }

                if ($request->suficiencia_abdominal == 'NS' && $media >= 5) {
                    $reprovado = 'S';
                } else if ($request->suficiencia_abdominal == 'S' && $media >= 5) {
                    $reprovado = 'N';
                } else {
                    $reprovado = 'S';
                }

                $avaliacoaTaf_update->media = number_format($media, '3', '.', '');
                $avaliacoaTaf_update->reprovado = $reprovado;
                $avaliacoaTaf_update->save();
            }

            if ($aluno->save() && $user->save()) {
                if ($aluno->email == null) {
                    $pendencias[] = 'Email não informado';
                }
                if ($aluno->turma_id == null) {
                    $pendencias[] = 'Turma não informada';
                }
                if ($aluno->area_id == null) {
                    $pendencias[] = 'Área não informada';
                }

                if (isset($pendencias)) {
                    $pendencia_cel = '<td data-toggle="tooltip" data-placement="top" title="' . implode(', ', $pendencias) . '" style="color: #DF0101; text-align: center; font-size: 18px;"><i class="ion-ios-minus-outline"></i></td>';
                    unset($pendencias);
                } else {
                    $pendencia_cel = '<td style="color: #0B610B; text-align: center; font-size: 18px;"><i class="ion-ios-checkmark-outline"></i></td>';
                }

                $turma = ($aluno->turma->turma) ?? 'Não informada';

                $sexo['M'] = 'Masculino';
                $sexo['F'] = 'Feminino';

                $alterar_situacao_diversa = ($ownauthcontroller->PermissaoCheck(11)) ? '<a href="javascript: void(0);" class="no-style" onclick="DialogAdicionarAluSitDivNovo(' . $aluno->id . ');" title="Incluir aluno na lista de alunos em situações diversas."> <i class="ion-forward" style="font-size: 24px;"></i> </a>' : '';

                $data['status'] = 'ok';
                $data['tr_aluno'] = '   <td>' . $aluno->numero . '</td>
                                        <td>' . $aluno->nome_guerra . '</td>
                                        <td>' . $aluno->nome_completo . '</td>
                                        <td>' . $sexo[$aluno->sexo] . '</td>
                                        <td>' . $turma . '<br />' . $aluno->area->area . '</td>                                        
                                        ' . $pendencia_cel . '
                                        <td class="botao-action-table" style="text-align: center;">
                                            <a href="javascript: void(0);" class="no-style" onclick="dialogInfoUser(' . $aluno->user->id . ', \'aluno\');" title="Detalhes do operador"> <i class="ion-information-circled"></i> </a>
                                            <a href="javascript: void(0);" class="no-style" onclick="dialogEditarCadastroAluno(' . $aluno->id . ', \'byID\');" title="Editar configurações do operador"> <i class="ion-android-create"></i> </a>
                                            ' . $alterar_situacao_diversa . '
                                        </td>            
                                        <script>
                                            $(function () {
                                                $(\'[data-toggle="tooltip"]\').tooltip();
                                            });
                                        </script>';
            } else {
                $data['status'] = 'err';
                $data['response'] = 'Não foi possível realizar a atualização.';
            }
        }

        $this->classLog->RegistrarLog('Editou cadastro de aluno', auth()->user()->email);
        return $data;
    }

    public function OpcoesdeListagemSelecaoAlunos(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request)
    {

        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();

        if ($ownauthcontroller->PermissaoCheck(1) && isset($request->anos_de_formacao) && $request->anos_de_formacao == 0) {
            $error[] = 'Pelo menos um ano de formação deve ser selecionado';
        } else if (!isset($request->anos_de_formacao)) {
            $id_ano_formacao = ($ano_corrente->id) ?? 0;
        } else {
            $id_ano_formacao = $request->anos_de_formacao;
        }

        if ($ownauthcontroller->PermissaoCheck(1) && !isset($request->omcts)) {
            $error[] = 'Pelo menos uma UETE deve ser selecionada';
        } else if ($ownauthcontroller->PermissaoCheck(1)) {
            $omcts = $request->omcts;
        } else {
            $omcts = array(session()->get('login.omctID'));
        }

        if (!isset($request->segmento)) {
            $error[] = 'Pelo menos um dos segmentos deve ser selecionado';
        } else {
            $segmento = $request->segmento;
        }

        if (!isset($request->areas)) {
            $error[] = 'Pelo menos uma das áreas deve ser selecionada';
        } else {
            $areas = $request->areas;
        }

        if (isset($error)) {
            $data['status'] = 'err';
            $data['response'] = implode('<br />', $error);
        } else {
            $alunos = Alunos::whereIn('omcts_id', $omcts)->where('data_matricula', $id_ano_formacao)
                ->whereIn('sexo', $segmento)
                ->whereIn('area_id', $areas)
                ->orderBy('sexo', 'asc')
                ->orderBy('numero', 'asc')->get();
            $omctsNome = OMCT::get();
            foreach ($omctsNome as $val) {
                $omctNome[$val->id] = $val->omct;
            }

            $sexo['M'] = 'Masculino';
            $sexo['F'] = 'Feminino';

            foreach ($omcts as $omct) {

                foreach ($alunos as $aluno) {
                    if ($aluno->omcts_id == $omct) {

                        /* BUSCANDO POR PENDÊNCIAS */

                        if ($aluno->email == null) {
                            $pendencias[] = 'Email não informado';
                        }
                        if ($aluno->turma_id == null) {
                            $pendencias[] = 'Turma não informada';
                        }
                        if ($aluno->area_id == null || $aluno->area_id == 5) {
                            $pendencias[] = 'Área não informada';
                        }

                        if (isset($pendencias)) {
                            $pendencia_cel = '<td data-toggle="tooltip" data-placement="top" title="' . implode(', ', $pendencias) . '" style="color: #DF0101; text-align: center; font-size: 18px;"><i class="ion-ios-minus-outline"></i></td>';
                            unset($pendencias);
                        } else {
                            $pendencia_cel = '<td style="color: #0B610B; text-align: center; font-size: 18px;"><i class="ion-ios-checkmark-outline"></i></td>';
                        }

                        $turma = ($aluno->turma->turma) ?? 'Não informada';

                        /* VERIFICA SE OPERADOR TEM PERMISSÃO DE ALTERAR SITUAÇÃO DIVERSA DE ALUNO */

                        $alterar_situacao_diversa = ($ownauthcontroller->PermissaoCheck(11)) ? '<a href="javascript: void(0);" class="no-style" onclick="DialogAdicionarAluSitDivNovo(' . $aluno->id . ');" title="Incluir aluno na lista de alunos em situações diversas."> <i class="ion-forward" style="font-size: 24px;"></i> </a>' : '';
                        $remover_aluno = ($ownauthcontroller->PermissaoCheck(9)) ? '<a href="javascript: void(0);" class="no-style" onclick="dialogRemoverAluno(' . $aluno->id . ');" title="Remover aluno definitivamente do sistema"> <i class="ion-android-delete" style="font-size: 22px;"></i> </a>' : '';

                        $lista[] = '    <tr id="alunoID_' . $aluno->id . '">
                                            <td>' . $aluno->numero . '</td>
                                            <td>' . $aluno->nome_guerra . '</td>
                                            <td>' . $aluno->nome_completo . '</td>
                                            <td>' . $sexo[$aluno->sexo] . '</td>
                                            <td>' . $turma . '<br />' . $aluno->area->area . '</td>
                                            ' . $pendencia_cel . '
                                            <td class="botao-action-table" style="text-align: center;">
                                                <a href="javascript: void(0);" class="no-style" onclick="dialogInfoUser(' . $aluno->user->id . ', \'aluno\');" title="Detalhes do aluno"> <i class="ion-information-circled"></i> </a>
                                                <a href="javascript: void(0);" class="no-style" onclick="loadAdminAjaxContent(\'admin/aluno/' . $aluno->id . '\');" title="Editar cadastro do aluno"> <i class="ion-android-create"></i> </a>
                                                <!--<a href="javascript: void(0);" class="no-style" onclick="dialogEditarCadastroAluno(' . $aluno->id . ', \'byID\');" title="Editar cadastro do aluno"> <i class="ion-android-create"></i> </a>-->
                                                ' . $alterar_situacao_diversa . '
                                                ' . $remover_aluno . '
                                            </td>
                                        </tr>';
                    }
                }

                if (isset($lista)) {
                    $table[] = '<div style="text-align: center; margin-top: -32px">
                                    <h4 style="color: #696969;">' . $omctNome[$omct] . '</h4>
                                </div>';
                    $table[] = '<table class="table table-striped" style="margin: 60px 0 90px 0;">';
                    $table[] = '<thead>
                                    <tr>
                                        <th>NR</th>
                                        <th>NOME DE GUERRA</th>
                                        <th>NOME COMPLETO</th>
                                        <th>SEGMENTO</th>
                                        <th>TURMA/ÁREA</th>
                                        <th style="text-align: center;">PENDÊNCIAS</th>
                                        <th style="text-align: center; width: 150px;">AÇÃO</th>
                                    </tr>';
                    $table[] = implode('', $lista);
                    $table[] = '</table>';
                    unset($lista);
                } else {
                    $table[] = '<div style="text-align: center;">
                                    <h4 style="color: #696969;">' . $omctNome[$omct] . '</h4>
                                    <div style="margin: 20px 0 60px 0;">
                                        Nenhuma ocorrência no filtro aplicado
                                    </div>
                                </div>';
                }
            }

            $data['status'] = 'ok';
            $data['response'] = implode('', $table) . '
            <script>
                $(function () {
                    $(\'[data-toggle="tooltip"]\').tooltip();
                });
            </script>';
        }

        return $data;
    }

    public function LoadAlunosSitDiv(\App\Http\Controllers\OwnAuthController $ownauthcontroller)
    {
        if ($ownauthcontroller->PermissaoCheck(1)) {
            $alunos = AlunosSitDiv::orderBy('data_matricula', 'desc')->orderBy('omcts_id', 'desc')->get();
            $th_omct = '<th>OMCT</th>';
        } else {
            $alunos = AlunosSitDiv::orderBy('data_matricula', 'desc')->where('omcts_id', session()->get('login.omctID'))->get();
            $th_omct = '';
        }

        $th_acao = ($ownauthcontroller->PermissaoCheck(11)) ? '<th style="text-align: center;">AÇÃO</th>' : '';

        if (count($alunos) > 0) {

            $table[] =  '  <table class="table table-striped" style="margin: 60px 0 90px 0;">
                                <thead>
                                    <tr>
                                        <th>NÚMERO</th>
                                        <th>NOME DE GUERRA</th>
                                        <th>NOME COMPLETO</th>
                                        ' . $th_omct . '
                                        <th style="text-align: center;">ANO DE FORMAÇÃO</th>
                                        <th style="width: 360px;">SITUAÇÃO</th>
                                        ' . $th_acao . '
                                    </tr>
                                </thead>';

            foreach ($alunos as $aluno) {
                $observacao = ($aluno->situacoes_diversas_obs != '') ? ': ' . $aluno->situacoes_diversas_obs : '';
                $table[] = '<tr id="alunoID_' . $aluno->id . '">
                                <td>' . $aluno->numero . '</td>
                                <td>' . $aluno->nome_guerra . '</td>
                                <td>' . $aluno->nome_completo . '</td>';
                $table[] = ($ownauthcontroller->PermissaoCheck(1)) ? '<td>' . $aluno->omct->sigla_omct . '</td>' : '';
                $table[] = '<td style="text-align: center;">' . $aluno->ano_formacao->formacao . '</td>';
                $table[] = '<td id="situacao_alunoID_' . $aluno->id . '"><span style="color: #B40404;"><b>' . $aluno->situacao->situacao . '</b></span>' . $observacao . '</td>';
                $table[] = ($ownauthcontroller->PermissaoCheck(11)) ? '<td style="text-align: center; font-size: 18px;"><a href="javascript: void(0);" class="no-style" onclick="dialogEditarCadastroAlunoSitDivNovo(' . $aluno->id . ');" title="Alterar situação do aluno"> <i class="ion-android-create"></i> </a></td>' : '';
                $table[] = '</tr>';
            }

            $table[] = '</table>';

            $data['response'] = implode('', $table);
        } else {
            $data['response'] = '   <div class="box-registro-not-found">
                                        <i class="ion-social-snapchat-outline" style="font-size: 32px"></i><br />
                                        <span style="color: brown">Não há registro de alunos em situações diversas</span>
                                    </div>';
        }

        return $data;
    }

    public function DialogAdicionarAluSitDiv(Request $request)
    {

        $sit_div = SituacoesDiversas::orderBy('id', 'asc')->get();
        $aluno = Alunos::find($request->id);
        $aluno_seg = ($aluno->sexo == 'M') ? 'enquadrado o ALUNO' : 'enquadrada a ALUNA';
        foreach ($sit_div as $situacao) {
            $sit[] = '  <div class="custom-control custom-radio">
                            <input type="radio" id="situacao_' . $situacao->id . '" name="sitDivID" class="custom-control-input" value="' . $situacao->id . '">
                            <label class="custom-control-label" for="situacao_' . $situacao->id . '">' . $situacao->situacao . '</label>
                        </div>';
        }


        $data['header'] = '<i class="ion-forward" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Incluir em situação diversa';
        $data['body'] = '   <div class="alert alert-danger error_aluno_situacao_diversa" role="alert"></div>
                            <div style="width: 80%; margin: 18px auto; color: #696969; font-size: 16px;"><b>Selecione em qual situação estará ' . $aluno_seg . ' ' . $aluno->nome_guerra . ':</b></div>
                            <form id="incluir_aluno_situacao_diversa">
                                <div style="width: 60%; margin: 6px auto;">
                                    <input type="hidden" name="_token" value="' . csrf_token() . '" />
                                    <input type="hidden" name="alunoID" value="' . $request->id . '" />
                                    <div style="margin: 36px auto; width: 80%; max-width: 380px;">
                                        ' . implode('', $sit) . '
                                    </div>
                                </div>
                                <div style="margin: 2px auto; width: 80%; max-width: 380px;">
                                    <div style="float: left; margin-top: 6px;">
                                        <i class="ion-chatbubble-working" style="font-size: 24px; color: #696969;"></i>
                                    </div>
                                    <div style="float: right; width: 93%; margin-top: 6px; padding: 0 0 10px 6px; ">
                                        <textarea class="form-control" id="observacao" name="observacao" rows="3" placeholder="Observações pertinentes a alteração"></textarea>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </form>';

        $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="AdicionarAluSitDiv(this, ' . $request->id . ');">
                                Salvar alteração
                            </button>';

        return $data;
    }

    public function notaDisc()
    {
        $disciplina = Disciplinas::find(3);
        return $disciplina->getNotasAluno(960);
    }

    public function AdicionarAluSitDiv(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request)
    {

        if ($ownauthcontroller->PermissaoCheck(11)) {
            if (isset($request->sitDivID)) {

                $aluno = Alunos::find($request->id);
                $alunoSitDiv = new AlunosSitDiv;
                $observacao = ($request->observacao == '') ? '' : $request->observacao;

                $alunoSitDiv->id = $aluno->id;
                $alunoSitDiv->numero = $aluno->numero;
                $alunoSitDiv->nome_completo = $aluno->nome_completo;
                $alunoSitDiv->nome_guerra = $aluno->nome_guerra;
                $alunoSitDiv->data_nascimento = $aluno->data_nascimento;
                $alunoSitDiv->data_matricula = $aluno->data_matricula;
                $alunoSitDiv->primeira_data_praca = $aluno->primeira_data_praca;
                $alunoSitDiv->turma_id = $aluno->turma_id;
                $alunoSitDiv->omcts_id = $aluno->omcts_id;
                $alunoSitDiv->area_id = $aluno->area_id;
                $alunoSitDiv->sexo = $aluno->sexo;
                $alunoSitDiv->email = $aluno->email;
                $alunoSitDiv->situacoes_diversas_id = $request->sitDivID;
                $alunoSitDiv->situacoes_diversas_obs = $observacao;

                if ($alunoSitDiv->save()) {

                    // DADOS A SEREM INSERIDOS NA TABELA alunos_situacoes_diversas_historico

                    $volunt_av = (AlunosVoluntAv::where('alunos_id', $aluno->id)->count() > 0) ? 'S' : 'N';


                    $add_historico = new AlunosSitDivHistorico;
                    $add_historico->aluno = $aluno->nome_completo;
                    $add_historico->aluno_id = $aluno->id;
                    $add_historico->omct_id = $aluno->omcts_id;

                    //Original Julião
                    /*$aluno_data['cadastro'] = array(
                        "numero" => $alunoSitDiv->numero,
                        "nome_completo" => $alunoSitDiv->nome_completo,
                        "nome_guerra" => $alunoSitDiv->nome_guerra,
                        "data_nascimento" => $alunoSitDiv->data_nascimento,
                        "data_matricula" => $alunoSitDiv->ano_formacao->data_matricula,
                        "ano_formacao" => $alunoSitDiv->ano_formacao->formacao,
                        "turma" => $aluno->turma->turma,
                        "omct" => $alunoSitDiv->omct->sigla_omct,
                        "area" => $alunoSitDiv->area->area,
                        "sexo" => $alunoSitDiv->sexo,
                        "email" => $alunoSitDiv->email,
                        "atleta" => $aluno->atleta_marexaer,
                        "voluntario_aviacao" => $volunt_av
                    );*/

                    foreach ($aluno->getAttributes() as $key => $value) {
                        $aluno_data['cadastro'][$key] = $value;
                    }
                    $aluno_data['cadastro']['ano_formacao'] = $alunoSitDiv->ano_formacao->formacao;
                    $aluno_data['cadastro']['turma'] = $aluno->turma->turma;
                    $aluno_data['cadastro']['omct'] = $alunoSitDiv->omct->sigla_omct;
                    $aluno_data['cadastro']['area'] = $alunoSitDiv->area->area;
                    $aluno_data['cadastro']['atleta'] = $aluno->atleta_marexaer;
                    $aluno_data['cadastro']['voluntario_aviacao'] = $volunt_av;

                    // SELECIONANDO TODAS AS DISCIPLINAS DO ANO DE FORMAÇÃO EM QUESTÃO

                    $disciplinas = Disciplinas::where('ano_formacao_id', $alunoSitDiv->data_matricula)->get();

                    foreach ($disciplinas as $item) {
                        $aluno_data['avaliacoes'][] = $item->getNotasAluno($aluno->id);
                    }

                    $notas_taf = AvaliacaoTaf::where('aluno_id', $aluno->id)->first();

                    if ($notas_taf) {
                        $aluno_data['avaliacoes']['taf'] = array(
                            "corrida" => $notas_taf->corrida_nota,
                            "flex_bra" => $notas_taf->flexao_braco_nota,
                            "flex_bar" => $notas_taf->flexao_barra_nota,
                            "abdom" => $notas_taf->abdominal_suficiencia,
                            "ND" => $notas_taf->media,
                        );
                    } else {
                        $aluno_data['avaliacoes']['taf'] = null;
                    }

                    $aluno_data['observacoes']['titulo'] = $alunoSitDiv->situacao->situacao;
                    $aluno_data['observacoes']['content'] = $observacao;

                    $add_historico->data = serialize($aluno_data);

                    $add_historico->save();


                    /* AQUI EU ATUALIZO TODAS AS OS CAMPOS alunos_situacoes_diversas_id NAS TABELAS QUE CONTÉM INFORMAÇÕES DO RESPECTIVO ALUNO */

                    \App\Models\AlunosNFEI::where('alunos_id', $aluno->id)->update(['alunos_situacoes_diversas_id' => $aluno->id]);
                    \App\Models\AlunosVoluntAv::where('alunos_id', $aluno->id)->update(['alunos_situacoes_diversas_id' => $aluno->id]);
                    \App\Models\AvaliacoesNotas::where('alunos_id', $aluno->id)->update(['alunos_situacoes_diversas_id' => $aluno->id]);

                    if ($aluno->delete()) {
                        $data['status'] = 'ok';
                        $data['response'] = '<td colspan="7" style="text-align: center; background-color: #BCF5A9; color: #696969;"><b>' . $aluno->nome_completo . ' agora está enquadrado em situações diversas. Clique na aba correspondente para editar.</b></td>';
                    } else {
                        $data['status'] = 'err';
                        $data['response'] = 'Houve um erro ao mover o aluno!';
                    }
                } else {
                    $data['status'] = 'err';
                    $data['response'] = 'Houve um erro ao mover o aluno!';
                }
            } else {
                $data['status'] = 'err';
                $data['response'] = 'Selecione uma situação para qual deseja transferir o aluno';
            }
        } else {
            $data['status'] = 'not_authorized';
        }
        $this->classLog->RegistrarLog('Adicionou aluno em lista de situações diversas', auth()->user()->email);
        return $data;
    }

    function DialogEditarCadastroAlunoSitDiv(Request $request)
    {
        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $sit_div = SituacoesDiversas::orderBy('id', 'asc')->get();
        $aluno = AlunosSitDiv::find($request->id);
        $aluno_seg = ($aluno->sexo == 'M') ? 'enquadrado o ALUNO' : 'enquadrada a ALUNA';
        foreach ($sit_div as $situacao) {
            $input_check = ($aluno->situacoes_diversas_id == $situacao->id) ? 'checked' : '';
            $sit[] = '  <div class="custom-control custom-radio">
                            <input type="radio" id="situacao_' . $situacao->id . '" name="sitDivID" class="custom-control-input" value="' . $situacao->id . '" ' . $input_check . ' />
                            <label class="custom-control-label" for="situacao_' . $situacao->id . '">' . $situacao->situacao . '</label>
                        </div>';
        }

        $sit[] = '  <div class="custom-control custom-radio">
                        <input type="radio" id="rematricular" name="sitDivID" class="custom-control-input" value="rematricular" />
                        <label class="custom-control-label" for="rematricular"><b>REMATRICULAR </b>(formação: ' . $ano_corrente->formacao . ')</label>
                    </div>';


        $data['header'] = '<i class="ion-forward" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Incluir em situação diversa';
        $data['body'] = '   <div class="alert alert-danger error_aluno_situacao_diversa" role="alert"></div>
                            <div style="width: 82%; margin: 18px auto; color: #696969; font-size: 16px;"><b>Atualize SFC a situação que se encontra ' . $aluno_seg . ' ' . $aluno->nome_guerra . ':</b></div>
                            <form id="atualizar_aluno_situacao_diversa">
                                <div style="width: 70%; margin: 6px auto;">
                                    <input type="hidden" name="_token" value="' . csrf_token() . '" />
                                    <input type="hidden" name="alunoID" value="' . $request->id . '" />
                                    <div style="margin: 36px auto; width: 80%; max-width: 380px;">
                                        ' . implode('', $sit) . '
                                    </div>
                                </div>
                                <div id="tip_rematricular" class="alert alert-warning" role="alert" style="margin-top: -14px; margin-bottom: 12px;">
                                    <strong>ATENÇÃO:</strong> Ao rematricular este aluno todas suas informações de escolhas e notas serão redefinidas.
                                </div>
                                <div id="textarea_observacao" style="margin: 2px auto; width: 80%; max-width: 380px;">
                                    <div style="float: left; margin-top: 6px;">
                                        <i class="ion-chatbubble-working" style="font-size: 24px; color: #696969;"></i>
                                    </div>
                                    <div style="float: right; width: 93%; margin-top: 6px; padding: 0 0 10px 6px; ">
                                        <textarea class="form-control" id="observacao" name="observacao" rows="3" placeholder="Observações pertinentes a alteração">' . $aluno->situacoes_diversas_obs . '</textarea>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </form>';

        $data['footer'] = ' <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="AtualizarAluSitDiv(this, ' . $request->id . ');">
                                Salvar alteração
                            </button>
                            <script>
                                $(\'input[type="radio"]\').click(function(){
                                    if($(this).val()=="rematricular"){
                                        $("div#tip_rematricular").slideDown(160);
                                        $("div#textarea_observacao").slideUp(100);    
                                    } else {
                                        $("div#tip_rematricular").slideUp(100);
                                        $("div#textarea_observacao").slideDown(160);                                        
                                    }    
                                });   
                            </script>';

        return $data;
    }

    public function AtualizarAluSitDiv(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request)
    {
        if ($request->sitDivID != 'rematricular') {
            if ($ownauthcontroller->PermissaoCheck(11)) {

                /* PREPARANDO OS DADOS PARA FAZER A ATUALIZAÇÃO */

                $aluno = AlunosSitDiv::find($request->id);
                $observacao = ($request->observacao != '') ? $request->observacao : '';
                \App\Models\AlunosSitDiv::where('id', $request->id)->update(['situacoes_diversas_id' => $request->sitDivID, 'situacoes_diversas_obs' => $observacao]);

                $aluno = AlunosSitDiv::find($request->id);
                $observacao = ($aluno->situacoes_diversas_obs != '') ? ': ' . $aluno->situacoes_diversas_obs : '';

                // ATUALIZANDO TABELA alunos_voluntarios_aviacao

                $aluno_historico = AlunosSitDivHistorico::where('aluno_id', $aluno->id)->first();

                $aluno_data = unserialize($aluno_historico->data);

                $aluno_data['observacoes']['titulo'] = $aluno->situacao->situacao;
                $aluno_data['observacoes']['content'] = $observacao;

                $aluno_historico->data = serialize($aluno_data);
                $aluno_historico->save();

                $data['response'] = '<span style="color: #B40404;"><b>' . $aluno->situacao->situacao . '</b></span>' . $observacao;
                $data['status'] = 'ok';
                $data['rematricula'] = 'n';
            } else {
                $data['status'] = 'err';
                $data['response'] = 'Você não tem permissão para atualizar situações diversas de alunos';
            }
        } else {

            /* MIGRANDO TODOS OS DADOS PARA A TABELA ALUNOS */

            $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();

            $alunoSitDiv = AlunosSitDiv::find($request->id);
            $aluno = new Alunos;

            $aluno->id = $request->id;
            $aluno->numero = $alunoSitDiv->numero;
            $aluno->nome_completo = $alunoSitDiv->nome_completo;
            $aluno->nome_guerra = $alunoSitDiv->nome_guerra;
            $aluno->data_nascimento = $alunoSitDiv->data_nascimento;
            $aluno->data_matricula = $ano_corrente->id;
            $aluno->primeira_data_praca = $alunoSitDiv->primeira_data_praca;
            $aluno->turma_id = $alunoSitDiv->turma_id;
            $aluno->omcts_id = $alunoSitDiv->omcts_id;
            $aluno->area_id = $alunoSitDiv->area_id;
            $aluno->sexo = $alunoSitDiv->sexo;
            $aluno->email = $alunoSitDiv->email;

            $user = new User;

            $user->email = $alunoSitDiv->email;
            $user->password = $alunoSitDiv->email;

            // $user->save();

            if ($aluno->save()) {
                \App\Models\AlunosNFEI::where('alunos_situacoes_diversas_id', $aluno->id)->delete();
                \App\Models\AlunosVoluntAv::where('alunos_situacoes_diversas_id', $aluno->id)->delete();
                \App\Models\AvaliacoesNotas::where('alunos_situacoes_diversas_id', $aluno->id)->delete();
                \App\Models\AvaliacaoTaf::where('aluno_id', $aluno->id)->delete();
                if ($alunoSitDiv->delete()) {
                    $data['rematricula'] = 'y';
                    $data['status'] = 'ok';
                    $data['response'] = '<td colspan="7" style="text-align: center; background-color: #BCF5A9; color: #696969;"><b>' . $aluno->nome_completo . ' agora está rematriculado no ' . $aluno->omct->sigla_omct . '. Clique na aba correspondente para editar.</b></td>';
                } else {
                    $data['status'] = 'err';
                    $data['response'] = 'Houve um erro ao mover o aluno!';
                }
            }
        }
        $this->classLog->RegistrarLog('Atualizou informações de alunos em situações diversas', auth()->user()->email);
        return $data;
    }

    public function ViewLancamentos(\App\Http\Controllers\OwnAuthController $ownauthcontroller)
    {
        return view('lancamentos.lancamentosIndex')->with('ownauthcontroller', $ownauthcontroller);
    }

    public function ViewSelecaoUeteAluno(Request $request)
    {
        $uetes = FuncoesController::retornaUetePerfil($this->ownauthcontroller);

        $anoFormacao = AnoFormacao::whereId($request->id_ano_formacao)->get()->first();

        $rota = $request->path();

        return view('admin.consulta.consulta-uete-aluno', compact('uetes', 'anoFormacao', 'rota'))
            ->with('ownauthcontroller', $this->ownauthcontroller);
    }

    public function ViewSelecaoUeteAlunoPunicao(Request $request)
    {
        $uetes = FuncoesController::retornaUetePerfil($this->ownauthcontroller);

        $anoFormacao = AnoFormacao::whereId($request->id_ano_formacao)->get()->first();

        $rota = $request->path();

        $enquadramentos = Enquadramentos::all();
        $comportamentos = Comportamento::all();

        return view('admin.consulta.consulta-uete-aluno-punido', compact('uetes', 'anoFormacao', 'rota', 'enquadramentos', 'comportamentos'))
            ->with('ownauthcontroller', $this->ownauthcontroller);
    }

    public function ViewAlunosReprovados(Request $request)
    {
        $uetes = FuncoesController::retornaUetePerfil($this->ownauthcontroller);

        $anoFormacao = AnoFormacao::whereId($request->id_ano_formacao)->get()->first();

        $rota = $request->path();

        return view('admin.consulta.consulta-uete-aluno-reprovado', compact('uetes', 'anoFormacao', 'rota'));
    }

    public function RemoverProntoFaltas(Request $request)
    {
        $avaliacao = Avaliacoes::find($request->id);

        if (!isset($request->id_uete)) {
            $retorno['status'] = 'err';
            $retorno['response'][] = '<li style="color:rgb(255,0,0)">Selecione a UETE.</li>';
            return response()->json($retorno);
        }

        $retorno['status'] = 'success';
        $retorno['response'] = '<li style="color:rgb(0,0,255)">Prontos e Notas Removidos.</li>';

        $param = $request->id_uete;

        if (AvaliacoesNotas::where('avaliacao_id', '=', $avaliacao->id)->whereHas('aluno', function ($q) use ($param) {
            $q->where('omcts_id', '=', $param);
        })->delete() < 0) {
            $retorno['status'] = 'err';
            $retorno['response'][] = '<li style="color:rgb(255,0,0)">Erro ao Remover Notas de Avaliações.</li>';
        }

        if (AvaliacoesProntoFaltas::where('avaliacao_id', '=', $avaliacao->id)->whereHas('aluno', function ($q) use ($param) {
            $q->where('omcts_id', '=', $param);
        })->delete() < 0) {
            $retorno['status'] = 'err';
            $retorno['response'][] = '<li style="color:rgb(255,0,0)">Erro ao Remover Prontos Faltas.</li>';
        }

        if (AvaliacoesProntoFaltasStatus::where('avaliacao_id', '=', $avaliacao->id)->whereHas('uete', function ($q) use ($param) {
            $q->where('id', '=', $param);
        })->delete() < 0) {
            $retorno['status'] = 'err';
            $retorno['response'][] = '<li style="color:rgb(255,0,0)">Erro ao Prontos Faltas Status.</li>';
        }

        return response()->json($retorno);
    }

    public function AplicaEscolhaQms(Request $request){

        if($request->session()->has('aplicar_qms')){
            
            $aplicar_qms = unserialize($request->session()->get('aplicar_qms'));
            $aplicar_qms['recuperado'] = true;

            $request->session()->regenerate();

            if($aplicar_qms['segmento'] == 'M'){

                $select = EscolhaQMS::select('escolha_qms_masculino as escolha_qms_final')->where([['ano_formacao_id', '=', $aplicar_qms['ano_formacao']->id]])->first();
                if($select->escolha_qms_final == null){
                    $update = EscolhaQMS::where([['ano_formacao_id', '=', $aplicar_qms['ano_formacao']->id]])
                    ->update(['escolha_qms_masculino' => serialize($aplicar_qms)]);
                }else{
                    $update = EscolhaQMS::where([['ano_formacao_id', '=', $aplicar_qms['ano_formacao']->id]])
                    ->update(['escolha_qms_masculino' => null]);
                }

            }else{

                $select = EscolhaQMS::select('escolha_qms_feminino as escolha_qms_final')->where([['ano_formacao_id', '=', $aplicar_qms['ano_formacao']->id]])->first();
                if($select->escolha_qms_final == null){
                    $update = EscolhaQMS::where([['ano_formacao_id', '=', $aplicar_qms['ano_formacao']->id]])
                    ->update(['escolha_qms_feminino' => serialize($aplicar_qms)]);
                }else{
                    $update = EscolhaQMS::where([['ano_formacao_id', '=', $aplicar_qms['ano_formacao']->id]])
                    ->update(['escolha_qms_feminino' => null]);
                }

            }

            if($update > 0){
                $retorno['status'] = 'success';
                $retorno['response'][] = 'Informações Registradas Com Sucesso!!!';
            }else{
                $retorno['status'] = 'err';
                $retorno['response'][] = 'Não foi possível Registrar!!!';
            }
        }else{
            $retorno['status'] = 'err';
            $retorno['response'][] = 'Recarregue Página!!!';
        }

        return response()->json($retorno);
    }
}

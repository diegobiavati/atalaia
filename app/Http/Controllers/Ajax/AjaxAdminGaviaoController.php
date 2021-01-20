<?php

namespace App\Http\Controllers\Ajax;

//use Request;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
/* MODELS */

use App\Models\Operadores;
use App\Models\OperadoresTipo;

use App\Http\OwnClasses\ClassLog;
use App\Models\Alunos;
use App\Models\AlunosSitDiv;
use App\Models\AnoFormacao;
use App\Models\Areas;
use App\Models\Imagens;
use App\Models\OMCT;
use App\Models\PostoGrad;
use App\Models\QMS;
use App\Models\QMSMatriz;
use App\User;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

setlocale(LC_ALL, "pt_BR.utf8");
//date_default_timezone_set('America/Sao_Paulo');

class AjaxAdminGaviaoController extends Controller
{
    protected $classLog;
    protected $ownauthcontroller;

    public function __construct(Request $request, ClassLog $classLog, OwnAuthController $ownauthcontroller)
    {
        $this->request = $request;
        $this->classLog = $classLog;
        $classLog->ip = $_SERVER['REMOTE_ADDR'];
        $this->ownauthcontroller = $ownauthcontroller;
    }

    public function GerenciarOperadoresGaviao(\App\Http\Controllers\OwnAuthController $ownauthcontroller)
    {
        if ($this->ownauthcontroller->PermissaoCheck(1)) {
            $operadores = Operadores::whereNotNull('qms_matriz_id')->where([['ativo', '=', 'S']])->orderBy('qms_matriz_id', 'asc')->orderBy('postograd_id', 'asc')->get();
        } else {
            $operadores = Operadores::where([['qms_matriz_id', '=',session()->get('login.qmsID.0.qms_matriz_id')], ['ativo', '=', 'S']])->orderBy('postograd_id', 'asc')->get();
        }

        $funcoesOperadores = OperadoresTipo::get();
        foreach ($funcoesOperadores as $funcao) {
            $data[$funcao->id] = $funcao->funcao_abrev;
        }

        $this->classLog->RegistrarLog('Acesso ao menu para gerenciamento de operadores', auth()->user()->email);
        return view('ajax.gerenciar-operadores')->with('operadores', $operadores)->with('data', $data)->with('ownauthcontroller', $ownauthcontroller);
    }

    public function VisaoGeralGaviao(\App\Http\Controllers\OwnAuthController $ownauthcontroller){
        //Busca o Ano de Formação do Período de Qualificação
        $anoFormacao = FuncoesController::retornaAnoFormacaoAtivoQualificacao();

        $anoFormacaoID = ($anoFormacao->id) ?? 0;
        
        $alunos['total'] = Alunos::retornaAlunosComQmsESAGeral($anoFormacaoID)->count();
        $alunos['total_curso'] = Alunos::retornaAlunosComQmsESA($anoFormacaoID)->count();
        $alunos['porcentagem_alunos'] = ( ($alunos['total'] > 0) ? floor(($alunos['total_curso'] * 100) / $alunos['total']) : 0);

        return  view('ajax.visao-geral-gaviao')->with('total_operadores', Operadores::whereNotNull('qms_matriz_id')->where([['ativo', '=', 'S']])->count())
                    ->with('ano_corrente', $anoFormacao)
                    ->with('alunos', $alunos)
                    ->with('ownauthcontroller', $ownauthcontroller);
    }

    public function DialogEditarOperadorGaviao(Request $request)
    {
        $operador = Operadores::find($request->route('id'));
        $user = User::where('email', '=', $operador->email)->first();
        
        $imagens = Imagens::find(($user->imagens_id == null) ? 1 : $user->imagens_id);

        $style_display = ($user->imagens_id != 1 && $user->imagens_id != null) ? 'display: block;' : 'display: none;';

        foreach (explode(',', $operador->id_funcao_operador) as $funcao) {
            $funcao_operador[] = $funcao;
        }

        if(session()->get('login.qmsID.0.qms_matriz_id') == 9999){
            $qmsMatriz = array(1,2,3,4,5,9999);
            $operadores_tipo = OperadoresTipo::where([['id', '>=', 9000]])->get();
        }else{
            $qmsMatriz = array(session()->get('login.qmsID.0.qms_matriz_id'));
            $operadores_tipo = OperadoresTipo::whereIn('id', [9000,9001,9002])->get();
        }

        $cursos = QMSMatriz::whereIn('id', $qmsMatriz)->get();
        
        foreach ($operadores_tipo as $tipo) {
            $attr_checked = (in_array($tipo->id, $funcao_operador)) ? 'checked' : '';
            $tipos[] = '<div class="custom-control custom-checkbox">
                            <input type="checkbox" value="' . $tipo->id . '" name="tipo_operador_check[]" class="custom-control-input" id="tipo_operador_check_' . $tipo->id . '" ' . $attr_checked . '>
                            <label class="custom-control-label" for="tipo_operador_check_' . $tipo->id . '">' . $tipo->funcao . '</label>
                        </div>';
        }

        foreach ($cursos as $curso) {
            $attr_selected = ($operador->qms_matriz_id == $curso->id) ? 'selected' : '';
            $options_qms[] = '<option value="' . $curso->id . '" ' . $attr_selected . '>' . $curso->qms . '</option>';
        }

        $postograds = PostoGrad::get();
        foreach ($postograds as $postograd) {
            $attr_selected = ($operador->postograd_id == $postograd->id) ? 'selected' : '';
            $options_postograd[] = '<option value="' . $postograd->id . '" ' . $attr_selected . '>' . $postograd->postograd . '</option>';
        }

        return view('ajax.editar-operadores', compact('operador', 'user', 'imagens', 'style_display', 'options_postograd', 'options_qms', 'tipos'));
    }

    public function DialogAdicionarOperadorGaviao(Request $request)
    {

        if(session()->get('login.qmsID.0.qms_matriz_id') == 9999){
            $qmsMatriz = array(1,2,3,4,5,9999);
            $operadores_tipo = OperadoresTipo::where([['id', '>=', 9000]])->get();
        }else{
            $qmsMatriz = array(session()->get('login.qmsID.0.qms_matriz_id'));
            $operadores_tipo = OperadoresTipo::whereIn('id', [9000,9001,9002])->get();
        }

        $cursos = QMSMatriz::whereIn('id', $qmsMatriz)->get();

        foreach ($operadores_tipo as $tipo) {
            $tipos[] = '<div class="custom-control custom-checkbox">
                            <input type="checkbox" value="' . $tipo->id . '" name="tipo_operador_check[]" class="custom-control-input" id="tipo_operador_check_' . $tipo->id . '" />
                            <label class="custom-control-label" for="tipo_operador_check_' . $tipo->id . '">' . $tipo->funcao . '</label>
                        </div>';
        }

        /* LOOP QUE BUSCA OS CURSOS */
        $options_qms[] = '<option value="0">Informe o Curso</option>';
        
        foreach ($cursos as $curso) {
            $options_qms[] = '<option value="' . $curso->id . '">' . $curso->qms  . '</option>';
        }

        /* LOOP QUE BUSCA O POSTO E GRADUAÇÕES */
        $options_postograd[] = '<option value="0">Informe o Posto ou Graduação</option>';
        $postograds = PostoGrad::get();
        foreach ($postograds as $postograd) {
            $options_postograd[] = '<option value="' . $postograd->id . '">' . $postograd->postograd . '</option>';
        }

        return view('ajax.adicionar-operadores', compact('options_postograd', 'options_qms', 'tipos'));
    }

    public function SelecionaQMS(Request $request){

        if(auth()->check()){
            session()->put('qms_selecionada', (int)$request->qms_id);
            $data['success'] = true;
        }else{
            $data['success'] = false;
        }
        
        return response()->json($data);
    }

    public function GerenciarAlunosGaviao(\App\Http\Controllers\OwnAuthController $ownauthcontroller)
    {
        $anos_formacao = AnoFormacao::get();
        $ano_corrente_data = FuncoesController::retornaAnoFormacaoAtivoQualificacao();
        
        $alunos = Alunos::retornaAlunosComQmsESA();

        foreach($alunos as $aluno){
            $qmss[$aluno->qms->id] = $aluno->qms;
        }

        foreach($qmss as $qms){
            
            $alunos_por_qms = $alunos->where('qms_id', $qms->id)->where('data_matricula', $ano_corrente_data->id);

            $qmss_data[$qms->id] = array(
                "total_sit_div" => 0,
                "total_masculino" => $alunos_por_qms->where('sexo', 'M')->count(),
                "total" => count($alunos_por_qms),
                "lista" => $alunos_por_qms
            );
        }

        return view('ajax.alunos-gaviao')->with('ownauthcontroller', $ownauthcontroller)
            ->with('alunos', $alunos)
            ->with('qmss', $qmss)
            ->with('anos_formacao', $anos_formacao)
            ->with('ano_corrente_data', $ano_corrente_data)
            ->with('qmss_data', $qmss_data);
    }

    public function ListagemSelecaoAlunosGaviao(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request)
    {

        $ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();

        if ($ownauthcontroller->PermissaoCheck(1) && isset($request->anos_de_formacao) && $request->anos_de_formacao == 0) {
            $error[] = 'Pelo menos um ano de formação deve ser selecionado';
        } else if (!isset($request->anos_de_formacao)) {
            $id_ano_formacao = ($ano_corrente->id) ?? 0;
        } else {
            $id_ano_formacao = $request->anos_de_formacao;
        }

        if ($ownauthcontroller->PermissaoCheck([1, 10]) && !isset($request->qmss)) {
            $error[] = 'Pelo menos uma QMS deve ser selecionada';
        } else if ($ownauthcontroller->PermissaoCheck([1, 10])) {
            $qmss = $request->qmss;
        } else {
            $qmss = array(session()->get('login.qmsID.0.id'));
        }

        if (!isset($request->segmento)) {
            $error[] = 'Pelo menos um dos segmentos deve ser selecionado';
        } else {
            $segmento = $request->segmento;
        }

        if (isset($error)) {
            $data['status'] = 'err';
            $data['response'] = implode('<br />', $error);

            return $data;
        } else {

            $alunos = Alunos::retornaAlunosComQmsESAGeral($id_ano_formacao)
            ->whereIn('qms_id', $qmss)
            ->whereIn('sexo', $segmento)
            ->orderBy('sexo', 'asc')
            ->orderBy('numero', 'asc')->get();

            $qmss = QMS::whereIn('id', $qmss)->get();

            $sexo['M'] = 'Masculino';
            $sexo['F'] = 'Feminino';

        }

        return view('ajax.relatorios.listagem-alunos-gaviao', compact('alunos', 'qmss', 'sexo', 'ownauthcontroller'));
    }

    public function Relatorios(\App\Http\Controllers\OwnAuthController $ownauthcontroller)
    {
        return view('ajax.view-relatorios')->with('ownauthcontroller', $ownauthcontroller);
    }
}

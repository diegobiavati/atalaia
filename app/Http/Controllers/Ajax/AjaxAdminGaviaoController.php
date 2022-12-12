<?php

namespace App\Http\Controllers\Ajax;

//use Request;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Relatorios\RelatoriosSSAA;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Http\Controllers\Utilitarios\ImportadorController;
use App\Http\FPDF\PDF_DEM_NOTAS;
/* MODELS */

use App\Models\Operadores;
use App\Models\OperadoresTipo;

use App\Http\OwnClasses\ClassLog;
use App\Models\Alunos;
use App\Models\AlunosSitDiv;
use App\Models\AlunosSitDivHistorico;
use App\Models\AnoFormacao;
use App\Models\Areas;
use App\Models\CapitaniMSAccess;
use App\Models\EsaAvaliacoes;
use App\Models\EscolhaQMS;
use App\Models\Imagens;
use App\Models\LancamentoFo;
use App\Models\Mencoes;
use App\Models\ModeloNotasCapitani;
use App\Models\OMCT;
use App\Models\PostoGrad;
use App\Models\QMS;
use App\Models\QMSMatriz;
use App\Models\TurmasEsa;
use App\User;
use Exception;

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
        if(in_array('9999', session()->get('login.perfil'))){//Caso seja o Administrador
            $operadores = Operadores::whereNotNull('qms_matriz_id')->where([['ativo', '=', 'S']])
            ->orderBy('qms_matriz_id', 'asc')
            ->orderBy('postograd_id', 'asc')->get();
        }else if(in_array('9004', session()->get('login.perfil'))){//Caso seja Operador do CA

            $operadorNaoPermitido = [9999];
            
            $operadores = Operadores::whereNotNull('qms_matriz_id')
            ->where([['ativo', '=', 'S']])
            ->orderBy('qms_matriz_id', 'asc')
            ->orderBy('postograd_id', 'asc')->get();
            
            //Remove o Perfil Administrador
            for($i=0;$i<=count($operadores);$i++){
                $operador = $operadores[$i];

                $operador->permissoes = explode(',', $operador->id_funcao_operador);
                foreach($operadorNaoPermitido as $permitido){
                    if(in_array($permitido, $operador->permissoes)){
                        unset($operador, $operadores[$i]);
                        break;
                    }
                }
            }

        }else{
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
        
        $alunosCurso = Alunos::retornaAlunosComQmsESA($anoFormacaoID);
        $alunos['total'] = Alunos::retornaAlunosComQmsESAGeral($anoFormacaoID)->count();
        $alunos['total_curso'] = $alunosCurso->count();
        $alunos['porcentagem_alunos'] = ( ($alunos['total'] > 0) ? floor(($alunos['total_curso'] * 100) / $alunos['total']) : 0);

        $param['anoFormacao'] = $anoFormacaoID;
        $param['alunosId'] = $alunosCurso->pluck('id')->toArray();
        
        $lancamentoFo = LancamentoFo::whereHas('aluno', function ($query) use ($param) {
            $query->where(['data_matricula' => $param['anoFormacao']])
            ->whereIn('aluno_id', $param['alunosId']);
        })->get();

        $fatd = LancamentoFo::whereHas('aluno', function ($query) use ($param) {
            $query->where(['data_matricula' => $param])
            ->whereIn('aluno_id', $param['alunosId']);
        })->whereHas('fatdLancada', function ($query) {
            $query->where(['justificado' => null]);
        })->get();

        /*if(session()->has('qms_selecionada') && !(session()->get('qms_selecionada') == 9999)){

            $aluno->whereHas('qms', function($q){
                $q->where('qms_matriz_id', '=', session()->get('qms_selecionada'));
            });
            
        }else if(!in_array('9999', session()->get('login.perfil'))
                && !in_array('9003', session()->get('login.perfil'))
                && !in_array('9004', session()->get('login.perfil'))
                && !in_array('9005', session()->get('login.perfil'))
        ){
            if($anoFormacaoID > 0){
                $aluno->where('qms_id', QMS::where([
                    ['escolha_qms_id', '=', EscolhaQMS::where('ano_formacao_id', $anoFormacaoID)->first()->id],
                    ['qms_matriz_id', '=', session()->get('login.qmsID.0.qms_matriz_id')]
                ])->first()->id );
            }
        } */

        //Avaliações sem devolução dos próximos 30 dias
        $avaliacoes = EsaAvaliacoes::whereNull('id_operador_devolucao')->where('realizacao', '<=', 'ADDDATE(CURRENT_TIMESTAMP(), INTERVAL 1 MONTH)');

        if(session()->has('qms_selecionada') && !(session()->get('qms_selecionada') == 9999)){
            $avaliacoes->join('esa_disciplinas', 'esa_avaliacoes.id_esa_disciplinas', '=', 'esa_disciplinas.id')
            ->join('atalaia.qms', 'esa_disciplinas.id_qms', '=', 'atalaia.qms.id')
            ->where('atalaia.qms.qms_matriz_id', '=', session()->get('qms_selecionada'));
        }else if(!$this->ownauthcontroller->PerfilCheck(['9999', '9005', '9004', '9003'])){
            $avaliacoes->join('esa_disciplinas', 'esa_avaliacoes.id_esa_disciplinas', '=', 'esa_disciplinas.id')
            ->join('atalaia.qms', 'esa_disciplinas.id_qms', '=', 'atalaia.qms.id')
            ->where('atalaia.qms.qms_matriz_id', '=', session()->get('login.qmsID.0.qms_matriz_id'));
        }

        return  view('ajax.visao-geral-gaviao')->with('total_operadores', Operadores::whereNotNull('qms_matriz_id')->where([['ativo', '=', 'S']])->count())
                    ->with('avaliacoes', $avaliacoes->get())
                    ->with('ano_corrente', $anoFormacao)
                    ->with('alunos', $alunos)
                    ->with('fatd', $fatd)
                    ->with('lancamentoFo', $lancamentoFo)
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

        if(in_array('9999', session()->get('login.perfil'))){
            $qmsMatriz = array(1,2,3,4,5,9999);
            $operadores_tipo = OperadoresTipo::where([['id', '>=', 9000]])->get();
        }else if(in_array('9004', session()->get('login.perfil'))){
            $qmsMatriz = array(1,2,3,4,5,9999);
            $operadores_tipo = OperadoresTipo::whereIn('id', [9000,9001,9002,9003,9004])->get();
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
        if(in_array('9999', session()->get('login.perfil'))){//Administrador
            $qmsMatriz = array(1,2,3,4,5,9999);
            $operadores_tipo = OperadoresTipo::where([['id', '>=', 9000]])->get();
        }else if(in_array('9004', session()->get('login.perfil'))){//Operador CA
            $qmsMatriz = array(1,2,3,4,5,9999);
            $operadores_tipo = OperadoresTipo::whereIn('id', [9000,9001,9002,9003,9004])->get();
        }else if(in_array('9005', session()->get('login.perfil'))){//Operador SSAA
            $qmsMatriz = array(1,2,3,4,5,9999);
            $operadores_tipo = OperadoresTipo::whereIn('id', [9005])->get();
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
        $alunosAnoCorrente = Alunos::retornaAlunosComQmsESA($ano_corrente_data->id);

        foreach($alunos as $aluno){
            $cursos[$aluno->qms->qms_matriz_id] = $aluno->qms;
            $qmss[$aluno->qms->id] = $aluno->qms;
        }

        foreach($qmss as $qms){
            
            $alunos_por_qms = $alunosAnoCorrente->where('qms_id', $qms->id);

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
            ->with('qmss_data', $qmss_data)
            ->with('cursos', $cursos);
    }

    public function ShowChkBoxAnoFormacaoQms($idAnoFormacao){
        $alunosAnoCorrente = Alunos::retornaAlunosComQmsESA($idAnoFormacao);

        foreach($alunosAnoCorrente as $aluno){
            $qmss[$aluno->qms->id] = $aluno->qms;
        }

        return view('ajax.chkbox-ano-qms', compact('qmss'));
    }

    public function ListagemSelecaoAlunosTurma(\App\Http\Controllers\OwnAuthController $ownauthcontroller, Request $request){
     
        $anoFormacao = AnoFormacao::find($request->ano_formacao);
        $curso = FuncoesController::retornaCursoAnoFormacao($anoFormacao, $request->curso);

        $alunos = Alunos::retornaAlunosComQmsEspecifica($anoFormacao->id, [$curso->id])->get();
        
        $turmas = TurmasEsa::where('qms_id', $curso->id)->get();

        return view('ajax.view-listagem-turma', compact('alunos', 'turmas'));
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
            $qmss = array(FuncoesController::retornaQMSPerfil($id_ano_formacao)->id);
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

    public function DiplomaDigital(\App\Http\Controllers\OwnAuthController $ownauthcontroller)
    {
        $url_diploma_periodo = '/gaviao/ajax/diploma/diploma-periodo';

        return view('ajax.diploma.view-diploma', compact('ownauthcontroller', 'url_diploma_periodo'));
    }

    public function SelecionaAlunoTurma(){

        $idTurma = $this->request->idTurma;
        $idAluno = $this->request->idAluno;

        $data['status'] = 'err';
        $data['response'] = 'Algo Deu Errado.';

        if(Alunos::find($idAluno)->update(['turma_esa_id' => $idTurma])){
            $data['status'] = 'success';
            $data['response'] = 'Gravado com Sucesso.';
        }

        return response()->json($data);
    }

    public function LoadAlunosSitDiv(\App\Http\Controllers\OwnAuthController $ownauthcontroller)
    {
        if ($ownauthcontroller->PerfilCheck([9004, 9999])) {
            $alunos = AlunosSitDiv::whereNotNull('qms_id')->orderBy('data_matricula', 'desc')->orderBy('qms_id', 'desc')->get();
        } else {
            $ano_corrente_data = FuncoesController::retornaAnoFormacaoAtivoQualificacao();
            
            $alunos = AlunosSitDiv::whereNotNull('qms_id')->orderBy('data_matricula', 'desc')
                ->where('qms_id', QMS::where([
                ['escolha_qms_id', '=', EscolhaQMS::where('ano_formacao_id', $ano_corrente_data->id)->first()->id],
                ['qms_matriz_id', '=', session()->get('login.qmsID.0.qms_matriz_id')]
                ])->first()->id)
                ->get();
        }

        return view('ajax.view-listagem-aluno-sit-diversas', compact('ownauthcontroller', 'alunos'));
    }

    public function DemonstrativoNotasGaviao(OwnAuthController $ownauthcontroller, Request $request){
       
        if(!$ownauthcontroller->PermissaoCheck([1,27]) && $request->curso_id!=FuncoesController::retornaQMSPerfil($request->id_ano_formacao)->id){
            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';
        } else {

            $relatorioSSAA = new RelatoriosSSAA();
            return $relatorioSSAA->DemonstrativoNotasGaviao($request->id_ano_formacao, $request->curso_id);

        }
    }

    public function ViewOpcoesSSAA(OwnAuthController $ownauthcontroller){
        return view('ssaa.index')->with('ownauthcontroller', $ownauthcontroller);
    }
}

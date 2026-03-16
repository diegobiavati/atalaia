<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Models\AnoFormacao;
use App\Models\ConteudoAtitudinal;
use App\Models\Fatd;
use App\Models\LancamentoFo;
use App\Models\OMCT;
use App\Models\Operadores;
use App\Models\TurmasPB;
use Illuminate\Support\Facades\DB;
use App\Http\FPDF\PDF;
use App\Models\Alunos;
use App\Models\Comportamento;
use App\Models\Enquadramentos;
use App\Models\Napd;
use App\Models\QMS;
use App\Models\TurmasEsa;
use Illuminate\Support\Facades\Validator;

class LancamentosController extends Controller
{

    protected $_ownauthcontroller;

    public function __construct(OwnAuthController $ownauthcontroller)
    {

        $this->_ownauthcontroller = $ownauthcontroller;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        /*if ($this->_ownauthcontroller->PermissaoCheck(1)) {
            $uetes = OMCT::where('id', '<>', 1)->get(); //Remove a ESA
        } else {
            $uetes = OMCT::where('id', session()->get('login.omctID'))->get();
        }*/
        
        $lancamentoFo = LancamentoFo::find($id);
        $ano_formacao = $lancamentoFo->aluno->data_matricula;

        $conteudoAtitudinal = ConteudoAtitudinal::all();
        $napds = Napd::all();
        //$turmas = $lancamentoFo->aluno->turma;

        $rotaTurma = '/ajax/lancamentosTurma';

        //$operadores = $lancamentoFo->operador;
        //$funcaoOperador = session()->get('login.perfil');

        $readOnly = 'readOnly';
        if(session()->get('login.omctID') || !isset($lancamentoFo->aluno->turma_esa_id)){
            $uetes = array($lancamentoFo->aluno->omct);

            $turmas = array($lancamentoFo->aluno->turma);

            return view('lancamentos.lancamentoFatoObservado', compact('uetes', 'conteudoAtitudinal', 'turmas', 'rotaTurma', 'lancamentoFo', 'ano_formacao', 'readOnly', 'napds'))
            ->with('ownauthcontroller', $this->_ownauthcontroller);
        }else{
            $cursos = array($lancamentoFo->aluno->qms);

            $turmas = array($lancamentoFo->aluno->turmaEsa);

            if(isset($lancamentoFo->aluno->turmaEsa)){
                return view('lancamentos.lancamentoFatoObservado', compact('cursos', 'conteudoAtitudinal', 'turmas', 'rotaTurma', 'lancamentoFo', 'ano_formacao', 'readOnly', 'napds'))
                ->with('ownauthcontroller', $this->_ownauthcontroller);
            }else{
                $mensagem = 'Selecione a Turma do Aluno no Período ESA';
                return view('ajax.erros.view-erro-padrao-centralizado', compact('mensagem'));
            }
            
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $retorno['status'] = 'err';
        $retorno['response'] = 'Ocorreu um Erro, Relogue no Sistema';

        /*if($request->textAreaProvidencias == null){
            $retorno['status'] = 'err';
            $retorno['response'] = 'Informe a Providência.';

            return response()->json($retorno);
        }*/
        //$lancamentoFo = LancamentoFo::find($id);

        if(isset($request->napd)){
            $lancamentoFo = LancamentoFo::find($id);

            $lancamentoFo->update(['napd_id' => $request->napd]);
        }

        //Se for cancelamento de FO
        if(isset($request->textAreaCancelamento)){
            if ($this->CancelarFO($request, $id)) {
                $retorno['status'] = 'success';
                $retorno['response'] = 'Cancelamento de FO Efetuado Com Sucesso.';
            }else{
                $retorno['status'] = 'err';
                $retorno['response'] = 'Erro ao Tentar Cancelar FO.';
            }
        }else{

            if($request->textAreaProvidencias == null){
                $retorno['status'] = 'err';
                $retorno['response'] = 'Informe a Providência.';
    
                return response()->json($retorno);
            }
            
            if ($this->LancarProvidencia($request, $id)) {
                $retorno['status'] = 'success';
                $retorno['response'] = 'Providência Lançada Com Sucesso.';
            }else{
                $retorno['status'] = 'err';
                $retorno['response'] = 'Usuário sem Previlégios.';
            }
        }
    
        return response()->json($retorno);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $retorno['status'] = 'err';
        $retorno['response'] = 'Ocorreu um Erro, Relogue no Sistema';

        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        $dataFO = (is_null($request->dataFO) ? date('Y/m/d') : date('Y/m/d', strtotime($request->dataFO)));
        
        $listaAtitudinal = [];
        $listaAlunos = [];
        foreach ($request->all() as $parametros => $value) {
            if (substr($parametros, 0, 10) == 'atitudinal') {
                $listaAtitudinal[] = (int) substr($parametros, 11);
            } elseif (substr($parametros, 0, 5) == 'aluno') {
                $listaAlunos[] = (int) substr($parametros, 6);
            }
        }

        if(session()->get('login.qmsID')){
            $comandanteCurso = QMS::find($request->qmsID)->comandanteCurso;
        }else{
            $operadoresAtivos = Operadores::where([['omcts_id', '=', $request->omctID], ['ativo', '=', 'S']])->get();
            
            foreach($operadoresAtivos as $operador){
                $funcaoOperador = explode(',', $operador->id_funcao_operador);

                if(in_array(2, $funcaoOperador)){
                    $comandanteCurso = $operador;
                    break;
                }
            }
        }

        if(!isset($comandanteCurso)){
            $retorno['status'] = 'err';
            $retorno['response'] = session()->get('login.qmsID') ? 'Cadastre o CMT de Curso no Sistema.' : 'Cadastre o CMT de UETE no Sistema.';
        }else{
            $invalidaOperacao = false;
            foreach ($listaAlunos as $idAluno) {
                $lancamentoFo = new LancamentoFo();
                $lancamentoFo->aluno_id = $idAluno;
                $lancamentoFo->operador_id = session()->get('login')['operadorID'];
                $lancamentoFo->tipo = $request->radioTipoFO;
                $lancamentoFo->observacao = $request->textAreaObservacaoFO;
                $lancamentoFo->comandante_operador_id = $comandanteCurso->id;
    
                //if($request->textAreaProvidencias != null && $this->_ownauthcontroller->PerfilCheck([2,9001])){
                if($request->textAreaProvidencias != null){
                    $lancamentoFo->providencia = $request->textAreaProvidencias;
                    $lancamentoFo->frad = 'S';
                }
    
                $lancamentoFo->data_obs = $dataFO;
                $lancamentoFo->napd_id = $request->napd;
    
                $lancamentoFo->conteudo_atitudinal = json_encode($listaAtitudinal);
    
                if (!$lancamentoFo->save()) {
                    $invalidaOperacao = true;
                } else {
                    $this->LancarProvidencia($request, $lancamentoFo->id);
                }
            }
    
            if (!$invalidaOperacao) {
                $retorno['status'] = 'success';
                $retorno['response'] = 'Fato Observado Registrado.';
            } else {
                $retorno['response'] = 'Ocorreu um Erro Ao Salvar o Registro!!';
            }
        }
        
        return response()->json($retorno);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        if(!session()->has('login.qmsID')){
            if ($this->_ownauthcontroller->PermissaoCheck(1)) {
                $uetes = OMCT::where('id', '<>', 1)->get(); //Remove a ESA
            } else {
                $uetes = OMCT::where('id', session()->get('login.omctID'))->get();
            }
        }
        
        //Registra o Observador na Sessão
        $operadores = Operadores::find(session()->get('login.operadorID'));

        if (isset($operadores)) {
            session()->flash('nomeOperador', $operadores->posto->postograd_abrev . ' ' . $operadores->nome_guerra);

            $explode = explode('_', $id);

            switch ($explode[0]) {
                case 'lancarFO':
                    $readOnly = null;
                    
                    if(count($explode) == 1){
                        return view('lancamentos.lancamentoSelecaoAno');
                    }

                    $conteudoAtitudinal = ConteudoAtitudinal::orderBy('descricao', 'ASC')->get();
                    $napds = Napd::all();

                    $rotaTurma = '/ajax/lancamentosTurma';

                    $operadores = Operadores::find(session()->get('login')['operadorID']);
                    $funcaoOperador = explode(',', $operadores->id_funcao_operador);

                    if(session()->has('login.qmsID')){
                        //$cursos = FuncoesController::retornaCursoPerfilAnoFormacao(AnoFormacao::find($explode[1]));
                        $qmsMatriz = array(1, 2, 3, 4, 5);
                        $cursos = QMS::whereIn('qms_matriz_id', $qmsMatriz)->whereHas('escolhaQms', function ($q) use ($explode) {
                            $q->where('ano_formacao_id', AnoFormacao::find($explode[1])->id);
                        })->get();

                        return view('lancamentos.lancamentoFatoObservado', compact('cursos', 'conteudoAtitudinal', 'rotaTurma', 'funcaoOperador', 'readOnly', 'napds'))
                            ->with('ownauthcontroller', $this->_ownauthcontroller);
                    }

                    return view('lancamentos.lancamentoFatoObservado', compact('uetes', 'conteudoAtitudinal', 'rotaTurma', 'funcaoOperador', 'readOnly'))
                        ->with('ownauthcontroller', $this->_ownauthcontroller);
                case 'viewConsultarFO':

                    $rotaConsulta = '/ajax/listaFatosObservados';
                    $rotaConsultaCia = '/gaviao/ajax/carregaSelectCurso';

                    if(count($explode) == 1){
                        return view('lancamentos.lancamentoConsultaFOSelecaoAno');
                    }

                    if(session()->has('login.qmsID')){
                        $anoFormacao = AnoFormacao::find($explode[1]);
                        $alunos = Alunos::retornaAlunosComQmsESA($anoFormacao->id);
                        
                        if($alunos->isEmpty()){
                            $uetes = OMCT::where('id', '<>', 1)->get(); //Remove a ESA

                            return view('lancamentos.lancamentoConsultaFO', compact('uetes', 'rotaConsulta', 'rotaConsultaCia'))->with('ownauthcontroller', $this->_ownauthcontroller);
                        }

                        $cursos = FuncoesController::retornaCursoPerfilAnoFormacao($anoFormacao);
                        
                        return view('lancamentos.lancamentoConsultaFO', compact('cursos', 'rotaConsulta', 'rotaConsultaCia'))->with('ownauthcontroller', $this->_ownauthcontroller);
                    }

                    return view('lancamentos.lancamentoConsultaFO', compact('uetes', 'rotaConsulta', 'rotaConsultaCia'))->with('ownauthcontroller', $this->_ownauthcontroller);
                case 'viewConsultarFATD':
                    if(count($explode) == 1){
                        return view('lancamentos.lancamentoConsultaFATDSelecaoAno');
                    }

                    if(session()->has('login.qmsID')){
                        $anoFormacao = AnoFormacao::find($explode[1]);
                        $alunos = Alunos::retornaAlunosComQmsESA($anoFormacao->id);

                        if($alunos->isEmpty()){
                            $uetes = OMCT::where('id', '<>', 1)->get(); //Remove a ESA

                            return view('lancamentos.lancamentoConsultaFATD', compact('uetes'))->with('ownauthcontroller', $this->_ownauthcontroller);
                        }

                        $cursos = FuncoesController::retornaCursoPerfilAnoFormacao(AnoFormacao::find($explode[1]));
                        
                        return view('lancamentos.lancamentoConsultaFATD', compact('cursos'))->with('ownauthcontroller', $this->_ownauthcontroller);
                    }

                    return view('lancamentos.lancamentoConsultaFATD', compact('uetes'))->with('ownauthcontroller', $this->_ownauthcontroller);
            }
        }
    }

    public function ConsultaTurma(Request $request)
    {
        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        if(isset($request->qmsID)){
            $turmas = TurmasEsa::whereHas('alunos', function ($query) use ($request) {
                //$query->where(['qms_id' => $request->qmsID, 'data_matricula' => $request->anoFormacaoID]);
                $anoFormacaoID = $request->anoFormacaoID;
                $query->where(['qms_id' => $request->qmsID])->where(function($query) use ($anoFormacaoID){
                    return $query->where([['data_matricula', '=', $anoFormacaoID]])->orWhere([['ano_formacao_reintegr_id', '=', $anoFormacaoID]]);
                });

            })->get();
        }else{
            $turmas = TurmasPB::whereHas('alunos', function ($query) use ($request) {
                $query->where(['omcts_id' => $request->omctID, 'data_matricula' => $request->anoFormacaoID]);
            })->get();
        }
        
        return view('lancamentos.lancamentoConsultaTurma', compact('turmas'));
    }

    public function ViewTurma(Request $request)
    {

        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        if($request->qmsID != 'undefined'){
            
            $anoFormacaoID = $request->anoFormacaoID;
            $alunosTurma = Alunos::join('turmas_esa', 'alunos.turma_esa_id', '=', 'turmas_esa.id')
            ->where(function($query) use ($anoFormacaoID){
                return $query->where([['data_matricula', '=', $anoFormacaoID]])->orWhere([['ano_formacao_reintegr_id', '=', $anoFormacaoID]]);
            })
            ->where([['alunos.qms_id', '=', $request->qmsID], ['alunos.turma_esa_id', '=', $request->turmaID]])
            ->select(['alunos.id', 'alunos.numero', 'alunos.nome_guerra', 'turmas_esa.id as turmas_id', 'turmas_esa.turma'])
            ->get();

        }else{
            $alunosTurma = DB::select("SELECT alunos.id, alunos.numero, alunos.nome_guerra
            , omcts.id AS omct_id, omcts.sigla_omct, omcts.omct, omcts.gu
            , turmas_pb.id AS turmas_id, turmas_pb.turma
            FROM alunos
                INNER JOIN omcts ON (omcts.id = alunos.omcts_id)
                INNER JOIN turmas_pb ON (turmas_pb.id = alunos.turma_id)
            WHERE alunos.data_matricula = $request->anoFormacaoID
                AND alunos.omcts_id = $request->omctID
                AND alunos.turma_id = $request->turmaID");
        }
        
        return view('lancamentos.lancamentoAlunosFO', compact('alunosTurma'));
    }

    public function ViewListaFatosObservados(Request $request)
    {

        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        if (!isset($request->omctID) && !isset($request->qmsID)) {
            if(session()->get('login.qmsID')){
                return '<font style="color:red;font-size:14px;">Selecione um Curso</font>';
            }
            return '<font style="color:red;font-size:14px;">Selecione uma UETE</font>';
        }

        $anoFormacao = AnoFormacao::find($request->ano_formacao);
        $dataFim = $anoFormacao->ano_per_basico.'-12-31';

        if(isset($request->omctID)){
            $whereUeteCurso = ((isset($request->omctID) && $request->omctID <> 'todas_omct') ? " AND alunos.omcts_id = $request->omctID AND lancamento_fo.data_obs BETWEEN '$anoFormacao->data_matricula' AND '$dataFim'" : null);
        }else{
            $whereUeteCurso = ((isset($request->qmsID) && $request->qmsID <> 'todas_qmss') ? " AND alunos.qms_id = $request->qmsID" : null);

            if(isset($request->cia)){
                if($request->cia == 1){
                    $whereUeteCurso .= " AND turmas_esa.turma IN ('I1', 'I2', 'I3')";//Turma I1, I2, I3
                }else if($request->cia == 2){
                    $whereUeteCurso .= " AND turmas_esa.turma IN ('I4', 'I5', 'I6')";//Turma I4, I5, I6
                }
            }
        }

        $whereNumeroAluno = (isset($request->numero_aluno) ? " AND alunos.numero = $request->numero_aluno" : null);
        $whereNomeGuerra = (isset($request->nome_aluno) ? " AND alunos.nome_guerra LIKE '%$request->nome_aluno%'" : null);

        $whereOpcaoRel = ' ';
        if(isset($request->opcaoRel)){
            switch($request->opcaoRel){
                case 1://Listar Todos
                    $whereOpcaoRel = $whereOpcaoRel;
                break;
                case 2://Listar Resolvidos
                    $whereOpcaoRel = ' AND lancamento_fo.providencia IS NOT NULL AND lancamento_fo.cancelado = \'N\'';
                break;
                case 3://Listar Resolvidos Com FATD
                    $whereOpcaoRel = " AND lancamento_fo.fatd = 'S' AND lancamento_fo.cancelado = 'N'";
                break;
                case 4://Listar Não Resolvidos
                    $whereOpcaoRel = ' AND lancamento_fo.providencia IS NULL AND lancamento_fo.cancelado = \'N\'';
                break;
                case 5://Listar Canceladas
                    $whereOpcaoRel = ' AND lancamento_fo.cancelado = \'S\'';
                break;
            }
        }

        $lancamentoFO = DB::select("SELECT lancamento_fo.id, lancamento_fo.data_obs, lancamento_fo.tipo, lancamento_fo.observacao, alunos.numero, alunos.nome_guerra, omcts.omct as uete, qms.qms AS curso
                                        , lancamento_fo.providencia, lancamento_fo.fatd, lancamento_fo.cancelado
                                        FROM lancamento_fo
                                        INNER JOIN alunos ON (alunos.id = lancamento_fo.aluno_id)
                                        INNER JOIN omcts ON (omcts.id = alunos.omcts_id)
                                        LEFT JOIN qms ON (qms.id = alunos.qms_id)
                                        LEFT JOIN turmas_esa ON (turmas_esa.id = alunos.turma_esa_id)
                                        WHERE (alunos.data_matricula = $anoFormacao->id OR alunos.ano_formacao_reintegr_id = $anoFormacao->id)" . $whereUeteCurso . $whereNumeroAluno . $whereNomeGuerra. $whereOpcaoRel
                                        . 'ORDER BY lancamento_fo.data_obs DESC');
        
        return view('lancamentos.lancamentoListaFatosObservados', compact('lancamentoFO'))->with('relacao', $request->relacao);
    }

    public function ViewListaFATD(Request $request)
    {

        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        if (!isset($request->omctID) && !isset($request->qmsID)) {
            if(session()->get('login.qmsID')){
                return '<font style="color:red;font-size:14px;">Selecione um Curso</font>';
            }
            return '<font style="color:red;font-size:14px;">Selecione uma UETE</font>';
        }

        $rota = 'ajax.ficha-fatd';

        $lancamentoFATD = LancamentoFo::whereHas('aluno', function ($query) use ($request) {
            $anoFormacao = AnoFormacao::find($request->ano_formacao);

            $where = array();

            if(isset($request->omctID)){
                if (($request->omctID <> 'todas_omct')) {
                    $where['omcts_id'] = $request->omctID;
                }
            }else{
                if (($request->qmsID <> 'todas_qmss')) {
                    $where['qms_id'] = $request->qmsID;
                }
            }

            if (isset($request->numero_aluno)) {
                $where['numero'] = $request->numero_aluno;
            }

            if (isset($request->nome_aluno)) {
                $where[] = array('nome_completo', 'like', '%' . $request->nome_aluno . '%');
            }

            $query->where(function($query) use($anoFormacao){
                return $query->where([['data_matricula', '=', $anoFormacao->id]])->orWhere([['ano_formacao_reintegr_id', '=', $anoFormacao->id]]);
            })->where($where);

        })->orderByDesc('data_obs')->whereHas('fatdLancada', function ($query) {
            $query->whereNotNull('lancamento_fo_id');
        })->get();

        return view('lancamentos.lancamentoListaFATD', compact('lancamentoFATD', 'rota'))->with('ownauthcontroller', $this->_ownauthcontroller);
    }

    public function ViewTelaFATD($id)
    {

        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        $fatd = Fatd::where([['lancamento_fo_id', '=', $id]])->first();

        $comportamentos = Comportamento::all();
        $enquadramentos = Enquadramentos::orderBy('ordem')->get();

        //Registra o Observador na Sessão
        $operadores = Operadores::find(session()->get('login.operadorID'));

        if (isset($operadores)) {
            session()->flash('nomeOperador', $operadores->posto->postograd_abrev . ' ' . $operadores->nome_guerra);

            return view('lancamentos.lancamentoFATD', compact('fatd', 'comportamentos', 'enquadramentos'));
        }
    }

    public function ViewFichaFATD(Request $request)
    {
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');

        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        $fatd = Fatd::where(['lancamento_fo_id' => $request->fatdID])->with('lancamentoFo')->first();

        if(isset($fatd->lancamentoFo->comandante_operador_id)){
            $comandanteCurso = $fatd->lancamentoFo->comandanteCurso;
        }else{
            if(session()->get('login.qmsID')){
                $comandanteCurso = QMS::find($fatd->lancamentoFo->aluno->qms_id)->comandanteCurso;
            }else{
                //$comandanteCurso = Operadores::where([['omcts_id', '=', $fatd->lancamentoFo->aluno->omcts_id], ['id_funcao_operador', '=', '2'], ['ativo', '=', 'S']])->first();
                $operadoresAtivos = Operadores::where([['omcts_id', '=', $fatd->lancamentoFo->aluno->omcts_id], ['ativo', '=', 'S']])->get();
    
                foreach($operadoresAtivos as $operador){
                    $funcaoOperador = explode(',', $operador->id_funcao_operador);
    
                    if(in_array(2, $funcaoOperador)){
                        $comandanteCurso = $operador;
                        break;
                    }
                }
    
            }
        }
        
        $pdf = new PDF();
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        $pdf->SetFont('Times', 'B', 10);


        $pdf->SetXY(10, 11);
        $pdf->Cell(0, 4, utf8_decode('MINISTÉRIO DA DEFESA'), 0, 1, 'C', false);
        $pdf->Cell(0, 4, utf8_decode('EXÉRCITO BRASILEIRO'), 0, 1, 'C', false);
        $pdf->Cell(0, 4, 'ESCOLA DE SARGENTOS DAS ARMAS', 0, 1, 'C', false);
        $pdf->Cell(0, 4, '(ESCOLA SARGENTO MAX WOLFF FILHO)', 0, 1, 'C', false);
        $pdf->SetFont('Times', 'B', 8);

        //Cria a Borda
        $pdf->Rect(10, 10, 190, 278);

        if(session()->get('login.qmsID')){
            $pdf->Cell(0, 4, utf8_decode('Curso de '.$fatd->lancamentoFo->aluno->qms->qms), 0, 1, 'C', false);
        }else{
            $pdf->Cell(0, 4, utf8_decode($fatd->lancamentoFo->aluno->omct->omct), 0, 1, 'C', false);
        }

        $pdf->SetFont('Times', 'B', 12);
        $pdf->ln(5);
        $pdf->Cell(0, 4, utf8_decode('FORMULÁRIO DE APURAÇÃO DE TRANSGRESSÃO DISCIPLINAR'), 0, 1, 'C', false);

        $pdf->SetFont('Times', '', 10);

        $pdf->Line(10, 41, 200, 41);
        $pdf->ln(5);
        $pdf->WriteHTML('Processo Nr <b>' . str_pad($fatd->nr_processo, 3, 0, STR_PAD_LEFT) . '-' . $fatd->ano . '</b>');

        $pdf->SetXY(155, 45);
        $pdf->WriteHTML('<b>Data:</b>____/_______/______');// . strftime('%e %b %y', strtotime($fatd->lancamentoFo->data_obs)));
        $pdf->Line(10, 51, 200, 51);

        $pdf->SetFont('Times', 'B', 12);
        $pdf->SetXY(10, 55);
        $pdf->Cell(0, 4, utf8_decode('IDENTIFICAÇÃO DO MILITAR'), 0, 1, 'C', false);

        $pdf->SetFont('Times', '', 10);

        $pdf->SetXY(10, 60);
        $pdf->Cell(0, 5, utf8_decode('Grau hierárquico: Aluno CFGS'), 0, 1, 'L', false);

        $pdf->WriteHTML(utf8_decode('Nrº / Nome: ' .$fatd->lancamentoFo->aluno->numero.' - '. str_replace($fatd->lancamentoFo->aluno->nome_guerra, '<b>' . $fatd->lancamentoFo->aluno->nome_guerra . '</b>', $fatd->lancamentoFo->aluno->nome_completo)));
        $pdf->SetXY(145, 65);
        $pdf->Cell(0, 5, 'Nr / Idt ' . $fatd->lancamentoFo->aluno->doc_idt_militar . ' ' . $fatd->lancamentoFo->aluno->doc_idt_militar_o_exp, 0, 1, 'L', false);
        $pdf->Line(10, 75, 200, 75);

        $pdf->SetFont('Times', 'B', 12);
        $pdf->SetXY(10, 80);
        $pdf->Cell(0, 4, utf8_decode('IDENTIFICAÇÃO DO PARTICIPANTE'), 0, 1, 'C', false);

        $pdf->SetFont('Times', '', 10);

        $pdf->Cell(0, 5, utf8_decode('Grau hierárquico: ' . $fatd->lancamentoFo->operador->posto->postograd_abrev), 0, 1, 'L', false);
        $pdf->WriteHTML(utf8_decode('Nome: ' . str_replace($fatd->lancamentoFo->operador->nome_guerra, '<b>' . $fatd->lancamentoFo->operador->nome_guerra . '</b>', $fatd->lancamentoFo->operador->nome)));
        //$pdf->Cell(145, 0, 'Nome Completo: '.utf8_decode($fatd->lancamentoFo->operador->nome), 0, 1, 'L', false);
        $pdf->SetXY(145, 93);
        $pdf->Cell(0, 0, 'Nr / Idt ' . $fatd->lancamentoFo->operador->idt_militar . ' ' . $fatd->lancamentoFo->operador->idt_militar_o_exp, 0, 1, 'L', false);
        
        $pdf->Cell(0, 8, 'Subunidade/OM: ' . utf8_decode((isset($fatd->lancamentoFo->operador->omcts) 
        ? $fatd->lancamentoFo->operador->omcts->sigla_omct 
        : $fatd->lancamentoFo->operador->qms->qms_sigla)) , 0, 1, 'L', false);

        $pdf->Line(10, 105, 200, 105);

        $pdf->SetFont('Times', 'B', 12);
        $pdf->SetXY(10, 110);
        $pdf->Cell(0, 4, utf8_decode('RELATO DO FATO'), 0, 1, 'C', false);

        $pdf->SetFont('Times', '', 10);
        $pdf->WriteHTML(utf8_decode($fatd->lancamentoFo->observacao));

        $pdf->SetXY(10, 190);
        
        $guarnicao = explode('-', (isset($fatd->lancamentoFo->operador->omcts) ? $fatd->lancamentoFo->operador->omcts->gu : $fatd->lancamentoFo->operador->qms->gu) );

        $pdf->Cell(0, 4, utf8_decode($guarnicao[0] . ', ' . $guarnicao[1] . strftime(', ____ de ____________________________ de %Y', strtotime($fatd->lancamentoFo->data_obs))), 0, 1, 'C', false);
        $pdf->Ln(10);
        $pdf->WriteHTML(utf8_decode('<p align="center">______________________________________________</p>'));
        $pdf->WriteHTML(utf8_decode('<p align="center">' . trim($fatd->lancamentoFo->operador->nome) . ' - ' . $fatd->lancamentoFo->operador->posto->postograd_abrev . '</p>'));
        $pdf->WriteHTML('<p align="center">Participante</p>');

        $pdf->Line(10, 220, 200, 220);
        $pdf->SetFont('Times', 'B', 12);
        $pdf->SetXY(10, 225);
        $pdf->Cell(0, 4, utf8_decode('CIENTE DO MILITAR ARROLADO'), 0, 1, 'C', false);

        $pdf->SetFont('Times', '', 10);
        $pdf->Ln(5);
        $pdf->MultiCell(0, 5, utf8_decode('      Declaro que tenho conhecimento de que me está sendo imputada a autoria dos fatos acima e me foi concedido o prazo de três dias úteis, para apresentar, por escrito, as minhas justificativas ou razões de defesa.'));
        $pdf->Ln(5);

        $pdf->Cell(0, 4, utf8_decode($guarnicao[0] . ', ' . $guarnicao[1] . strftime(', ____ de ____________________________ de %Y', strtotime($fatd->lancamentoFo->data_obs))), 0, 1, 'C', false);
        $pdf->Ln(10);

        $pdf->SetFont('Times', '', 12);
        $pdf->WriteHTML(utf8_decode('<p align="center">______________________________________________</p>'));
        $pdf->WriteHTML(utf8_decode('<p align="center">' . trim($fatd->lancamentoFo->aluno->nome_completo)) . ' - Aluno(a) CFGS/' . $fatd->lancamentoFo->aluno->ano_formacao->ano_cfs . '</p>');
        $pdf->SetXY(0, 275);
        $pdf->WriteHTML('<b><p align="center">Arrolado(a)</p></b>');


        //2ª Página
        $pdf->AddPage();
        //Cria a Borda
        $pdf->Rect(10, 10, 190, 278);

        $pdf->SetFont('Times', 'B', 12);
        $pdf->Ln(5);
        $pdf->Cell(0, 4, utf8_decode('JUSTIFICATIVAS / RAZÕES DE DEFESA'), 0, 1, 'C', false);

        $pdf->SetXY(15, 20);
        $pdf->Cell(180, 8, '', 'B', 2, 'C', false);
        $pdf->Cell(180, 8, '', 'B', 2, 'C', false);
        $pdf->Cell(180, 8, '', 'B', 2, 'C', false);
        $pdf->Cell(180, 8, '', 'B', 2, 'C', false);
        $pdf->Cell(180, 8, '', 'B', 2, 'C', false);
        $pdf->Cell(180, 8, '', 'B', 2, 'C', false);
        $pdf->Cell(180, 8, '', 'B', 2, 'C', false);
        $pdf->Cell(180, 8, '', 'B', 2, 'C', false);
        $pdf->Cell(180, 8, '', 'B', 2, 'C', false);
        $pdf->Cell(180, 8, '', 'B', 2, 'C', false);
        $pdf->Cell(180, 8, '', 'B', 2, 'C', false);

        $pdf->SetFont('Times', '', 10);
        $pdf->Ln(5);
        $pdf->Cell(0, 4, utf8_decode($guarnicao[0] . ', ' . $guarnicao[1] . strftime(', ____ de ____________________________ de %Y', strtotime($fatd->lancamentoFo->data_obs))), 0, 1, 'C', false);
        $pdf->Ln(12);

        $pdf->SetFont('Times', '', 12);
        $pdf->WriteHTML(utf8_decode('<p align="center">' . trim($fatd->lancamentoFo->aluno->nome_completo)) . ' - Aluno(a) CFGS/' . $fatd->lancamentoFo->aluno->ano_formacao->ano_cfs . '</p>');
        $pdf->SetXY(0, 135);
        $pdf->WriteHTML('<b><p align="center">Arrolado(a)</p></b>');

        $pdf->Line(10, 140, 200, 140);
        $pdf->Ln(10);
        $pdf->Cell(0, 4, utf8_decode('DECISÃO DA AUTORIDADE COMPETENTE'), 0, 1, 'C', false);
        $pdf->Ln(10);
        $pdf->Cell(0, 4, utf8_decode('SOU DO PARECER QUE:'), 0, 1, 'L', false);
        $pdf->Cell(0, 6, '', 'B', 2, 'C', false);
        $pdf->Cell(0, 6, '', 'B', 2, 'C', false);
        $pdf->Cell(0, 6, '', 'B', 2, 'C', false);
        $pdf->Cell(0, 6, '', 'B', 2, 'C', false);
        $pdf->Cell(0, 6, '', 'B', 2, 'C', false);
        $pdf->Cell(0, 6, '', 'B', 2, 'C', false);
        $pdf->Cell(0, 6, '', 'B', 2, 'C', false);
        $pdf->Cell(0, 6, '', 'B', 2, 'C', false);
        $pdf->Cell(0, 6, '', 'B', 2, 'C', false);
        $pdf->Cell(0, 6, '', 'B', 2, 'C', false);
        $pdf->Cell(0, 6, '', 'B', 2, 'C', false);

        $pdf->SetFont('Times', '', 10);
        $pdf->Ln(10);
        $pdf->Cell(0, 4, utf8_decode($guarnicao[0] . ', ' . $guarnicao[1] . strftime(', ____ de ____________________________ de %Y', strtotime($fatd->lancamentoFo->data_obs))), 0, 1, 'C', false);

        $pdf->SetFont('Times', '', 12);
        $pdf->Ln(10);
        $pdf->WriteHTML(utf8_decode('<p align="center">' . $comandanteCurso->nome . ' - ' . $comandanteCurso->posto->postograd_abrev . '</p>'));

        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(0, 6, $comandanteCurso->operadoresTipo->funcao_abrev, 0, 1, 'C', false);

        $pdf->SetXY(20, 282);
        $pdf->Cell(0, 5, utf8_decode('PUNIÇÃO PUBLICADA NO Adt BI Nr ______________, de ________ de _______________________ de ________'), 0, 1, 'L', false);

        $pdf->Output('I', 'Ficha_FATD.pdf');
        exit();
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function LancarProvidencia(Request $request, $id)
    {

        if($this->_ownauthcontroller->PerfilCheck([1,2,9001,9999])){
            $providencia = $request->textAreaProvidencias;
            $fatd = ((isset($request->btnPunir) && $request->btnPunir == 'Fatd') ? 'S' : 'N');
            $frad = ((isset($request->btnPunir) && $request->btnPunir == 'Frad') ? 'S' : 'N');

            $lancamentoFo = LancamentoFo::find($id);
            if ($fatd == 'S') {
                $this->LancarFatd($lancamentoFo);
            } else {
                $this->ExcluirFatd($lancamentoFo);
            }

            return ($lancamentoFo->update(['providencia' => $providencia, 'fatd' => $fatd, 'frad' => $frad]));
        }else{
            return false;
        }
        
    }

    private function LancarFatd(LancamentoFo $lancamentoFo)
    {

        if(session()->get('login.qmsID')){
            $where = 'INNER JOIN qms ON (qms.id = alunos.qms_id)
            WHERE qms.id = ' . $lancamentoFo->aluno->qms_id;
        }else{
            $where = 'INNER JOIN omcts ON (omcts.id = alunos.omcts_id)
            WHERE omcts.id = ' . $lancamentoFo->aluno->omcts_id. ' AND alunos.data_matricula = '.$lancamentoFo->aluno->data_matricula;
        }
        //Verificar o último processo da uete
        $processo = DB::select('SELECT fatd.nr_processo, fatd.ano, lancamento_fo.id, lancamento_fo.fatd FROM lancamento_fo
                            LEFT JOIN fatd ON (fatd.lancamento_fo_id = lancamento_fo.id)
                            INNER JOIN alunos ON (alunos.id = lancamento_fo.aluno_id)
                            '.$where.'
                            AND fatd.ano = ' . date('Y') . '
                            ORDER BY fatd.nr_processo DESC limit 1');

        $nr_processo = (isset($processo[0]->nr_processo) ? ($processo[0]->nr_processo + 1) : 1);

        $verificaFatd = Fatd::where(['lancamento_fo_id' => $lancamentoFo->id])->first();

        if (!isset($verificaFatd)) {
            $fatd = Fatd::create(['lancamento_fo_id' => $lancamentoFo->id, 'nr_processo' => $nr_processo, 'ano' => date('Y')]);
        }
    }

    private function ExcluirFatd(LancamentoFo $lancamentoFo)
    {
        if(isset($lancamentoFo->fatdLancada)){
            return $lancamentoFo->fatdLancada->delete();
        }else{
            return true;
        }
    }

    public function LancarFatdSargenteante(Request $request, $id)
    {

        $dados = $request->all();
        $dados['dt_bi'] = FuncoesController::formatDateBrtoEn($request->dt_bi);

        $regras = array();
        if ($request->justificado == 'N') {
            $regras = [
                'enquadramento' => 'required|string', 'bi_desc' => 'required|string', 'dt_bi' => 'required|date', 'comportamento_id' => 'required|numeric'
            ];

            if (!in_array($request->enquadramento_id, [1, 3])) {
                $regras['nr_dias'] = 'required|numeric';
            } else {
                $request->nr_dias = null;
            }
        }

        $atributos = ['dt_bi' => 'Data BI', 'nr_dias' => 'Nº Dias', 'enquadramento' => 'Enquadramento NASE', 'bi_desc' => 'BI', 'comportamento_id' => 'Comportamento'];

        $validador = Validator::make($dados, $regras, [], $atributos);

        if ($validador->passes()) {
            $fatd = Fatd::where(['lancamento_fo_id' => $id])->first();

            $fatd->operador_id = session()->get('login')['operadorID'];
            //Atualiza o Comandante de Curso
            $fatd->lancamentoFo->comandante_operador_id = $fatd->lancamentoFo->aluno->qms->comandanteCurso->id;
            $fatd->lancamentoFo->save();
            $fatd->justificado = isset($request->justificado) ? $request->justificado : $fatd->justificado;

            if ($fatd->justificado == 'N') {
                
                $fatd->enquadramento_id = $request->enquadramento_id;
                $fatd->enquadramento = $request->enquadramento;
                $fatd->bi_desc = $request->bi_desc . ' do ' . (session()->has('login.omctID') ? $fatd->lancamentoFo->aluno->omct->sigla_omct : 'Curso de '.$fatd->lancamentoFo->aluno->qms->qms) ;
                $fatd->dt_bi = FuncoesController::formatDateBrtoEn($request->dt_bi);
                $fatd->nr_dias = $request->nr_dias;
                $fatd->comportamento_id = $request->comportamento_id;
            }

            if ($fatd->save()) {
                $retorno['status'] = 'success';
                $retorno['response'] = 'FATD Atualizada Com Sucesso.';
            } else {
                $retorno['status'] = 'err';
                $retorno['response'] = 'Não Foi Possível Atualizar FATD.';
            }
        } else {
            $retorno['status'] = 'err';
            $retorno['response'] = $validador->errors()->all();
        }

        return response()->json($retorno);
    }

    function CancelarFO(Request $request, $id)
    {
        //Se perfil sargenteante deixa cancelar...
        if($this->_ownauthcontroller->PerfilCheck([4, 9002])){

            $lancamentoFO = LancamentoFo::find($id);

            if($lancamentoFO->cancelado == 'N' && !isset($lancamentoFO->cancelado_operador_id)){
                if($this->ExcluirFatd($lancamentoFO)){
                    return ($lancamentoFO->update(['cancelado_motivo' => $request->textAreaCancelamento, 'cancelado' => 'S', 'cancelado_operador_id' => session()->get('login.operadorID')]));
                }else{
                    false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /*function CorrecaoComandante(){

        $alunosIds = Alunos::retornaAlunosComQmsESAGeral(4)->pluck('id')->toArray();

        //Pega somente os alunos que são da ESA
        $lancamentoFO = LancamentoFo::whereHas('aluno', function ($query) use ($alunosIds){
            $query->whereIn('aluno_id', $alunosIds);
        })->where([['data_obs', '>=', '2022-01-01']])->get();

        foreach($lancamentoFO as $lancamento){

            $comandanteCurso = QMS::find($lancamento->aluno->qms_id)->comandanteCurso;
            
            if(isset($comandanteCurso)){
                $lancamento->comandante_operador_id = $comandanteCurso->id;

                $lancamento->save();
            }else{
                dd($comandanteCurso, $lancamento->aluno, $lancamento->aluno->qms);
            }
            
        }
    }*/
}

<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Models\Alunos;
use App\Models\ConteudoAtitudinal;
use App\Models\LancamentoFo;
use App\Models\OMCT;
use App\Models\Operadores;
use App\Models\TurmasPB;
use Illuminate\Support\Facades\DB;

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

        $invalidaOperacao = false;
        foreach ($listaAlunos as $idAluno) {
            $lancamentoFo = new LancamentoFo();
            $lancamentoFo->aluno_id = $idAluno;
            $lancamentoFo->operador_id = session()->get('login')['operadorID'];
            $lancamentoFo->tipo = $request->radioTipoFO;
            $lancamentoFo->observacao = $request->textAreaObservacaoFO;
            //$lancamentoFo->providencia = $request->textAreaProvidencias;
            $lancamentoFo->data_obs = $dataFO;
            
            $lancamentoFo->conteudo_atitudinal = json_encode($listaAtitudinal);

            if (!$lancamentoFo->save()) {
                $invalidaOperacao = true;
            }else{
                $this->LancarProvidencia($request, $lancamentoFo->id);
            }
        }

        if (!$invalidaOperacao) {
            $retorno['status'] = 'success';
            $retorno['response'] = 'Fato Observado Registrado.';
        } else {
            $retorno['response'] = 'Ocorreu um Erro Ao Salvar o Registro!!';
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
        if ($this->_ownauthcontroller->PermissaoCheck(1)) {
            $uetes = OMCT::where('id', '<>', 1)->get(); //Remove a ESA
        } else {
            $uetes = OMCT::where('id', session()->get('login.omctID'))->get();
        }

        //Registra o Observador na Sessão
        $operadores = Operadores::find(session()->get('login.operadorID'));

        if (isset($operadores)) {
            session()->flash('nomeOperador', $operadores->posto->postograd_abrev . ' ' . $operadores->nome_guerra);

            switch ($id) {
                case 'lancarFO':

                    $conteudoAtitudinal = ConteudoAtitudinal::all();

                    if($this->_ownauthcontroller->PermissaoCheck(1)){
                        $turmas = TurmasPB::all();
                    }else{
                        $turmas = Alunos::where(['omcts_id' => session()->get('login.omctID')])->with('turma')->groupBy('turma_id')->get(['turma_id']);
                        
                    }
                    
                    $rotaTurma = 'ajax/lancamentosTurma';

                    $operadores = Operadores::find(session()->get('login')['operadorID']);
                    $funcaoOperador = explode(',', $operadores->id_funcao_operador);

                    $readOnly = null;

                    return view('lancamentos.lancamentoFatoObservado', compact('uetes', 'conteudoAtitudinal', 'turmas', 'rotaTurma', 'funcaoOperador', 'readOnly'))
                        ->with('ownauthcontroller', $this->_ownauthcontroller);
                case 'viewConsultarFO':

                    $rotaConsulta = 'ajax/listaFatosObservados';

                    return view('lancamentos.lancamentoConsultaFO', compact('uetes', 'rotaConsulta'))->with('ownauthcontroller', $this->_ownauthcontroller);
            }
        }
    }

    public function ViewTurma(Request $request)
    {

        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        $alunosTurma = DB::select("SELECT alunos.id, alunos.numero, alunos.nome_guerra
                        , omcts.id AS omct_id, omcts.sigla_omct, omcts.omct, omcts.gu
                        , turmas_pb.id AS turmas_id, turmas_pb.turma
                        FROM alunos
                            INNER JOIN omcts ON (omcts.id = alunos.omcts_id)
                            INNER JOIN turmas_pb ON (turmas_pb.id = alunos.turma_id)
                        WHERE alunos.data_matricula = $request->anoFormacaoID
                            AND alunos.omcts_id = $request->omctID
                            AND alunos.turma_id = $request->turmaID");

        return view('lancamentos.lancamentoAlunosFO', compact('alunosTurma'));
    }

    public function ViewListaFatosObservados(Request $request)
    {

        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        $whereUete = ((isset($request->omctID) && $request->omctID <> 'todas_omct') ? " AND alunos.omcts_id = $request->omctID" : null);
        $whereNumeroAluno = (isset($request->numero_aluno) ? " AND alunos.numero = $request->numero_aluno" : null);
        $whereNomeGuerra = (isset($request->nome_aluno) ? " AND alunos.nome_guerra = '$request->nome_aluno'" : null);

        $lancamentoFO = DB::select("SELECT lancamento_fo.id, lancamento_fo.data_obs, lancamento_fo.tipo, lancamento_fo.observacao, alunos.numero, alunos.nome_guerra, omcts.omct as uete
                                        , lancamento_fo.providencia, lancamento_fo.fatd
                                        FROM lancamento_fo
                                        INNER JOIN alunos ON (alunos.id = lancamento_fo.aluno_id)
                                        INNER JOIN omcts ON (omcts.id = alunos.omcts_id)
                                        WHERE alunos.data_matricula = $request->ano_formacao " . $whereUete . $whereNumeroAluno . $whereNomeGuerra);

        return view('lancamentos.lancamentoListaFatosObservados', compact('lancamentoFO'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($this->_ownauthcontroller->PermissaoCheck(1)) {
            $uetes = OMCT::where('id', '<>', 1)->get(); //Remove a ESA
        } else {
            $uetes = OMCT::where('id', session()->get('login.omctID'))->get();
        }

        $lancamentoFo = LancamentoFo::find($id);
        $ano_formacao = $lancamentoFo->aluno->data_matricula;

        $conteudoAtitudinal = ConteudoAtitudinal::all();
        $turmas = TurmasPB::all();

        $rotaTurma = 'ajax/lancamentosTurma';

        $operadores = $lancamentoFo->operador;
        $funcaoOperador = explode(',', $operadores->id_funcao_operador);

        $readOnly = 'readOnly';

        return view('lancamentos.lancamentoFatoObservado', compact('uetes', 'conteudoAtitudinal', 'turmas', 'rotaTurma', 'funcaoOperador', 'lancamentoFo', 'ano_formacao', 'readOnly'))
            ->with('ownauthcontroller', $this->_ownauthcontroller);
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

        $lancamentoFo = LancamentoFo::find($id);

        if ($lancamentoFo->providencia == null) {
            if ($this->LancarProvidencia($request, $id)) {
                $retorno['status'] = 'success';
                $retorno['response'] = 'Providência Lançada Com Sucesso.';
            }
        } else {
            $retorno['status'] = 'err';
            $retorno['response'] = 'Já Existe Uma Providência Lançada.';
        }

        return response()->json($retorno);
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

    private function LancarProvidencia(Request $request, $id){

        $providencia = $request->textAreaProvidencias;
        $fatd = ((isset($request->btnPunir) && $request->btnPunir == 'on') ? 'S': 'N');

        return (LancamentoFo::find($id)->update(['providencia' => $providencia, 'fatd' => $fatd]));
    }
}

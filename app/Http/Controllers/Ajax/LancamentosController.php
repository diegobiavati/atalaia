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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PharIo\Manifest\Author;

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

        $lancamentoFo = new LancamentoFo();
        $lancamentoFo->ano_formacao_id = $request->ano_formacao;
        $lancamentoFo->omct_id = $request->omctID;
        $lancamentoFo->operador_id = session()->get('login')['operadorID'];
        $lancamentoFo->tipo = $request->radioTipoFO;
        $lancamentoFo->observacao = $request->textAreaObservacaoFO;
        $lancamentoFo->providencia = $request->textAreaProvidencias;
        $lancamentoFo->turma_id = $request->turmaID;
        $lancamentoFo->data_obs = $dataFO;

        if ($lancamentoFo->save()) {
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

        session()->flash('nomeOperador', $operadores->posto->postograd_abrev . ' ' . $operadores->nome_guerra);

        switch ($id) {
            case 'lancarFO':

                $conteudoAtitudinal = ConteudoAtitudinal::all();
                $turmas = TurmasPB::all();

                $rotaTurma = 'ajax/lancamentosTurma';

                $operadores = Operadores::find(session()->get('login')['operadorID']);
                $funcaoOperador = explode(',', $operadores->id_funcao_operador);

                $readOnly = '';

                return view('lancamentos.lancamentoFatoObservado', compact('uetes', 'conteudoAtitudinal', 'turmas', 'rotaTurma', 'funcaoOperador', 'readOnly'))
                    ->with('ownauthcontroller', $this->_ownauthcontroller);
            case 'consultarFO':

                $conteudoAtitudinal = ConteudoAtitudinal::all();
                $turmas = TurmasPB::all();

                $rotaTurma = 'ajax/lancamentosTurmaConsulta';

                $operadores = Operadores::find(session()->get('login')['operadorID']);
                $funcaoOperador = explode(',', $operadores->id_funcao_operador);

                $readOnly = 'readonly';

                LancamentoFo::all();

                return view('lancamentos.lancamentoFatoObservado', compact('uetes', 'conteudoAtitudinal', 'turmas',  'rotaTurma', 'funcaoOperador', 'readOnly'));
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
}

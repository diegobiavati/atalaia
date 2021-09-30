<?php

namespace App\Http\Controllers\Ajax\Diploma;

use App\Exports\AlunosDiplomaExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Models\AnoFormacao;
use App\Models\ConteudoAtitudinal;
use App\Models\OMCT;
use App\Models\Operadores;
use Illuminate\Http\Request;

class DiplomaController extends Controller
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
        return view('ajax.diploma.view-diploma-periodo', compact('ownauthcontroller'));
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
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
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

                    $conteudoAtitudinal = ConteudoAtitudinal::all();

                    $rotaTurma = '/ajax/lancamentosTurma';

                    $operadores = Operadores::find(session()->get('login')['operadorID']);
                    $funcaoOperador = explode(',', $operadores->id_funcao_operador);

                    if(session()->has('login.qmsID')){
                        $cursos = FuncoesController::retornaCursoPerfilAnoFormacao(AnoFormacao::find($explode[1]));

                        return view('lancamentos.lancamentoFatoObservado', compact('cursos', 'conteudoAtitudinal', 'turmas', 'rotaTurma', 'funcaoOperador', 'readOnly'))
                            ->with('ownauthcontroller', $this->_ownauthcontroller);
                    }

                    return view('lancamentos.lancamentoFatoObservado', compact('uetes', 'conteudoAtitudinal', 'turmas', 'rotaTurma', 'funcaoOperador', 'readOnly'))
                        ->with('ownauthcontroller', $this->_ownauthcontroller);
                case 'viewConsultarFO':

                    $rotaConsulta = '/ajax/listaFatosObservados';

                    if(count($explode) == 1){
                        return view('lancamentos.lancamentoConsultaFOSelecaoAno');
                    }

                    if(session()->has('login.qmsID')){
                        $cursos = FuncoesController::retornaCursoPerfilAnoFormacao(AnoFormacao::find($explode[1]));
                        
                        return view('lancamentos.lancamentoConsultaFO', compact('cursos', 'rotaConsulta'))->with('ownauthcontroller', $this->_ownauthcontroller);
                    }

                    return view('lancamentos.lancamentoConsultaFO', compact('uetes', 'rotaConsulta'))->with('ownauthcontroller', $this->_ownauthcontroller);
                case 'viewConsultarFATD':
                    if(count($explode) == 1){
                        return view('lancamentos.lancamentoConsultaFATDSelecaoAno');
                    }

                    if(session()->has('login.qmsID')){
                        $cursos = FuncoesController::retornaCursoPerfilAnoFormacao(AnoFormacao::find($explode[1]));
                        
                        return view('lancamentos.lancamentoConsultaFATD', compact('cursos'))->with('ownauthcontroller', $this->_ownauthcontroller);
                    }

                    return view('lancamentos.lancamentoConsultaFATD', compact('uetes'))->with('ownauthcontroller', $this->_ownauthcontroller);
            }
        }
    }

    public function exportAlunos(){
        return new AlunosDiplomaExport();//Excel::download(new AlunosDiplomaExport, 'alunosDiploma.xlsx');
    }
}
?>
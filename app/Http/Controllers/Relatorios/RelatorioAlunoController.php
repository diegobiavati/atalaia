<?php

namespace App\Http\Controllers\Relatorios;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Http\OwnClasses\ClassLog;
use App\Models\Alunos;
use App\Models\AnoFormacao;
use App\Models\Areas;
use App\Models\OMCT;
use App\Models\SituacaoMatricula;
use App\Models\SituacoesDiversas;

class RelatorioAlunoController extends Controller
{

    protected $classLog;
    protected $ownauthcontroller;

    public function __construct(OwnAuthController $ownauthcontroller, ClassLog $classLog)
    {
        $this->ownauthcontroller = $ownauthcontroller;
        $this->classLog = $classLog;
        $this->classLog->ip = $_SERVER['REMOTE_ADDR'];
    }

    public function ViewRelatorioGeral(Request $request)
    {

        if ($this->ownauthcontroller->PermissaoCheck(1)) {
            $uetes = OMCT::where('id', '<>', 1)->get(); //Remove a ESA
        } else {
            $uetes = OMCT::where('id', session()->get('login.omctID'))->get();
        }

        $anoFormacao = AnoFormacao::whereId($request->id_ano_formacao)->get()->first();
        $areas = Areas::get();

        $alunos = new Alunos();
        $colunas = $alunos->getFillableDescription();

        // Repassando para a view
        return view('relatorios.relacao-geral-alunos', compact('uetes', 'anoFormacao', 'areas', 'colunas'))
            ->with('ownauthcontroller', $this->ownauthcontroller);
    }

    public function ViewFichaIndividualAluno(Request $request)
    {
        if ($this->ownauthcontroller->PermissaoCheck(1)) {
            $uetes = OMCT::where('id', '<>', 1)->get(); //Remove a ESA
        } else {
            $uetes = OMCT::where('id', session()->get('login.omctID'))->get();
        }

        $anoFormacao = AnoFormacao::whereId($request->id_ano_formacao)->get()->first();

        //Repassando para a view
        return view('relatorios.relacao-ficha-individual-aluno', compact('uetes', 'anoFormacao'))
            ->with('ownauthcontroller', $this->ownauthcontroller);
    }

    function RelacaoAlunosProntos(Request $request)
    {

        if (!$this->ownauthcontroller->PermissaoCheck(1) && $request->omctID != session()->get('login.omctID')) {
            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';
        } else {

            $arrayExcessao = ['ano_formacao_id', 'omctID', 'ordem', 'alteracao', 'ciente', 'relacao'];
            $arrayColunas = [];

            $ano_formacao = AnoFormacao::find($request->ano_formacao_id);

            if (isset($request->segmento)) {
                $segmento_array = $request->segmento;
            } else {
                $segmento_array = array('M', 'F');
            }

            if (isset($request->areas)) {
                $areas_array = $request->areas;
            } else {
                $areas_array = array();
            }

            $alunosSelect = Alunos::with('ano_formacao')
                ->with('turma')
                ->with('area')
                ->with('instrumento')
                ->with('uf_nascimento')
                ->with('situacao_anterior')
                ->with('situacao_matricula')
                //->with('situacao_atual')
                ->with('uf')
                ->with('profissao_mae')
                ->with('profissao_pai')
                ->with('raca')
                ->with('religiao')
                ->with('escolaridade')
                ->with('renda')
                ->with('estado_civil')
                ->whereIn('sexo', $segmento_array)
                ->whereIn('area_id', $areas_array)
                ->where('data_matricula', $request->ano_formacao_id)
                ->where('omcts_id', (($request->omctID == 'todas_omct') ? '<>' : '='), (($request->omctID == 'todas_omct') ? 1 : $request->omctID))
                ->orderBy('numero', 'desc')->get();

            switch ($request->omctID) {
                case 'todas_omct';
                    $omct = OMCT::where('id', '<>', 1)->get(); //Remove a ESA do select
                    break;
                default:
                    $omct = OMCT::where('id', $request->omctID)->get();
                    break;
            }

            $alunos = [];

            foreach ($alunosSelect as $aluno) {
                $alunos[$aluno->omcts_id][] = $aluno;
            }

            $aluno = new Alunos();

            //Pegar os campos dinâmicamente
            foreach ($request->request as $key => $valor) {
                if (!is_array($valor)) {
                    if (!in_array($key, $arrayExcessao)) {
                        $arrayColunas[$key] = $aluno->getFillableDescription()[$key];
                    }
                }
            }

            $aluno = null;

            $this->classLog->RegistrarLog('Acessou lista de alunos prontos', auth()->user()->email);

            return view('relatorios.new-relacao-alunos-prontos')
                ->with('ano_formacao', $ano_formacao)
                ->with('omct', $omct)
                ->with('alunos', $alunos)
                ->with('arrayColunas', $arrayColunas)
                ->with('relacao', $request->relacao);
        }
    }

    public function ViewRelacaoAlunos(Request $request)
    {
        $anoFormacao = AnoFormacao::whereId($request->ano_formacao_id)->get()->first();

        $rota = 'relatorios.relacao-ficha-individual-aluno';

        $alunos = Alunos::carregaAlunosVsAlunosSitDiv($anoFormacao->id);

        if ($request->omctID != 'todas_omct') {
            $alunos = Alunos::filtraAlunosOmct($alunos, $request->omctID);
        }

        if (isset($request->numero_aluno)) {
            $alunos = Alunos::filtraAlunosNumero($alunos, $request->numero_aluno);
        }

        if (isset($request->nome_aluno)) {
            $alunos = Alunos::filtraAlunosNome($alunos, $request->nome_aluno);
        }

        return view('relatorios/ficha-individual-do-aluno', compact('anoFormacao', 'rota', 'alunos'));
    }

    public function RelatorioRelacaoAlunos(Request $request){

        $valida = FuncoesController::validaSessao();

        if (isset($valida)) {
            return $valida;
        }
        
        $params = json_decode($request->params);
        
        $aluno = new Alunos(collect($params)->all());
        $aluno->load('imagem_aluno')->load('situacao_anterior')->load('situacao_matricula');
        $aluno->importaImagemAluno();

        //Se for Aluno em Situação Diversas tem que pegar de SituacoesDiversas::find($alunoSitDiv->situacoes_diversas_id)->situacao;
        //Senão SituacaoMatricula::find($aluno->id_situacao_matricula)->situacao_matricula;
        if(isset($params->situacoes_diversas_id)){
            $situacaoAtual = SituacoesDiversas::find($params->situacoes_diversas_id)->situacao;
        }else{
            $situacaoAtual = SituacaoMatricula::find($aluno->id_situacao_matricula)->situacao_matricula;
        }

        return view('relatorios/relatorio-ficha-individual-aluno', compact('aluno', 'situacaoAtual'));
    }
}

<?php

namespace App\Http\Controllers\Aluno;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Http\Controllers\Utilitarios\SmbClientPhp;
use App\Http\OwnClasses\ClassLog;

use App\Models\AlunosDependente;
use App\Models\Banco;
use App\Models\Escolaridade;
use App\Models\EstadoCivil;
use App\Models\Parentesco;
use App\Models\Profissao;
use App\Models\Raca;
use App\Models\Religiao;
use App\Models\Renda;
use App\Models\SituacaoAnterior;
use App\Models\SituacaoAtual;
use App\Models\SituacaoMatricula;
use App\Models\Uf;
use App\Models\TurmasPB;
use App\Models\Alunos;
use App\Models\AlunosCurso;
use App\Models\AlunosSitDiv;
use App\Models\Areas;
use App\Models\AnoFormacao;
use App\Models\Instrumentos;
use App\Models\OMCT;
use App\Models\SituacoesDiversas;
use Illuminate\Support\Facades\Validator;

class AlunoApiController extends Controller
{
    protected $classLog;
    protected $ownauthcontroller;

    public function __construct(Alunos $aluno, Request $request, ClassLog $classLog, OwnAuthController $ownauthcontroller)
    {
        $this->aluno = $aluno;
        $this->request = $request;
        $this->classLog = $classLog;
        $this->classLog->ip = $_SERVER['REMOTE_ADDR'];

        $this->ownauthcontroller = $ownauthcontroller;
    }

    protected function loadDependencia()
    {
        $omcts = OMCT::where('id', '<>', 1)->get();
        foreach ($omcts as $omct) {
            if ($this->ownauthcontroller->PermissaoCheck(1)) {
                $options_omcts[] = $omct;
            } else if (session()->get('login.omctID') == $omct->id) {
                $options_omcts[] = $omct;
            }
        }

        $areas = Areas::get();
        $instrumentos = Instrumentos::get();
        $turmas = TurmasPB::get();
        $anoCorrente = AnoFormacao::orderBy('formacao', 'desc')->get();
        $ufs = Uf::get();
        $profissaos = Profissao::get();
        $situacaoMatriculas = SituacaoMatricula::get();
        $situacaoAtuals = "";
        $situacaoAnteriors = SituacaoAnterior::get();
        $situacaoEstadoCivils = EstadoCivil::get();
        $situacaoRacas = Raca::get();
        $situacaoReligiaos = Religiao::get();
        $situacaoEscolaridades = Escolaridade::get();
        $situacaoParentescos = Parentesco::get();
        $situacaoRendas = Renda::get();
        $situacaoBancos = Banco::get();

        //Para Carregar o datalist 
        //$alunos['list'] = Alunos::get();
        $alunos['list'] = Alunos::carregaAlunosVsAlunosSitDiv();

        return compact(
            'options_omcts',
            'areas',
            'instrumentos',
            'turmas',
            'anoCorrente',
            'ufs',
            'profissaos',
            'situacaoMatriculas',
            'situacaoAtuals',
            'situacaoAnteriors',
            'situacaoEstadoCivils',
            'situacaoRacas',
            'situacaoReligiaos',
            'situacaoEscolaridades',
            'situacaoParentescos',
            'situacaoRendas',
            'situacaoBancos',
            'alunos'
        );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $compact = $this->loadDependencia();

        // Repassando para a view
        return view('admin.aluno.index', $compact)
            ->with('ownauthcontroller', $this->ownauthcontroller);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        $retorno['status'] = 'err';
        $retorno['response'] = 'Houve um Erro';


        $dados = $request->all();
        $dados['data_nascimento'] = FuncoesController::formatDateBrtoEn($dados['data_nascimento']);
        $dados['primeira_data_praca'] = FuncoesController::formatDateBrtoEn($dados['primeira_data_praca']);
        $dados['data_cb'] = FuncoesController::formatDateBrtoEn($dados['data_cb']);
        $dados['data_sgttemp'] = FuncoesController::formatDateBrtoEn($dados['data_sgttemp']);
        $dados['data_baixa_ultima_om'] = FuncoesController::formatDateBrtoEn($dados['data_baixa_ultima_om']);
        $dados['doc_idt_militar_dt_exp'] = FuncoesController::formatDateBrtoEn($dados['doc_idt_militar_dt_exp']);

        $validador = Validator::make($dados, $this->aluno->regras(), [], $this->aluno->atributos());

        $validaAluno = Alunos::where([['nome_guerra', '=', $dados['nome_guerra']], ['data_matricula', '=', $dados['data_matricula']]])->get();
        if (sizeof($validaAluno) > 0) {
            $retorno['response'] = ['O Nome de Guerra já está sendo utilizado.'];
            return response()->json($retorno);
        }

        if (!preg_match('|^[\pL\s]+$|u', $dados['nome_guerra'])) {
            $retorno['response'] = ['Existem Caracteres Não Permitidos, Utilize Somente Letras.'];
            return response()->json($retorno);
        }

        if ($validador->passes()) {
            $retorno['status'] = 'ok';

            $insert = $this->aluno->create($dados);

            // Verifica se inseriu com sucesso
            // Redireciona para a listagem das categorias
            // Passa uma session flash success (sessão temporária)
            if ($insert) {
                $alunoCurso = new AlunosCurso();
                $alunoCurso->id_aluno = $insert->id;
                $alunoCurso->senha = 1234;
                $alunoCurso->nota_cacfs = 0.0;
                $alunoCurso->id_qmsnaipe = 99;

                $alunoCurso->save();

                //Dependentes
                if (isset($request->id_parentesco)) {

                    $alunosDependentes = new AlunosDependente($dados);

                    for ($i = 0; $i < sizeof($alunosDependentes->id_parentesco); $i++) {
                        $dependente = new AlunosDependente();
                        $dependente->id_aluno = $insert->id;
                        $dependente->id_parentesco = (isset($alunosDependentes->id_parentesco[$i]) ? $alunosDependentes->id_parentesco[$i] : null);
                        $dependente->dep_nome_completo = (isset($alunosDependentes->dep_nome_completo[$i]) ? $alunosDependentes->dep_nome_completo[$i] : null);
                        $dependente->dep_data_nascimento = (isset($alunosDependentes->dep_data_nascimento[$i]) ? FuncoesController::formatDateBrtoEn($alunosDependentes->dep_data_nascimento[$i]) : null);
                        $dependente->dep_naturalidade = (isset($alunosDependentes->dep_naturalidade[$i]) ? $alunosDependentes->dep_naturalidade[$i] : null);
                        $dependente->dep_endereco = (isset($alunosDependentes->dep_endereco[$i]) ? $alunosDependentes->dep_endereco[$i] : null);
                        $dependente->dep_id_profissao = (isset($alunosDependentes->dep_id_profissao[$i]) ? $alunosDependentes->dep_id_profissao[$i] : null);
                        $dependente->dep_id_escolaridade = (isset($alunosDependentes->dep_id_escolaridade[$i]) ? $alunosDependentes->dep_id_escolaridade[$i] : null);
                        $dependente->dep_trabalho_ativo = (isset($alunosDependentes->dep_trabalho_ativo[$i]) ? $alunosDependentes->dep_trabalho_ativo[$i] : null);
                        $dependente->dep_trabalho_funcao = (isset($alunosDependentes->dep_trabalho_funcao[$i]) ? $alunosDependentes->dep_trabalho_funcao[$i] : null);
                        $dependente->dep_bi_publicacao = (isset($alunosDependentes->dep_bi_publicacao[$i]) ? $alunosDependentes->dep_bi_publicacao[$i] : null);

                        $dependente->save();
                    }
                }

                $retorno['status'] = 'ok';
                $retorno['response'] = 'Aluno(a) inserido com sucesso!';
                $retorno['id_aluno'] = $insert->id;
                $this->classLog->RegistrarLog('Implantou aluno ' . $dados['nome_completo'] . ' no sistema', auth()->user()->email);
            }
        } else {
            $retorno['response'] = $validador->errors()->all();
        }

        // Redireciona de volta com uma mensagem de erro
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
        $compact = $this->loadDependencia();

        $id = trim(explode('-', $id)[0]);

        $aluno = Alunos::with('ano_formacao')->with('dependentes')->with('imagem_aluno')->find($id);

        if (!isset($aluno)) {
            $alunoSitDiv = AlunosSitDiv::with('situacaoDivHistorico')->find($id);

            $unserialize = unserialize($alunoSitDiv->situacaoDivHistorico->data);

            foreach (TurmasPB::all() as $turma) {
                $turmas[$turma['turma']] = $turma;
            }

            foreach (OMCT::all() as $omct) {
                $omcts[$omct['sigla_omct']] = $omct;
            }

            foreach (Areas::all() as $area) {
                $areas[$area['area']] = $area;
            }

            if (isset($unserialize['cadastro']['ano_formacao'])) {
                //Precisa converter porque o Julião salva com outro nome alguns campos...
                $unserialize['cadastro']['data_matricula'] = AnoFormacao::where('formacao', $unserialize['cadastro']['ano_formacao'])->first()->id;
                $unserialize['cadastro']['atleta_marexaer'] = $unserialize['cadastro']['atleta'];
                $unserialize['cadastro']['turma_id'] = $turmas[$unserialize['cadastro']['turma']]['id'];
                $unserialize['cadastro']['omcts_id'] = $omcts[$unserialize['cadastro']['omct']]['id'];
                $unserialize['cadastro']['area_id'] = $areas[$unserialize['cadastro']['area']]['id'];
            }


            $aluno = new Alunos($unserialize['cadastro']);
            $aluno->id = $id;

            $aluno->load('imagem_aluno')->load('dependentes')->load('ano_formacao');

            $compact['situacaoAtuals'] = SituacoesDiversas::find($alunoSitDiv->situacoes_diversas_id)->situacao;
        } else {
            $compact['situacaoAtuals'] = SituacaoMatricula::find($aluno->id_situacao_matricula)->situacao_matricula;
        }

        $aluno->importaImagemAluno();

        // Repassando para a view
        if (isset($aluno)) {
            return view('admin.aluno.index', $compact)
                ->with('ownauthcontroller', $this->ownauthcontroller)
                ->with('aluno', $aluno);
        } else {
            return view('aluno.aluno-erro');
        }
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
        $retorno['status'] = 'err';
        $retorno['response'] = ['Nada foi encontrado.', 'Aluno pode se encontrar em situações diversas, por isso não receberá atualizações por essa rotina.'];

        if (!$aluno = $this->aluno->find($id))
            return response()->json($retorno);

        $dados = $request->all();

        $dados['data_nascimento'] = FuncoesController::formatDateBrtoEn($dados['data_nascimento']);
        $dados['primeira_data_praca'] = FuncoesController::formatDateBrtoEn($dados['primeira_data_praca']);
        $dados['data_cb'] = FuncoesController::formatDateBrtoEn($dados['data_cb']);
        $dados['data_sgttemp'] = FuncoesController::formatDateBrtoEn($dados['data_sgttemp']);
        $dados['data_baixa_ultima_om'] = FuncoesController::formatDateBrtoEn($dados['data_baixa_ultima_om']);
        $dados['doc_idt_militar_dt_exp'] = FuncoesController::formatDateBrtoEn($dados['doc_idt_militar_dt_exp']);

        $validador = Validator::make($dados, $aluno->regras(), [], $this->aluno->atributos());

        if (
            trim($dados['nome_guerra']) <> trim($aluno->nome_guerra)
            || ($dados['data_matricula'] <> $aluno->data_matricula)
        ) {

            $validaAluno = Alunos::where([['nome_guerra', '=', $dados['nome_guerra']], ['data_matricula', '=', $dados['data_matricula']]])->get();

            if (sizeof($validaAluno) > 0) {
                $retorno['response'] = ['O Nome de Guerra já está sendo utilizado.'];
                return response()->json($retorno);
            }
        }

        if (!preg_match('|^[\pL\s]+$|u', $dados['nome_guerra'])) {
            $retorno['response'] = ['Existem Caracteres Não Permitidos, Utilize Somente Letras.'];
            return response()->json($retorno);
        }

        if ($validador->passes()) {

            $update = $aluno->update($dados);

            if ($update) {

                //Dependentes
                if (isset($dados['id_parentesco'])) {

                    $alunosDependentes = new AlunosDependente($dados);

                    for ($i = 0; $i < sizeof($alunosDependentes->id_parentesco); $i++) {

                        $isUpdate = false;
                        if (isset($alunosDependentes->id_dependente[$i])) {
                            $isUpdate = true;

                            $dependente = AlunosDependente::find($alunosDependentes->id_dependente[$i]);
                        } else {
                            $isUpdate = false;

                            $dependente = new AlunosDependente();
                            $dependente->id_aluno = $aluno->id;
                        }

                        $dependente->id_parentesco = (isset($alunosDependentes->id_parentesco[$i]) ? $alunosDependentes->id_parentesco[$i] : null);
                        $dependente->dep_nome_completo = (isset($alunosDependentes->dep_nome_completo[$i]) ? $alunosDependentes->dep_nome_completo[$i] : null);
                        $dependente->dep_data_nascimento = (isset($alunosDependentes->dep_data_nascimento[$i]) ? FuncoesController::formatDateBrtoEn($alunosDependentes->dep_data_nascimento[$i]) : null);
                        $dependente->dep_naturalidade = (isset($alunosDependentes->dep_naturalidade[$i]) ? $alunosDependentes->dep_naturalidade[$i] : null);
                        $dependente->dep_endereco = (isset($alunosDependentes->dep_endereco[$i]) ? $alunosDependentes->dep_endereco[$i] : null);
                        $dependente->dep_id_profissao = (isset($alunosDependentes->dep_id_profissao[$i]) ? $alunosDependentes->dep_id_profissao[$i] : null);
                        $dependente->dep_id_escolaridade = (isset($alunosDependentes->dep_id_escolaridade[$i]) ? $alunosDependentes->dep_id_escolaridade[$i] : null);
                        $dependente->dep_trabalho_ativo = (isset($alunosDependentes->dep_trabalho_ativo[$i]) ? $alunosDependentes->dep_trabalho_ativo[$i] : null);
                        $dependente->dep_trabalho_funcao = (isset($alunosDependentes->dep_trabalho_funcao[$i]) ? $alunosDependentes->dep_trabalho_funcao[$i] : null);
                        $dependente->dep_bi_publicacao = (isset($alunosDependentes->dep_bi_publicacao[$i]) ? $alunosDependentes->dep_bi_publicacao[$i] : null);

                        if ($isUpdate) {
                            $dependente->update();
                        } else {
                            $dependente->save();
                        }
                    }
                }

                $retorno['status'] = 'ok';
                $retorno['response'] = 'Aluno(a) atualizado com sucesso!';
                $retorno['id_aluno'] = $aluno->id;
                $this->classLog->RegistrarLog('Alterou aluno ' . $aluno->id . ' - ' . $aluno->nome_completo . ' no sistema', auth()->user()->email);
            }
        } else {
            $retorno['status'] = 'err';
            $retorno['response'] = $validador->errors()->all();
        }

        // Redireciona de volta com uma mensagem de erro
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
}

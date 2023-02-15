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
use App\Models\AlunosClassificacao;
use App\Models\AlunosCurso;
use App\Models\AlunosSitDiv;
use App\Models\Areas;
use App\Models\AnoFormacao;
use App\Models\Disciplinas;
use App\Models\Instrumentos;
use App\Models\Mencoes;
use App\Models\ModeloNotasCapitani;
use App\Models\OMCT;
use App\Models\SituacoesDiversas;
use App\Models\TurmasEsa;
use App\Models\Users;
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
        $this->classLog->ip = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR']: null);

        $this->ownauthcontroller = $ownauthcontroller;
    }

    protected function loadDependencia()
    {
        $omcts = OMCT::where('id', '<>', 1)->get();
        foreach ($omcts as $omct) {
            if ($this->ownauthcontroller->PermissaoCheck(1) || session()->has('login.qmsID')) {
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

        $readOnly = (!$this->ownauthcontroller->PermissaoCheck(1)) ? 'readonly' : '';

        //Para Carregar o datalist 
        if(request()->is('gaviao/*')){
            $alunos['list'] = Alunos::retornaAlunosComQmsESA();
        }else{
            $alunos['list'] = Alunos::carregaAlunosVsAlunosSitDiv();
        }

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
            'alunos',
            'readOnly'
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

        $qms = array();
        // Repassando para a view
        return view('admin.aluno.index', $compact)
            ->with('ownauthcontroller', $this->ownauthcontroller)
            ->with('qms', $qms);
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

        $dados['cpf_pai'] = preg_replace('/[^0-9]/', '', $dados['cpf_pai']);
        $dados['cpf_mae'] = preg_replace('/[^0-9]/', '', $dados['cpf_mae']);

        $validador = Validator::make($dados, $this->aluno->regras(), [], $this->aluno->atributos());

        if(!isset($dados['data_matricula'])){
            $retorno['response'] = ['Informe o Ano de Formação.'];
            return response()->json($retorno);
        }
        $validaAluno = Alunos::where([['nome_guerra', '=', $dados['nome_guerra']], ['data_matricula', '=', $dados['data_matricula']]])->get();
        if (sizeof($validaAluno) > 0) {
            $retorno['response'] = ['O Nome de Guerra já está sendo utilizado.'];
            return response()->json($retorno);
        }

        if (!preg_match('|^[\pL\s]+$|u', $dados['nome_guerra'])) {
            $retorno['response'] = ['Existem Caracteres Não Permitidos Ou O Nome de Guerra Está em Branco, Utilize Somente Letras.'];
            return response()->json($retorno);
        }

        if ($validador->passes()) {
            $retorno['status'] = 'ok';

            $dados['nome_guerra'] = strtoupper($dados['nome_guerra']);
            $dados['nome_completo'] = strtoupper($dados['nome_completo']);
            $dados['bonificacao_atleta'] = ($dados['atleta_marexaer'] == 'S' ? $dados['bonificacao_atleta'] : null);
            $insert = $this->aluno->create($dados);

            // Verifica se inseriu com sucesso
            // Redireciona para a listagem das categorias
            // Passa uma session flash success (sessão temporária)
            if ($insert) {
                $alunoCurso = new AlunosCurso();
                $alunoCurso->id_aluno = $insert->id;
                $alunoCurso->senha = 1234;
                $alunoCurso->nota_cacfs = 0.0;
                //$alunoCurso->id_qmsnaipe = 99;
                $alunoCurso->id_qms = null;

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

        $qms = array();
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

            $cursos = FuncoesController::retornaCursoPerfilAnoFormacao(AnoFormacao::find( (isset($aluno->ano_formacao_reintegr_id) ? $aluno->ano_formacao_reintegr_id : $aluno->data_matricula) ));

            if($aluno->sexo == 'M'){
                $qms = $cursos->where('segmento', 'M');
            }else{
                $qms = $cursos->where('segmento', 'F');
            }
        }

        $aluno->importaImagemAluno();

        // Repassando para a view
        if (isset($aluno)) {
            return view('admin.aluno.index', $compact)
                ->with('ownauthcontroller', $this->ownauthcontroller)
                ->with('aluno', $aluno)
                ->with('qms', $qms);
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

        $dados['cpf_pai'] = preg_replace('/[^0-9]/', '', $dados['cpf_pai']);
        $dados['cpf_mae'] = preg_replace('/[^0-9]/', '', $dados['cpf_mae']);


        if(session()->get('login.omctID')){
            $dados['qms_id'] = (isset($dados['id_qms']) ? $dados['id_qms'] : null);
            $dados['periodo_cfs'] = (isset($dados['id_qms']) ? 'PQ' : 'PB');
        }
        
        $validador = Validator::make($dados, (($this->ownauthcontroller->PermissaoCheck(1)) ? $aluno->regrasEsa() : $aluno->regras()), [], $this->aluno->atributos());

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
            $retorno['response'] = ['Existem Caracteres Não Permitidos Ou O Nome de Guerra Está em Branco, Utilize Somente Letras.'];
            return response()->json($retorno);
        }

        if ($validador->passes()) {

            $dados['bonificacao_atleta'] = ((isset($dados['atleta_marexaer']) && $dados['atleta_marexaer'] == 'S') ? $dados['bonificacao_atleta'] : null);

            if(trim($aluno->email) <> trim($dados['email'])){
                Users::where(['email' => $aluno->email])->update(['email' => $dados['email']]);
            }
            
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

    public static function viewListagemTurma(Request $request)
    {
        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        $turmaESA = TurmasEsa::find($request->id_turma);
        
        $turmaAlunos = Alunos::retornaAlunosComQmsEspecifica($turmaESA->qms->escolhaQms->anoFormacao->id, [$turmaESA->qms_id])
                ->where('turma_esa_id', $turmaESA->id)->get();

        return view('ajax.componentes.componenteTurmaAlunos', compact('turmaAlunos'));
    }

    public function modeloPBCapitani(Request $request){

            ModeloNotasCapitani::truncate();

            $alunosID = Alunos::retornaAlunosComQmsESAGeral($request->id_ano_formacao)->get(['id']);

            $id_anoForm_disc = Alunos::retornaAlunosComQmsESAGeral($request->id_ano_formacao)
                                ->groupBy('data_matricula')->pluck('data_matricula')->toArray();

            $alunos_classif = AlunosClassificacao::whereIn('aluno_id', $alunosID)
            ->whereHas('aluno', function($q) use ($request) {
                $q->orderBy('numero', 'asc');
            })->get();

            //dd(unserialize($alunos_classif[0]->data_demonstrativo));

            $disciplinas_capitani['ARMTO'] = 'ARMT';
            $disciplinas_capitani['LID'] = 'LIDMIL';
            $disciplinas_capitani['ÉTICA'] = 'ETICA';
            $disciplinas_capitani['TEC MIL 1'] = 'TEC_MIL_I';
            $disciplinas_capitani['TEC MIL 2'] = 'TEC_MIL_II';
            $disciplinas_capitani['TEC MIL 3'] = 'TEC_MIL_III';
            $disciplinas_capitani['HIST'] = 'HIST';
            $disciplinas_capitani['Hist Mil BR'] = 'HIST';
            $disciplinas_capitani['ING 1'] = 'INGLES_I';
            $disciplinas_capitani['TFM'] = 'TFM1';
            
            $mencoes = Mencoes::get();
            $disciplinas = Disciplinas::whereIn('ano_formacao_id', $id_anoForm_disc)->get();

            foreach($disciplinas as $disciplina){
                if($disciplina->tfm == 'N'){
                    $disciplinas_array['TFM-'.$disciplina->tfm][$disciplinas_capitani[$disciplina->nome_disciplina_abrev]][$disciplina->id] = $disciplina;   
                    $disciplinas_utilz[] = $disciplinas_capitani[$disciplina->nome_disciplina_abrev];
                }
            }

            $disciplinas = $disciplinas_array;
            
            $lista_retorno = array();
            $lista_colunas = array('Ano_Cad', 'Numero', 'ND_TFM');
            
            foreach($disciplinas_utilz as $dsp){
                $lista_colunas[] = $dsp;
            }
            
            array_push($lista_colunas, 'N1', 'class', 'MEN', 'QR_1');

            $lista_colunas = array_unique($lista_colunas);
            
            foreach ($alunos_classif as $class){

                $modeloNotas = new ModeloNotasCapitani();

                $modeloNotas->ano_cad = $class->anoFormacao->formacao;
                $modeloNotas->numero = $class->aluno->numero;
                
                $mencao_aluno = 'Não Informada';
                                
                $data_array = unserialize($class->data_demonstrativo);
                $media_disciplina = null;
                $qr = 0;
                
                if(!isset($data_array['avaliacoes_tfm'])){
                    foreach($data_array as $key => $data){
                        if(is_numeric($key) && $data['disciplina_id'] == 99999){
                            $data_array['avaliacoes_tfm']['media_tfm'] = number_format($data['media'], '3', ',', '');
                            break;
                        }
                    }
                }

                $modeloNotas->nd_tfm = $data_array['avaliacoes_tfm']['media_tfm'];
                
                $modeloNotas->nd_armt = 0.000;
                $modeloNotas->nd_lidmil = 0.000;
                $modeloNotas->nd_etica = 0.000;
                $modeloNotas->nd_tec_mil_1 = 0.000;
                $modeloNotas->nd_tec_mil_2 = 0.000;
                $modeloNotas->nd_tec_mil_3 = 0.000;
                $modeloNotas->nd_hist = 0.000;
                $modeloNotas->nd_ingles_1 = 0.000;

                foreach ($disciplinas['TFM-N'] as $keydisc => $disciplina){
                    foreach($data_array as $key => $data){
                    
                        if(is_numeric($key)){
                            if(in_array($data['disciplina_id'], array_keys($disciplina))){
                                if(isset($data['AR'])){
                                    $qr++;
                                }
                                $media_disciplina = (isset($data['media_sem_peso']) ? $data['media_sem_peso'] : $data['media']);
                                break;
                            }
                        }
                    }
                   
                    $media_disciplina = (is_numeric($media_disciplina) ? number_format($media_disciplina, '3', ',', '') : number_format(0, '3', ',', ''));
                    
                    switch($keydisc){
                        case 'ARMT':
                            $modeloNotas->nd_armt = $media_disciplina;
                            break;
                        case 'LIDMIL':
                            $modeloNotas->nd_lidmil = $media_disciplina;
                            break;
                        case 'ETICA':
                            $modeloNotas->nd_etica = $media_disciplina;
                            break;
                        case 'TEC_MIL_I':
                            $modeloNotas->nd_tec_mil_1 = $media_disciplina;
                            break;
                        case 'TEC_MIL_II':
                            $modeloNotas->nd_tec_mil_2 = $media_disciplina;
                            break;
                        case 'TEC_MIL_III':
                            $modeloNotas->nd_tec_mil_3 = $media_disciplina;
                            break;
                        case 'HIST':
                            $modeloNotas->nd_hist = $media_disciplina;
                            break;
                        case 'INGLES_I':
                            $modeloNotas->nd_ingles_1 = $media_disciplina;
                            break;
                    }

                }

                $modeloNotas->n1 = $class->nota_final_arredondada;
                $modeloNotas->class = $class->classificacao;
                                
                foreach($mencoes as $mencao){
                    if($class->nota_final>=$mencao->inicio && $class->nota_final<=$mencao->fim){
                        $mencao_aluno = $mencao->mencao;
                        break;
                    }
                }

                $modeloNotas->qr_1 = $qr;
                $modeloNotas->men = $mencao_aluno;

                $lista_retorno[] = $modeloNotas;

                $modeloNotas->save();
            }

            $this->classLog->RegistrarLog('Acessou modelo de notas capitani', auth()->user()->email);
            //return view('relatorios.modelo-notas-capitani', compact('mencoes', 'disciplinas', 'disciplinas_capitani', 'alunos_classif'));
            return view('relatorios.modelo-notas-capitani', compact('lista_colunas', 'lista_retorno'));

    }
}
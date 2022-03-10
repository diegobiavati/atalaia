<?php

namespace App\Http\Controllers\Aluno;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Http\OwnClasses\ClassLog;
use App\Models\Alunos;
use App\Models\AlunosSitDiv;
use App\Models\AlunosSitDivHistorico;
use App\Models\AlunosVoluntAv;
use App\Models\AnoFormacao;
use App\Models\AvaliacaoTaf;
use App\Models\Disciplinas;
use App\Models\Motivos;
use App\Models\SituacoesDiversas;

class AlunoSitDiversasController extends Controller
{

    protected $request;
    protected $ownauthcontroller;

    public function __construct(Request $request, OwnAuthController $ownauthcontroller, ClassLog $classLog)
    {
        $this->request = $request;
        $this->ownauthcontroller = $ownauthcontroller;
        $this->classLog = $classLog;
        $this->classLog->ip = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR']: null);
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
        $retorno['response'] = [];

        if ($this->ownauthcontroller->PermissaoCheck(11)) {

            if ($request->tipoRequisicao == 'Salvar') {
                if (!isset($request->sitDivID)) {
                    array_push($retorno['response'], 'Informe se foi Trancamento ou Desligamento.');
                }

                if (!isset($request->opcao)) {
                    array_push($retorno['response'], 'Informe se foi a Pedido ou Ex Ofício.');
                }

                if (!isset($request->adido)) {
                    array_push($retorno['response'], 'Informe se Adido ou Não Adido.');
                }

                if (!isset($request->numero_bi)) {
                    array_push($retorno['response'], 'Informe o Número do BI.');
                }

                if (!isset($request->data_bi)) {
                    array_push($retorno['response'], 'Informe a Data do BI.');
                }

                if (!isset($request->motivo)) {
                    array_push($retorno['response'], 'Informe o Motivo.');
                }

                if (!isset($request->amparo)) {
                    array_push($retorno['response'], 'Informe o Amparo.');
                }
            }
        } else {
            array_push($retorno['response'], 'Usuário Sem Permissão Para Efetuar o Lançamento.');
            return response()->json($retorno);
        }

        $request->data_bi = FuncoesController::formatDateBrtoEn($request->data_bi);

        if (!FuncoesController::validDate($request->data_bi)) {
            array_push($retorno['response'], 'Data BI Inválida');
        }

        if ($retorno['status'] == 'err' && count($retorno['response']) > 0) {
            return response()->json($retorno);
        }

        if ($request->alunoID > 0) {

            if (($alunoSitDiv = AlunosSitDiv::find($request->alunoID))) { //Se tiver AlunoSitDiv é Para Fazer Update

                $alunoSitDiv->situacoes_diversas_id = (isset($request->sitDivID) ? $request->sitDivID : $alunoSitDiv->situacoes_diversas_id);
                $alunoSitDiv->solicitacao_situacao = (isset($request->opcao) ? $request->opcao : $alunoSitDiv->solicitacao_situacao);
                $alunoSitDiv->adido = (isset($request->adido) ? $request->adido : $alunoSitDiv->adido);
                $alunoSitDiv->situacoes_diversas_obs = (isset($request->observacao) ? (($request->observacao == '') ? '' : $request->observacao) : $alunoSitDiv->situacoes_diversas_obs);
                $alunoSitDiv->numero_bi = (isset($request->numero_bi) ? $request->numero_bi : $alunoSitDiv->numero_bi);
                $alunoSitDiv->data_bi = (isset($request->data_bi) ? $request->data_bi : $alunoSitDiv->data_bi);
                $alunoSitDiv->id_motivo = (isset($request->motivo) ? $request->motivo : $alunoSitDiv->id_motivo);
                $alunoSitDiv->amparo = (isset($request->amparo) ? $request->amparo : $alunoSitDiv->amparo);

                if ($alunoSitDiv->save()) {

                    /* Corrigir Problema da Situações Diversas */
                    \App\Models\AlunosNFEI::where('alunos_id', $alunoSitDiv->id)->update(['alunos_situacoes_diversas_id' => $alunoSitDiv->id]);
                    \App\Models\AlunosVoluntAv::where('alunos_id', $alunoSitDiv->id)->update(['alunos_situacoes_diversas_id' => $alunoSitDiv->id]);
                    \App\Models\AvaliacoesNotas::where('alunos_id', $alunoSitDiv->id)->update(['alunos_situacoes_diversas_id' => $alunoSitDiv->id]);
                    \App\Models\LancamentoFo::where('aluno_id', $alunoSitDiv->id)->update(['alunos_situacoes_diversas_id' => $alunoSitDiv->id, 'aluno_id' => null]);

                    if ($aluno = Alunos::find($alunoSitDiv->id)) {
                        $aluno->delete();
                        $this->classLog->RegistrarLog('Adicionou aluno em lista de situações diversas', auth()->user()->email);
                    } 
                    /*Fim Correção*/

                    $retorno['status'] = 'ok';
                    array_push($retorno['response'], $alunoSitDiv->nome_completo . ' foi editado em situações diversas.');

                    $this->classLog->RegistrarLog('Alterou Situação Diversas do aluno número ' . $alunoSitDiv->numero, auth()->user()->email);
                }
            } else {
                $aluno = Alunos::find($request->alunoID);
                $alunoSitDiv = new AlunosSitDiv();
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

                $alunoSitDiv->solicitacao_situacao = $request->opcao;
                $alunoSitDiv->adido = $request->adido;

                $alunoSitDiv->situacoes_diversas_obs = $observacao;

                $alunoSitDiv->numero_bi = $request->numero_bi;
                $alunoSitDiv->data_bi = $request->data_bi;
                $alunoSitDiv->id_motivo = $request->motivo;
                $alunoSitDiv->amparo = $request->amparo;

                if ($alunoSitDiv->save()) {
                    // DADOS A SEREM INSERIDOS NA TABELA alunos_situacoes_diversas_historico
                    $volunt_av = (AlunosVoluntAv::where('alunos_id', $aluno->id)->count() > 0) ? 'S' : 'N';

                    $add_historico = new AlunosSitDivHistorico;
                    $add_historico->aluno = $aluno->nome_completo;
                    $add_historico->aluno_id = $aluno->id;
                    $add_historico->omct_id = $aluno->omcts_id;

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

                    if ($add_historico->save()) {
                        /* AQUI EU ATUALIZO TODAS AS OS CAMPOS alunos_situacoes_diversas_id NAS TABELAS QUE CONTÉM INFORMAÇÕES DO RESPECTIVO ALUNO */
                        \App\Models\AlunosNFEI::where('alunos_id', $aluno->id)->update(['alunos_situacoes_diversas_id' => $aluno->id]);
                        \App\Models\AlunosVoluntAv::where('alunos_id', $aluno->id)->update(['alunos_situacoes_diversas_id' => $aluno->id]);
                        \App\Models\AvaliacoesNotas::where('alunos_id', $aluno->id)->update(['alunos_situacoes_diversas_id' => $aluno->id]);
                        \App\Models\LancamentoFo::where('aluno_id', $aluno->id)->update(['alunos_situacoes_diversas_id' => $aluno->id, 'aluno_id' => null]);

                        if ($aluno->delete()) {
                            $retorno['status'] = 'ok';
                            array_push($retorno['response'], $aluno->nome_completo . ' agora está enquadrado em situações diversas. Clique na aba correspondente para editar.');

                            $this->classLog->RegistrarLog('Adicionou aluno em lista de situações diversas', auth()->user()->email);
                        } else {
                            $retorno['status'] = 'err';
                            array_push($retorno['response'], 'Houve um erro ao mover o aluno!');
                        }
                    } else {
                        $retorno['status'] = 'err';
                        array_push($retorno['response'], 'Houve um erro ao lançar aluno!');
                    }
                } else {
                    $retorno['status'] = 'err';
                    $retorno['response'] = 'Houve um erro ao mover o aluno!';
                }
            }
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

        if (($aluno = AlunosSitDiv::find($id))) {
            //$situacoesDiversas = SituacoesDiversas::find($aluno->situacoes_diversas_id);
            //$motivos = Motivos::find($aluno->id_motivo);

        } else {
            $aluno = Alunos::find($id);
        }

        $situacoesDiversas = SituacoesDiversas::whereNotNull('descricao')->orderBy('id', 'asc')->get();
        $motivos = Motivos::all();
        $html = view('admin.aluno.alunoSitDiversas', compact('aluno', 'situacoesDiversas', 'motivos'))->render();

        $list[] = $html;

        return response()->json($list);
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

        $id = isset($request->idaluno) ? $request->idaluno : $id;
            if($request->requisicao == 'reintegrar'){
                
                //Reintegrando o Aluno de Situacao diversas para Aluno no Ano Letivo
                $alunoSitDiv = AlunosSitDiv::find($id);
                if(isset($alunoSitDiv->id)){
                $idAluno = $alunoSitDiv->id;
                $aluno = new Alunos;

                    $alunoSitDivHistorico = \App\Models\AlunosSitDivHistorico::where('aluno_id', $idAluno)->first();

                    $data = unserialize($alunoSitDivHistorico->data);

                    $aluno->id = $idAluno;
                    foreach($data['cadastro'] as $key => $valor){
                            $aluno->$key = $valor;
                    }

                    if($request->sistema == 'gaviao'){
                        //Informa o ano que ele será integrado
                        $anoFormacao = AnoFormacao::where('per_ativo_qualificacao', 'S')->first();
                        $aluno->ano_formacao_reintegr_id = $anoFormacao->id;
                    }else{
                        $aluno->ano_formacao_reintegr_id = null;
                    }
                    
                    unset($aluno->ano_formacao);
                    unset($aluno->turma);
                    unset($aluno->omct);
                    unset($aluno->area);
                    unset($aluno->atleta);
                    unset($aluno->voluntario_aviacao);

                    $aluno->save();

                    \App\Models\AlunosNFEI::where('alunos_situacoes_diversas_id', $idAluno)->update(['alunos_id' => $idAluno]);
                    \App\Models\AlunosVoluntAv::where('alunos_situacoes_diversas_id', $idAluno)->update(['alunos_id' => $idAluno]);
                    \App\Models\AvaliacoesNotas::where('alunos_situacoes_diversas_id', $idAluno)->update(['alunos_id' => $idAluno]);
                    \App\Models\LancamentoFo::where('alunos_situacoes_diversas_id', $idAluno)->update(['aluno_id' => $idAluno, 'alunos_situacoes_diversas_id' => null]);

        
                    if(isset($data['avaliacoes']['taf'])){
                        $avaliacaoTaf = new AvaliacaoTaf;
                        $avaliacaoTaf->corrida_nota = $data['avaliacoes']['taf']['corrida'];
                        $avaliacaoTaf->flexao_braco_nota = $data['avaliacoes']['taf']['flex_bra'];
                        $avaliacaoTaf->flexao_barra_nota = $data['avaliacoes']['taf']['flex_bar'];
                        $avaliacaoTaf->abdominal_suficiencia = $data['avaliacoes']['taf']['abdom'];
                        $avaliacaoTaf->media = $data['avaliacoes']['taf']['ND'];

                        $avaliacaoTaf->save();
                    }
                
                if($alunoSitDiv->delete()){
                    $retorno['status'] = 'ok';
                    $retorno['response'] = '<td colspan="7" style="text-align: center; background-color: #BCF5A9; color: #696969;"><b>' . $aluno->nome_completo . ' agora está reintegrado no ' . $aluno->omct->sigla_omct . '.</b></td>';
        
                $this->classLog->RegistrarLog('Reintegrou o aluno '.$aluno->nome_completo.' que estava em situações diversas', auth()->user()->email);
                return $retorno;
                }
            }
            
        }else{
            $retorno['status'] = 'err';
            $retorno['response'] = '<td colspan="7" style="text-align: center; background-color: #BCF5A9; color: #696969;"><b>Solicitacao Invalida</b></td>';
            return ;
        }
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

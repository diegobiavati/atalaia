<?php

namespace App\Http\Controllers\Ajax;

//use Request;
use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;
/* MODELS */

use App\Models\Alunos;
use App\Models\Areas;
use App\Models\AnoFormacao;
use App\Models\Instrumentos;
use App\Models\OMCT;
use App\Models\TurmasPB;
use App\Http\OwnClasses\OwnValidator;
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

setlocale(LC_ALL, "pt_BR.utf8");

class AjaxImplantarAlunoController extends Controller
{
    protected $classLog;

    public function __construct(Request $request, ClassLog $classLog)
    {
        $this->request = $request;
        $this->classLog = $classLog;
        $classLog->ip = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null);
    }

    public function DialogImplantarAluno(\App\Http\Controllers\OwnAuthController $ownauthcontroller)
    {

        /* LOOP QUE BUSCA AS OMCTS */

        $options_omcts[] = '<option value="0" disabled selected hidden>OMCT</option>';
        $omcts = OMCT::where('id', '<>', 1)->get();
        foreach ($omcts as $omct) {
            if ($ownauthcontroller->PermissaoCheck(1)) {
                $options_omcts[] = '<option value="' . $omct->id . '">' . $omct->omct . '</option>';
            } elseif (session()->get('login.omctID') == $omct->id) {
                $options_omcts[] = '<option value="' . $omct->id . '">' . $omct->omct . '</option>';
            }
        }

        /* LOOP QUE BUSCA AS ÁREAS */

        $options_areas[] = '<option value="0" disabled selected hidden>Área do Aluno</option>';
        $areas = Areas::get();
        foreach ($areas as $area) {
            $options_areas[] = '<option value="' . $area->id . '">' . $area->area . '</option>';
        }

        /* LOOP QUE BUSCA OS INSTRUMENTO */

        $options_instrumentos[] = '<option value="0" disabled selected hidden>Instrumento</option>';
        $instrumentos = Instrumentos::get();
        foreach ($instrumentos as $instrmento) {
            $options_instrumentos[] = '<option value="' . $instrmento->id . '">' . $instrmento->instrumento . '</option>';
        }

        // TURMAS

        $options_turmas[] = '<option value="0" disabled selected hidden>Turma</option>';
        $turmas = TurmasPB::get();
        foreach ($turmas as $turma) {
            $options_turmas[] = '<option value="' . $turma->id . '">' . $turma->turma . '</option>';
        }

        $options_anoFormacao[] = '<option value="0" disabled selected hidden>Ano de Formação</option>';
        $anoCorrente = AnoFormacao::orderBy('formacao', 'desc')->get();
        foreach ($anoCorrente as $opcao) {
            $options_anoFormacao[] = '<option value="' . $opcao->id . '">' . $opcao->formacao . '</option>';
        }

        $options_dataMatricula[] = '<option value="0" disabled selected hidden>Data Matrícula</option>';
        foreach ($anoCorrente as $opcao) {
            $options_dataMatricula[] = '<option value="' . $opcao->id . '">' . $opcao->data_matricula . '</option>';
        }

        $options_ufs[] = '<option value="0" disabled selected hidden>UF</option>';
        $ufs = Uf::get();
        foreach ($ufs as $uf) {
            $options_ufs[] = '<option value="' . $uf->id . '">' . $uf->uf_sigla . '</option>';
        }

        $options_profissao[] = '<option value="0" disabled selected hidden>Profissão</option>';
        $profissaos = Profissao::get();
        foreach ($profissaos as $profissao) {
            $options_profissao[] = '<option value="' . $profissao->id . '">' . $profissao->profissao . ((trim($profissao->militar_postograd) != '') ? ' - ' . $profissao->militar_postograd : '') . '</option>';
        }

        $options_situacaoMatricula[] = '<option value="0" disabled selected hidden>Situação no Ato da Matrícula</option>';
        $situacaoMatriculas = SituacaoMatricula::get();
        foreach ($situacaoMatriculas as $situacaoMatricula) {
            $options_situacaoMatricula[] = '<option value="' . $situacaoMatricula->id . '">' . $situacaoMatricula->situacao_matricula . '</option>';
        }

        $options_situacaoAtual[] = '<option value="0" disabled selected hidden>Situação Atual</option>';
        $situacaoAtuals = SituacaoAtual::get();
        foreach ($situacaoAtuals as $situacaoAtual) {
            $options_situacaoAtual[] = '<option value="' . $situacaoAtual->id . '">' . $situacaoAtual->situacao . ' - ' . $situacaoAtual->motivo . '</option>';
        }

        $options_situacaoAnterior[] = '<option value="0" disabled selected hidden>Situação Anterior</option>';
        $situacaoAnteriors = SituacaoAnterior::get();
        foreach ($situacaoAnteriors as $situacaoAnterior) {
            $options_situacaoAnterior[] = '<option value="' . $situacaoAnterior->id . '">' . $situacaoAnterior->situacao_anterior . '</option>';
        }

        $options_estadoCivil[] = '<option value="0" disabled selected hidden>Estado Civil</option>';
        $situacaoEstadoCivils = EstadoCivil::get();
        foreach ($situacaoEstadoCivils as $situacaoEstadoCivil) {
            $options_estadoCivil[] = '<option value="' . $situacaoEstadoCivil->id . '">' . $situacaoEstadoCivil->estado_civil . '</option>';
        }

        $options_raca[] = '<option value="0" disabled selected hidden>Cor/Raça</option>';
        $situacaoRacas = Raca::get();
        foreach ($situacaoRacas as $situacaoRaca) {
            $options_raca[] = '<option value="' . $situacaoRaca->id . '">' . $situacaoRaca->descricao . '</option>';
        }

        $options_religiao[] = '<option value="0" disabled selected hidden>Cor/Raça</option>';
        $situacaoReligiaos = Religiao::get();
        foreach ($situacaoReligiaos as $situacaoReligiao) {
            $options_religiao[] = '<option value="' . $situacaoReligiao->id . '">' . $situacaoReligiao->religiao . '</option>';
        }

        $options_escolaridade[] = '<option value="0" disabled selected hidden>Escolaridade</option>';
        $situacaoEscolaridades = Escolaridade::get();
        foreach ($situacaoEscolaridades as $situacaoEscolaridade) {
            $options_escolaridade[] = '<option value="' . $situacaoEscolaridade->id . '">' . $situacaoEscolaridade->escolaridade . '</option>';
        }

        $options_parentesco[] = '<option value="0" disabled selected hidden>Parentesco</option>';
        $situacaoParentescos = Parentesco::get();
        foreach ($situacaoParentescos as $situacaoParentesco) {
            $options_parentesco[] = '<option value="' . $situacaoParentesco->id . '">' . $situacaoParentesco->parentesco . '</option>';
        }

        $options_renda[] = '<option value="0" disabled selected hidden>Renda</option>';
        $situacaoRendas = Renda::get();
        foreach ($situacaoRendas as $situacaoRenda) {
            $options_renda[] = '<option value="' . $situacaoRenda->id . '">' . $situacaoRenda->descricao_renda . '</option>';
        }

        $options_banco[] = '<option value="0" disabled selected hidden>Banco</option>';
        $situacaoBancos = Banco::get();
        foreach ($situacaoBancos as $situacaoBanco) {
            $options_banco[] = '<option value="' . $situacaoBanco->id . '">' . $situacaoBanco->nome_banco . '</option>';
        }

        $dependentes = '<div style="border-bottom: 1px solid;float:left;width:98%;border-color:#ccc;padding: 0 0 5px 5px;margin-top:10px;">';
        $dependentes .=       '<div class="divImplantarAluno" style="width: 15%;border-bottom:none;">';
        $dependentes .=             '<label class="labelDescricao">Parentesco</label>';
        $dependentes .=             '<select class="custom-select" name="id_parentesco[]" style="margin-top:5px;">' . implode('', $options_parentesco) . '</select>';
        $dependentes .=       '</div>';
        $dependentes .=        '<div class="divImplantarAluno" style="margin-left:20px;width:25%;">';
        $dependentes .=             '<label class="labelDescricao">Nome Dependente</label>';
        $dependentes .=             '<input class="no-style" name="dep_nome_completo[]" type="text" maxlength="255" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;"/>';
        $dependentes .=        '</div>';
        $dependentes .=         '<div class="divImplantarAluno" style="margin-left:20px;width:10%;">';
        $dependentes .=             '<label class="labelDescricao" style="font-size: 11px;">Data Nascimento</label>';
        $dependentes .=             '<input class="no-style data_mask" name="dep_data_nascimento[]" type="text" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;"/>';
        $dependentes .=         '</div>';
        $dependentes .=         '<div class="divImplantarAluno" style="margin-left:20px;width:20%;">';
        $dependentes .=              '<label class="labelDescricao">Naturalidade</label>';
        $dependentes .=              '<input class="no-style" name="dep_naturalidade[]" type="text" maxlength="100" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;"/>';
        $dependentes .=         '</div>';

        $dependentes .=         '<div class="clear"></div>';
        $dependentes .=         '<div class="divImplantarAluno" style="width:40%;">';
        $dependentes .=             '<label class="labelDescricao">Endereço</label>';
        $dependentes .=             '<input class="no-style" name="dep_endereco[]" type="text" maxlength="100" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;"/>';
        $dependentes .=         '</div>';
        $dependentes .=         '<div class="divImplantarAluno" style="border-bottom:none;width: 17%;margin-left:20px;">';
        $dependentes .=             '<label class="labelDescricao">Possui Emprego</label>';
        $dependentes .=             '<select class="custom-select" name="dep_trabalho_ativo[]" style="margin-top:5px;">';
        $dependentes .=                 '<option value="0" disabled selected hidden>Possui Emprego</option>';
        $dependentes .=                 '<option value="S">Sim</option>';
        $dependentes .=                 '<option value="N">Não</option>';
        $dependentes .=             '</select>';
        $dependentes .=         '</div>';
        $dependentes .=         '<div class="divImplantarAluno" style="border-bottom:none;width: 25%; margin-left:20px;">';
        $dependentes .=             '<label class="labelDescricao">Escolaridade</label>';
        $dependentes .=             '<select class="custom-select" name="dep_id_escolaridade[]" style="margin-top:5px;">' . implode('', $options_escolaridade) . '</select>';
        $dependentes .=         '</div>';

        $dependentes .=         '<div class="clear"></div>';
        $dependentes .=         '<div class="divImplantarAluno" style="width: 30%; border-bottom:none;">';
        $dependentes .=             '<label class="labelDescricao">Profissão</label>';
        $dependentes .=             '<select class="custom-select" name="dep_id_profissao[]" style="margin-top:5px;">' . implode('', $options_profissao) . '</select>';
        $dependentes .=         '</div>';
        $dependentes .=         '<div class="divImplantarAluno" style="margin-left:20px;width:20%;">';
        $dependentes .=             '<label class="labelDescricao">Função de Trabalho</label>';
        $dependentes .=             '<input class="no-style" name="dep_trabalho_funcao[]" type="text" maxlength="255" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;"/>';
        $dependentes .=         '</div>';
        $dependentes .=         '<div class="divImplantarAluno" style="margin-left:20px;width:20%;">';
        $dependentes .=             '<label class="labelDescricao">BI de Publicação</label>';
        $dependentes .=             '<input class="no-style" name="dep_bi_publicacao[]" type="text" maxlength="255" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;"/>';
        $dependentes .=         '</div>';
        $dependentes .= '</div>';

        $data['header'] = '<i class="ion-person-add" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Implantar aluno';
        $data['body'] = '<div class="alert alert-danger errors-implantar-aluno" role="alert" style="margin-bottom:10px;"></div>
                            <form id="implantar_aluno">
                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <div class="nav new-nav-tabs" id="nav-tab" role="tablist">
                                    <a class="new-nav-item new-nav-link active" data-toggle="tab" href="#nav-implantar-aluno1" role="tab" aria-controls="nav-home" aria-selected="true"><font style="color: rgb(0,175,123);">Matrícula no CFS</font></a>
                                    <a class="new-nav-item new-nav-link"        data-toggle="tab" href="#nav-implantar-aluno2" role="tab" aria-controls="nav-home" aria-selected="true"><font style="color: rgb(0,175,123);">Período Básico do CFS</font></a>
                                    <a class="new-nav-item new-nav-link"        data-toggle="tab" href="#nav-implantar-aluno3" role="tab" aria-controls="nav-home" aria-selected="true"><font style="color: rgb(0,175,123);">Situação Militar ou Civil Anterior</font></a>
                                    <a class="new-nav-item new-nav-link"        data-toggle="tab" href="#nav-implantar-aluno4" role="tab" aria-controls="nav-home" aria-selected="true"><font style="color: rgb(0,175,123);">Endereço Residencial Atual</font></a>
                                    <a class="new-nav-item new-nav-link"        data-toggle="tab" href="#nav-implantar-aluno5" role="tab" aria-controls="nav-home" aria-selected="true"><font style="color: rgb(0,175,123);">Documentação</font></a>
                                    <a class="new-nav-item new-nav-link"        data-toggle="tab" href="#nav-implantar-aluno6" role="tab" aria-controls="nav-home" aria-selected="true"><font style="color: rgb(0,175,123);">Dados Pessoais</font></a>
                                    <a class="new-nav-item new-nav-link"        data-toggle="tab" href="#nav-implantar-aluno7" role="tab" aria-controls="nav-home" aria-selected="true"><font style="color: rgb(0,175,123);">Dependentes</font></a>
                                    <a class="new-nav-item new-nav-link"        data-toggle="tab" href="#nav-implantar-aluno8" role="tab" aria-controls="nav-home" aria-selected="true"><font style="color: rgb(0,175,123);">Situação Sócio-Econômica</font></a>
                                    <!--<a class="new-nav-item new-nav-link"        data-toggle="tab" href="#nav-implantar-aluno9" role="tab" aria-controls="nav-home" aria-selected="true"><font style="color: rgb(0,175,123);">Dados Bancários</font></a>-->
                                    <a class="new-nav-item new-nav-link"        data-toggle="tab" href="#nav-implantar-aluno10" role="tab" aria-controls="nav-home" aria-selected="true"><font style="color: rgb(0,175,123);">Fardamento</font></a>
                                </div>
                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="nav-implantar-aluno1" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">
                                        
                                        <div class="divImplantarAluno">
                                            <label class="labelDescricao">Número Inscrição</label>
                                            <input class="no-style" style="margin-top:10px;" id="al_inscricao" name="al_inscricao" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="8" placeholder=""/>
                                        </div>    
                                        <div class="divImplantarAluno" style="margin-left:20px; width: 18%;">
                                            <label class="labelDescricao">Número</label>
                                            <input class="no-style" style="width: 100%;margin-top:10px;" id="numero" name="numero" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="5" placeholder=""/>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; width: 55%;">
                                            <label class="labelDescricao">Nome Completo</label>
                                            <input class="no-style" style="width: 100%;margin-top:10px;" name="nome_completo" type="text" autocomplete="off" placeholder="" />
                                        </div>

                                        <div class="clear"></div> 
                                        <div class="divImplantarAluno" style="width: 25%;">
                                            <label class="labelDescricao">Nome de Guerra</label>
                                            <input class="no-style" style="width: 100%;margin-top:10px;" name="nome_guerra" type="text" autocomplete="off" placeholder=""  />
                                        </div>
                                        <div class="divImplantarAluno" style="border-bottom:none; margin-left:20px;">
                                            <label class="labelDescricao">Segmento</label>
                                            <select class="custom-select" name="sexo" style="margin-top:5px;">
                                                <option value="0" disabled selected hidden>Segmento</option>
                                                <option value="M">Masculino</option>
                                                <option value="F">Feminino</option>
                                            </select>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px;">
                                            <label class="labelDescricao">Data de Nascimento</label>
                                            <input class="no-style data_mask" style="width: 100%;margin-top:10px;" name="data_nascimento" type="text" value="" maxlength="10" autocomplete="off" placeholder="" />
                                        </div>
                                        
                                        <div class="clear"></div>
                                        <div class="clear"></div> 
                                        <div class="divImplantarAluno" style="width: 25%;">
                                            <label class="labelDescricao">Cidade (Naturalidade)</label>
                                            <input class="no-style" name="nasc_cidade" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;"/>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; border-bottom:none; width: 15%;">
                                            <label class="labelDescricao">UF (Naturalidade)</label>
                                            <select class="custom-select" name="nasc_id_uf" style="margin-top:5px;">' . implode('', $options_ufs) . '</select>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; width: 20%;">
                                            <label class="labelDescricao">País (Naturalidade)</label>
                                            <input class="no-style" name="nasc_pais" type="text" maxlength="50" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;"/>
                                        </div>

                                        <div class="clear"></div>
                                        <div class="divImplantarAluno" style="border-bottom:none; width: 20%;">
                                            <label class="labelDescricao">Ano de Formação</label>
                                            <select class="custom-select" id="ano_formacao" name="ano_formacao" style="margin-top:5px;" onchange="$(\'select#data_matricula\').val( $(this).val() );">' . implode('', $options_anoFormacao) . '</select>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; border-bottom:none; width: 20%;">
                                            <label class="labelDescricao">Data de Matrícula</label>
                                            <select class="custom-select" id="data_matricula" name="data_matricula" style="margin-top:5px;" readonly>' . implode('', $options_dataMatricula) . '</select>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px;width: 40%;">
                                            <label class="labelDescricao">Email</label>
                                            <input class="no-style" name="email" type="email" maxlength="60" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;"/>
                                        </div>

                                        <div class="clear"></div>
                                        <div class="divImplantarAluno" style="border-bottom:none; width: 30%;">
                                            <label class="labelDescricao">Situação no Ato da Matrícula</label>
                                            <select class="custom-select" id="id_situacao_matricula" name="id_situacao_matricula" style="margin-top:5px;">' . implode('', $options_situacaoMatricula) . '</select>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; border-bottom:none; width: 20%;">
                                            <label class="labelDescricao">Situação Atual</label>
                                            <select class="custom-select" id="id_situacao_atual" name="id_situacao_atual" style="margin-top:5px;">' . implode('', $options_situacaoAtual) . '</select>
                                        </div>
                                        

                                    </div>

                                    <div class="tab-pane fade" id="nav-implantar-aluno2" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">
                                        
                                        <div class="divImplantarAluno" style="border-bottom:none; width: 22%"> 
                                            <label class="labelDescricao">Área do Aluno</label>
                                            <select class="custom-select" name="area_id" style="margin-top:5px;" onchange="if($(this).val()==3){ $(\'select#instrumento\').prop(\'disabled\', false); } else { $(\'select#instrumento\').prop(\'disabled\', true); $(\'select#instrumento option[value=0]\').prop(\'selected\', true);}">' . implode('', $options_areas) . '</select>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; border-bottom:none;" width: 25%">
                                            <label class="labelDescricao">Tipo de Instrumento</label>
                                            <select class="custom-select" id="instrumento" name="instrumento" style="margin-top:5px;" disabled>' . implode('', $options_instrumentos) . '</select>
                                        </div>
                                        
                                        <div class="clear"></div>
                                        <!--<div class="divImplantarAluno" style="width: 22%">
                                            <label class="labelDescricao">Naípe</label> 
                                            <input class="no-style" name="naipe_XXX" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;"/>
                                        </div>-->
                                        <div class="divImplantarAluno" style="border-bottom:none; width: 25%">
                                            <label class="labelDescricao">OMCT</label>  
                                            <select class="custom-select" name="omcts_id" style="margin-top:5px;">' . implode('', $options_omcts) . '</select>
                                        </div>
                                        <div class="divImplantarAluno" style="border-bottom:none; margin-left:20px; width: 15%">
                                            <label class="labelDescricao">Turma</label> 
                                            <select class="custom-select" name="turma_id" style="margin-top:5px;">' . implode('', $options_turmas) . '</select>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 18%; margin-left:20px;">
                                            <label class="labelDescricao">Classificação no CACFS</label>  
                                            <input class="no-style" name="classif_cacfs" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;"/>
                                        </div>

                                        <div class="clear"></div>
                                        <div class="divImplantarAluno" style="border-bottom:none;">
                                            <label class="labelDescricao">Atleta Marexaer</label> 
                                            <select class="custom-select" name="atleta_marexaer" style="margin-top:5px;" onchange="if($(this).val()==\'S\'){ $(\'input#modalidade\').prop(\'disabled\', false); $(\'input#habilidades\').prop(\'disabled\', false); } else { $(\'input#modalidade\').prop(\'disabled\', true); $(\'input#habilidades\').prop(\'disabled\', true); $(\'input#modalidade option[value=0]\').prop(\'selected\', true);}">
                                                <option value="0" disabled selected hidden>Atleta</option>
                                                <option value="S">Sim</option>
                                                <option value="N">Não</option>
                                            </select>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 22%; margin-left:20px;">
                                            <label class="labelDescricao">Modalidade</label>  
                                            <input disabled class="no-style" name="modalidade" id="modalidade" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;"/>
                                        </div>
                                        <div class="divImplantarAluno" style=" width: 22%; margin-left:20px;">
                                            <label class="labelDescricao">Habilidades</label>  
                                            <input disabled class="no-style" name="habilidades" id="habilidades" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;"/>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade" id="nav-implantar-aluno3" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">
                                    
                                        <div class="divImplantarAluno" style="border-bottom:none; width:25%">
                                            <label class="labelDescricao">Situação Anterior (militar ou civil)</label>
                                            <select class="custom-select" name="id_situacao_anterior" style="margin-top:5px;">' . implode('', $options_situacaoAnterior) . '</select>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px;width:17%;"> 
                                            <label class="labelDescricao">Data de Praça Anterior</label>
                                            <input class="no-style data_mask" style="width: 100%;margin-top:10px;" name="data_praca" type="text" maxlength="10" autocomplete="off" placeholder="" />
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px;"> 
                                            <label class="labelDescricao">Data de Promoção a Cabo</label>
                                            <input class="no-style data_mask" style="width: 100%;margin-top:10px;" name="data_cb" type="text" maxlength="10" autocomplete="off" placeholder="" />
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px;width:23%;"> 
                                            <label class="labelDescricao">Data de Promoção a Sgt Temp</label>
                                            <input class="no-style data_mask" style="width: 100%;margin-top:10px;" name="data_sgttemp" type="text" maxlength="10" autocomplete="off" placeholder="" />
                                        </div>

                                        <div class="clear"></div>
                                        <div class="divImplantarAluno"> 
                                            <label class="labelDescricao">Última OM</label>
                                            <input class="no-style" style="width: 100%;margin-top:10px;" name="nome_ultima_om" type="text" maxlength="150" autocomplete="off" placeholder="" />
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px;width:21%;"> 
                                            <label class="labelDescricao">Data de Baixa da Última OM</label>
                                            <input class="no-style data_mask" style="width: 100%;margin-top:10px;" name="data_baixa_ultima_om" type="text" maxlength="10" autocomplete="off" placeholder="" />
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px;"> 
                                            <label class="labelDescricao">Endereço da Última OM</label>
                                            <input class="no-style" style="width: 100%;margin-top:10px;" name="endereco_ultima_om" type="text" maxlength="255" autocomplete="off" placeholder="" />
                                        </div>

                                        <div class="clear"></div>
                                        <div class="divImplantarAluno"> 
                                            <label class="labelDescricao">Tempo SV Anterior</label>
                                            <input class="no-style" style="width: 100%;margin-top:10px;" name="temposv_anterior" type="text" maxlength="100" autocomplete="off" placeholder="" />
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px;"> 
                                            <label class="labelDescricao">TSCMM Anterior</label>
                                            <input class="no-style" style="width: 100%;margin-top:10px;" name="tscmm_anterior" type="text" maxlength="100" autocomplete="off" placeholder="" />
                                        </div>
                                                                        
                                    </div>

                                    <div class="tab-pane fade" id="nav-implantar-aluno4" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">
                                    
                                        <div class="divImplantarAluno" style="width: 30%;">
                                            <label class="labelDescricao">Endereço</label>
                                            <input class="no-style" name="endereco" type="text" maxlength="255" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 25%; margin-left:20px;">
                                            <label class="labelDescricao">Bairro</label>
                                            <input class="no-style" name="bairro" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 25%; margin-left:20px;">
                                            <label class="labelDescricao">Cidade</label>
                                            <input class="no-style" name="cidade" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;"/>
                                        </div>
                                        <div class="divImplantarAluno" style="border-bottom:none; margin-left:20px; width: 10%;">
                                            <label class="labelDescricao">UF</label>
                                            <select class="custom-select" name="id_uf" style="margin-top:5px;">' . implode('', $options_ufs) . '</select>
                                        </div>

                                        <div class="clear"></div> 
                                        <div class="divImplantarAluno" style="width: 12%;">
                                            <label class="labelDescricao">CEP</label>
                                            <input class="no-style cep_mask" name="cep" type="text" maxlength="255" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 15%; margin-left:20px;">
                                            <label class="labelDescricao">Telefone</label>
                                            <input class="no-style telefone_mask" name="telefone" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 17%; margin-left:20px;">
                                            <label class="labelDescricao">Celular 1</label>
                                            <input class="no-style celular_mask" name="celular1" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 17%; margin-left:20px;">
                                            <label class="labelDescricao">Celular 2</label>
                                            <input class="no-style celular_mask" name="celular2" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 17%; margin-left:20px;">
                                            <label class="labelDescricao">Celular 3</label>
                                            <input class="no-style celular_mask" name="celular3" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;"/>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade" id="nav-implantar-aluno5" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">

                                        <div class="divImplantarAluno" style="width:15%">
                                            <label class="labelDescricao">Nº Identidade Civil</label>
                                            <input class="no-style" style="margin-top:10px;width:100%;" name="doc_idt_civil" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="40" placeholder=""/>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; width: 15%;">
                                            <label class="labelDescricao">Org Expd (Idt Civil)</label>
                                            <input class="no-style" style="margin-top:10px;width:100%;" name="doc_idt_civil_o_exp" type="text" autocomplete="off" maxlength="40" placeholder=""/>
                                        </div>

                                        <div class="clear"></div>
                                        <div class="divImplantarAluno" style="width: 15%;">
                                            <label class="labelDescricao">Nº Título Eleitoral</label>
                                            <input class="no-style" style="margin-top:10px;width:100%;" name="doc_tit_eleitor" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="40" placeholder=""/>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; width: 17%;">
                                            <label class="labelDescricao">Seção (Título Eleitoral)</label>
                                            <input class="no-style" style="margin-top:10px;width:100%;" name="doc_tit_secao" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="40" placeholder=""/>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; width: 17%;">
                                            <label class="labelDescricao">Zona (Título Eleitoral)</label>
                                            <input class="no-style" style="margin-top:10px;width:100%;" name="doc_tit_zona" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="40" placeholder=""/>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; width: 20%;">
                                            <label class="labelDescricao">Cidade/UF (Título Eleitoral)</label>
                                            <input class="no-style" style="margin-top:10px;width:100%;" name="doc_tit_cidade_uf" type="text" autocomplete="off" maxlength="40" placeholder=""/>
                                        </div>

                                        <div class="clear"></div>
                                        <div class="divImplantarAluno" style="width: 18%;">
                                            <label class="labelDescricao">Certidão de Nascimento</label>
                                            <input class="no-style" style="margin-top:10px;" name="doc_cert_nascimento" type="text" autocomplete="off" maxlength="40" placeholder=""/>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; width: 10%;">
                                            <label class="labelDescricao">CPF</label>
                                            <input class="no-style" style="margin-top:10px;width:100%;" name="doc_cpf" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder=""/>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; width: 10%;">
                                            <label class="labelDescricao">CNH</label>
                                            <input class="no-style" style="margin-top:10px;width:100%;" name="doc_cnh" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder=""/>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; width: 10%;">
                                            <label class="labelDescricao">PIS</label>
                                            <input class="no-style" style="margin-top:10px;width:100%;" name="doc_pis" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder=""/>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; width: 10%;">
                                            <label class="labelDescricao">PASEP</label>
                                            <input class="no-style" style="margin-top:10px;width:100%;" name="doc_pasep" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder=""/>
                                        </div>

                                        <div class="clear"></div>
                                        <div class="divImplantarAluno" style="width: 16%;">
                                            <label class="labelDescricao">Nº Identidade Militar</label>
                                            <input class="no-style" style="margin-top:10px;width:100%;" name="doc_idt_militar" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder=""/>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; width: 14%;">
                                            <label class="labelDescricao">Org Expd (Idt Mil)</label>
                                            <input class="no-style" style="margin-top:10px;width:100%;" name="doc_idt_militar_o_exp" type="text" autocomplete="off" maxlength="50" placeholder=""/>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; width: 14%;">
                                            <label class="labelDescricao">Data Expd (Idt Mil)</label>
                                            <input class="no-style data_mask" style="margin-top:10px;width:100%;" name="doc_idt_militar_dt_exp" type="text" autocomplete="off" placeholder=""/>
                                        </div>

                                        <div class="clear"></div>
                                        <div class="divImplantarAluno" style="width: 10%;">
                                            <label class="labelDescricao">FAM</label>
                                            <input class="no-style" style="margin-top:10px;width:100%;" name="doc_fam" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder=""/>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; width: 10%;">
                                            <label class="labelDescricao">CAPEMI</label>
                                            <input class="no-style" style="margin-top:10px;width:100%;" name="doc_capemi" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder=""/>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; width: 10%;">
                                            <label class="labelDescricao">GBOEx</label>
                                            <input class="no-style" style="margin-top:10px;width:100%;" name="doc_gboex" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder=""/>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px; width: 10%;">
                                            <label class="labelDescricao">CP</label>
                                            <input class="no-style" style="margin-top:10px;width:100%;" name="doc_cp" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder=""/>
                                        </div>
                                        <div class="divImplantarAluno" style="margin-left:20px;">
                                            <label class="labelDescricao">PREC CP</label>
                                            <input class="no-style" style="margin-top:10px;width:100%;" name="doc_preccp" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder=""/>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade" id="nav-implantar-aluno6" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">
                                    
                                        <div class="divImplantarAluno" style="border-bottom:none;width: 15%;">
                                            <label class="labelDescricao">Estado Civil</label>
                                            <select class="custom-select" name="id_estado_civil" style="margin-top:5px;">' . implode('', $options_estadoCivil) . '</select>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 12%; margin-left:20px;">
                                            <label class="labelDescricao">Tipo Sanguíneo</label>
                                            <input class="no-style" name="tipo_sanguineo" type="text" maxlength="2" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 8%; margin-left:20px;">
                                            <label class="labelDescricao">Fator RH</label>
                                            <input class="no-style" name="fator_rh" type="text" maxlength="15" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 10%; margin-left:20px;">
                                            <label class="labelDescricao">Cabelos</label>
                                            <input class="no-style" name="cabelo" type="text" maxlength="50" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 8%; margin-left:20px;">
                                            <label class="labelDescricao">Altura</label>
                                            <input class="no-style" name="altura" type="number" step="0.01" min="0" max="10" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;"/>
                                        </div>
                                        <div class="divImplantarAluno" style="border-bottom:none;width: 25%; margin-left:20px;">
                                            <label class="labelDescricao">Tatuagem</label>
                                            <select class="custom-select" name="tatuagem" style="margin-top:5px;">
                                                <option value="0" disabled selected hidden>Tatuagem</option>
                                                <option value="S">Sim</option>
                                                <option value="N">Não</option>
                                            </select>
                                        </div>

                                        <div class="clear"></div> 
                                        <div class="divImplantarAluno" style="border-bottom:none;width: 14%">
                                            <label class="labelDescricao">Cor/Raça</label>
                                            <select class="custom-select" name="id_raca" style="margin-top:5px;">' . implode('', $options_raca) . '</select>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 15%; margin-left:20px;">
                                            <label class="labelDescricao">Cutis</label>
                                            <input class="no-style" name="cutis" type="text" maxlength="50" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 12%; margin-left:20px;">
                                            <label class="labelDescricao">Olhos</label>
                                            <input class="no-style" name="olhos" type="text" maxlength="50" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;"/>
                                        </div>
                                        <div class="divImplantarAluno" style="border-bottom:none;width: 29%; margin-left:20px;">
                                            <label class="labelDescricao">Religião</label>
                                            <select class="custom-select" name="id_religiao" style="margin-top:5px;">' . implode('', $options_religiao) . '</select>
                                        </div>
                                        <div class="divImplantarAluno" style="border-bottom:none;width: 20%; margin-left:20px;">
                                            <label class="labelDescricao">Escolaridade</label>
                                            <select class="custom-select" name="id_escolaridade" style="margin-top:5px;">' . implode('', $options_escolaridade) . '</select>
                                        </div>

                                        <div class="clear"></div> 
                                        <div class="divImplantarAluno" style="width: 50%;">
                                            <label class="labelDescricao">Nome do Pai</label>
                                            <input class="no-style" name="nome_pai" type="text" maxlength="255" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 30%; margin-left:20px; border-bottom:none;">
                                            <label class="labelDescricao">Profissão Pai</label>
                                            <select class="custom-select" name="id_profissao_pai" style="margin-top:5px;">' . implode('', $options_profissao) . '</select>
                                        </div>

                                        <div class="clear"></div> 
                                        <div class="divImplantarAluno" style="width: 50%;">
                                            <label class="labelDescricao">Nome da Mãe</label>
                                            <input class="no-style" name="nome_mae" type="text" maxlength="255" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 30%; margin-left:20px; border-bottom:none;">
                                            <label class="labelDescricao">Profissão Mãe</label>
                                            <select class="custom-select" name="id_profissao_mae" style="margin-top:5px;">' . implode('', $options_profissao) . '</select>
                                        </div>

                                        <div class="clear"></div> 
                                        <div class="divImplantarAluno" style="width: 100%;">
                                            <label class="labelDescricao">Observações</label>
                                            <input class="no-style" name="obs" type="text" maxlength="255" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;"/>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade" id="nav-implantar-aluno7" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 5px 25px 0px 5px;">
                                    
                                        ' . $dependentes . '
                                        
                                        <div style="margin-left:15px;border-bottom:none;margin-top:100px;width: 100%;">
                                            <button type="button" id="add-dependente"> + </button>
                                        </div>
                                    
                                    </div>

                                    <div class="tab-pane fade" id="nav-implantar-aluno8" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">
                                    
                                        <div class="divImplantarAluno" style="border-bottom:none;width: 25%;">
                                            <label class="labelDescricao">Renda Familiar</label>
                                            <select class="custom-select" name="id_renda" style="margin-top:5px;">' . implode('', $options_renda) . '</select>
                                        </div>
                                    
                                        <div class="clear"></div> 
                                        <div class="divImplantarAluno" style="border-bottom:none;width: 25%;">
                                            <label class="labelDescricao">Banco</label>
                                            <select class="custom-select" name="id_banco" style="margin-top:5px;">' . implode('', $options_banco) . '</select>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 12%; margin-left:20px;">
                                            <label class="labelDescricao">Agência</label>
                                            <input class="no-style" name="num_agencia_banco" type="text" maxlength="50" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 12%; margin-left:20px;">
                                            <label class="labelDescricao">Nº Conta</label>
                                            <input class="no-style" name="num_conta_bancaria" type="text" maxlength="50" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;"/>
                                        </div>

                                    </div>
                                    <!--<div class="tab-pane fade" id="nav-implantar-aluno9" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">Implantar 9</div>-->
                                    <div class="tab-pane fade" id="nav-implantar-aluno10" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">
                                    
                                        <div class="divImplantarAluno" style="width: 10%"> 
                                            <label class="labelDescricao">Boina</label>
                                            <input class="no-style" style="width: 100%;margin-top:10px;" name="farda_tam_boina" pattern="[0-9]+$" type="text" maxlength="11" autocomplete="off"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 10%;margin-left:20px;"> 
                                            <label class="labelDescricao">Gorro</label>
                                            <input class="no-style" style="width: 100%;margin-top:10px;" name="farda_tam_gorro" pattern="[0-9]+$" type="text" maxlength="11" autocomplete="off"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 10%;margin-left:20px;"> 
                                            <label class="labelDescricao">Camiseta</label>
                                            <input class="no-style" style="width: 100%;margin-top:10px;" name="farda_tam_camiseta" type="text" maxlength="3" autocomplete="off"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 10%;margin-left:20px;"> 
                                            <label class="labelDescricao">Gandola</label>
                                            <input class="no-style" style="width: 100%;margin-top:10px;" name="farda_tam_gandola" type="text" maxlength="3" autocomplete="off"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 10%;margin-left:20px"> 
                                            <label class="labelDescricao">Calça</label>
                                            <input class="no-style" style="width: 100%;margin-top:10px;" name="farda_tam_calca" type="text" maxlength="3" autocomplete="off"/>
                                        </div>
                                        <div class="divImplantarAluno" style="width: 10%;margin-left:20px"> 
                                            <label class="labelDescricao">Coturno</label>
                                            <input class="no-style" style="width: 100%;margin-top:10px;" name="farda_tam_coturno" pattern="[0-9]+$" type="text" maxlength="11" autocomplete="off"/>
                                        </div>

                                    </div>
                                </div>
                            </form>';

        $data['footer'] = '<button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="ImplantarAluno();">
                        Salvar
                    </button>                            
                    <script>
                        $(\'.data_mask\').mask(\'00/00/0000\');
                        $(\'.cep_mask\').mask(\'00000-000\');
                        $(\'.telefone_mask\').mask(\'(00)-0000-0000\');
                        $(\'.celular_mask\').mask(\'(00)-00000-0000\');

                        $(\'#nav-tab a\').click(function() {
                            $(\'div.errors-implantar-aluno\').empty().hide();
                        });

                        $(\'#add-dependente\').click(function(){
                            $(\'#nav-implantar-aluno7\').append(\'<div class="clear"></div>' . $dependentes . '\');
                            //Refaz a mascára
                            $(\'.data_mask\').mask(\'00/00/0000\');
                        });
                    </script>';

        return $data;
    }

    public function RegistrarAluno(Request $request)
    {

        if (auth()->user() != null) {
            $data['status'] = 'err';
            $data['response'] = 'Não foi possível implatar o aluno, faça o login novamente!!!';
        }

        /*$ano_corrente = AnoFormacao::orderBy('formacao', 'desc')->first();
        $id_ano_corrente = ($ano_corrente->id) ?? 0;*/

        $id_ano_corrente = $request->data_matricula;
        if ($request->data_matricula == 0) {
            $error[] = 'Matrícula no CFS -> Informe o Ano de Formação.';
        }

        @list($dia_n, $mes_n, $ano_n) = @explode('/', $request->data_nascimento);
        @list($dia_p, $mes_p, $ano_p) = @explode('/', $request->data_praca);
        @list($dia_idt_m, $mes_idt_m, $ano_idt_m) = @explode('/', $request->doc_idt_militar_dt_exp);

        @list($dia_cb, $mes_cb, $ano_cb) = @explode('/', $request->data_cb);
        @list($dia_sgttemp, $mes_sgttemp, $ano_sgttemp) = @explode('/', $request->data_sgttemp);
        @list($dia_b_u_o, $mes_b_u_o, $ano_b_u_o) = @explode('/', $request->data_baixa_ultima_om);

        if ($request->al_inscricao != '' && !is_numeric($request->al_inscricao)) {
            $error[] = 'Matrícula no CFS -> Informe o número de inscrição.';
        }

        if ($request->numero != '' && !is_numeric($request->numero)) {
            $error[] = 'Matrícula no CFS -> Se preenchido, campo número deverá conter somente numeros.';
        }

        if (strlen($request->nome_completo) < 5) {
            $error[] = 'Matrícula no CFS -> Nome parece muito curto. Informe o nome completo.';
        }

        if (strlen($request->nome_guerra) < 2) {
            $error[] = 'Matrícula no CFS -> Nome de guerra inválido.';
        }

        if ($request->sexo != 'M' && $request->sexo != 'F') {
            $error[] = 'Matrícula no CFS -> Selecione um segmento.';
        }

        if (isset($request->omcts_id) && $request->omcts_id == 0) {
            $error[] = 'Período Básico do CFS -> Informe a omct designada';
        }

        if ($request->turma == 0) {
            $turma_request = null;
        } else {
            $turma_request = $request->turma;
        }

        if (!@checkdate($mes_n, $dia_n, $ano_n)) {
            $error[] = 'Matrícula no CFS -> Data de Nascimento Inválida';
        } else {
            $data_nascimento = $ano_n . '-' . $mes_n . '-' . $dia_n;
        }

        if (isset($request->data_praca) && !@checkdate($mes_p, $dia_p, $ano_p)) {
            $error[] = 'Situação Militar ou Civil Anterior -> Data de Praça Anterior Inválida';
        } else {
            $data_praca = (isset($request->data_praca)) ? $ano_p . '-' . $mes_p . '-' . $dia_p : null;
        }

        if (isset($request->data_idt_militar) && !@checkdate($mes_idt_m, $dia_idt_m, $ano_idt_m)) {
            $error[] = 'Documentação -> Data da Expd (Idt Mil) é Inválida';
        } else {
            $data_idt_militar = (isset($request->data_idt_militar)) ? $ano_idt_m . '-' . $mes_idt_m . '-' . $dia_idt_m : null;
        }

        if (isset($request->data_cb) && !@checkdate($mes_cb, $dia_cb, $ano_cb)) {
            $error[] = 'Situação Militar ou Civil Anterior -> Data Promoção a Cabo é Inválida';
        } else {
            $data_cb = (isset($request->data_cb)) ? $ano_cb . '-' . $mes_cb . '-' . $dia_cb : null;
        }

        if (isset($request->data_sgttemp) && !@checkdate($mes_sgttemp, $dia_sgttemp, $ano_sgttemp)) {
            $error[] = 'Situação Militar ou Civil Anterior -> Data Promoção a Sgt Temp é Inválida';
        } else {
            $data_sgttemp = (isset($request->data_sgttemp)) ? $ano_sgttemp . '-' . $mes_sgttemp . '-' . $dia_sgttemp : null;
        }

        if (isset($request->data_baixa_ultima_om) && !@checkdate($mes_b_u_o, $dia_b_u_o, $ano_b_u_o)) {
            $error[] = 'Situação Militar ou Civil Anterior -> Data baixa da Última OM é Inválida';
        } else {
            $data_baixa_ultima_om = (isset($request->data_baixa_ultima_om)) ? $ano_b_u_o . '-' . $mes_b_u_o . '-' . $dia_b_u_o : null;
        }

        if ($request->area_id == 0) {
            $error[] = 'Período Básico do CFS -> Selecione uma Área do Aluno.';
        }

        if ($request->area_id == 3 && $request->instrumento == 0) {
            $error[] = 'Período Básico do CFS -> Alunos da Área de Música Devem Possuir um Instrumento';
        }

        if (is_numeric($request->atleta_marexaer) && $request->atleta_marexaer == 0) {
            $error[] = 'Período Básico do CFS -> Informe se o Aluno é Atleta Marexaer.';
        }

        if (OwnValidator::ValidarEmail($request->email)) {
            $user = User::where('email', $request->email)->first();
            if (!isset($user->email)) {
                $email = $request->email;
            } else {
                $error[] = 'Matrícula no CFS -> Este Email já se Encontra Cadastrado.';
            }
        } else {
            $error[] = 'Matrícula no CFS -> E-mail Informado é Inválido.';
        }

        if (strlen(trim($request->nasc_cidade)) < 2) {
            $error[] = 'Matrícula no CFS -> Informe a Cidade(Naturalidade).';
        }

        if (strlen(trim($request->nasc_pais)) < 2) {
            $error[] = 'Matrícula no CFS -> Informe a País(Naturalidade).';
        }

        if ($request->nasc_id_uf == 0) {
            $error[] = 'Matrícula no CFS -> Selecione uma UF(Naturalidade).';
        }

        if (isset($error)) {
            $data['status'] = 'err';
            $data['response'] = implode('<br />', $error);
        } else {
            $aluno = new Alunos();

            $aluno->numero = $request->numero;
            $aluno->nome_completo = $request->nome_completo;
            $aluno->nome_guerra = $request->nome_guerra;
            $aluno->sexo = $request->sexo;
            $aluno->omcts_id = $request->omcts_id;
            $aluno->turma_id = $turma_request;
            $aluno->data_nascimento = $data_nascimento;
            $aluno->atleta_marexaer = ((isset($request->atleta_marexaer)) ? $request->atleta_marexaer : 'N');
            $aluno->primeira_data_praca = $data_praca;
            $aluno->data_matricula = $id_ano_corrente;
            $aluno->area_id = $request->area_id;
            $aluno->instrumento_id = $request->instrumento;
            $aluno->email = $email;

            $user = new User();
            $user->email = $email;
            $user->password = bcrypt($aluno->doc_cpf);

            $aluno->al_inscricao = $request->al_inscricao;
            $aluno->nasc_cidade = $request->nasc_cidade;
            $aluno->nasc_id_uf = $request->nasc_id_uf;
            $aluno->nasc_pais = $request->nasc_pais;
            //$aluno->ano_formacao = $request->ano_formacao;

            $aluno->id_situacao_matricula = $request->id_situacao_matricula;
            $aluno->id_situacao_atual = $request->id_situacao_atual;

            //precisa verificar oque fazer....
            //$aluno->naipe_XXX = $request->naipe_XXX;

            $aluno->turma_id = $request->turma_id;
            $aluno->classif_cacfs = $request->classif_cacfs;
            $aluno->modalidade = $request->modalidade;
            $aluno->habilidades = $request->habilidades;
            $aluno->id_situacao_anterior = $request->id_situacao_anterior;
            $aluno->data_cb = $data_cb;
            $aluno->data_sgttemp = $data_sgttemp;
            $aluno->nome_ultima_om = $request->nome_ultima_om;
            $aluno->data_baixa_ultima_om = $data_baixa_ultima_om;
            $aluno->endereco_ultima_om = $request->endereco_ultima_om;
            $aluno->temposv_anterior = $request->temposv_anterior;

            $aluno->tscmm_anterior = $request->tscmm_anterior;
            $aluno->endereco = $request->endereco;
            $aluno->bairro = $request->bairro;
            $aluno->cidade = $request->cidade;
            $aluno->id_uf = $request->id_uf;
            $aluno->cep = $request->cep;
            $aluno->telefone = $request->telefone;
            $aluno->celular1 = $request->celular1;
            $aluno->celular2 = $request->celular2;
            $aluno->celular3 = $request->celular3;
            $aluno->email = $request->email;

            $aluno->doc_idt_civil = $request->doc_idt_civil;
            $aluno->doc_idt_civil_o_exp = $request->doc_idt_civil_o_exp;
            $aluno->doc_tit_eleitor = $request->doc_tit_eleitor;
            $aluno->doc_tit_secao = $request->doc_tit_secao;
            $aluno->doc_tit_zona = $request->doc_tit_zona;
            $aluno->doc_tit_cidade_uf = $request->doc_tit_cidade_uf;
            $aluno->doc_cert_nascimento = $request->doc_cert_nascimento;
            $aluno->doc_cpf = $request->doc_cpf;
            $aluno->doc_cnh = $request->doc_cnh;
            $aluno->doc_pis = $request->doc_pis;

            $aluno->doc_pasep = $request->doc_pasep;
            $aluno->doc_idt_militar = $request->doc_idt_militar;
            $aluno->doc_idt_militar_o_exp = $request->doc_idt_militar_o_exp;
            $aluno->doc_idt_militar_dt_exp = $data_idt_militar;
            $aluno->doc_fam = $request->doc_fam;
            $aluno->doc_capemi = $request->doc_capemi;
            $aluno->doc_gboex = $request->doc_gboex;
            $aluno->doc_cp = $request->doc_cp;
            $aluno->doc_preccp = $request->doc_preccp;
            $aluno->id_estado_civil = $request->id_estado_civil;

            $aluno->tipo_sanguineo = $request->tipo_sanguineo;
            $aluno->fator_rh = $request->fator_rh;
            $aluno->cabelo = $request->cabelo;
            $aluno->altura = $request->altura;
            $aluno->tatuagem = $request->tatuagem;
            $aluno->id_raca = $request->id_raca;
            $aluno->cutis = $request->cutis;
            $aluno->olhos = $request->olhos;

            $aluno->id_religiao = $request->id_religiao;
            $aluno->id_escolaridade = $request->id_escolaridade;

            $aluno->nome_pai = $request->nome_pai;
            $aluno->id_profissao_pai = $request->id_profissao_pai;
            $aluno->nome_mae = $request->nome_mae;
            $aluno->id_profissao_mae = $request->id_profissao_mae;
            $aluno->obs = $request->obs;

            $aluno->id_renda = $request->id_renda;
            $aluno->id_banco = $request->id_banco;
            $aluno->num_agencia_banco = $request->num_agencia_banco;
            $aluno->num_conta_bancaria = $request->num_conta_bancaria;

            $aluno->farda_tam_boina = $request->farda_tam_boina;
            $aluno->farda_tam_gorro = $request->farda_tam_gorro;
            $aluno->farda_tam_camiseta = $request->farda_tam_camiseta;
            $aluno->farda_tam_gandola = $request->farda_tam_gandola;
            $aluno->farda_tam_calca = $request->farda_tam_calca;
            $aluno->farda_tam_coturno = $request->farda_tam_coturno;


            if ($aluno->save() && $user->save()) {
                $aluno = Alunos::where([['email', '=', $aluno->email], ['numero', '=', $aluno->numero], ['doc_cpf', '=', $aluno->doc_cpf]])->first();
                //Insere Parentes

                if (isset($request->id_parentesco)) {
                    for ($i = 0; $i < sizeof($request->id_parentesco); $i++) {
                        $alunoDependente = new AlunosDependente();
                        $alunoDependente->id_aluno = $aluno->id;
                        $alunoDependente->id_parentesco = $request->id_parentesco[$i];
                        $alunoDependente->dep_nome_completo = $request->dep_nome_completo[$i];

                        @list($dia_dt_nasc, $mes_dt_nasc, $ano_dt_nasc) = @explode('/', $request->dep_data_nascimento[$i]);
                        $alunoDependente->dep_data_nascimento = $ano_dt_nasc . '-' . $mes_dt_nasc . '-' . $dia_dt_nasc;

                        $alunoDependente->dep_naturalidade = $request->dep_naturalidade[$i];
                        $alunoDependente->dep_endereco = $request->dep_endereco[$i];
                        $alunoDependente->dep_trabalho_ativo = $request->dep_trabalho_ativo[$i];
                        $alunoDependente->dep_id_escolaridade = $request->dep_id_escolaridade[$i];
                        $alunoDependente->dep_id_profissao = $request->dep_id_profissao[$i];
                        $alunoDependente->dep_trabalho_funcao = $request->dep_trabalho_funcao[$i];
                        $alunoDependente->dep_bi_publicacao = $request->dep_bi_publicacao[$i];

                        if (!$alunoDependente->save()) {
                            $data['status'] = 'err';
                            $data['response'] = 'Usuário Inserido, Mas Houve Problema ao Inserir o(s) Dependente(s)';
                        }
                    }
                }

                $data['status'] = 'ok';
                $data['response'] = 'Implantou Aluno Com Dependentes.';
            } else {
                $data['status'] = 'err';
                $data['response'] = 'Não foi possível implantar o aluno.';
            }

            $this->classLog->RegistrarLog('Implantou aluno no sistema', auth()->user()->email);
        }

        return $data;
    }
}

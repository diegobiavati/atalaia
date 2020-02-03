<?php

namespace App\Imports;

use App\Models\Alunos;
use App\Models\AlunosSitDiv;
use App\Models\AnoFormacao;
use App\Models\Areas;
use App\Models\OMCT;
use App\Models\TurmasPB;
use App\Models\Uf;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AlunosImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;
    protected $anoFormacao;
    protected $ufs = array();
    protected $turmas;
    protected $uetes;

    function __construct()
    {

        ini_set('memory_limit', '-1');

        foreach (AnoFormacao::all() as $formacao) {
            $this->anoFormacao[$formacao['formacao']] = $formacao;
        }

        foreach (Uf::all() as $uf) {
            $this->ufs[$uf['uf_sigla']] = $uf;
        }

        foreach(TurmasPB::all() as $turma){
            $this->turmas[$turma['turma']] = $turma;
        }

    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        if (isset($row['id_situacao_atual']) && ($row['id_situacao_atual'] != 'null')) {
           
            $idSituacaoAtual = (int) $row['id_situacao_atual'];

            if (!($idSituacaoAtual >= 100 && $idSituacaoAtual <= 103)) {

                $alunosSitDiv = new AlunosSitDiv();
                $alunosSitDiv->numero = $row['al_numero'];
                $alunosSitDiv->nome_completo = $row['al_nomecompleto'];
                $alunosSitDiv->nome_guerra = $row['al_nomeguerra'];
                $alunosSitDiv->data_nascimento = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_nascimento']));
                $alunosSitDiv->data_matricula = $this->anoFormacao[$row['al_anoformacao']]->id;
                $alunosSitDiv->primeira_data_praca = ((isset($row['data_pracaanterior']) && ($row['data_pracaanterior'] != 'null')) ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_pracaanterior'])) : null);
                $alunosSitDiv->turma_id = $this->turmas[ (((strlen($row['pb_turma']) == 2) ? substr_replace($row['pb_turma'], '0', 1, 0) : $row['pb_turma'])) ]->id;
                $alunosSitDiv->omcts_id = OMCT::retornaOmctsSisPB()[$row['pb_cod_omct']]['cod_no_atalaia'];
                $alunosSitDiv->area_id = Areas::retornaAreasSisPB()[$row['codgr_area']]['cod_no_atalaia'];
                $alunosSitDiv->sexo = $row['sexo'];

                //Verificar se o e-mail é o mesmo...
                $alunosSitDiv->email = $row['email'];
                $alunosSitDiv->situacoes_diversas_id = AlunosSitDiv::retornaSitDiversasPB()[$row['id_situacao_atual']]['cod_no_atalaia'];

                /*Parei aqui*/
                $alunosSitDiv->situacoes_diversas_obs = $row['situacao_atual_motivo'];
                
                dd($alunosSitDiv);
            }
        }

        /*$param = $row;
        if ($row['al_anoformacao'] > 2019) {
            $aluno = Alunos::where(['alunos.numero' => $row['al_numero']])
                ->whereHas('ano_formacao', function ($query) use ($param) {
                    $query->where('formacao', '=', $param['al_anoformacao']);
                })
                ->update([
                    'nome_completo' => $row['al_nomecompleto'],
                    'al_inscricao' => $row['al_inscricao'],
                    'primeira_data_praca' => ((isset($row['data_pracaanterior']) && ($row['data_pracaanterior'] != 'null')) ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_pracaanterior'])) : null),
                    'classif_cacfs' => ((isset($row['classif_cacfs']) && ($row['classif_cacfs'] != 'null')) ? $row['classif_cacfs'] : null),
                    'modalidade' => ((isset($row['modalidade']) && ($row['modalidade'] != 'null')) ? $row['modalidade'] : null),
                    'habilidades' => ((isset($row['habilidades']) && ($row['habilidades'] != 'null')) ? $row['habilidades'] : null),
                    'nasc_cidade' => ((isset($row['nasc_cidade']) && ($row['nasc_cidade'] != 'null')) ? $row['nasc_cidade'] : null),
                    'nasc_pais' => ((isset($row['nasc_pais']) && ($row['nasc_pais'] != 'null')) ? $row['nasc_pais'] : null),
                    'nasc_id_uf' => ((isset($row['nasc_uf']) && ($row['nasc_uf'] != 'null')) ? $this->ufs[$row['nasc_uf']]->id : null),
                    'data_incorporacao' => ((isset($row['data_incorporacao']) && ($row['data_incorporacao'] != 'null')) ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_incorporacao'])) : null),
                    'id_situacao_anterior' => ((isset($row['id_situacao_anterior']) && ($row['id_situacao_anterior'] != 'null')) ? $row['id_situacao_anterior'] : null),
                    'id_situacao_matricula' => ((isset($row['id_situacao_matricula']) && ($row['id_situacao_matricula'] != 'null')) ? $row['id_situacao_matricula'] : null),
                    //'id_situacao_pb' => $idSituacaoPb,
                    //'id_situacoes_diversas' => $idSituacaoDiversas,
                    //'id_situacao_atual' => ((isset($row['id_situacao_atual']) && ($row['id_situacao_atual'] != 'null')) ? $row['id_situacao_atual'] : null),
                    'data_pracacfs' => ((isset($row['data_pracacfs']) && ($row['data_pracacfs'] != 'null')) ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_pracacfs'])) : null),
                    'data_apresentacao' => ((isset($row['data_apresentacao']) && ($row['data_apresentacao'] != 'null')) ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_apresentacao'])) : null),
                    'data_cb' => ((isset($row['data_cb']) && ($row['data_cb'] != 'null')) ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_cb'])) : null),
                    'data_sgttemp' => ((isset($row['data_sgttemp']) && ($row['data_sgttemp'] != 'null')) ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_sgttemp'])) : null),
                    'data_baixa_ultima_om' => ((isset($row['data_baixa_ultima_om']) && ($row['data_baixa_ultima_om'] != 'null')) ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_baixa_ultima_om'])) : null),
                    'nome_ultima_om' => ((isset($row['ultimaom_nome']) && ($row['ultimaom_nome'] != 'null')) ? $row['ultimaom_nome'] : null),
                    'endereco_ultima_om' => ((isset($row['ultimaom_end']) && ($row['ultimaom_end'] != 'null')) ? $row['ultimaom_end'] : null),
                    'temposv_anterior' => ((isset($row['temposv_anterior']) && ($row['temposv_anterior'] != 'null')) ? $row['temposv_anterior'] : null),
                    'tscmm_anterior' => ((isset($row['tscmm_ant']) && ($row['tscmm_ant'] != 'null')) ? $row['tscmm_ant'] : null),
                    'endereco' => ((isset($row['end_endereco']) && ($row['end_endereco'] != 'null')) ? $row['end_endereco'] : null),
                    'bairro' => ((isset($row['end_bairro']) && ($row['end_bairro'] != 'null')) ? $row['end_bairro'] : null),
                    'cidade' => ((isset($row['end_cidade']) && ($row['end_cidade'] != 'null')) ? $row['end_cidade'] : null),
                    'id_uf' => ((isset($row['end_uf']) && ($row['end_uf'] != 'null')) ? $this->ufs[$row['end_uf']]->id : null),
                    'cep' => ((isset($row['end_cep']) && ($row['end_cep'] != 'null')) ? $row['end_cep'] : null),
                    'telefone' => ((isset($row['telefone']) && ($row['telefone'] != 'null')) ? $row['telefone'] : null),
                    'celular1' => ((isset($row['celular']) && ($row['celular'] != 'null')) ? $row['celular'] : null),
                    'celular2' => ((isset($row['celular2']) && ($row['celular2'] != 'null')) ? $row['celular2'] : null),
                    'celular3' => ((isset($row['celular3']) && ($row['celular3'] != 'null')) ? $row['celular3'] : null),
                    'nome_mae' => ((isset($row['fil_mae']) && ($row['fil_mae'] != 'null')) ? $row['fil_mae'] : null),
                    'id_profissao_mae' => ((isset($row['fil_mae_profissao']) && ($row['fil_mae_profissao'] != 'null')) ? $row['fil_mae_profissao'] : null),
                    'nome_pai' => ((isset($row['fil_pai']) && ($row['fil_pai'] != 'null')) ? $row['fil_pai'] : null),
                    'id_profissao_pai' => ((isset($row['fil_pai_profissao']) && ($row['fil_pai_profissao'] != 'null')) ? $row['fil_pai_profissao'] : null),
                    'doc_idt_civil' => ((isset($row['doc_idtcivil']) && ($row['doc_idtcivil'] != 'null')) ? $row['doc_idtcivil'] : null),
                    'doc_idt_civil_o_exp' => ((isset($row['doc_idtcivil_oexp']) && ($row['doc_idtcivil_oexp'] != 'null')) ? substr($row['doc_idtcivil_oexp'], 0, 50) : null),
                    'doc_tit_eleitor' => ((isset($row['doc_titnumero']) && ($row['doc_titnumero'] != 'null')) ? $row['doc_titnumero'] : null),
                    'doc_tit_secao' => ((isset($row['doc_titsecao']) && ($row['doc_titsecao'] != 'null')) ? $row['doc_titsecao'] : null),
                    'doc_tit_zona' => ((isset($row['doc_titzona']) && ($row['doc_titzona'] != 'null')) ? $row['doc_titzona'] : null),
                    'doc_tit_cidade_uf' => ((isset($row['doc_titcidadeuf']) && ($row['doc_titcidadeuf'] != 'null')) ? $row['doc_titcidadeuf'] : null),
                    'doc_cert_nascimento' => ((isset($row['doc_certnasc']) && ($row['doc_certnasc'] != 'null')) ? $row['doc_certnasc'] : null),
                    'doc_cpf' => ((isset($row['doc_cpf']) && ($row['doc_cpf'] != 'null')) ? $row['doc_cpf'] : null),
                    'doc_cnh' => ((isset($row['doc_cnh']) && ($row['doc_cnh'] != 'null')) ? $row['doc_cnh'] : null),
                    'doc_pis' => ((isset($row['doc_pis']) && ($row['doc_pis'] != 'null')) ? $row['doc_pis'] : null),
                    'doc_pasep' => ((isset($row['doc_pasep']) && ($row['doc_pasep'] != 'null')) ? $row['doc_pasep'] : null),
                    'doc_idt_militar' => ((isset($row['doc_idtmilitar']) && ($row['doc_idtmilitar'] != 'null')) ? $row['doc_idtmilitar'] : null),
                    'doc_idt_militar_o_exp' => ((isset($row['doc_idtmilitar_oexp']) && ($row['doc_idtmilitar_oexp'] != 'null')) ? $row['doc_idtmilitar_oexp'] : null),
                    'doc_idt_militar_dt_exp' => ((isset($row['doc_idtmilitar_dtexp']) && ($row['doc_idtmilitar_dtexp'] != 'null')) ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['doc_idtmilitar_dtexp'])) : null),
                    'doc_fam' => ((isset($row['doc_fam']) && ($row['doc_fam'] != 'null')) ? $row['doc_fam'] : null),
                    'doc_capemi' => ((isset($row['doc_capemi']) && ($row['doc_capemi'] != 'null')) ? $row['doc_capemi'] : null),
                    'doc_gboex' => ((isset($row['doc_gboex']) && ($row['doc_gboex'] != 'null')) ? $row['doc_gboex'] : null),
                    'doc_cp' => ((isset($row['doc_cp']) && ($row['doc_cp'] != 'null')) ? $row['doc_cp'] : null),
                    'doc_preccp' => ((isset($row['doc_preccp']) && ($row['doc_preccp'] != 'null')) ? $row['doc_preccp'] : null),
                    'tipo_sanguineo' => ((isset($row['sang_tiposanguineo']) && ($row['sang_tiposanguineo'] != 'null')) ? $row['sang_tiposanguineo'] : null),
                    'fator_rh' => ((isset($row['sang_fatorrh']) && ($row['sang_fatorrh'] != 'null')) ? $row['sang_fatorrh'] : null),
                    'cabelo' => ((isset($row['cabelos']) && ($row['cabelos'] != 'null')) ? $row['cabelos'] : null),
                    'altura' => ((isset($row['altura']) && ($row['altura'] != 'null')) ? $row['altura'] : null),
                    'cutis' => ((isset($row['cutis']) && ($row['cutis'] != 'null')) ? $row['cutis'] : null),
                    'olhos' => ((isset($row['olhos']) && ($row['olhos'] != 'null')) ? $row['olhos'] : null),
                    'tatuagem' => ((isset($row['tatuagem']) && ($row['tatuagem'] != 'null')) ? $row['tatuagem'] : null),
                    'id_raca' => (isset($row['id_raca']) ? (((int) $row['id_raca'] == 0) ? 1 : $row['id_raca']) : null),
                    'id_religiao' => ((isset($row['id_religiao']) && ($row['id_religiao'] != 'null')) ? $row['id_religiao'] : null),
                    'id_escolaridade' => ((isset($row['id_escolaridade']) && ($row['id_escolaridade'] != 'null')) ? $row['id_escolaridade'] : null),
                    'id_escolaridade_superior' => ((isset($row['cod_escolaridade_superior']) && ($row['cod_escolaridade_superior'] != 'null')) ? $row['cod_escolaridade_superior'] : null),
                    'id_renda' => ((isset($row['id_renda']) && ($row['id_renda'] != 'null')) ? $row['id_renda'] : null),
                    'obs' => ((isset($row['obs']) && ($row['obs'] != 'null')) ? $row['obs'] : null),
                    'id_banco' => ((isset($row['id_banco']) && ($row['id_banco'] != 'null')) ? $row['id_banco'] : null),
                    'num_agencia_banco' => ((isset($row['fin_agencia']) && ($row['fin_agencia'] != 'null')) ? $row['fin_agencia'] : null),
                    'num_conta_bancaria' => ((isset($row['fin_conta']) && ($row['fin_conta'] != 'null')) ? $row['fin_conta'] : null),
                    'data_desligamento_cfs' => ((isset($row['exc_data']) && ($row['exc_data'] != 'null')) ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['exc_data'])) : null),
                    'num_bi_desligamento_cfs' => ((isset($row['exc_bi']) && ($row['exc_bi'] != 'null')) ? $row['exc_bi'] : null),
                    'id_motivo_desligamento_cfs' => ((isset($row['exc_cod_motivo']) && ($row['exc_cod_motivo'] != 'null')) ? $row['exc_cod_motivo'] : null),
                    'tp_req_desligamento_cfs' => ((isset($row['exc_tiporequisicao']) && ($row['exc_tiporequisicao'] != 'null')) ? $row['exc_tiporequisicao'] : null),
                    'amparo_desligamento_cfs' => ((isset($row['exc_amparo']) && ($row['exc_amparo'] != 'null')) ? $row['exc_amparo'] : null),
                    'id_req_desligamento_cfs' => ((isset($row['exc_cod_requerimento']) && ($row['exc_cod_requerimento'] != 'null')) ? $row['exc_cod_requerimento'] : null),
                    'id_estado_civil' => ((isset($row['cod_estadocivil']) && ($row['cod_estadocivil'] != 'null')) ? $row['cod_estadocivil'] : null),
                    'endereco_guarnicao' => ((isset($row['end_gu_endereco']) && ($row['end_gu_endereco'] != 'null')) ? $row['end_gu_endereco'] : null),
                    'bairro_guarnicao' => ((isset($row['end_gu_bairro']) && ($row['end_gu_bairro'] != 'null')) ? $row['end_gu_bairro'] : null),
                    'cidade_guarnicao' => ((isset($row['end_gu_cidade']) && ($row['end_gu_cidade'] != 'null')) ? $row['end_gu_cidade'] : null),
                    'id_uf_guarnicao' => ((isset($row['end_gu_uf']) && ($row['end_gu_uf'] != 'null')) ? $this->ufs[$row['end_gu_uf']]->id : null),
                    'cep_guarnicao' => ((isset($row['end_gu_cep']) && ($row['end_gu_cep'] != 'null')) ? $row['end_gu_cep'] : null),
                    'farda_tam_boina' => ((isset($row['farda_tam_boina']) && ($row['farda_tam_boina'] != 'null')) ? $row['farda_tam_boina'] : null),
                    'farda_tam_gorro' => ((isset($row['farda_tam_gorro']) && ($row['farda_tam_gorro'] != 'null')) ? $row['farda_tam_gorro'] : null),
                    'farda_tam_camiseta' => ((isset($row['farda_tam_camiseta']) && ($row['farda_tam_camiseta'] != 'null')) ? $row['farda_tam_camiseta'] : null),
                    'farda_tam_gandola' => ((isset($row['farda_tam_gandola']) && ($row['farda_tam_gandola'] != 'null')) ? $row['farda_tam_gandola'] : null),
                    'farda_tam_calca' => ((isset($row['farda_tam_calca']) && ($row['farda_tam_calca'] != 'null')) ? $row['farda_tam_calca'] : null),
                    'farda_tam_coturno' => ((isset($row['farda_tam_coturno']) && ($row['farda_tam_coturno'] != 'null')) ? $row['farda_tam_coturno'] : null)
                ]);
        }*/
    }

    public function rules(): array
    {
        return [
            '*.ano' => function ($attribute, $value, $onFailure) {
                if (!array_key_exists((int) $value, $this->anoFormacao)) {
                    $onFailure('Ano de Formação Não Cadastrado');
                }
            }
        ];
    }
}

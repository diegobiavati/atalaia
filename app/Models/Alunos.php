<?php

namespace App\Models;

use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Http\Controllers\Utilitarios\SmbClientPhp;
use App\Imports\AlunosImport;
use App\Imports\AlunosImportInsert;
use Illuminate\Database\Eloquent\Model;
use App\Models\OMCT;

class Alunos extends Model
{
    protected $connection = 'mysql';
    protected $table = 'alunos';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'periodo_cfs',
        'qms_id',
        'numero',
        'nome_completo',
        'nome_guerra',
        'data_nascimento',
        'data_matricula',
        'ano_formacao_reintegr_id',
        'primeira_data_praca',
        'turma_id',
        'turma_esa_id',
        'omcts_id',
        'area_id',
        'instrumento_id',
        'nasc_pais',
        'atleta_marexaer',
        'bonificacao_atleta',
        'modalidade',
        'habilidades',
        'sexo',
        'email',
        'cotista',
        'precedencia',
        'data_cadastro',
        'al_inscricao',
        'nasc_cidade',
        'nasc_id_uf',
        'data_incorporacao',
        'id_situacao_anterior',
        'id_situacao_matricula',
        //'id_situacao_pb',
        //'id_situacao_atual',
        'data_pracacfs',
        'data_apresentacao',
        'data_cb',
        'data_sgttemp',
        'data_baixa_ultima_om',
        'nome_ultima_om',
        'endereco_ultima_om',
        'temposv_anterior',
        'tscmm_anterior',
        'endereco',
        'bairro',
        'cidade',
        'id_uf',
        'cep',
        'telefone',
        'celular1',
        'celular2',
        'celular3',
        'nome_mae',
        'cpf_mae',
        'id_profissao_mae',
        'nome_pai',
        'cpf_pai',
        'id_profissao_pai',
        'doc_idt_civil',
        'doc_idt_civil_o_exp',
        'doc_tit_eleitor',
        'doc_tit_secao',
        'doc_tit_zona',
        'doc_tit_cidade_uf',
        'doc_cert_nascimento',
        'doc_cpf',
        'doc_cnh',
        'doc_pis',
        'doc_pasep',
        'doc_idt_militar',
        'doc_idt_militar_o_exp',
        'doc_idt_militar_dt_exp',
        'doc_fam',
        'doc_capemi',
        'doc_gboex',
        'doc_cp',
        'doc_preccp',
        'tipo_sanguineo',
        'fator_rh',
        'cabelo',
        'altura',
        'cutis',
        'olhos',
        'tatuagem',
        'id_raca',
        'id_religiao',
        'id_escolaridade',
        'id_escolaridade_superior',
        'escolaridade_per_matr_sup',
        'id_renda',
        'obs',
        'id_banco',
        'num_agencia_banco',
        'num_conta_bancaria',
        'data_desligamento_cfs',
        'num_bi_desligamento_cfs',
        'id_motivo_desligamento_cfs',
        'tp_req_desligamento_cfs',
        'amparo_desligamento_cfs',
        'id_req_desligamento_cfs',
        'id_estado_civil',
        'data_alteracao',
        'endereco_guarnicao',
        'bairro_guarnicao',
        'cidade_guarnicao',
        'id_uf_guarnicao',
        'cep_guarnicao',
        'gramatica_naturalidade_cidade',
        'gramatica_naturalidade_uf',
        'farda_tam_boina',
        'farda_tam_gorro',
        'farda_tam_camiseta',
        'farda_tam_gandola',
        'farda_tam_calca',
        'farda_tam_coturno',
        'anulacaoCancelamento',
        'recursosDisciplinares',
        'recompensas'
    ];

    public function turma()
    {
        return ($this->belongsTo('App\Models\TurmasPB', 'turma_id', 'id')) ?? 'Não informada';
    }

    public function turmaEsa()
    {
        return ($this->belongsTo('App\Models\TurmasEsa', 'turma_esa_id', 'id')) ?? 'Não informada';
    }

    public function omct()
    {
        return $this->belongsTo(OMCT::class, 'omcts_id', 'id');
    }

    public function area()
    {
        return $this->belongsTo('App\Models\Areas', 'area_id', 'id');
    }

    public function ano_formacao()
    {
        return $this->belongsTo('App\Models\AnoFormacao', 'data_matricula', 'id');
    }

    public function ano_formacao_rematr()
    {
        return $this->belongsTo('App\Models\AnoFormacao', 'ano_formacao_reintegr_id', 'id');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'email', 'email');
    }

    public function classificacao()
    {
        return $this->hasOne('App\Models\AlunosClassificacao', 'aluno_id', 'id');
    }

    public function instrumento()
    {
        return $this->belongsTo('App\Models\Instrumentos', 'instrumento_id', 'id');
    }

    public function aluno_voluntario_aviacao()
    {
        return $this->hasOne('App\Models\AlunosVoluntAv', 'alunos_id', 'id');
    }

    public function uf_nascimento()
    {
        return $this->belongsTo('App\Models\Uf', 'nasc_id_uf', 'id');
    }

    public function uf()
    {
        return $this->belongsTo('App\Models\Uf', 'id_uf', 'id');
    }

    public function situacao_anterior()
    {
        return $this->belongsTo('App\Models\SituacaoAnterior', 'id_situacao_anterior', 'id');
    }

    public function situacao_matricula()
    {
        return $this->belongsTo('App\Models\SituacaoMatricula', 'id_situacao_matricula', 'id');
    }

    /*public function situacao_atual()
    {
        return $this->belongsTo('App\Models\SituacaoAtual', 'id_situacao_atual', 'id');
    }*/

    public function profissao_mae()
    {
        return $this->belongsTo('App\Models\Profissao', 'id_profissao_mae', 'id');
    }

    public function profissao_pai()
    {
        return $this->belongsTo('App\Models\Profissao', 'id_profissao_pai', 'id');
    }

    public function raca()
    {
        return $this->belongsTo('App\Models\Raca', 'id_raca', 'id');
    }

    public function religiao()
    {
        return $this->belongsTo('App\Models\Religiao', 'id_religiao', 'id');
    }

    public function escolaridade()
    {
        return $this->belongsTo('App\Models\Escolaridade', 'id_escolaridade', 'id');
    }

    public function renda()
    {
        return $this->belongsTo('App\Models\Renda', 'id_renda', 'id');
    }

    public function estado_civil()
    {
        return $this->belongsTo('App\Models\EstadoCivil', 'id_estado_civil', 'id');
    }

    public function passaporte()
    {
        return $this->hasOne('App\Models\TelegramAlunoAuth', 'aluno_id', 'id');
    }

    public function imagem_aluno()
    {
        return $this->hasOne('App\Models\ImagemAluno', 'id_aluno', 'id')->withDefault([
            'nome_arquivo' => 'no-image.jpg'
        ]);
    }

    public function conselhoEscolar(){
        return $this->hasMany('App\Models\AlunosConselhoEscolar', 'aluno_id', 'id');
    }

    public function lancamento_fo(){
        return $this->hasMany('App\Models\LancamentoFo', 'aluno_id', 'id');
    }
    
    public function capitaniNotas(){
        return $this->hasMany('App\Models\CapitaniMSAccess', 'aluno_id', 'id');
    }

    public function nascimento()
    {
        if ($this->data_nascimento) {
            list($ano, $mes, $dia) = explode('-', $this->data_nascimento);
            $data_nascimento = $dia . '/' . $mes . '/' . $ano;
        } else {
            $data_nascimento = 'Não informada';
        }

        return $data_nascimento;
    }

    public function formacao(){
        if(isset($this->ano_formacao_reintegr_id)){
            return $this->ano_formacao_rematr;
        }else{
            return $this->ano_formacao;
        }
    }

    public function PrimeiraDataPraca()
    {

        if ($this->primeira_data_praca) {
            list($ano, $mes, $dia) = explode('-', $this->primeira_data_praca);
            $data_praca = $dia . '/' . $mes . '/' . $ano;
        } else {
            list($ano, $mes, $dia) = explode('-', $this->ano_formacao->data_matricula);
            $data_praca = $dia . '/' . $mes . '/' . $ano;
        }

        return $data_praca;
    }

    public function Segmento()
    {
        if ($this->sexo) {
            $segmento = ($this->sexo == 'M') ? 'Masculino' : 'Feminino';
        } else {
            $data_praca = 'Não informado';
        }

        return $segmento;
    }

    public function dependentes()
    {
        return $this->hasMany(AlunosDependente::class, 'id_aluno');
    }

    public function alunos_curso()
    {
        return $this->hasOne(AlunosCurso::class, 'id_aluno', 'id');
    }

    public function qms()
    {
        return $this->belongsTo('App\Models\QMS', 'qms_id', 'id')->withDefault([
            'qms' => 'Não Informada'
        ]);
    }

    //para pegar esse método tem que buscar por 'numero_nome_guerra'
    public function getNumeroNomeGuerraAttribute()
    {
        return $this->numero . ' - ' . $this->nome_guerra;
    }

    /* public function AvaliacaoTaf(){
        return $this->belongsTo(AvaliacaoTaf::class);
    } */

    public static function carregaAlunosVsAlunosSitDiv($anoFormacao = 0)
    {
        if ($anoFormacao == 0) {
            $alunos = Alunos::get();
            $alunoSitDivs = AlunosSitDiv::with('situacaoDivHistorico')->get();
        } else {
            $alunos = Alunos::where(['data_matricula' => $anoFormacao])->get();
            $alunoSitDivs = AlunosSitDiv::with('situacaoDivHistorico')->where(['data_matricula' => $anoFormacao])->get();
        }

        foreach ($alunoSitDivs as $alunoSitDiv) {
            if (isset($alunoSitDiv->situacaoDivHistorico->data)) {
                $unserialize = unserialize($alunoSitDiv->situacaoDivHistorico->data);

                $aluno = new Alunos($unserialize['cadastro']);
                
                $aluno->id = $alunoSitDiv->situacaoDivHistorico->aluno_id;

                $aluno->data_matricula = $alunoSitDiv->data_matricula;
                $aluno->turma_id = $alunoSitDiv->turma_id;
                $aluno->omcts_id = $alunoSitDiv->omcts_id;
                $aluno->area_id = $alunoSitDiv->area_id;
                $aluno->situacoes_diversas_id = $alunoSitDiv->situacoes_diversas_id;
                $aluno->situacoes_diversas_obs = preg_replace('/(\'|")/', '”', $alunoSitDiv->situacoes_diversas_obs);
                
                $alunos->push($aluno);
            }
        }
        return $alunos;
    }

    private static function retornaAlunosComQms($anoFormacaoID=0){
        
        $aluno = Alunos::whereNotNull('qms_id')->orderBy('numero');

        if($anoFormacaoID > 0){
            //return $aluno->where([['data_matricula', '=', $anoFormacaoID]])->orWhere([['ano_formacao_reintegr_id', '=', $anoFormacaoID]]);
            //Passa a Pegar os alunos que foram integrados
            return $aluno->where(function($query) use ($anoFormacaoID){
                return $query->where([['data_matricula', '=', $anoFormacaoID]])->orWhere([['ano_formacao_reintegr_id', '=', $anoFormacaoID]]);
            });
            
        }

        return $aluno->with('qms');
    }

    public static function retornaAlunosComQmsESA($anoFormacaoID=0, $colunas=[]){
        
        $aluno = Alunos::retornaAlunosComQmsESAGeral($anoFormacaoID);
      
        if(session()->has('qms_selecionada') && !(session()->get('qms_selecionada') == 9999)){

            $aluno->whereHas('qms', function($q){
                $q->where('qms_matriz_id', '=', session()->get('qms_selecionada'));
            });
            
        }else if(!in_array('9999', session()->get('login.perfil'))
                && !in_array('9003', session()->get('login.perfil'))
                && !in_array('9004', session()->get('login.perfil'))
                && !in_array('9005', session()->get('login.perfil'))
        ){
            if($anoFormacaoID > 0){
                $aluno->where('qms_id', QMS::where([
                    ['escolha_qms_id', '=', EscolhaQMS::where('ano_formacao_id', $anoFormacaoID)->first()->id],
                    ['qms_matriz_id', '=', session()->get('login.qmsID.0.qms_matriz_id')]
                ])->first()->id );
            }
        }                

        if(count($colunas) == 0){
            $colunas = null;
        }

        return $aluno->get($colunas);
    }

    public static function retornaAlunosComQmsESAGeral($anoFormacaoID=0){
        $qms_alias_esa = ['infantaria', 'cavalaria', 'artilharia', 'engenharia', 'comunicacoes'];

        $aluno = Alunos::retornaAlunosComQms($anoFormacaoID)
                ->whereHas('qms', function($query) use($qms_alias_esa) {
                    $query->whereIn('qms_alias', $qms_alias_esa);
                });
                
        return $aluno;
    }

    public static function retornaAlunosComQmsEspecifica($anoFormacaoID, $arrayIdQms){
        return Alunos::retornaAlunosComQms($anoFormacaoID)->whereIn('qms_id', $arrayIdQms);
    }

    public static function filtraAlunosOmctAreaSeg($alunos, $omct_id, $area_id, $segmento)
    { //Lista de Alunos

        $retorno = [];
        $contador = count($alunos);
        for ($i = 0; $i < $contador; $i++) {
            if (
                $alunos[$i]->omcts_id == $omct_id
                && $alunos[$i]->area_id == $area_id
                && $alunos[$i]->sexo == $segmento
            ) {
                $retorno[] = $alunos[$i];
            }
        }
        return $retorno;
    }

    public static function filtraAlunosOmct($alunos, $omct_id)
    { //Lista de Alunos

        $retorno = [];
        $contador = count($alunos);
        for ($i = 0; $i < $contador; $i++) {
            if ($alunos[$i]->omcts_id == $omct_id) {
                $retorno[] = $alunos[$i];
            }
        }
        return $retorno;
    }

    public static function filtraAlunosNumero($alunos, $numero)
    { //Lista de Alunos

        $retorno = [];
        $contador = count($alunos);
        for ($i = 0; $i < $contador; $i++) {
            if ($alunos[$i]->numero == $numero) {
                $retorno[] = $alunos[$i];
            }
        }
        return $retorno;
    }

    public static function filtraAlunosNome($alunos, $nomeAluno)
    { //Lista de Alunos

        $retorno = [];
        $contador = count($alunos);
        for ($i = 0; $i < $contador; $i++) {
            if (strpos($alunos[$i]->nome_completo, strtoupper($nomeAluno)) === 0) {
                $retorno[] = $alunos[$i];
            }
        }
        return $retorno;
    }

    public function regrasEsa()
    {
        
        return [
            'numero' => 'required|numeric',
            'nome_completo' => 'required',
            'nome_guerra' => 'required',
            //'nome_guerra' => 'required|unique:alunos,nome_guerra,'.$this->id.',id,data_matricula,'. $this->data_matricula,
            'data_nascimento' => 'required|date',
            'data_matricula' => 'required|numeric',
            'area_id' => 'required|numeric',
            'atleta_marexaer' => 'required|string',
            'sexo' => 'required|string',
            'email' => 'required|unique:alunos,email,' . $this->id,
            'al_inscricao' => 'required|numeric',
            'nasc_cidade' => 'required',
            'nasc_pais' => 'required'
        ];
    }

    public function regras()
    {

        return [
            'numero' => 'required|numeric',
            'nome_completo' => 'required',
            'nome_guerra' => 'required',
            //'nome_guerra' => 'required|unique:alunos,nome_guerra,'.$this->id.',id,data_matricula,'. $this->data_matricula,
            'data_nascimento' => 'required|date',
            'data_matricula' => 'required|numeric',
            'area_id' => 'required|numeric',
            'sexo' => 'required|string',
            'atleta_marexaer' => 'required|string',
            'email' => 'required|unique:alunos,email,' . $this->id,
            'al_inscricao' => 'required|numeric',
            'nasc_cidade' => 'required',
            'nasc_pais' => 'required'
        ];
    }

    public function atributos()
    {
        /*return [
            'numero' => 'Número',
            'nome_completo' => 'Nome Completo',
            'nome_guerra' => 'Nome de Guerra',
            'data_nascimento' => 'Data de Nascimento',
            'data_matricula' => 'Ano de Formação',
            'area_id' => 'Área do Aluno',
            'atleta_marexaer' => 'Atleta Marexaer',
            'sexo' => 'Segmento',
            'email' => 'E-mail',
            'al_inscricao' => 'Número Incrição',
            'nasc_cidade' => 'Cidade (Naturalidade)',
            'nasc_pais' => 'País (Naturalidade)',
            'nome_completo' => 'Nome Completo'
        ];*/

        return [
            'numero' => 'Número',
            'qms_id' => 'QMS',
            'nome_completo' => 'Nome Completo',
            'nome_guerra' => 'Nome de Guerra',
            'data_nascimento' => 'Data de Nascimento',
            'data_matricula' => 'Ano de Formação',
            'primeira_data_praca' => '1ª Data Praça',
            'turma_id' => 'Turma',
            'area_id' => 'Área do Aluno',
            'instrumento_id' => 'Tipo Instrumento',
            'nasc_pais' => 'País (Naturalidade)',
            'atleta_marexaer' => ' Atleta Marexaer',
            'modalidade' => 'Modalidade',
            'habilidades' => 'Habilidades',
            'sexo' => 'Segmento',
            'email' => ' E-mail',
            
            //'precedencia' => '(Definir)',
            'data_cadastro' => 'Data de Cadastro',
            'al_inscricao' => 'Número Inscrição',
            'nasc_cidade' => 'Cidade (Naturalidade)',
            'nasc_id_uf' => 'UF (Naturalidade)',
            //'data_incorporacao' => '(Definir)',
            'id_situacao_anterior' => 'Situação Anterior (militar ou civil)',
            'id_situacao_matricula' => 'Situação no Ato da Matrícula',
            //'id_situacao_pb' => '(Definir)',
            //'id_situacao_atual' => 'Situação Atual',
            //'data_pracacfs' => '(Definir)',
            //'data_apresentacao' => '(Definir)',
            'data_cb' => 'Data de Promoção a Cabo',
            'data_sgttemp' => 'Data de Promoção a Sgt Temp',
            'data_baixa_ultima_om' => ' Data de Baixa da Última OM',
            'nome_ultima_om' => 'Última OM',
            'endereco_ultima_om' => 'Endereço da Última OM',
            'temposv_anterior' => 'Tempo SV Anterior',
            'tscmm_anterior' => 'TSCMM Anterior',
            'endereco' => 'Endereço',
            'bairro' => 'Bairro',
            'cidade' => 'Cidade',
            'id_uf' => 'UF',
            'cep' => 'CEP',
            'telefone' => 'Telefone',
            'celular1' => 'Celular 1',
            'celular2' => 'Celular 2',
            'celular3' => 'Celular 3',
            'nome_mae' => 'Nome da Mãe',
            'id_profissao_mae' => 'Profissão Mãe',
            'nome_pai' => 'Nome da Pai',
            'id_profissao_pai' => 'Profissão Pai',
            'doc_idt_civil' => 'Nº Identidade Civil',
            'doc_idt_civil_o_exp' => 'Org Expd (Idt Civil)',
            'doc_tit_eleitor' => 'Nº Título Eleitoral',
            'doc_tit_secao' => 'Seção (Título Eleitoral)',
            'doc_tit_zona' => 'Zona (Título Eleitoral)',
            'doc_tit_cidade_uf' => 'Cidade/UF (Título Eleitoral)',
            'doc_cert_nascimento' => 'Certidão de Nascimento',
            'doc_cpf' => 'CPF',
            'doc_cnh' => 'CNH',
            'doc_pis' => 'PIS',
            'doc_pasep' => 'PASEP',
            'doc_idt_militar' => 'Nº Identidade Militar',
            'doc_idt_militar_o_exp' => ' Org Expd (Idt Mil)',
            'doc_idt_militar_dt_exp' => 'Data Expd (Idt Mil)',
            'doc_fam' => 'FAM',
            'doc_capemi' => 'CAPEMI',
            'doc_gboex' => 'GBOEx',
            'doc_cp' => 'CP',
            'doc_preccp' => ' PREC CP',
            'tipo_sanguineo' => 'Tipo Sanguíneo',
            'fator_rh' => 'Fator RH',
            'cabelo' => 'Cabelos',
            'altura' => 'Altura',
            'cutis' => 'Cutis',
            'olhos' => 'Olhos',
            'tatuagem' => 'Tatuagem',
            'id_raca' => 'Cor/Raça',
            'id_religiao' => 'Religião',
            'id_escolaridade' => 'Escolaridade',
            //'id_escolaridade_superior' => '(Definir)',
            //'escolaridade_per_matr_sup' => '(Definir)',
            'id_renda' => 'Renda Familiar',
            'obs' => ' Observações',
            'id_banco' => 'Banco',
            'num_agencia_banco' => 'Agência',
            'num_conta_bancaria' => 'Nº Conta',
            //'data_desligamento_cfs' => '(Definir)',
            //'num_bi_desligamento_cfs' => '(Definir)',
            //'id_motivo_desligamento_cfs' => '(Definir)',
            //'tp_req_desligamento_cfs' => '(Definir)',
            //'amparo_desligamento_cfs' => '(Definir)',
            //'id_req_desligamento_cfs' => '(Definir)',
            'id_estado_civil' => ' Estado Civil',
            //'data_alteracao' => '(Definir)',
            //'endereco_guarnicao' => '(Definir)',
            //'bairro_guarnicao' => '(Definir)',
            //'cidade_guarnicao' => '(Definir)',
            //'id_uf_guarnicao' => '(Definir)',
            //'cep_guarnicao' => '(Definir)',
            //'gramatica_naturalidade_cidade' => '(Definir)',
            //'gramatica_naturalidade_uf' => '(Definir)',
            'farda_tam_boina' => 'Boina',
            'farda_tam_gorro' => 'Gorro',
            'farda_tam_camiseta' => 'Camiseta',
            'farda_tam_gandola' => 'Gandola',
            'farda_tam_calca' => 'Calça',
            'farda_tam_coturno' => 'Coturno',
            'turma_esa_id' => 'Turma(ESA)'
        ];
    }

    public function getFillableDescription()
    {

        $columns = $this->getFillable();
        $return = [];



        foreach ($columns as $column) {
            if (key_exists($column, $this->atributos())) {
                $object = (object) array();
                $object->field = $column;
                $object->description = $this->atributos()[$column];

                $return[$object->field] = $object;

                /*if ($object->field == 'id_situacao_matricula') {
                    $object = (object) array('field' => 'situacao_atual', 'description' => 'Situação Atual');
                    $return[$object->field] = $object;
                }*/
            }
        }



        return $return;
    }

    public function import($nameFile)
    {
        try {
            $import = new AlunosImport();
            $import->import($nameFile);

            $retorno['status'] = 'ok';
            $retorno['response'] = 'Alunos Inseridos';
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.

                $retorno['status'] = 'error';
                $retorno['error'] = '<b>Na linha ' . $failure->row() . '</b>';

                foreach ($failure->errors() as $error) {
                    $retorno['error'] = $retorno['error'] . ' ' . $error . '<BR>';
                }
            }
        }

        return response()->json($retorno);
    }

    public function importInsert($nameFile)
    {
        try {
            $import = new AlunosImportInsert();
            $import->import($nameFile);

            $retorno['status'] = 'ok';
            $retorno['response'] = 'Alunos Inseridos';
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.

                $retorno['status'] = 'error';
                $retorno['error'] = '<b>Na linha ' . $failure->row() . '</b>';

                foreach ($failure->errors() as $error) {
                    $retorno['error'] = $retorno['error'] . ' ' . $error . '<BR>';
                }
            }
        }

        return response()->json($retorno);
    }

    public function importaImagemAluno()
    {
        //Verifica senão existe imagem
        if ($this->imagem_aluno->nome_arquivo == 'no-image.jpg' && $this->data_matricula <= 2) {
            $extension = 'jpg';

            $name = uniqid($this->ano_formacao->formacao . $this->numero);
            $nameFile = "{$name}.{$extension}";

            FuncoesController::LimpaPastaTemp();
            //Source
            $source = '/esanet/UploadDeArquivos/Sistemas/NetAluno/Fotos/' . $this->ano_formacao->formacao . '_' . $this->numero . '.jpg';
            $destination = storage_path('app/public/') . 'temp/' . $this->ano_formacao->formacao . '_' . $this->numero . '.jpg';

            $smbClientePhp = new SmbClientPhp('//192.168.0.3/ESANet/', 'ESA/2tenjoaovictor', '@Joao321');
            $smbClientePhp->get($source, $destination);

            if (is_file($destination)) {

                //Destination
                $destinationPath = storage_path('app/public/') . 'imagens_aluno/' . $this->ano_formacao->formacao . '/';

                if (!is_dir($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                if (copy($destination, $destinationPath . $nameFile)) {
                    $this->imagem_aluno->nome_arquivo = $nameFile;
                    $this->imagem_aluno->save();
                }
            }
        }
    }
}

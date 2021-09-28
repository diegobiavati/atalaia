<?php

namespace App\Http\OwnClasses;

use App\Models\Alunos;
use App\Models\AlunosClassificacao;
use App\Models\AlunosVoluntAv;
use App\Models\EscolhaQMS;
use App\Models\EscolhaQMSAlunosOpcoes;
use App\Models\QMS;
use App\Models\OMCT;
use Exception;
use Illuminate\Support\Facades\DB;


class EscolhaQMSLoader
{

    private $alunos;
    private $qmEsses;
    private $classificacao;
    private $alunos_opcoes;
    private $voluntarios_aviacao;
    public $ano_formacao;
    public $escolha_qms_id;

    public function __construct(AlunosClassificacao $classificacao, QMS $qmEsses, EscolhaQMSAlunosOpcoes $alunos_opcoes, AlunosVoluntAv $voluntarios_aviacao, Alunos $alunos)
    {
        $this->qmEsses = $qmEsses;
        $this->classificacao = $classificacao;
        $this->alunos_opcoes = $alunos_opcoes;
        $this->voluntarios_aviacao = $voluntarios_aviacao;
        $this->alunos = $alunos;
    }


    public function getVagasQMSAviacao($segmento)
    {
        if ($segmento != 'M' && $segmento != 'F') {
            return false;
        } else if ($segmento == 'M') {
            $vaga = $this->qmEsses->where('escolha_qms_id', $this->escolha_qms_id)->where('qms_alias', 'aviacao')->first();
            return $vaga->vagas;
        } else if ($segmento == 'F') {
            $vaga = $this->qmEsses->where('escolha_qms_id', $this->escolha_qms_id)->where('qms_alias', 'aviacao_feminino')->first();
            return $vaga->vagas;
        } else {
            return false;
        }
    }

    public function getClassificacao()
    {
        if (is_numeric($this->ano_formacao)) {
            return $this->classificacao->where('ano_formacao_id', $this->ano_formacao)->get();
        } else {
            return false;
        }
    }

    public function getAlunosOpcoes($id = null, $segmento = null)
    {

        if (is_null($id) && is_null($segmento)) {

            $opcoes_data = $this->alunos_opcoes->where('escolha_qms_id', $this->escolha_qms_id)->get();
            foreach ($opcoes_data as $res) {
                if (!is_null($res->opcoes)) {
                    $data[$res->aluno_id] =  unserialize($res->opcoes);
                }
            }

            return (isset($data)) ? $data : false;
        } else if (!is_null($id) && is_array($id)) {

            // $segmento SERÁ IGNORADO

            $opcoes_data = $this->alunos_opcoes->whereIn('aluno_id', $id)->where('escolha_qms_id', $this->escolha_qms_id)->get();
            foreach ($opcoes_data as $res) {
                if (!is_null($res->opcoes)) {
                    $data[$res->aluno_id]['info'] = json_encode($res);
                    $data[$res->aluno_id]['opcoes'] =  unserialize($res->opcoes);
                }
            }

            return (isset($data)) ? $data : false;
        } else if (!is_null($id) && !is_array($id)) {
           
            $opcoes_data =  $this->alunos_opcoes->where('aluno_id', $id)->where('escolha_qms_id', $this->escolha_qms_id)->first();

            $data[$opcoes_data->aluno_id]['opcoes'] = unserialize($opcoes_data->opcoes);
            $data[$opcoes_data->aluno_id]['info'] = json_encode($opcoes_data);

            return $data;
        } else if (is_null($id) && !is_null($segmento)) {

            $opcoes_data = $this->alunos_opcoes->where('escolha_qms_id', $this->escolha_qms_id)->get();
            foreach ($opcoes_data as $res) {
                if ($res->aluno->sexo == $segmento) {
                    $data[$res->aluno_id] =  unserialize($res->opcoes);
                }
            }
            return (isset($data)) ? $data : false;
        }
    }

    public function getAlunosAviacao($segmento, $reprovado = 'N')
    {

        if ($segmento == 'M') {
            $escolhaQmsDesignado = EscolhaQMS::select('escolha_qms_masculino as escolha_qms_final')->where([['id', '=', $this->escolha_qms_id], ['ano_formacao_id', '=', $this->ano_formacao]])->first();
        } else {
            $escolhaQmsDesignado = EscolhaQMS::select('escolha_qms_feminino as escolha_qms_final')->where([['id', '=', $this->escolha_qms_id], ['ano_formacao_id', '=', $this->ano_formacao]])->first();
        }
       
        if($reprovado == 'N' && $escolhaQmsDesignado->escolha_qms_final != null){
            $escolha_result = unserialize($escolhaQmsDesignado->escolha_qms_final);

            $data = collect();
            foreach($escolha_result['alunos_aviacao'] as $alunos){
                $data->push($alunos->classificacao);
            }
        }else{

            // SELECIONANDO TODOS OS VOLUNTARIOS APTOS PARA AVIAÇÃO
            $volunt_avIDs = $this->voluntarios_aviacao->where('apto', 1)->whereNotNull('alunos_id')->get(['alunos_id']);

            //  SELECIONANDO TODOS OS ALUNOS $volunt_avIDs DO ANO DE FORMAÇÃO SELECIONADO DO SEGMENTO SELECIONADO
            $alunosIDs = $this->alunos->where('data_matricula', $this->ano_formacao)->where('sexo', $segmento)->whereIn('id', $volunt_avIDs)->get(['id']);

            // SELECIONANDO TODAS AS NOTAS FINAIS DOS ALUNOS $alunosIDs
            if ($reprovado == 'N') {
                $data = $this->classificacao->whereIn('aluno_id', $alunosIDs)->where('reprovado', 'N')->orderBy('nota_final', 'desc')->skip(0)->take($this->getVagasQMSAviacao($segmento))->get();
            } else {
                $data = $this->classificacao->whereIn('aluno_id', $alunosIDs)->orderBy('nota_final', 'desc')->skip(0)->take($this->getVagasQMSAviacao($segmento))->get();
            }
        }

        return (isset($data)) ? $data : false;
    }

    /*
    
        Está última função faz a distribuição dos alunos nas qms escolhidas COMBATENTE E LOGISTICA
    
    */

    public function designacaoFinalQMS($segmento, $tipo, $reprovado = 'N')
    {

        // SELECIONANDO TODAS AS VAGAS DENTRO DA PROPRIEDADE $this->escolha_qms_id, $segmento, exceto vagas de aviação
        if ($segmento == 'M') {
            $qms_data = $this->qmEsses->where('escolha_qms_id', $this->escolha_qms_id)->where('segmento', $segmento)->where('qms_alias', '<>', 'aviacao')->get();
            $escolhaQmsDesignado = EscolhaQMS::select('escolha_qms_masculino as escolha_qms_final')->where([['id', '=', $this->escolha_qms_id], ['ano_formacao_id', '=', $this->ano_formacao]])->first();
        } else {
            $qms_data = $this->qmEsses->where('escolha_qms_id', $this->escolha_qms_id)->where('segmento', $segmento)->where('qms_alias', '<>', 'aviacao_feminino')->get();
            $escolhaQmsDesignado = EscolhaQMS::select('escolha_qms_feminino as escolha_qms_final')->where([['id', '=', $this->escolha_qms_id], ['ano_formacao_id', '=', $this->ano_formacao]])->first();
        }
        //$escolhaQmsDesignado->escolha_qms_final = null;
        if($reprovado == 'N' && $escolhaQmsDesignado->escolha_qms_final != null){
            $alunos = unserialize($escolhaQmsDesignado->escolha_qms_final)['aluno'];
            $alunos['recuperado'] = true;

            if ($tipo == 'por_area') {
                $escolha_result = $alunos;
            }

            if ($tipo == 'por_qms') {
                foreach ($alunos as $key => $item) {
                    if (isset($item['opcoes'])) {
                        foreach ($item['opcoes'] as $prioridade) {
                                $escolha_result[$prioridade][$key] = array(
                                    "numero" => $item['numero'],
                                    "nome" => $item['nome'],
                                    "nome_guerra" => $item['nome_guerra'],
                                    "omct" => $item['omct'],
                                    "NPB" =>  $item['NPB'],
                                    "classificacao" =>  $item['classificacao'],
                                    "classificacao_por_area" =>  $item['classificacao_por_area'],
                                    "qmsdesignda" => $item['qmsdesignda'],
                                    "qmsdesignda_nome" => $item['qmsdesignda_nome'],
                                    "qmsdesignda_nome_sigla" => $item['qmsdesignda_nome_sigla'],
                                    "opcoes" => $item['opcoes']
                                );
                                break;
                        }
                    }
                }
            }
            
        }else{
           
            foreach ($qms_data as $item) {
                $qms_id_vagas[$item->id] = $item->vagas;
                $qms_id_nome[$item->id] = $item->qms;
                $qms_id_nome_sigla[$item->id] = $item->qms_sigla;
            }

            // SELECIONANDO TODOS OS ALUNOS DESIGNADOS PARA QMS AVIAÇÃO

            $alunos_aviacao = $this->getAlunosAviacao($segmento, $reprovado);

            foreach ($alunos_aviacao as $item) {
                $alunos_aviacao_ID[] = $item->aluno_id;
            }

            $alunos_aviacao_ID = ($alunos_aviacao_ID) ?? array(0);

            // SELECIONANDO TODOS OS ALUNOS APROVADOS DO CORRENTE ANO DE FORMAÇÃO

            if ($reprovado == 'N') {
                $alunos_aprovados = $this->classificacao->where('ano_formacao_id', $this->ano_formacao)->where('reprovado', 'N')->get();
            } else {
                $alunos_aprovados = $this->classificacao->where('ano_formacao_id', $this->ano_formacao)->get();
            }


            foreach ($alunos_aprovados as $item) {
                $aprovados[] = $item->aluno_id;
            }

            $aprovados = ($aprovados) ?? array(0);

            // SELECIONANDO TODOS OS ALUNOS DO SEGMENTO SELECIONADO (aprovados menos os já designados para aviação)

            //$alunos = $this->alunos->where('sexo', $segmento)->whereIn('id', $alunos_aprovados)->whereNotIn('id', $alunos_aviacao_ID)->orderBy($this->alunos->classificacao(), 'asc')->get();

            $alunos = DB::select("SELECT alunos.*, alunos_classificacao.classificacao, alunos_classificacao.classificacao_por_area, alunos_classificacao.nota_final_arredondada FROM alunos
                                    INNER JOIN alunos_classificacao ON alunos.id=alunos_classificacao.aluno_id
                                    WHERE sexo='" . $segmento . "' 
                                    AND alunos.id IN(" . implode(',', $aprovados) . ")
                                    AND alunos.id NOT IN(" . implode(',', $alunos_aviacao_ID) . ")
                                    ORDER BY classificacao ASC");

            foreach ($alunos as $item) {
                $alunosIDs[] = $item->id;
            }

            $alunos_opcoes =  $this->getAlunosOpcoes($alunosIDs);

            // UETEs 

            $omct = OMCT::get();
            foreach ($omct as $item) {
                $omct_id_nome_abrev[$item->id] = $item->sigla_omct;
                $omct_id_nome[$item->id] = $item->omct;
            }

            //dd($alunos_opcoes[43]);

            if ($tipo == 'por_area') {
                foreach ($alunos as $item) {
                    if (isset($alunos_opcoes[$item->id])) {
                        $o = 0;
                        foreach ($alunos_opcoes[$item->id]['opcoes'] as $prioridade) {
                            $o++;
                            if ($qms_id_vagas[$prioridade] > 0) {
                                $escolha_result[$item->id] = array(
                                    "numero" => $item->numero,
                                    "nome" => $item->nome_completo,
                                    "nome_guerra" => $item->nome_guerra,
                                    "omct" => $omct_id_nome_abrev[$item->omcts_id],
                                    "NPB" =>  $item->nota_final_arredondada,
                                    "classificacao" =>  $item->classificacao,
                                    "classificacao_por_area" =>  $item->classificacao_por_area,
                                    "qmsdesignda" => $prioridade,
                                    "qmsdesignda_nome" => $qms_id_nome[$prioridade],
                                    "qmsdesignda_nome_sigla" => $qms_id_nome_sigla[$prioridade],
                                    "opcoes" => $alunos_opcoes[$item->id]['opcoes'],
                                    "opcao_atendido" => $o
                                );
                                $qms_id_vagas[$prioridade]--;
                                break;
                            }
                        }
                    }
                }
                //return (isset($escolha_result)) ? $escolha_result : false;
            }

            if ($tipo == 'por_qms') {

                foreach ($alunos as $item) {
                    if (isset($alunos_opcoes[$item->id])) {
                        foreach ($alunos_opcoes[$item->id]['opcoes'] as $prioridade) {
                            if ($qms_id_vagas[$prioridade] > 0) {
                                $escolha_result[$prioridade][$item->id] = array(
                                    "numero" => $item->numero,
                                    "nome" => $item->nome_completo,
                                    "nome_guerra" => $item->nome_guerra,
                                    "omct" => $omct_id_nome_abrev[$item->omcts_id],
                                    "NPB" =>  $item->nota_final_arredondada,
                                    "classificacao" =>  $item->classificacao,
                                    "classificacao_por_area" =>  $item->classificacao_por_area,
                                    "qmsdesignda" => $prioridade,
                                    "qmsdesignda_nome" => $qms_id_nome[$prioridade],
                                    "qmsdesignda_nome_sigla" => $qms_id_nome_sigla[$prioridade],
                                    "opcoes" => $alunos_opcoes[$item->id]['opcoes']
                                );
                                $qms_id_vagas[$prioridade]--;
                                break;
                            }
                        }
                    }
                }
                
                //return (isset($escolha_result)) ? $escolha_result : false;
            }
        }
        return (isset($escolha_result)) ? $escolha_result : false;
    }

    public function designacaoFinalQMSDetalhada($segmento, $reprovado = 'N')
    {

        // SELECIONANDO TODAS AS VAGAS DENTRO DA PROPRIEDADE $this->escolha_qms_id, $segmento, exceto vagas de aviação
        if ($segmento == 'M') {
            $qms_data = $this->qmEsses->where('escolha_qms_id', $this->escolha_qms_id)->where('segmento', $segmento)->where('qms_alias', '<>', 'aviacao')->get();
            $escolhaQmsDesignado = EscolhaQMS::select('escolha_qms_masculino as escolha_qms_final')->where([['id', '=', $this->escolha_qms_id], ['ano_formacao_id', '=', $this->ano_formacao]])->first();
        } else {
            $qms_data = $this->qmEsses->where('escolha_qms_id', $this->escolha_qms_id)->where('segmento', $segmento)->where('qms_alias', '<>', 'aviacao_feminino')->get();
            $escolhaQmsDesignado = EscolhaQMS::select('escolha_qms_feminino as escolha_qms_final')->where([['id', '=', $this->escolha_qms_id], ['ano_formacao_id', '=', $this->ano_formacao]])->first();
        }
       
        if($reprovado == 'N' && $escolhaQmsDesignado->escolha_qms_final != null){
            $escolha_result = unserialize($escolhaQmsDesignado->escolha_qms_final);
        }else{
            foreach ($qms_data as $item) {
                $qms_id_vagas[$item->id] = $item->vagas;
                $qms_id_vagas_fixa[$item->id] = $item->vagas;
                $qms_id_nome[$item->id] = $item->qms;
                $qms_id_nome_sigla[$item->id] = $item->qms_sigla;
            }
    
            // SELECIONANDO TODOS OS ALUNOS DESIGNADOS PARA QMS AVIAÇÃO
            $alunos_aviacao = $this->getAlunosAviacao($segmento, $reprovado);

            $alunos_temp = array();
            foreach ($alunos_aviacao as $item) {
                $alunos_aviacao_ID[] = $item->aluno_id;

                $alunos_temp[$item->aluno->id] = $item->aluno;
            }

            //Guarda os alunos da aviação
            $escolha_result['alunos_aviacao'] = $alunos_temp;
    
            $alunos_aviacao_ID = ($alunos_aviacao_ID) ?? array(0);
    
            // SELECIONANDO TODOS OS ALUNOS DO CORRENTE ANO DE FORMAÇÃO (reprovado ou nao)
    
            //$alunos_aprovados = $this->classificacao->where('ano_formacao_id', $this->ano_formacao)->get();
    
            if ($reprovado == 'N') {
                $alunos_class_data = $this->classificacao->where('reprovado', $reprovado)->where('ano_formacao_id', $this->ano_formacao)->get();
            } else {
                $alunos_class_data = $this->classificacao->where('ano_formacao_id', $this->ano_formacao)->get();
            }
    
            foreach ($alunos_class_data as $item) {
                if (is_numeric($item->aluno_id)) {
                    $alunos_class[] = $item->aluno_id;
                }
            }
    
            $alunos_class = ($alunos_class) ?? array(0);
            //dd(implode(',', $alunos_class));
            // SELECIONANDO TODOS OS ALUNOS DO SEGMENTO SELECIONADO (aprovados menos os já designados para aviação)
    
            //$alunos = $this->alunos->where('sexo', $segmento)->whereIn('id', $alunos_aprovados)->whereNotIn('id', $alunos_aviacao_ID)->orderBy($this->alunos->classificacao(), 'asc')->get();
    
            $alunos = DB::select("SELECT alunos.*, alunos_classificacao.classificacao, alunos_classificacao.classificacao_por_area, alunos_classificacao.nota_final_arredondada FROM alunos
                                    INNER JOIN alunos_classificacao ON alunos.id=alunos_classificacao.aluno_id
                                    WHERE sexo='" . $segmento . "'
                                    AND alunos.id IN(" . implode(',', $alunos_class) . ")
                                    AND alunos.id NOT IN(" . implode(',', $alunos_aviacao_ID) . ")
                                    ORDER BY classificacao ASC");
  
            foreach ($alunos as $item) {
                $alunosIDs[] = $item->id;
            }

            if (isset($alunosIDs)) {
                $alunos_opcoes =  $this->getAlunosOpcoes($alunosIDs);
            }
    
            // UETEs 
            $omct = OMCT::get();
            foreach ($omct as $item) {
                $omct_id_nome_abrev[$item->id] = $item->sigla_omct;
                $omct_id_nome[$item->id] = $item->omct;
            }
    
            for ($i = 1; $i <= count($qms_data); $i++) {
                $opcao_atendido[$i] = 1;
                foreach ($qms_data as $item) {
                    $opcao[$i][$item->id] = 1;

                    $opcao_qms[$item->id] = 1;
                }
            }
            
            foreach ($alunos as $item) {
                if (isset($alunos_opcoes[$item->id])) {
                    $o = 0;
                    //dd($item);
                    foreach ($alunos_opcoes[$item->id]['opcoes'] as $prioridade) {
                        $o++;
                        if ($qms_id_vagas[$prioridade] > 0) {
                            $qms_id_vagas[$prioridade]--;
    
                            //dd($qms_id_nome[$prioridade], $prioridade);
    
                            $escolha_result['aluno'][$item->id] = array(
                                "numero" => $item->numero,
                                "nome" => $item->nome_completo,
                                "nome_guerra" => $item->nome_guerra,
                                "omct_id" => $item->omcts_id,
                                "omct" => $omct_id_nome_abrev[$item->omcts_id],
                                "NPB" =>  $item->nota_final_arredondada,
                                "classificacao" =>  $item->classificacao,
                                "classificacao_por_area" =>  $item->classificacao_por_area,
                                "qmsdesignda" => $prioridade,
                                "qmsdesignda_nome" => $qms_id_nome[$prioridade],
                                "qmsdesignda_nome_sigla" => $qms_id_nome_sigla[$prioridade],
                                "classificacao_qmsdesignda" => $opcao_qms[$prioridade],
                                "opcoes" => $alunos_opcoes[$item->id]['opcoes'],
                                "qms_vagas_restantes" => $qms_id_vagas,
                                "opcao_atendido" => $o
                            );
                            $opcao_atendido[$o]++;
                            $opcao_qms[$prioridade]++;
                            break;
                        }
                    }
    
                    $i = 0;
                    foreach ($alunos_opcoes[$item->id]['opcoes'] as $prioridade) {

                        try{
                            $i++;
                            $opcao[$i][$prioridade]++;
                        }catch(Exception $e){

                            //Remover Depois
                            //Corrige o lançamento de prioridade errado para o Ano de 2022
                            $alunos_opcoes_new = $alunos_opcoes[$item->id]['opcoes'];
                            for($t = 1; $t <= count($alunos_opcoes_new); $t++){
                                if(isset($alunos_opcoes_new['prioridade_'.$t]) && $alunos_opcoes_new['prioridade_'.$t] == 102){
                                    unset($alunos_opcoes_new['prioridade_'.$t]);
                                }
                            }
                            
                            $info = json_decode($alunos_opcoes[$item->id]['info']);
                            
                            EscolhaQMSAlunosOpcoes::where([['id', '=', $info->id]])->update(['opcoes' => serialize($alunos_opcoes_new)]);
                            //dd($alunos_opcoes_new);
                            //dd($alunos_opcoes[$item->id], $item->numero, $opcao);
                            //Fim Remover
                        }                        
                    }
                }
            }
    
            $escolha_result['qmEsses'] = $qms_data;
            $escolha_result['estatistica']['opcao_atendido'] = $opcao_atendido;
            $escolha_result['estatistica']['opcao'] = $opcao;
            $escolha_result['qms_vagas_fixas'] = $qms_id_vagas_fixa;
            $escolha_result['recuperado'] = false;
        }

        return (isset($escolha_result)) ? $escolha_result : false;
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlunosSitDiv extends Model
{

    protected $table = 'alunos_situacoes_diversas';
    public $timestamps = true;
    

    public function turma(){
        return $this->belongsTo('App\Models\TurmasPB', 'turma_id', 'id'); 
    }
    
    public function omct(){
        return $this->belongsTo('App\Models\OMCT', 'omcts_id', 'id'); 
    }

    public function area(){
        return $this->belongsTo('App\Models\Areas', 'area_id', 'id'); 
    }

    public function ano_formacao(){
        return $this->belongsTo('App\Models\AnoFormacao', 'data_matricula', 'id'); 
    }

    public function situacao(){
        return $this->belongsTo('App\Models\SituacoesDiversas', 'situacoes_diversas_id', 'id'); 
    }

    public function situacaoDivHistorico(){
        return $this->belongsTo('App\Models\AlunosSitDivHistorico', 'id', 'aluno_id'); 
    }

    public function motivos(){
        return $this->belongsTo('App\Models\Motivos', 'id_motivo', 'id'); 
    }

    public function nascimento(){
        if ($this->data_nascimento){
            list($ano, $mes, $dia)=explode('-', $this->data_nascimento);
            $data_nascimento = $dia.'/'.$mes.'/'.$ano;
        } else {
            $data_nascimento = 'Não informada';
        }

        return $data_nascimento;
    }

    public function PrimeiraDataPraca(){
        if ($this->primeira_data_praca){
            list($ano, $mes, $dia)=explode('-', $this->primeira_data_praca);
            $data_praca = $dia.'/'.$mes.'/'.$ano;
        } else {
            list($ano, $mes, $dia)=explode('-', $this->ano_formacao->data_matricula);
            $data_praca = $dia.'/'.$mes.'/'.$ano;  
        }

        return $data_praca;
    }

    public function Segmento(){
        if ($this->sexo){
            $segmento = ($this->sexo=='M')?'Masculino':'Feminino';
        } else {
            $data_praca = 'Não informado';
        }

        return $segmento;
    }    

    public static function retornaSitDiversasPB(){
        $retorno = Array();

        $retorno[18]['cod_no_atalaia'] = 3;

        $retorno[23]['cod_no_atalaia'] = 3;

        $retorno[25]['cod_no_atalaia'] = 1;

        $retorno[26]['cod_no_atalaia'] = 1;

        $retorno[27]['cod_no_atalaia'] = 1;

        $retorno[28]['cod_no_atalaia'] = 1;
        
        $retorno[30]['cod_no_atalaia'] = 3;

        $retorno[31]['cod_no_atalaia'] = 3;

        $retorno[32]['cod_no_atalaia'] = 3;

        $retorno[33]['cod_no_atalaia'] = 3;

        $retorno[34]['cod_no_atalaia'] = 3;

        $retorno[35]['cod_no_atalaia'] = 3;

        $retorno[36]['cod_no_atalaia'] = 3;

        $retorno[37]['cod_no_atalaia'] = 3;

        $retorno[50]['cod_no_atalaia'] = 4;

        $retorno[88]['cod_no_atalaia'] = 4;

        $retorno[99]['cod_no_atalaia'] = 4;
        return $retorno;
    }
}

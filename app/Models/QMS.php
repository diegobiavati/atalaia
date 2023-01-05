<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QMS extends Model
{
    protected $connection = 'mysql';
    protected $table = 'qms';
    protected $fillable = ['comandante_operador_id'];

    public $timestamps = false;

    public function escolhaQms(){
        return $this->hasOne('App\Models\EscolhaQMS', 'id', 'escolha_qms_id');
    }

    public function comandanteCurso()
    {
        return $this->belongsTo('App\Models\Operadores', 'comandante_operador_id', 'id');
    }

    public function consultaTurmas(){
        return TurmasEsa::whereHas('alunos', function ($query) {
            $anoFormacao = $this->escolhaQms->anoFormacao;
            $query->where(['qms_id' => $this->id])->where(function($query) use ($anoFormacao){
                return $query->where([['data_matricula', '=', $anoFormacao->id]])->orWhere([['ano_formacao_reintegr_id', '=', $anoFormacao->id]]);
            });
        })->get();
    }
}

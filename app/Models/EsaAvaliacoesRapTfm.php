<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsaAvaliacoesRapTfm extends Model
{
    use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

    protected $connection = 'mysql_ssaa';
    protected $table = 'esa_avaliacoes_rap_tfm';
    protected $fillable = ['id_esa_avaliacoes', 'alunos_faltas', 'local_aplicacao', 'data_aplicacao', 'acidentes', 'fatores_neg_pos', 'cond_meter_nao', 'cond_meter_sim',
                             'efetivo_curso', 'efetivo_realizou', 'id_operador_devolucao'
                        ];
    protected $casts = [
        'data_aplicacao' => 'json',
        'alunos_faltas' => 'json'
    ];

    public function esaAvaliacoes(){
        return $this->belongsTo('App\Models\EsaAvaliacoes', 'id_esa_avaliacoes', 'id');
    } 

    public function faltas(){
        return $this->belongsToJson('App\Models\Alunos', 'alunos_faltas[]->id_aluno');
    }

    public function motivoFalta(){
        return $this->belongsToJson('App\Models\EsaMotivosFaltas', 'alunos_faltas[]->id_motivo');
    }
}
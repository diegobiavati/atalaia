<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsaAvaliacoesRap extends Model
{
    protected $connection = 'mysql_ssaa';
    protected $table = 'esa_avaliacoes_rap';
    protected $fillable = ['id_esa_avaliacoes', 'id_turmas_esa', 'alunos_faltas', 'duracao', 'hora_inicio', 'hora_termino', 'erros_impressao', 'erros_interpretacao', 'local_aplicacao',
                             'cond_local_adequacao', 'cond_local_arrumacao', 'cond_local_silencio', 'cond_local_iluminacao', 'fatores_influencia_aplicacao', 'efetivo_realizou', 
                             'efetivo_termino', 'primeiro_discente', 'segundo_discente', 'terceiro_discente', 'maioria_efetivo', 'todo_efetivo', 'id_operador_devolucao'
                        ];

    public function esaAvaliacoes(){
        return $this->belongsTo('App\Models\EsaAvaliacoes', 'id_esa_avaliacoes', 'id');
    } 

    public function esaTurma(){
        return $this->belongsTo('App\Models\TurmasEsa', 'id_turmas_esa', 'id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsaAvaliacoesRap extends Model
{
    use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

    protected $connection = 'mysql_ssaa';
    protected $table = 'esa_avaliacoes_rap';
    protected $fillable = ['id_esa_avaliacoes', 'id_turmas_esa', 'alunos_faltas', 'duracao', 'hora_inicio', 'hora_termino', 'erros_impressao', 'erros_interpretacao', 'local_aplicacao',
                             'cond_local_adequacao', 'cond_local_arrumacao', 'cond_local_silencio', 'cond_local_iluminacao', 'fatores_influencia_aplicacao', 'efetivo_realizou', 
                             'efetivo_termino', 'primeiro_discente', 'segundo_discente', 'terceiro_discente', 'maioria_efetivo', 'todo_efetivo', 'id_operador_devolucao'
                        ];
    protected $casts = [
        'primeiro_discente' => 'json',
        'segundo_discente' => 'json',
        'terceiro_discente' => 'json',
        'alunos_faltas' => 'json'
    ];

    public function esaAvaliacoes(){
        return $this->belongsTo('App\Models\EsaAvaliacoes', 'id_esa_avaliacoes', 'id');
    } 

    public function esaTurma(){
        return $this->belongsTo('App\Models\TurmasEsa', 'id_turmas_esa', 'id');
    }

    public function primeiroDiscente(){
        return $this->belongsTo('App\Models\Alunos', 'primeiro_discente->id_aluno', 'id');
    }

    public function segundoDiscente(){
        return $this->belongsTo('App\Models\Alunos', 'segundo_discente->id_aluno', 'id');
    }

    public function terceiroDiscente(){
        return $this->belongsTo('App\Models\Alunos', 'terceiro_discente->id_aluno', 'id');
    }

    public function faltas(){
        return $this->belongsToJson('App\Models\Alunos', 'alunos_faltas[]->id_aluno');
    }
}
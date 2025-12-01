<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvaliacoesProntoFaltas extends Model
{
    protected $table = 'avaliacoes_pronto_faltas';
    public $timestamps = false;

    public function aluno(){
        //return $this->hasOne('App\Models\Alunos', 'id', 'aluno_id'); 
        return $this->belongsTo('App\Models\Alunos', 'aluno_id', 'id'); 
    }

    public function alunoSitDiv(){
        //return $this->hasOne('App\Models\AlunosSitDiv', 'id', 'aluno_id'); 
        return $this->belongsTo('App\Models\AlunosSitDiv', 'aluno_id', 'id'); 
    }

}

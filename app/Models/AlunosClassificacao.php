<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlunosClassificacao extends Model
{
    protected $table = 'alunos_classificacao';
    public $timestamps = true;

    public function aluno()
    {
        return $this->hasOne(Alunos::class, 'id', 'aluno_id');
        //return $this->belongsTo('App\Models\Alunos', 'id', 'aluno_id');
                        
    }

            
}

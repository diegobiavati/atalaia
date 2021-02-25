<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurmasEsa extends Model
{
    protected $table = 'turmas_esa';
    public $timestamps = false;

    public function alunos()
    {
        return $this->hasMany('App\Models\Alunos', 'turma_esa_id');
    }
}

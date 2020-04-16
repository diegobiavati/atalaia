<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurmasPB extends Model
{
    protected $table = 'turmas_pb';
    public $timestamps = false;

    public function alunos()
    {
        return $this->hasMany('App\Models\Alunos', 'turma_id');
    }
}

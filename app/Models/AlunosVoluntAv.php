<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlunosVoluntAv extends Model
{
    protected $table = 'alunos_voluntarios_aviacao';
    public $timestamps = false;

    public function aluno()
    {
        return $this->belongsTo('App\Models\Alunos', 'alunos_id', 'id');
    }
}

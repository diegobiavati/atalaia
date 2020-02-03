<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlunosConselhoEscolar extends Model
{
    protected $table = 'alunos_em_conselho_escolar';
    public $timestamps = false;
    
    public function aluno()
    {
        return $this->hasOne(Alunos::class, 'id', 'aluno_id');
    }    
}

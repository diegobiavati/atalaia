<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapitaniMSAccess extends Model
{
    protected $table = 'alunos_classificacao_cfgs';

    public function aluno()
    {
        return $this->belongsTo(Alunos::class, 'aluno_id', 'id');
    }
}

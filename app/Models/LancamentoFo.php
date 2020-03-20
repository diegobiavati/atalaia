<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LancamentoFo extends Model
{
    protected $table = 'lancamento_fo';

    protected $fillable = ['providencia', 'fatd'];

    public function aluno()
    {
        return $this->belongsTo('App\Models\Alunos', 'aluno_id', 'id');
    }

    public function operador()
    {
        return $this->belongsTo('App\Models\Operadores', 'operador_id', 'id');
    }
}

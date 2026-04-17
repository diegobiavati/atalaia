<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LancamentoFo extends Model
{
    protected $table = 'lancamento_fo';

    protected $fillable = ['providencia', 'fatd', 'frad', 'cancelado', 'cancelado_motivo', 'cancelado_operador_id', 'napd_id'];

    public function aluno()
    {
        return $this->belongsTo('App\Models\Alunos', 'aluno_id', 'id');
    }

    public function operador()
    {
        return $this->belongsTo('App\Models\Operadores', 'operador_id', 'id');
    }

    public function comandanteCurso()
    {
        return $this->belongsTo('App\Models\Operadores', 'comandante_operador_id', 'id');
    }

    public function operadorCancelado()
    {
        return $this->belongsTo('App\Models\Operadores', 'cancelado_operador_id', 'id');
    }

    public function fatdLancada()
    {
        return $this->belongsTo('App\Models\Fatd', 'id', 'lancamento_fo_id');
    }
}

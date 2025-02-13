<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlunosSitDivHistorico extends Model
{
    protected $table = 'alunos_situacoes_diversas_historico';
    public $timestamps = true;

    public function omct(){
        return $this->belongsTo('App\Models\OMCT', 'omct_id', 'id'); 
    }
}

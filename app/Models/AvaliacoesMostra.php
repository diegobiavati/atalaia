<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvaliacoesMostra extends Model
{
    protected $fillable = ['avaliacoes_id', 'nome_arquivo', 'status', 'operador_id', 'omct_id'];

    public function operadores(){
        return ($this->belongsTo('App\Models\Operadores', 'operador_id', 'id')) ?? 'Não informada';
    }

    public function omct(){
        return ($this->belongsTo('App\Models\OMCT', 'omct_id', 'id')) ?? 'Não informada';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvaliacoesMostrasRespostas extends Model
{
    protected $fillable = ['avaliacoes_id', 'nome_arquivo', 'omct_id', 'visualizado', 'operador_visu_id'];

    public function omct(){
        return ($this->belongsTo('App\Models\OMCT', 'omct_id', 'id')) ?? 'Não informada';
    }

    public function avaliacoes(){
        return ($this->belongsTo('App\Models\Avaliacoes', 'avaliacoes_id', 'id')) ?? 'Não informada';
    }

}

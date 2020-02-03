<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapaOutrosDados extends Model
{
    protected $fillable = [
        'omct_id', 
        'area_id', 
        'ano_formacao_id', 
        'sexo', 
        'qtdade_previstomtcl',
        'qtdade_designadomtcl',
        'qtdade_adiamentomtcl',
        'qtdade_em_1mtcl',
        'qtdade_em_2mtcl',
        'qtdade_em_mtcladiamento',
        'qtdade_em_mtclordjudicial',
        'qtdade_pqessa',
        'qtdade_pqesslog',
        'qtdade_pqciavex'
    ];
    
    public function uete()
    {
        return ($this->belongsTo('App\Models\OMCT', 'omct_id', 'id')) ?? 'Não informada';
    }

    public function area()
    {
        return ($this->belongsTo('App\Models\Areas', 'area_id', 'id')) ?? 'Não informada';
    }
}

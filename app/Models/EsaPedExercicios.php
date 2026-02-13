<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsaPedExercicios extends Model
{
    protected $connection = 'mysql_ssaa';
    protected $fillable = ['id_esa_ped_ano', 'exercicio', 'exercicio_abrev'];

    public function esapedexercicio()
    {
        return $this->belongsTo('App\Models\EsaDisciplinas', 'id_esa_disciplinas', 'id');
    }
}

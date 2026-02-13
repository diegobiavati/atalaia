<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsaPeds extends Model
{
    protected $connection = 'mysql_ssaa';
    protected $fillable = ['id_esa_ped_exercicio', 'min', 'max', 'mencao', 'n_ped'];

    public function esapedexercicio()
    {
        return $this->belongsTo('App\Models\EsaDisciplinas', 'id_esa_disciplinas', 'id');
    }
}

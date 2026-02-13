<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsaPedAnos extends Model
{
    protected $connection = 'mysql_ssaa';
    protected $fillable = ['ano', 'tipo'];

    public function esapedexercicio()
    {
        return $this->hasMany('App\Models\EsaPedExercicios', 'id_esa_ped_ano', 'id');
    }
}

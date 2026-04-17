<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnoFormacao extends Model
{
    protected $table = 'ano_formacao';
    public $timestamps = false;

    public function disciplinas()
    {
        return $this->hasMany('App\Models\Disciplinas');
    }

    public function escolhaQMS()
    {
        return $this->hasOne('App\Models\EscolhaQMS', 'ano_formacao_id', 'id');
    }
}

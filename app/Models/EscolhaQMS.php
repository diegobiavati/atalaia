<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscolhaQMS extends Model
{
    protected $table = 'escolha_qms';
    public $timestamps = true;

    public function anoFormacao()
    {
        return $this->belongsTo('App\Models\AnoFormacao', 'ano_formacao_id', 'id');
    }

    public function qmss()
    {
        return $this->hasMany(QMS::class, 'escolha_qms_id', 'id');
    }
}

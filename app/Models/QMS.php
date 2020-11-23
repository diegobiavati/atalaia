<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QMS extends Model
{
    protected $table = 'qms';
    public $timestamps = false;

    public function escolhaQms(){
        return $this->hasOne('App\Models\EscolhaQMS', 'id', 'escolha_qms_id');
    }
}

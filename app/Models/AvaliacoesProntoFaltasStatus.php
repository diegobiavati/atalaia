<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvaliacoesProntoFaltasStatus extends Model
{
    protected $table = 'avaliacoes_pronto_faltas_status';
    public $timestamps = false;

    public function uete(){
        return $this->hasOne('App\Models\OMCT', 'id', 'omcts_id'); 
    }
}

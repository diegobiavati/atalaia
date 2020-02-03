<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operadores extends Model
{
    protected $table = 'operadores';
    public $timestamps = false;
    
    protected $fillable = array('nome', 'nome_guerra', 'postograd_id', 'omcts_id', 'id_funcao_operador', 'id_funcao_operador', 'tel_pronto_atendimento');

    public function postograd(){
        return $this->belongsTo('App\Models\PostoGrad');
    }

    public function omcts(){
        return $this->belongsTo('App\Models\OMCT', 'omcts_id', 'id');
    }

    public function usuario(){
        return $this->belongsTo('App\User', 'email', 'email');
    }

}

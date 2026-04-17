<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operadores extends Model
{
    protected $connection = 'mysql';
    protected $table = 'operadores';
    public $timestamps = false;

    protected $fillable = array('nome', 'nome_guerra', 'postograd_id', 'omcts_id', 'id_funcao_operador', 'id_funcao_operador', 'tel_pronto_atendimento');

    public function postograd()
    {
        return $this->belongsTo('App\Models\PostoGrad');
    }

    public function posto()
    {
        return $this->belongsTo('App\Models\PostoGrad', 'postograd_id', 'id');
    }

    public function omcts()
    {
        return $this->belongsTo('App\Models\OMCT', 'omcts_id', 'id');
    }

    public function usuario()
    {
        return $this->belongsTo('App\User', 'email', 'email');
    }

    public function operadoresTipo()
    {
        return $this->belongsTo('App\Models\OperadoresTipo', 'id_funcao_operador', 'id');
    }

    public function qms()
    {
        return $this->belongsTo('App\Models\QMSMatriz', 'qms_matriz_id', 'id');
    }
}

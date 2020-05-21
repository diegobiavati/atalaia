<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fatd extends Model
{
    protected $table = 'fatd';
    protected $primaryKey = 'lancamento_fo_id';

    protected $fillable = ['lancamento_fo_id', 'nr_processo', 'ano'];

    public function lancamentoFo()
    {
        return $this->belongsTo('App\Models\LancamentoFo', 'lancamento_fo_id', 'id');
    }

    public function sargenteante()
    {
        return $this->belongsTo('App\Models\Operadores', 'operador_id', 'id');
    }

    public function tipo_enquadramento()
    {
        return $this->belongsTo('App\Models\Enquadramentos', 'enquadramento_id', 'id');
    }

    public function comportamento()
    {
        return $this->belongsTo('App\Models\Comportamento', 'comportamento_id', 'id');
    }
}

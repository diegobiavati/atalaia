<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fatd extends Model
{
    protected $table = 'fatd';

    protected $fillable = ['lancamento_fo_id', 'nr_processo', 'ano'];

    public function lancamentoFo()
    {
        return $this->belongsTo('App\Models\LancamentoFo', 'lancamento_fo_id', 'id');
    }
}

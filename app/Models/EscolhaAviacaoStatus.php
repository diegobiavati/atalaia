<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscolhaAviacaoStatus extends Model
{
    protected $table = 'escolha_aviacao_status';
    public $timestamps = false;

    protected $fillable = ['ano_formacao_id', 'status'];
}

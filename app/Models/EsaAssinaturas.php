<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsaAssinaturas extends Model
{
    protected $connection = 'mysql_ssaa';
    protected $table = 'esa_assinaturas';
    protected $fillable = ['id_operador', 'assina_relatorio', 'caminho_assinatura'];

    public function operador()
    {
        return ($this->belongsTo('App\Models\Operadores', 'id_operador', 'id')) ?? 'Não informado';
    }
}

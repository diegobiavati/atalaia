<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Portaria;
use App\Models\Teste;

class Teste extends Model
{
    protected $fillable = [
        'taf_portarias_id',
        'nome_teste',
        'valor_inicial',
        'valor_final',
        'grau'
    ];

    public function rules($id = '')
    {
        return [
            'taf_portarias_id' => 'required',
            'nome_teste' => 'required',
            'valor_inicial' => 'required',
            'grau' => 'required',

        ];
    }

    public function portaria()
    {
        return $this->belongsTo(Portaria::class, 'taf_portarias_id', 'id');
    }

}

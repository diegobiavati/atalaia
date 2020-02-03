<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Teste;

class Portaria extends Model
{
    protected $fillable = [
        'nome_portaria',
    ];

    public function rules($id = '')
    {
        return [
            'nome_portaria' => 'required',

        ];
    }

    public function testes()
    {
        return $this->hasMany(Teste::class);
    }
}

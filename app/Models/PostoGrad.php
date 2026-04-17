<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostoGrad extends Model
{
    protected $table = 'postograd';
    public $timestamps = false;

    public function operadores()
    {
        return $this->hasMany('App\Models\Operadores');
    }
}

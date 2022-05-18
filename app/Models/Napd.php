<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Napd extends Model
{
    protected $table = 'napd';
    public $timestamps = false;

    public function getDescricao(){
        return '(Nr: '.$this->numero.', D: '.$this->desdobramento.', Gp: '.$this->grupo.', Clasf: '.$this->classificacao.')-'.$this->especificacao;
    }
}

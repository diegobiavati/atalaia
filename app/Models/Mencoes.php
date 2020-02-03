<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mencoes extends Model
{
    protected $table = 'mencoes';
    public $timestamps = false;

    public function getMencao($nota){
        $mencoes = $this->get();
        foreach($mencoes as $item){
            if($nota>=$item->inicio && $nota<=$item->fim){
                $mencao = $item->mencao;
                break;
            }
        }
        return ($mencao)??'I';
    }

}

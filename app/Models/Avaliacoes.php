<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avaliacoes extends Model
{
    protected $table = 'avaliacoes';
    public $timestamps = false;

    public function disciplinas(){
        return $this->belongsTo('App\Models\Disciplinas');
    }

    public function avaliacoesMostra(){
        return $this->hasMany('App\Models\AvaliacoesMostra');
    }

    public function avaliacoesMostrasRespostas(){
        return $this->hasMany('App\Models\AvaliacoesMostrasRespostas');
    }

    public function getNota($gbo, $type='') {
        
        /*
            $type PODE SER

            numeral
            ceil
            floor

        */

        $gbm = $this->gbm;
        $nota = $gbo*10/$gbm;
        if($type==''){
            $nota = number_format($nota, 3, ',', '');
        } else if(is_int($type)){
            $nota = number_format($gbo*10/$gbm, $type, ',', '');
        } else if($type=='ceil') {
            $nota = ceil($nota);
        } else if($type=='floor') {
            $nota = floor($nota);
        }

        return $nota;
    }

    public function getNotaByNota($nota, $separador=',') {

        $nota = number_format($nota, 3, '.', '');        
        
        $nota = str_replace('.', $separador, $nota);
        
        return $nota;

    }
    
    public function pronto(){
        return $this->belongsTo('App\Models\AvaliacoesProntoFaltasStatus', 'id', 'avaliacao_id');
    }
}

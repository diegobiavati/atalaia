<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Areas extends Model
{
    protected $table = 'areas';
    public $timestamps = false;

    public static function retornaAreasSisPB(){
        $retorno = Array();

        $retorno[0]['area'] = 'Não Especificada';
        $retorno[0]['cod_no_atalaia'] = 5;

        $retorno[1]['area'] = 'Geral/Aviação';
        $retorno[1]['cod_no_atalaia'] = 1;

        $retorno[2]['area'] = 'Aviação';
        $retorno[2]['cod_no_atalaia'] = 4;

        $retorno[3]['area'] = 'Música';
        $retorno[3]['cod_no_atalaia'] = 3;

        $retorno[4]['area'] = 'Saúde';
        $retorno[4]['cod_no_atalaia'] = 2;

        return $retorno;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OMCT extends Model
{
    protected $table = 'omcts';
    public $timestamps = false;
    
     function operadores(){
        return $this->hasMany('App\Models\Operadores', 'omcts_id', 'id');
    }  

    public static function retornaOmctsSisPB(){
        $retorno = Array();

        $retorno[0]['om_sigla'] = 'NE';
        $retorno[0]['cod_no_atalaia'] = 99;

        $retorno[1]['om_sigla'] = '1º GAAAe';
        $retorno[1]['cod_no_atalaia'] = 2;

        $retorno[2]['om_sigla'] = '10º BIL';
        $retorno[2]['cod_no_atalaia'] = 4;

        $retorno[3]['om_sigla'] = '12º GAC';
        $retorno[3]['cod_no_atalaia'] = 3;

        $retorno[4]['om_sigla'] = '14º GAC';
        $retorno[4]['cod_no_atalaia'] = 5;

        $retorno[5]['om_sigla'] = '20º RCB';
        $retorno[5]['cod_no_atalaia'] = 11;
        
        $retorno[6]['om_sigla'] = '51º BIS';
        $retorno[6]['cod_no_atalaia'] = 0;

        $retorno[7]['om_sigla'] = '6º RCB';
        $retorno[7]['cod_no_atalaia'] = 7;

        $retorno[8]['om_sigla'] = '41º BI Mtz';
        $retorno[8]['cod_no_atalaia'] = 6;

        $retorno[9]['om_sigla'] = '23º BC';
        $retorno[9]['cod_no_atalaia'] = 10;

        $retorno[10]['om_sigla'] = '23º BI';
        $retorno[10]['cod_no_atalaia'] = 8;

        $retorno[11]['om_sigla'] = '4º BPE';
        $retorno[11]['cod_no_atalaia'] = 0;

        $retorno[12]['om_sigla'] = '4º GACL';
        $retorno[12]['cod_no_atalaia'] = 13;

        $retorno[13]['om_sigla'] = '13º RC Mec';
        $retorno[13]['cod_no_atalaia'] = 12;

        $retorno[14]['om_sigla'] = '16º BI Mtz';
        $retorno[14]['cod_no_atalaia'] = 9;

        $retorno[15]['om_sigla'] = '4º BE Cmb';
        $retorno[15]['cod_no_atalaia'] = 14;

        $retorno[99]['om_sigla'] = 'ESA';
        $retorno[99]['cod_no_atalaia'] = 1;
        return $retorno;
    }
}

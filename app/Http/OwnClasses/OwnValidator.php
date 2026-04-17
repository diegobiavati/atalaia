<?php

namespace App\Http\OwnClasses;

use App\Http\OwnClasses\ClassLog;

class OwnValidator
{
    static function ValidarPW($senha)
    {
        $senhasProibidas = array('cavalaria', 'furacão', 'furacao', 'osorio', 'osório', 'rondon', 'mallet', 'caxias', 'CAVALARIA', 'FURACÃO', 'FURACAO', 'OSORIO', 'OSÓRIO', 'RONDON', 'MALLET', 'CAXIAS', 'TESTE', 'teste', 'brasil', 'BRASIL');
        $sequenciasProibidas = array('123', '321', 'abc', '234', '432', '987', 'qwe', 'qaz', 'zaq', 'wsx', 'xsw', 'www');

        $key = array_search($senha, $senhasProibidas);

        if (strlen($senha) < 6) {
            $msgProib = 'A senha deve possuir no mínimo 6 caracteres';
        } elseif ($key !== false) {
            $msgProib = 'Senha ' . $senhasProibidas[$key] . ' de fácil dedução.';
        } else {
            foreach ($sequenciasProibidas as $string) {
                if (stristr($senha, $string)) {
                    $msgProib = 'A senha não deverá conter sequências como ' . $string . '.';
                    break;
                }
            }
        }

        // FINALIZANDO, VERIFICO SE $msgProib FOI CRIADA
        return (isset($msgProib)) ? $msgProib : 'ok';
    }

    static function ValidarEmail($mail)
    {
        if (preg_match("/^([[:alnum:]_.-]){3,}@([[:lower:][:digit:]_.-]{3,})(\.[[:lower:]]{2,3})(\.[[:lower:]]{2})?$/", $mail)) {
            return true;
        } else {
            return false;
        }
    }
}

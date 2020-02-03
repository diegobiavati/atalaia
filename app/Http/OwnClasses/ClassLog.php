<?php

/*

    Classe para criação de Logs com ação dos usuários

*/

namespace App\Http\OwnClasses;

class ClassLog
{

    //protected $root_path = '/atalaia/storage/logs/logsistema/'; // certifique-se que este diretório exista
    protected $root_path = '/app/public/logs/logsistema/'; // certifique-se que este diretório exista
    /* 
        EM CONDIÇÕES NORMAIS, A PASTA atalaia acima, deverá ser  omitida, ficando assim /storage/logs/logsistema/ 
        
    */
    public $ip;

    public function getLogFileName()
    {

        //Solução improvisada para rodar a schedule e conter logs...
        /*$_SERVER['DOCUMENT_ROOT'] = (!empty($_SERVER['DOCUMENT_ROOT'])) ? $_SERVER['DOCUMENT_ROOT'] : str_replace('\\', '/', getcwd()) . '/public/';

        $document_root = explode('/', $_SERVER['DOCUMENT_ROOT']);
        $path_array = array_pop($document_root);
        $path = implode('/', $document_root) . $this->root_path;*/

        $path = storage_path().$this->root_path;
        
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        foreach (scandir($path) as $file) {
            if ($file != '.' && $file != '..') {
                $fullname[] = $path . $file;
                $filename[] = $file;
                $filesize[] = filesize($path . $file) / 1024000;
                $date[] = strtotime(date("Y-m-d H:i:s.", filectime($path . $file)));
            }
        }

        if (isset($fullname)) {

            // MAIOR VALOR DA ARRAY

            $max_date = max($date);

            // BUSCANDO O INDICE DO VALOR MÁXIMO (max($date))

            $key = array_search($max_date, $date);

            // RETORNA O NOME DO ARQUIVO EM QUE O LOG DEVE SER GRAVADO

            if ($filesize[$key] > 1 || !is_writable($fullname[$key])) {
                $log_file_name = $path . date('YmdHis') . '.log';
            } else {
                $log_file_name = $fullname[$key];
            }

            if (isset($log_file_name)) {
                return $log_file_name;
            } else {
                return false;
            }
        } else {
            $log_file_name = $path . date('YmdHis') . '.log';
            return $log_file_name;
        }
    }

    public function RegistrarLog($log_data, $usr = 'GEST')
    {
        $log = date('Y-m-d H:i:s') . ' | ' . $this->ip . ' | ' . $usr . ' | ' . $log_data;

        if ($this->getLogFileName()) {
            $log_file_name = $this->getLogFileName();
            file_put_contents($log_file_name, $log . "\n", FILE_APPEND);
        } else {
            return false;
        }
    }
}

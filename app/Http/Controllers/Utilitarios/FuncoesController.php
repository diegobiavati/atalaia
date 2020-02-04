<?php

namespace App\Http\Controllers\Utilitarios;

use App\Models\AnoFormacao;
use DateTime;
use Doctrine\DBAL\Schema\View;
use Throwable;

class FuncoesController
{

    public static function formatDateBrtoEn($date)
    {

        if (!isset($date)) {
            return null;
        }

        @list($day, $month, $year) = @explode('/', $date);

        return $year . '-' . $month . '-' . $day;
    }

    public static function formatDateEntoBr($date)
    {

        if (!isset($date)) {
            return null;
        }

        @list($year, $month, $day) = @explode('-', $date);

        return $day . '/' . $month . '/' . $year;
    }

    public static function validDate($date)
    {
        if (!preg_match("/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $date)) {
            return false;
        }

        @list($year, $month, $day) = @explode('-', $date); // Padrão Americano yyyy/mm/dd

        // verifica se a data é válida!
        // 1 = true (válida)
        // 0 = false (inválida)
        $res = checkdate($month, $day, $year);

        if ($res == 1) {
            return true;
        } else {
            return false;
        }
    }

    public static function LimpaPastaTemp()
    {

        $caminho = storage_path() . '/app/public/temp/';
        $dir = opendir($caminho);

        while ($nome = readdir($dir)) {
            if ($nome != '.' && $nome != '..') {
                if (date('d-m-Y', filemtime($caminho . $nome)) != date('d-m-Y')) {
                    unlink($caminho . $nome);
                }
            }
        }
        closedir($dir);
    }

    public static function validaSessao()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        return null;
    }

    public static function retornaBotaoAnoFormacao(){
        $anos_formacao = AnoFormacao::orderBy('formacao', 'desc')->get();
        if(count($anos_formacao)>0){
            $response_ano_formacao[] = '<div style="text-align: center; margin-top: 52px;"><div class="btn-group btn-group-toggle" data-toggle="buttons">';
            $i=0;
            foreach($anos_formacao as $ano_formacao){
                $status_active_label = ($i==0)?'active':'';
                $status_checked_input = ($i==0)?'checked':'';
                $response_ano_formacao[] = '<label class="btn btn-secondary '.$status_active_label.'" style="text-align: center;" ><input type="radio" name="ano_formacao" value="'.$ano_formacao->id.'" '.$status_checked_input.' /> '.$ano_formacao->formacao.'</label>';
                $i++;
            }
            $response_ano_formacao[] = '</div></div>';
        } else {
            $response_ano_formacao[] = '<div style="text-align: center;">NÃO HÁ ANO DE FORMAÇÃO CADASTRADO NO SISTEMA</div>';
        }
        
        return implode('', $response_ano_formacao);
    }

    public static function retornaIdadePelaDataNascimento($dataNascimento){//Formato Y-m-d

        $date = new DateTime($dataNascimento);
        $interval = $date->diff(new DateTime(date('Y-m-d')));
        return $interval->format('%Y');
    }
}
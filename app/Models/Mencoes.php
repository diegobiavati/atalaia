<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mencoes extends Model
{
    protected $table = 'mencoes';
    public $timestamps = false;

    private $_frequencia = null;

    public function getMencao($nota)
    {
        $mencoes = $this->get();
        foreach ($mencoes as $item) {
            if ($nota >= $item->inicio && $nota <= $item->fim) {
                $mencao = $item->mencao;
                break;
            }
        }
        return ($mencao) ?? 'I';
    }

    public static function getMencaoV2($nota)
    {
        $mencoes = Mencoes::get();
        foreach ($mencoes as $item) {
            if ($nota >= $item->inicio && $nota <= $item->fim) {
                return $item;
            }
        }
        return 'I';
    }

    public function geraFrequencia()
    {

        $this->_frequencia = collect();
        switch ($this->mencao) {
            case 'I':
                $this->_frequencia->push(['0,0 a 0,9' => ['freq' => 0, '%' => 0], '1,0 a 1,9' => ['freq' => 0, '%' => 0], '2,0 a 2,9' => ['freq' => 0, '%' => 0], '3,0 a 3,9' => ['freq' => 0, '%' => 0], '4,0 a 4,9' => ['freq' => 0, '%' => 0]]);
                break;
            case 'R':
                $this->_frequencia->push(['5,0 a 5,9' => ['freq' => 0, '%' => 0], '6,0 a 6,9' => ['freq' => 0, '%' => 0]]);
                break;
            case 'B':
                $this->_frequencia->push(['7,0 a 7,9' => ['freq' => 0, '%' => 0]]);
                break;
            case 'MB':
                $this->_frequencia->push(['8,0 a 8,9' => ['freq' => 0, '%' => 0], '9,0 a 9,4' => ['freq' => 0, '%' => 0]]);
                break;
            case 'E':
                $this->_frequencia->push(['9,5 a 10,0' => ['freq' => 0, '%' => 0]]);
                break;
        }
    }

    public function getFrequencia()
    {
        return $this->_frequencia;
    }

    public function setFrequencia($nota, $realizaram)
    {
        $array = $this->_frequencia->get(0);
        $indice = null;
        foreach ($array as $item => $valor) {
            if ($nota >= 0.0 && $nota <= 0.999) {
                $indice = '0,0 a 0,9';
            } elseif ($nota >= 1.0 && $nota <= 1.999) {
                $indice = '1,0 a 1,9';
            } elseif ($nota >= 2.0 && $nota <= 2.999) {
                $indice = '2,0 a 2,9';
            } elseif ($nota >= 3.0 && $nota <= 3.999) {
                $indice = '3,0 a 3,9';
            } elseif ($nota >= 4.0 && $nota <= 4.999) {
                $indice = '4,0 a 4,9';
            } elseif ($nota >= 5.0 && $nota <= 5.999) {
                $indice = '5,0 a 5,9';
            } elseif ($nota >= 6.0 && $nota <= 6.999) {
                $indice = '6,0 a 6,9';
            } elseif ($nota >= 7.0 && $nota <= 7.999) {
                $indice = '7,0 a 7,9';
            } elseif ($nota >= 8.0 && $nota <= 8.999) {
                $indice = '8,0 a 8,9';
            } elseif ($nota >= 9.0 && $nota <= 9.499) {
                $indice = '9,0 a 9,4';
            } elseif ($nota >= 9.5 && $nota <= 10.0) {
                $indice = '9,5 a 10,0';
            }

            if ($item == $indice) {
                $array[$item]['freq']++;
                $array[$item]['%'] = ($array[$item]['freq'] / $realizaram) * 100;
            }
        }

        $this->_frequencia->put(0, $array);
    }
}

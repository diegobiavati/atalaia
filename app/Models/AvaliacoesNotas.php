<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvaliacoesNotas extends Model
{
    protected $table = 'avaliacoes_notas';
    public $timestamps = false;

    public function avaliacao(){
        return $this->hasOne('App\Models\Avaliacoes', 'id', 'avaliacao_id');
    }
    
    public function aluno(){
        return $this->hasOne('App\Models\Alunos', 'id', 'alunos_id');
    }

    public function getNota() {
        
        $gbo = $this->gbo;
        $gbm = $this->avaliacao->gbm;
        $nota = number_format($gbo*10/$gbm, 3, '.', '');

        return $nota;
    }      

}

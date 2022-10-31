<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvaliacoesNotas extends Model
{
    protected $table = 'avaliacoes_notas';
    public $timestamps = false;

    protected $fillable = [
        'alunos_id', 'avaliacao_id', 'gbo', 'nota_tfm', 'suficiencia_abdominal'
    ];

    public function avaliacao()
    {
        return $this->hasOne('App\Models\Avaliacoes', 'id', 'avaliacao_id');
    }

    public function aluno()
    {
        return $this->hasOne('App\Models\Alunos', 'id', 'alunos_id');
    }

    public function getNota()
    {

        //Verifica se é TFM
        if ($this->avaliacao->disciplinas->tfm == 'S') {
            //Verifica se é Abdominal
            if ($this->avaliacao->tfm_abdominal == 'S') {
                //S = Suficiente; NS = Insuficiente
                $nota = $this->suficiencia_abdominal;
                return $nota;
            } else {
                if($this->aluno->atleta_marexaer == 'S'){

                }
                dd($this->aluno->atleta_marexaer);
                $nota = number_format($this->nota_tfm, 3, '.', '');
                return $nota;
            }
        } else {
            $gbo = $this->gbo;
            $gbm = $this->avaliacao->gbm;
            return ($gbo > 0) ? $nota = number_format($gbo * 10 / $gbm, 3, '.', '') : 0;
        }
    }
}

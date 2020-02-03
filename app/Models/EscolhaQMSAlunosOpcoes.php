<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Alunos;
use App\Models\EscolhaQMS;

class EscolhaQMSAlunosOpcoes extends Model
{
    protected $table = 'escolha_qms_alunos_opcoes';
    public $timestamps = false;

    public function aluno()
    {
        return $this->hasOne(Alunos::class, 'id', 'aluno_id');
    }

    public function escolhaQMS()
    {
        return $this->hasOne(EscolhaQMS::class, 'id', 'escolha_qms_id');
    }
}

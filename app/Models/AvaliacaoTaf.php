<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Alunos;
class AvaliacaoTaf extends Model
{
    protected $table = 'avaliacoes_taf';
    public $timestamps = false;

    protected $fillable = [
        'aluno_id',
        'corrida_nota',
        'flexao_braco_nota',
        'flexao_barra_nota',
        'abdominal_suficiencia'
    ];

    public function aluno()
    {
        return $this->hasOne(Alunos::class, 'id', 'aluno_id');
    }

}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagemAluno extends Model
{
    protected $table = 'imagem_aluno';

    public function aluno()
    {
        return $this->hasOne(Alunos::class, 'id', 'id_aluno');
    }
}

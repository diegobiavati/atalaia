<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramAlunoAuth extends Model
{
    protected $table = 'telegram_aluno_auth';
    public $timestamps = false;

    public function aluno(){
        return $this->belongsTo('App\Models\Alunos', 'aluno_id', 'id');
    }    
}

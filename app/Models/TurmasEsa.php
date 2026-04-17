<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurmasEsa extends Model
{
    protected $connection = 'mysql';
    protected $table = 'turmas_esa';
    public $timestamps = false;

    public function alunos()
    {
        return $this->hasMany('App\Models\Alunos', 'turma_esa_id');
    }

    public function qms()
    {
        return $this->belongsTo('App\Models\QMS', 'qms_id');
    }
}

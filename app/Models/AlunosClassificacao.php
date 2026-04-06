<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlunosClassificacao extends Model
{
    protected $table = 'alunos_classificacao';
    public $timestamps = true;

    public function aluno()
    {
        return $this->hasOne(Alunos::class, 'id', 'aluno_id');
        //return $this->belongsTo('App\Models\Alunos', 'id', 'aluno_id');
                        
    }

    public function anoFormacao()
    {
        return $this->hasOne(AnoFormacao::class, 'id', 'ano_formacao_id');                   
    }       

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!empty($model->data_demonstrativo)) {
                $model->data_demonstrativo_json = null;
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('data_demonstrativo')) {
                $model->data_demonstrativo_json = null;
            }
        });
    }
}

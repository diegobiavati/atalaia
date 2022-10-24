<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsaDisciplinas extends Model
{
    protected $table = 'esa_disciplinas';
    protected $fillable = ['id_qms', 'nome_disciplina', 'nome_disciplina_abrev', 'carga_horaria', 'tipo_disciplina', 'tfm'];
    
    private $_tipos_disciplinas = null;


    public function __construct()
    {
        $this->_tipos_disciplinas = collect([(object)['id' => 'C', 'descricao' => 'Comum'], (object)['id' => 'E', 'descricao' => 'Específicas']]);
    }

    public function qms()
    {
        return $this->belongsTo('App\Models\QMS', 'id_qms', 'id');
    }

    public function esaAvaliacoes()
    {
        return $this->hasMany('App\Models\EsaAvaliacoes', 'id', 'id_esa_disciplinas');
    }

    public function getTipoDisciplinas()
    {
        switch ($this->tipo_disciplina) {
            case 'C':
                return '(Comum)';
            case 'E':
                return '(Específicas)';
        }
    }

    public function getDescricaoTFM()
    {
        switch ($this->tfm) {
            case 'N':
                return null;
            case 'S':
                return '<br>(TFM)';
        }
    }

    public function getTodosTiposDisciplinas()
    {
        return $this->_tipos_disciplinas;
    }
}

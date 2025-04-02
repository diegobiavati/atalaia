<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsaDisciplinas extends Model
{
    protected $connection = 'mysql_ssaa';
    protected $table = 'esa_disciplinas';
    protected $fillable = ['id_qms', 'nome_disciplina', 'nome_disciplina_abrev', 'carga_horaria', 'tipo_disciplina', 'tfm'];

    private $_tipos_disciplinas = null;

    public function __construct()
    {
        $this->_tipos_disciplinas = collect([(object)['id' => 'C', 'descricao' => 'Comum'], (object)['id' => 'E', 'descricao' => 'Específicas'], (object)['id' => 'A', 'descricao' => 'Acadêmicas']]);
    }

    public function qms()
    {
        return $this->belongsTo('App\Models\QMS', 'id_qms', 'id');
    }

    public function esaAvaliacoes()
    {
        return $this->hasMany('App\Models\EsaAvaliacoes', 'id_esa_disciplinas', 'id');
    }

    public function getTipoDisciplinas()
    {
        switch ($this->tipo_disciplina) {
            case 'C':
                return '(Comum)';
            case 'E':
                return '(Específicas)';
            case 'A':
                return '(Acadêmicas)';
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

    public function esaAvaliacoesDemonstrativos()
    {
        return $this->hasMany(EsaAvaliacoesDemonstrativo::class, 'id_esa_disciplinas', 'id');
    }
}

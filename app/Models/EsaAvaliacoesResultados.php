<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class EsaAvaliacoesResultados extends Model
{
    protected $connection = 'mysql_ssaa';
    protected $table = 'esa_avaliacoes_resultados';
    protected $primaryKey = ['id_esa_avaliacoes', 'id_aluno'];
    public $incrementing = false;
    protected $fillable = ['id_esa_avaliacoes', 'id_aluno', 'nota', 'gbo_aluno', 'gbo_ssaa', 'id_operador'];

    public function esaAvaliacoes(){
        return $this->belongsTo('App\Models\EsaAvaliacoes', 'id_esa_avaliacoes', 'id');
    }

    public function operadores(){
        return ($this->belongsTo('App\Models\Operadores', 'id_operador', 'id')) ?? 'Não informado';
    }

    public function aluno(){
        return ($this->belongsTo('App\Models\Alunos', 'id_aluno', 'id')) ?? 'Não informado';
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        $keys = $this->getKeyName();
        if(!is_array($keys)){
            return parent::setKeysForSaveQuery($query);
        }

        foreach($keys as $keyName){
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if(is_null($keyName)){
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }
}

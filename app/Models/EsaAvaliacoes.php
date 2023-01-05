<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsaAvaliacoes extends Model
{
    protected $connection = 'mysql_ssaa';
    protected $table = 'esa_avaliacoes';
    protected $fillable = ['id_esa_disciplinas', 'nome_avaliacao', 'tipo_avaliacao', 'local_aplicacao', 'chamada', 'peso', 'proposta', 'realizacao', 'devolucao'];

    private $_tipo_avaliacoes = null;
    private $_chamadas = null;
    private $_avaliacoes = null;

    public function __construct()
    {
        $this->_tipo_avaliacoes = collect([(object)['id' => 'N', 'descricao' => 'Nota'], (object)['id' => 'C', 'descricao' => 'Conceitual']]);
        $this->_chamadas = collect([(object)['id' => '1', 'descricao' => '1ª Chamada'], (object)['id' => '2', 'descricao' => '2ª Chamada']]);
        $this->_avaliacoes = collect([
              (object)['id' => 'AA', 'descricao' => 'Acompanhamento']
            , (object)['id' => 'AF', 'descricao' => 'Formativa']
            , (object)['id' => 'AC', 'descricao' => 'Controle']
            , (object)['id' => 'AR', 'descricao' => 'Recuperação']
            //, (object)['id' => 'AI', 'descricao' => 'Interdisciplinar']
        ]);
    }

    public function esadisciplinas(){
        return $this->belongsTo('App\Models\EsaDisciplinas', 'id_esa_disciplinas', 'id');
    } 

    public function esaAvaliacoesRap(){
        return $this->hasMany('App\Models\EsaAvaliacoesRap', 'id_esa_avaliacoes', 'id');
    }

    public function getTodosTiposAvaliacoes(){
        return $this->_tipo_avaliacoes;
    }

    public function getTodasChamadas(){
        return $this->_chamadas;
    }

    public function getTodasAvaliacoes(){
        return $this->_avaliacoes;
    }

    public function getDescricao(){
        return $this->getTodasAvaliacoes()->first(function ($value, $key) {
            return $value->id == $this->nome_avaliacao;
        })->descricao;
    }
}

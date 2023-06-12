<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class EsaAvaliacoesIndices extends Model
{
    protected $connection = 'mysql_ssaa';
    protected $table = 'esa_avaliacoes_indice';
    
    protected $fillable = ['id_esa_avaliacoes', 'nr_item', 'score_total', 'assunto_basico', 'id_operador'];

    public function esaAvaliacoes(){
        return $this->belongsTo('App\Models\EsaAvaliacoes', 'id_esa_avaliacoes', 'id');
    }

    public function operadores(){
        return ($this->belongsTo('App\Models\Operadores', 'id_operador', 'id')) ?? 'Não informado';
    }

    public function esaAvaliacoesGbo(){
        return $this->hasMany('App\Models\EsaAvaliacoesGbo', 'id_esa_avaliacoes_indice', 'id');
    }

    public function getOrdenadoPorItem($id_esa_avaliacoes){
        return $this->where([['id_esa_avaliacoes', '=', $id_esa_avaliacoes]])
            ->orderByRaw('cast(esa_avaliacoes_indice.nr_item AS UNSIGNED)');
    }

    public function getAlunoIndicesItens($id_esa_avaliacoes, $id_aluno){
        return $this->getOrdenadoPorItem($id_esa_avaliacoes)->leftJoin('esa_avaliacoes_gbo', function($join) use($id_aluno) {
            $join->on('esa_avaliacoes_indice.id', '=', 'esa_avaliacoes_gbo.id_esa_avaliacoes_indice');
            $join->on('esa_avaliacoes_gbo.id_aluno', '=', DB::raw($id_aluno));
        });
    }
}

<?php

namespace App\Models;

use App\Http\Controllers\Utilitarios\FuncoesController;
use Illuminate\Database\Eloquent\Model;
use App\Models\AvaliacoesNotas;

class Disciplinas extends Model
{
    protected $table = 'disciplinas';
    public $timestamps = false;

    public function ano_formacao(){
        return $this->belongsTo('App\Models\AnoFormacao');
    }

/*     public function avaliacoes(){
        return $this->hasMany('App\Models\Avaliacoes');
    } */

    public function avaliacoes(){
        return $this->hasMany('App\Models\Avaliacoes', 'disciplinas_id', 'id');
    }

    public function avaliacao(){
        return $this->hasOne('App\Models\Avaliacoes', 'disciplinas_id', 'id');
    }


    public function getNotaDisciplina(){

        $peso_disciplina = $this->peso;
        
        foreach($this->avaliacoes as $avaliacao){
            $avaliacoesID[] = $avaliacao->id;
        }

        return $avaliacoesID;

    }

    public function getNotasAluno($aluno_id){      
        
        foreach($this->avaliacoes as $avaliacao){
            $avaliacoesID[] = $avaliacao->id;
            if($avaliacao->chamada==1){
                $disciplina_razao[] = 1;
            }
        }

        $razao = (isset($disciplina_razao))?array_sum($disciplina_razao):1;

        if(isset($avaliacoesID)){

            $notas_aluno = AvaliacoesNotas::whereIn('avaliacao_id', $avaliacoesID)->where('alunos_id', $aluno_id)->get();

            foreach($notas_aluno as $item){
                if($item->avaliacao->avaliacao_recuperacao==0){
                    $notas[$item->avaliacao->nome_abrev] = $item->getNota();
                } else {
                    $nota_recuperacao = $item->getNota();
                }
            }    
        }    

        if(isset($notas)){
            $avaliacoes = $notas;
            $nd = number_format(array_sum($notas)/$razao, 3, '.', '');
        } else {
            $nd = null;
            $avaliacoes = array(null);
        }

        if(isset($nota_recuperacao)){
            $nd = (number_format($nota_recuperacao, 3, '.', '')>5)?5:number_format($nota_recuperacao, 3, '.', '');
        } else {
            $nota_recuperacao = null;
        }

        $data = array(
            "alunoID" => $aluno_id,
            "disciplinaID" => $this->id,
            "disciplinaNome" => $this->nome_disciplina,
            "disciplinaNomeAbrev" => $this->nome_disciplina_abrev,
            "avaliacoes" => $avaliacoes,
            "avaliacoesRecperacao" => $nota_recuperacao,
            "ND" => $nd
        );

        return $data;
    }

    //Modificado para trazer as notas já calculadas igual no demonstrativo de notas...
    public function getNotasAluno2023($aluno_id){    
        
        $notas_aluno = unserialize(AlunosClassificacao::where([['aluno_id', '=', $aluno_id]])->first()->data_demonstrativo);

        foreach($notas_aluno as $key => $item){
            if(is_numeric($key) && $item['disciplina_id'] == $this->id){
                $nd = $item['media'];
                foreach($item['avaliacoes'] as $k => $avaliacao){
                    if($k == 'ACR'){
                        $nota_recuperacao = $avaliacao;  
                    }else if(key_exists($avaliacao->indice_notas, $item['notas'])){
                        $notas[$avaliacao->nome_abrev] = $item['notas'][$avaliacao->indice_notas];
                    }
                }
            }
        }    
        
        if(isset($notas)){
            $avaliacoes = $notas;
            $nd = number_format($nd, 3, '.', '');
        } else {
            $nd = null;
            $avaliacoes = array(null);
        }
        
        if(isset($nota_recuperacao)){
            $nd = (number_format($nota_recuperacao, 3, '.', '')>5)?5:number_format($nota_recuperacao, 3, '.', '');
        } else {
            $nota_recuperacao = null;
        }

        $data = array(
            "alunoID" => $aluno_id,
            "disciplinaID" => $this->id,
            "disciplinaNome" => $this->nome_disciplina,
            "disciplinaNomeAbrev" => $this->nome_disciplina_abrev,
            "avaliacoes" => $avaliacoes,
            "avaliacoesRecperacao" => $nota_recuperacao,
            "ND" => $nd
        );

        return $data;
    }
      
}

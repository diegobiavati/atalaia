<?php

namespace App\Models;

use App\Imports\AlunosDependenteImport;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;

class AlunosDependente extends Model
{
    protected $table = 'alunos_dependentes';

    protected $fillable = [
        'id',
        'id_dependente',//Para pegar o ID do Dependente caso exista
        'id_aluno',
        'id_parentesco',
        'dep_nome_completo',
        'dep_data_nascimento',
        'dep_naturalidade',
        'dep_endereco',
        'dep_id_profissao',
        'dep_id_escolaridade',
        'dep_trabalho_ativo',
        'dep_trabalho_funcao',
        'dep_bi_publicacao'
    ];

    public function alunos()
    {
        return $this->belongsTo(Alunos::class, 'id');
    }

    public function parentesco()
    {
        return $this->belongsTo('App\Models\Parentesco', 'id_parentesco', 'id');
    }

    public function import($nameFile)
    {
        Excel::import(new AlunosDependenteImport(), $nameFile);

        return redirect('/')->with('success', 'All good!');
    }
}

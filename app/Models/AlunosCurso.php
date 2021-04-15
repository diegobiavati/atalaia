<?php

namespace App\Models;

use App\Imports\AlunosCursoImport;
use Illuminate\Database\Eloquent\Model;

class AlunosCurso extends Model
{
    protected $table = 'alunos_curso';
    public $timestamps = true;

    protected $fillable = [
        'id_aluno',
        'periodo_cadastro',
        'senha',
        'nota_cacfs',
        'id_area',
        //'id_qmsnaipe',
        'id_qms',
        'id_pb_omct'
    ];

    public function alunos()
    {
        return $this->hasOne(Alunos::class, 'id', 'id_aluno');
    }

    public function import($nameFile)
    {
        try { 
            $import = new AlunosCursoImport();
            $import->import($nameFile);

            $retorno['status'] = 'ok';
            $retorno['response'] = 'Alunos Curso Inseridos';
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.

                $retorno['status'] = 'error';
                $retorno['error'] = '<b>Na linha ' . $failure->row().'</b>';

                foreach($failure->errors() as $error){
                    $retorno['error'] = $retorno['error'].' '.$error.'<BR>';
                }
            }

        }
       
        return response()->json($retorno);
    }
}

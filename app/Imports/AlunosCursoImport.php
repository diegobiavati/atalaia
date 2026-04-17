<?php

namespace App\Imports;

use App\Models\Alunos;
use App\Models\AlunosCurso;
use App\Models\Areas;
use App\Models\OMCT;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AlunosCursoImport implements ToModel, WithHeadingRow, WithBatchInserts, WithValidation
{
    use Importable;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        $aluno = Alunos::whereHas('ano_formacao', function ($query) use ($row) {
            $query->where('ano_per_basico', $row['ano']);
            //dd($row['ano'], $row['inscricao']);
        })->where('al_inscricao', $row['inscricao'])->get()->first();

        if (isset($aluno)) {
            $alunosCurso = AlunosCurso::create([
                'id_aluno' => $aluno->id,
                //'periodo_cadastro' => $row['periodo'],
                'senha' => $row['senha'],
                'nota_cacfs' => (($row['sca_notadou'] == 'NULL') ? 0 : $row['sca_notadou'])
                //'id_area' => Areas::retornaAreasSisPB()[$row['area']]['cod_no_atalaia'],
                //'id_pb_omct' => OMCT::retornaOmctsSisPB()[$row['sca_omct']]['cod_no_atalaia']
            ]);

            if (isset($alunosCurso)) {
                //$aluno->omcts_id = OMCT::retornaOmctsConcurso()[$row['sca_omct']]['cod_no_atalaia'];
                //$aluno->area_id = Areas::retornaAreasConcurso()[$row['area']]['cod_no_atalaia'];
                $aluno->classif_cacfs = (($row['sca_classfinal'] == 'NULL') ? 0 : $row['sca_classfinal']);
                $aluno->sexo = $row['sexo'];
                $aluno->save();
            }
        }
    }

    public function batchSize(): int
    {
        return 250;
    }

    public function rules(): array
    {

        return [
            '*.inscricao' => function ($attribute, $value, $onFailure) {

                if (
                    count(AlunosCurso::whereHas('alunos', function ($query) use ($value) {
                        $query->where('al_inscricao', trim($value));
                    })->get()) > 0
                ) {
                    $onFailure('Inscrição Já se Encontra Registrada na Base de Dados.');
                }
            }
        ];
    }
}

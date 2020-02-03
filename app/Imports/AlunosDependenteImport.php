<?php

namespace App\Imports;

use App\Models\AlunosDependente;
use App\Models\Alunos;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AlunosDependenteImport implements ToModel, WithHeadingRow, WithBatchInserts
{
    use Importable;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        $param = $row;
        $aluno = Alunos::where(['alunos.numero' => $row['al_numero']])
            ->whereHas('ano_formacao', function ($query) use ($param) {
                $query->where('formacao', '=', $param['al_anoformacao']);
            })->get();

        if (isset($aluno[0]->id)) {
            $alunoDependente = AlunosDependente::firstOrCreate([
                'id_aluno' => $aluno[0]->id,
                'id_parentesco' => $row['id_parentesco'],
                'dep_nome_completo' => $row['dep_nomecompleto'],
                'dep_data_nascimento' => ((isset($row['dep_datanascimento']) && ($row['dep_datanascimento'] != 'null')) ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['dep_datanascimento'])) : null),
                'dep_naturalidade' => ((isset($row['dep_naturalidade']) && ($row['dep_naturalidade'] != 'null')) ? $row['dep_naturalidade'] : null),
                'dep_endereco' => ((isset($row['dep_endereco']) && ($row['dep_endereco'] != 'null')) ? $row['dep_endereco'] : null),
                'dep_id_profissao' => ((isset($row['dep_cod_profissao']) && ($row['dep_cod_profissao'] != 'null')) ? $row['dep_cod_profissao'] : null),
                'dep_id_escolaridade' => ((isset($row['dep_cod_escolaridade']) && ($row['dep_cod_escolaridade'] != 'null')) ? $row['dep_cod_escolaridade'] : null),
                'dep_trabalho_ativo' => ((isset($row['dep_trabalho_ativo']) && ($row['dep_trabalho_ativo'] != 'null')) ? (($row['dep_trabalho_ativo'] == 'Selecionado') ? 'S': 'N') : null),
                'dep_trabalho_funcao' => ((isset($row['dep_trabalho_funcao']) && ($row['dep_trabalho_funcao'] != 'null')) ? $row['dep_trabalho_funcao'] : null),
                'dep_bi_publicacao' => ((isset($row['dep_bi_publicacao']) && ($row['dep_bi_publicacao'] != 'null')) ? $row['dep_bi_publicacao'] : null)
            ]);
        }

        
    }

    public function batchSize(): int
    {
        return 2000;
    }
}

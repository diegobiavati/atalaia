<?php

namespace App\Imports;

use App\Models\Alunos;
use App\Models\AnoFormacao;
use App\Models\Uf;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AlunosImportInsert implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts
{
    use Importable;
    protected $anoFormacao;
    protected $ufs = array();

    function __construct()
    {

        ini_set('memory_limit', '-1');

        //ini_set('display_errors', true); 
        //error_reporting(E_ALL);

        foreach (AnoFormacao::all() as $formacao) {
            $this->anoFormacao[$formacao['formacao']] = $formacao;
        }

        foreach (Uf::all() as $uf) {
            $this->ufs[$uf['uf_sigla']] = $uf;
        }
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        
        $aluno = Alunos::create([
            'data_matricula' => $this->anoFormacao[$row['ano']]->id,
            'al_inscricao' => $row['inscricao'],
            'nome_completo' => $row['nome'],
            'data_nascimento' => Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_nascimento'])),
            'nasc_cidade' => $row['naturalidade_cidade'],
            'nasc_id_uf' => $this->ufs[$row['naturalidade_uf']]->id,
            'nasc_pais' => $row['naturalidade_pais'],
            'data_incorporacao' => Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_incorporacao'])),
            'id_situacao_anterior' => (($row['cod_sitanterior'] == 100) ? 1 : $row['cod_sitanterior']),
            'id_situacao_matricula' => $row['cod_sit2'],
            //'id_situacao_pb' => $row['cod_sit3'],
            //'id_situacao_atual' => $row['cod_sit4'],
            'endereco' => $row['endereco'],
            'bairro' => $row['bairro'],
            'cidade' => $row['cidade'],
            'id_uf' => $this->ufs[$row['uf']]->id,
            'cep' => $row['cep'],
            'telefone' => $row['tel_residencial'],
            'celular1' => $row['tel_comercial'],
            'celular2' => $row['celular'],
            'celular3' => $row['celular2'],
            'email' => $row['email']
        ]);
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function rules(): array
    {
        return [
            '*.ano' => function ($attribute, $value, $onFailure) {
                if (!array_key_exists((int) $value, $this->anoFormacao)) {
                    $onFailure('Ano de Formação Não Cadastrado');
                }
            },
            '*.email' => function ($attribute, $value, $onFailure) {
                if (count(Alunos::where('email', trim($value))->get()) > 0) {
                    $onFailure('E-mail Já se Encontra Cadastrado');
                }
            }
        ];
    }
}

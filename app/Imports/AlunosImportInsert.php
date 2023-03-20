<?php

namespace App\Imports;

use App\Models\Alunos;
use App\Models\AlunosCurso;
use App\Models\AnoFormacao;
use App\Models\Areas;
use App\Models\OMCT;
use App\Models\Uf;
use Carbon\Carbon;
use Exception;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException;

class AlunosImportInsert implements ToModel, WithValidation, WithBatchInserts, WithHeadingRow
{
    use Importable;
    protected $anoFormacao;
    protected $ufs = array();

    function __construct()
    {

        ini_set('memory_limit', '-1');

        ini_set('display_errors', true); 
        error_reporting(E_ALL);

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
    
        try{
            $aluno = Alunos::create([
                'data_matricula' => $row['data_matricula'],
                'al_inscricao' => utf8_encode($row['al_inscricao']),
                'nome_completo' => $row['nome_completo'],
                'data_nascimento' => Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data_nascimento'])),
                'nasc_cidade' => $row['nasc_cidade'],
                'nasc_id_uf' => (array_key_exists($row['nasc_uf'], $this->ufs) ? $this->ufs[$row['nasc_uf']]->id : 28),
                'nasc_pais' => $row['nasc_pais'],
                'omcts_id' => OMCT::retornaOmctsConcurso()[$row['omcts_id']]['cod_no_atalaia'],
                'area_id' => Areas::retornaAreasConcurso()[$row['area_id']]['cod_no_atalaia'],
                'id_situacao_anterior' => 1, //Falta Verificar
                'id_situacao_matricula' => 100, //Falta Verificar
                'endereco' => $row['endereco'].', '.$row['numero'].(isset($row['complemento']) ? ', compl '.$row['complemento'] : null),
                'bairro' => $row['bairro'],
                'cidade' => $row['cidade'],
                'id_uf' => $this->ufs[$row['uf']]->id,
                'cep' => $row['cep'],
                'telefone' => isset($row['telefone']) ? $row['telefone'] : null,
                'celular1' => $row['celular'],
                'email' => $row['email'],
                'cotista' => $row['cota'],
                'doc_cpf' => $row['cpf'],
                'doc_idt_civil' => $row['identidade'],
                'doc_idt_civil_o_exp' => $row['orgao_expedidor'],
                'sexo' => $row['sexo'],
                'nome_mae' => $row['mae'],
                'nome_pai' => $row['pai']
            ]);

            if(isset($aluno)){
                $alunosCurso = AlunosCurso::create([
                    'id_aluno' => $aluno->id,
                    'senha' => 1234,
                    'nota_cacfs' => 0
                ]);
            }
        }catch(Exception $ex){
            dd($row, $ex);
        }
            
            
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

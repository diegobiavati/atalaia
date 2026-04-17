<?php

namespace App\Exports;

use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Models\Alunos;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AlunosDiplomaExport extends DefaultValueBinder implements FromCollection, Responsable, WithHeadings, WithCustomValueBinder, WithColumnFormatting
{
    use Exportable;

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     */
    private $fileName = 'alunosDiploma.xlsx';

    /**
     * Optional Writer Type
     */
    private $writerType = Excel::XLSX;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $alunos = Alunos::retornaAlunosComQmsESA(FuncoesController::retornaAnoFormacaoAtivoQualificacao()->id);

        $collection = new Collection();
        foreach ($alunos as $aluno) {
            $collection->push([
                'CPF' => FuncoesController::removerCaracterEspeciais(trim($aluno->doc_cpf)),
                'Nome' => trim($aluno->nome_completo),
                'NomeSocial' => trim($aluno->nome_completo),
                'Nascimento' => date('d/m/Y', strtotime($aluno->data_nascimento)),
                'RG' => FuncoesController::removerCaracterEspeciais(trim($aluno->doc_idt_civil)),
                'RGUF' => null,
                'Sexo' => $aluno->sexo,
                'NaturalidadePais' => trim('Brasileiro'),
                'NaturalidadeCidade' => trim($aluno->nasc_cidade),
                'NaturalidadeUF' => (isset($aluno->uf_nascimento)) ? $aluno->uf_nascimento->uf_sigla : null,
                'EnderecoLogradouro' => trim($aluno->endereco),
                'EnderecoNumero' => null,
                'EnderecoComplemento' => null,
                'EnderecoBairro' => trim($aluno->bairro),
                'EnderecoMunicipio' => trim($aluno->cidade),
                'EnderecoUF' => (isset($aluno->uf)) ? $aluno->uf->uf_sigla : null,
                'EnderecoCEP' => FuncoesController::removerCaracterEspeciais(trim($aluno->cep))
            ]);
        }

        return $collection;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'CPF',
            'Nome',
            'NomeSocial',
            'Nascimento',
            'RG',
            'RGUF',
            'Sexo',
            'NaturalidadePais',
            'NaturalidadeCidade',
            'NaturalidadeUF',
            'EnderecoLogradouro',
            'EnderecoNumero',
            'EnderecoComplemento',
            'EnderecoBairro',
            'EnderecoMunicipio',
            'EnderecoUF',
            'EnderecoCEP'
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        $cell->setValueExplicit($value, DataType::TYPE_STRING);
        return true;
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }
}

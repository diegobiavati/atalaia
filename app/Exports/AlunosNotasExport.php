<?php

namespace App\Exports;

use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Models\Alunos;
use App\Models\AlunosClassificacao;
use App\Models\Avaliacoes;
use App\Models\ConfDemonstrativos;
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

class AlunosNotasExport extends DefaultValueBinder implements FromCollection, Responsable, WithHeadings, WithCustomValueBinder, WithColumnFormatting
{
    use Exportable;

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     */
    private $fileName = 'aluno_notas.xlsx';
    private $_id_ano_formacao = null;
    private $_collection = null;

    /**
     * Optional Writer Type
     */
    private $writerType = Excel::XLSX;

    public function __construct(int $id_ano_formacao){
        $this->_id_ano_formacao = $id_ano_formacao;    
    }  
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {

        $alunosClassificacao = AlunosClassificacao::join('alunos', 'alunos_classificacao.aluno_id', '=', 'alunos.id')->join('omcts', 'alunos.omcts_id', 'omcts.id')
        ->where([['ano_formacao_id', '=', $this->_id_ano_formacao]])->get();

        $collection = new Collection();
        foreach ($alunosClassificacao as $aluno) {

            $dataDemonstrativo = unserialize($aluno->data_demonstrativo);

            $collection->put($aluno->aluno_id, collect([
                'Número' => $aluno->numero,
                'Nome Guerra' => $aluno->nome_guerra,
                'Segmento' => $aluno->sexo,
                'OMCT' => $aluno->sigla_omct
            ]));

            foreach($this->_collection as $item){
                foreach($item->get('avaliacoes') as $avaliacoes){
                    $identificador = $item->get('nome_disciplina').'-'.$avaliacoes->get('nome_avaliacao_abrev');
                    $collection->get($aluno->aluno_id)->put($identificador, 0);
                    foreach($dataDemonstrativo as $info){
                        if(is_array($info) && isset($info['avaliacoes'])){
                            foreach($info['avaliacoes'] as $aval){
                                if($item->get('id_disciplina') == $info['disciplina_id']
                                    && isset($aval->nome_abrev) && $aval->nome_abrev == $avaliacoes->get('nome_avaliacao_abrev')){
                                    
                                    $collection->get($aluno->aluno_id)->put($identificador, number_format($aval->nota, 3, ',', '.'));
                                    //dd($item, $collection->get($aluno->aluno_id));
                                }   
                            }
                        }
                    }
                }
            }
        }

        return $collection;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $configuracaoDemonstrativo = ConfDemonstrativos::first();

        $this->_collection = new Collection();
        foreach(explode(',', $configuracaoDemonstrativo->avaliacoes) as $avaliacao){
            $avaliacoes = Avaliacoes::find($avaliacao);

            if(is_null($this->_collection->get($avaliacoes->disciplinas->id))){
                $this->_collection->put($avaliacoes->disciplinas->id, collect([
                    'id_disciplina' => $avaliacoes->disciplinas->id,
                    'nome_disciplina' => $avaliacoes->disciplinas->nome_disciplina_abrev,
                    'avaliacoes' => collect()
                ]));
            }
            
            $this->_collection->get($avaliacoes->disciplinas->id)->get('avaliacoes')
            ->put($avaliacoes->id, collect(['id_avaliacao' => $avaliacoes->id, 'nome_avaliacao_abrev' => $avaliacoes->nome_abrev]));
        }
        
        $header = ['Número', 'Nome Guerra', 'Segmento', 'OMCT'];
        
        foreach($this->_collection as $item){
            foreach($item->get('avaliacoes') as $avaliacoes){
                $header[] = $item->get('nome_disciplina').'-'.$avaliacoes->get('nome_avaliacao_abrev');
            }
        }

        return $header;
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

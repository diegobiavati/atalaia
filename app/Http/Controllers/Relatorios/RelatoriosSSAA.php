<?php

namespace App\Http\Controllers\Relatorios;

use App\Http\Controllers\Ajax\ImportacaoController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Http\FPDF\PDF_DEM_NOTAS;
use App\Models\Alunos;
use App\Models\AnoFormacao;
use App\Models\EsaAvaliacoesRap;
use App\Models\Disciplinas;
use Illuminate\Http\Request;
use App\Http\Controllers\OwnAuthController;
use App\Http\FPDF\PDF;
use DateTime;
use DateTimeInterface;
use Exception;

class RelatoriosSSAA extends Controller {

    private $_ownauthcontroller = null;
    private $_request = null;

    public function __construct(Request $request, OwnAuthController $ownauthcontroller)
    {
        $this->_ownauthcontroller = $ownauthcontroller;
        $this->_request = $request;
        
    }

    public function DemonstrativoNotasGaviao($anoFormacaoId, $cursoId){

        //ImportacaoController::ImportaMSAccessCapitaniMysql();

        $anoFormacao = AnoFormacao::find($anoFormacaoId);
       
        $alunos = Alunos::retornaAlunosComQmsEspecifica($anoFormacao->id, [$cursoId])->get();
       
        //$alunos_id = Alunos::retornaAlunosComQmsEspecifica($anoFormacao->id, [$cursoId])->pluck('id')->toArray();
        //$capitaniMSAccess = CapitaniMSAccess::whereIn('aluno_id', $alunos_id)->get();
        /*$alunosNotas = Collection::make();
        
        foreach($alunos_id as $id){
            $filtroAluno = $capitaniMSAccess->filter(function($item, $key) use($id){
                return ($item->aluno_id == $id);
            });
            $alunosNotas->put($id, $filtroAluno);
        }*/

        $pdf = new PDF_DEM_NOTAS('P');
        
        $pdf->AliasNbPages();
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->SetLeftMargin(5);

        
        $disciplinas = Disciplinas::where('tfm', 'N')->get();

        
        foreach($alunos as $aluno){
            
            $pdf->AddPage();

            //Cria a Borda Externa
            $pdf->Rect(5, 10, 200, 18);
            $pdf->Image(public_path() . $aluno->qms->img, 190, 12, 14, 14);

            $pdf->SetY(11);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(30, 4, '(SSAA/SCP)', 0, 0, 'L', false);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(145, 4, date('d/m/Y'), 0, 0, 'R', false);
            
            //Cria a Borda Interna
            $pdf->Rect(17, 16, 172, 11);
            $pdf->Image(public_path() . '/images/logo_esa.png', 6, 16, 10, 10);

            //Cabeçalho Borda Interna
            $pdf->SetFont('Arial', 'BI', 6);
            $pdf->SetXY(17, 16);
            $pdf->Cell(97, 5, 'Aluno', 0, 0, 'L', false);
            $pdf->Cell(15, 5, 'Data Nasc', 0, 0, 'C', false);
            $pdf->Cell(5, 5, 'Turma', 0, 0, 'C', false);
            $pdf->Cell(42, 5, 'Sit Anterior', 0, 0, 'C', false);
            $pdf->Cell(11, 5, utf8_decode('Praça Anterior'), 0, 1, 'C', false);

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetXY(17, 21);
            $pdf->WriteHTML(utf8_decode('<b>'.$aluno->numero.' - '.$aluno->nome_guerra.' </b> ('.$aluno->nome_completo.')'));
            $pdf->SetX(114);
            //$pdf->Cell(97, 5, utf8_decode($aluno->numero.' - '.$aluno->nome_guerra.' - '.$aluno->nome_completo), 0, 0, 'L', false);
            $pdf->Cell(15, 5, FuncoesController::formatDateEntoBr($aluno->data_nascimento), 0, 0, 'C', false);
            $pdf->Cell(5, 5, (isset($aluno->turmaEsa->turma) ? $aluno->turmaEsa->turma : null), 0, 0, 'C', false);
            $pdf->SetFont('Arial', 'B', 6);
            $pdf->Cell(42, 5, (isset($aluno->situacao_anterior->situacao_anterior) ? utf8_decode($aluno->situacao_anterior->situacao_anterior) : null), 0, 0, 'C', false);
            $pdf->Cell(11, 5, FuncoesController::formatDateEntoBr($aluno->primeira_data_praca), 0, 1, 'C', false);

            $pdf->SetY(29);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            
            $pdf->Cell(45, 5, utf8_decode('1º Ano do CFGS'), 0, 0, 'L', false);
            $pdf->WriteHTML(utf8_decode('<b>UETE 1º Ano:</b> '.(isset($aluno->omct->sigla_omct) ? $aluno->omct->sigla_omct : null)));
            $pdf->Cell(45, 5, '', 0, 0, 'L', false);
            $pdf->WriteHTML('<b>CFGS:</b> '.$aluno->formacao()->formacao.'  '.$aluno->formacao()->ano_cfs);
            $pdf->SetXY(175, 29);

            $pdf->CheckBox($pdf, (!$aluno->conselhoEscolar->isEmpty()));
            $pdf->Cell(25, 5, utf8_decode('Aprv CE'), 0, 1, 'L', false);

            $pdf->SetY(35);

            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Cell(15, 5, 'TFM I', 0, 0, 'C', false);
            $pdf->Cell(15, 5, 'ARMT', 0, 0, 'C', false);
            $pdf->Cell(15, 5, 'LID MIL', 0, 0, 'C', false);
            $pdf->Cell(15, 5, utf8_decode('ÉTICA'), 0, 0, 'C', false);
            $pdf->Cell(15, 5, 'HIST', 0, 0, 'C', false);
            $pdf->Cell(18, 5, 'TEC MIL I', 0, 0, 'C', false);
            $pdf->Cell(18, 5, 'TEC MIL II', 0, 0, 'C', false);
            $pdf->Cell(18, 5, 'TEC MIL III', 0, 0, 'C', false);
            $pdf->Cell(14, 5, 'IG I', 0, 0, 'C', false);
            $pdf->Cell(14, 5, 'IIB', 0, 0, 'C', false);
            $pdf->Cell(14, 5, 'TOPO', 0, 0, 'C', false);
            $pdf->Cell(14, 5, 'PATR', 0, 0, 'C', false);
            $pdf->Cell(14, 5, 'GLO', 0, 1, 'C', false);

            try{
                $info_1Ano = unserialize($aluno->classificacao->data_demonstrativo)??[];
            }catch(Exception $ex){
                dd($aluno->classificacao, $aluno);
            }
            
        
            //Caso seja Aluno de Períodos Anteriores
            if(!key_exists('avaliacoes_tfm', $info_1Ano)){
                $filtro = array_filter($info_1Ano, function($v, $k){
                    return (is_numeric($k) && (key_exists('disciplina_id', $v) && $v['disciplina_id'] == '99999'));
                }, ARRAY_FILTER_USE_BOTH);

                $info_1Ano['avaliacoes_tfm']['media_tfm'] = array_shift($filtro)['media'];
            }
            
            $notas1Ano = [];
            foreach($info_1Ano as $k => $v){
                try{ 
                    if( is_numeric($k) && ( (!key_exists('tfm', $v) && ($v['disciplina_id'] != 99999 && $v['disciplina_id'] != 88888))  
                                                || (key_exists('tfm', $v) && $v['tfm'] == 'N') ) ){
                    
                        $filtro = $disciplinas->search(function($item, $key) use ($v){
                            return $v['disciplina_id'] == $item->id;
                        });
                    
                        switch($disciplinas->get($filtro)->nome_disciplina_abrev){
                            case 'ARMTO':
                                $notas1Ano['ARMTO'] = $v['media'];
                                break;
                            case 'LID':
                                $notas1Ano['LID'] = $v['media'];
                                break;
                            case 'ÉTICA':
                                $notas1Ano['ÉTICA'] = $v['media'];
                                break;
                            case 'TEC MIL 1':
                                $notas1Ano['TEC MIL I'] = $v['media'];
                                break;
                            case 'TEC MIL 2':
                                $notas1Ano['TEC MIL II'] = $v['media'];
                                break;
                            case 'TEC MIL 3':
                                $notas1Ano['TEC MIL III'] = $v['media'];
                                break;
                            case 'Hist Mil BR':
                                $notas1Ano['Hist Mil BR'] = $v['media'];
                                break;
                            case 'HIST':
                                $notas1Ano['Hist Mil BR'] = $v['media'];
                                break;
                        }
                    }
                }catch(Exception $ex){
                    dd($aluno, $v);
                }
            }
        
            $pdf->SetY(40);
            $pdf->SetFont('Arial', '', 7);
        
            $pdf->Cell(15, 5, number_format($info_1Ano['avaliacoes_tfm']['media_tfm'], 3, ',', ''), 0, 0, 'C', false);
            $pdf->Cell(15, 5, number_format($notas1Ano['ARMTO'], 3, ',', ''), 0, 0, 'C', false);
            $pdf->Cell(15, 5, number_format($notas1Ano['LID'], 3, ',', ''), 0, 0, 'C', false);
            $pdf->Cell(15, 5, number_format($notas1Ano['ÉTICA'], 3, ',', ''), 0, 0, 'C', false);
            $pdf->Cell(15, 5, number_format($notas1Ano['Hist Mil BR'], 3, ',', ''), 0, 0, 'C', false);
            $pdf->Cell(18, 5, number_format($notas1Ano['TEC MIL I'], 3, ',', ''), 0, 0, 'C', false);
            $pdf->Cell(18, 5, number_format($notas1Ano['TEC MIL II'], 3, ',', ''), 0, 0, 'C', false);
            $pdf->Cell(18, 5, number_format($notas1Ano['TEC MIL III'], 3, ',', ''), 0, 0, 'C', false);
            $pdf->Cell(14, 5, '', 0, 0, 'C', false);
            $pdf->Cell(14, 5, '', 0, 0, 'C', false);
            $pdf->Cell(14, 5, '', 0, 0, 'C', false);
            $pdf->Cell(14, 5, '', 0, 0, 'C', false);
            $pdf->Cell(14, 5, '', 0, 0, 'C', false);
        
            $pdf->SetY(50);
            $pdf->SetFont('Arial', 'BI', 8);
            $pdf->Cell(45, 5, 'Disciplina', 0, 0, 'C', false);
            $pdf->Cell(13, 5, 'AA/AA1', 0, 0, 'C', false);
            $pdf->Cell(13, 5, 'AA2', 0, 0, 'C', false);
            $pdf->Cell(13, 5, 'AA3', 0, 0, 'C', false);
            $pdf->Cell(13, 5, 'AC/AI', 0, 0, 'C', false);
            $pdf->Cell(13, 5, 'AC2', 0, 0, 'C', false);
            $pdf->Cell(13, 5, 'AR', 0, 0, 'C', false);
            $pdf->Cell(13, 5, 'AD', 0, 0, 'C', false);
            $pdf->Cell(13, 5, 'AF1', 0, 0, 'C', false);
            $pdf->Cell(13, 5, 'AF2', 0, 0, 'C', false);
            $pdf->Cell(13, 5, 'Diploma', 0, 0, 'C', false);
            $pdf->Cell(12, 5, 'NDC', 0, 0, 'C', false);
            $pdf->Cell(12, 5, utf8_decode('Bônus'), 0, 1, 'C', false);

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetFillColor(170, 170, 170);

            $fill = false;
            
            $notasFinais = null;
            foreach($aluno->capitaniNotas as $notas){
                $demonstrativo = json_decode($notas->data_demonstrativo);
                
                $pdf->SetX(5);
                $pdf->Cell(45, 5, utf8_decode($notas->disciplina), 0, 0, 'L', $fill);
                $pdf->Cell(13, 5, (isset($demonstrativo->AA_A1) ? number_format($demonstrativo->AA_A1, 3, ',', '') : null), 0, 0, 'C', $fill);
                $pdf->Cell(13, 5, (isset($demonstrativo->AA2)   ? number_format($demonstrativo->AA2, 3, ',', '')   : null), 0, 0, 'C', $fill);
                $pdf->Cell(13, 5, (isset($demonstrativo->AA3)   ? number_format($demonstrativo->AA3, 3, ',', '')   : null), 0, 0, 'C', $fill);
                $pdf->Cell(13, 5, (isset($demonstrativo->AC_AI) ? number_format($demonstrativo->AC_AI, 3, ',', '') : null), 0, 0, 'C', $fill);
                $pdf->Cell(13, 5, (isset($demonstrativo->AC2)   ? number_format($demonstrativo->AC2, 3, ',', '')   : null), 0, 0, 'C', $fill);
                $pdf->Cell(13, 5, (isset($demonstrativo->AR)    ? number_format($demonstrativo->AR, 3, ',', '')    : null), 0, 0, 'C', $fill);
                $pdf->Cell(13, 5, (isset($demonstrativo->AD)    ? number_format($demonstrativo->AD, 3, ',', '')    : null), 0, 0, 'C', $fill);
                $pdf->Cell(13, 5, (isset($demonstrativo->AF1)   ? number_format($demonstrativo->AF1, 3, ',', '')   : null), 0, 0, 'C', $fill);
                $pdf->Cell(13, 5, (isset($demonstrativo->AF2)   ? number_format($demonstrativo->AF2, 3, ',', '')   : null), 0, 0, 'C', $fill);
                $pdf->Cell(13, 5, (isset($demonstrativo->NDF)   ? $demonstrativo->NDF : null)                             , 0, 0, 'C', $fill);
                $pdf->Cell(13, 5, (isset($demonstrativo->NDC)   ? number_format($demonstrativo->NDC, 3, ',', '')   : null), 0, 0, 'C', $fill);
                $pdf->Cell(12, 5, (isset($demonstrativo->Bonus) ? number_format($demonstrativo->Bonus, 3, ',', '') : null), 0, 1, 'C', $fill);
    
                $notasFinais['N1'] =    (isset($demonstrativo->NPBARRED) ? number_format($demonstrativo->NPBARRED, 3, ',', '')    : null);
                $notasFinais['N2'] =    (isset($demonstrativo->NQ)       ? number_format($demonstrativo->NQ, 3, ',', '')          : null);
                $notasFinais['NACP'] =  (isset($demonstrativo->NACP)     ? number_format($demonstrativo->NACP, 3, ',', '')        : null);
                $notasFinais['NAA'] =   (isset($demonstrativo->NAA)      ? number_format($demonstrativo->NAA, 3, ',', '')         : null);
                $notasFinais['NFC'] =   (isset($demonstrativo->NFC)      ? number_format($demonstrativo->NFC, 3, ',', '')         : null);
                $notasFinais['MENCAO'] = (isset($demonstrativo->mencao)  ? $demonstrativo->mencao : '-');
                $notasFinais['QR'] =    (isset($demonstrativo->QR)       ? number_format($demonstrativo->QR, 3, ',', '')          : null);
                $notasFinais['CLASS'] = (isset($demonstrativo->ClasF)  ? utf8_decode($demonstrativo->ClasF) : '-');

                $notasFinais['DIZCLASS'] = (isset($demonstrativo->NFC_Diz) ? number_format((double)str_replace(',', '.', $demonstrativo->NFC_Diz), 3, ',', '') : null);
                $fill = !$fill;
            }
        
            $posicaoY = ($pdf->getY() + 3);

                $pdf->SetY(45);
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(125, 5, utf8_decode('2º Ano do CFGS'), 0, 0, 'L', false);
                
                
                $pdf->SetTextColor(112, 128, 144);
                $pdf->Cell(45, 5, utf8_decode('Dízima Clas: ').$notasFinais['DIZCLASS'], 0, 0, 'L', false);

                $pdf->SetTextColor(0, 0, 0);

            $pdf->setY($posicaoY);
            $pdf->Rect(5, $pdf->getY(), 40, 11);
            
            $pdf->SetFont('Arial', '', 6);
            $pdf->setX(16);
            $pdf->WriteHTML(utf8_decode('<b><u>Fórmula da N2</u></b>'));
            $pdf->ln(4);
            $pdf->setX(7);
            $pdf->WriteHTML('<u>(ND1)x1 +....+ (NDn)x1 + (NP1)x1,5</u>');
            $pdf->ln(3);
            $pdf->setX(20);
            $pdf->WriteHTML('n+1.5');

            $pdf->setXY(46, $posicaoY);
            $pdf->SetLineWidth(0.4);
            $pdf->Rect(46, $pdf->getY(), 20, 11);

            $pdf->WriteHTML(utf8_decode('<b><u>Fórmula da NACP</u></b>'));
            $pdf->ln(4);
            $pdf->setX(48);
            $pdf->WriteHTML('<u>(N1*1)+(N2*2)</u>');
            $pdf->ln(3);
            $pdf->setX(54);
            $pdf->WriteHTML('3');

            $pdf->setXY(68, $posicaoY);
            $pdf->Rect(67, $pdf->getY(), 21, 11);

            $pdf->WriteHTML(utf8_decode('<b><u>Fórmula da NFC</u></b>'));
            $pdf->ln(4);
            $pdf->setX(67);
            $pdf->WriteHTML('<u>(NACPx9)+(NAAx1)</u>');
            $pdf->ln(3);
            $pdf->setX(75);
            $pdf->WriteHTML('10');

            $pdf->SetLineWidth(0);
            /* Médias */
            $pdf->SetFont('Arial', '', 8);

            $pdf->setXY(90, ($posicaoY + 1.5));
            $pdf->Cell(12, 4, 'N1', 1, 1, 'C');
            $pdf->setX(90);
            $pdf->Cell(12, 4, $notasFinais['N1'], 1, 0, 'C');

            $posicaoX = $pdf->getX();
            $pdf->setXY($posicaoX,($posicaoY + 1.5));
            $pdf->Cell(12, 4, 'N2', 1, 1, 'C');
            $pdf->setX($posicaoX);
            $pdf->Cell(12, 4, $notasFinais['N2'], 1, 0, 'C');

            $posicaoX = $pdf->getX();
            $pdf->setXY($posicaoX,($posicaoY + 1.5));
            $pdf->Cell(12, 4, 'NACP', 1, 1, 'C');
            $pdf->setX($posicaoX);
            $pdf->Cell(12, 4, $notasFinais['NACP'], 1, 0, 'C');

            $posicaoX = $pdf->getX();
            $pdf->setXY($posicaoX,($posicaoY + 1.5));
            $pdf->Cell(12, 4, 'NAA', 1, 1, 'C');
            $pdf->setX($posicaoX);
            $pdf->Cell(12, 4, $notasFinais['NAA'], 1, 0, 'C');

            $posicaoX = $pdf->getX();
            $pdf->setXY($posicaoX,($posicaoY + 1.5));
            $pdf->Cell(12, 4, 'NFC', 1, 1, 'C');
            $pdf->setX($posicaoX);
            $pdf->Cell(12, 4, $notasFinais['NFC'], 1, 0, 'C');

            $posicaoX = $pdf->getX();
            $pdf->setXY($posicaoX,($posicaoY + 1.5));
            $pdf->Cell(12, 4, utf8_decode('Menção'), 1, 1, 'C');
            $pdf->setX($posicaoX);
            $pdf->Cell(12, 4, $notasFinais['MENCAO'], 1, 0, 'C');

            $posicaoX = $pdf->getX();
            $pdf->setXY($posicaoX,($posicaoY + 1.5));
            $pdf->Cell(12, 4, 'QR', 1, 1, 'C');
            $pdf->setX($posicaoX);
            $pdf->Cell(12, 4, $notasFinais['QR'], 1, 0, 'C');

            $posicaoX = $pdf->getX();
            $pdf->setXY($posicaoX,($posicaoY + 1.5));
            $pdf->Cell(12, 4, 'Clas', 1, 1, 'C');
            $pdf->setX($posicaoX);
            $pdf->Cell(12, 4, $notasFinais['CLASS'], 1, 1, 'C');


            $pdf->SetFont('Arial', '', 6);

            $posicaoY = ($pdf->getY() + 3);
            $pdf->setY($posicaoY);
            $pdf->Rect(5, $posicaoY, 43, 11);

            $pdf->setX(16);
            $pdf->WriteHTML(utf8_decode('<b><u>Fórmula da ND</u></b>'));
            $pdf->ln(4);
            $pdf->setX(5);
            $pdf->WriteHTML('<u>(AA)x1 +....+ (AAn)x1+(AC)x2+....+(ACn)x2</u>');
            $pdf->ln(3);
            $pdf->setX(20);
            $pdf->WriteHTML('1+...+1+2+...+2');


            $pdf->setXY(($pdf->getX() + 15), $posicaoY);
            $pdf->SetLineWidth(0.4);
            $pdf->Rect($pdf->getX(), $posicaoY, 156, 11);
            
            $pdf->setXY(($pdf->getX()), $posicaoY + 1);
            $pdf->MultiCell(155, 3, utf8_decode('Atenção: O aluno deverá realizar a conferência de suas notas e dados pessoais (Ex.: Data Nasc, Situação Anterior, Data Promoções Sgt T, Cb, Sd ...), os quais serão utilizados como critérios de desempate na classificação final. Os graus atribuídos pelo C Ens não serão computados para o cálculo da NFC, sendo utilizados os obtidos ao longo do ano, conforme Parágrafo Único do art.92 das NAA (EB60-N-06.004) e os graus atribuídos mediante parecer do comando.'), 0, 'J');


        }
        
        
        FuncoesController::LimpaPastaTemp();
        $nomeArquivo = 'Dem_Notas_'.uniqid().'.pdf';
        $pdf->Output('F', storage_path('app/public/temp/').$nomeArquivo);
        
        $data['success'] = true; 
        $data['arquivo'] = $nomeArquivo;
        $data['rota'] = 'ajax/relatorios/download-pdf';
        
        return response()->json($data);
        
        //exit();
    }

    public function relatorioAplicacaoProva(){

        $explode = explode('-', decrypt($this->_request->hash_rap));
        
        $esaAvaliacoesRap = EsaAvaliacoesRap::where([['id_esa_avaliacoes', '=', $explode[1]], ['id_turmas_esa', '=', $explode[2]]])->first();
        
        $condicoesAplicacao = array('MB', 'B', 'R', 'I');
        $pdf = new PDF('P');
        
        $pdf->AliasNbPages();
        $pdf->SetAutoPageBreak(true, 10);

        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 4, utf8_decode('RELATÓRIO DE APLICAÇÃO DE PROVA - RAP'), 0, 1, 'C');
        $pdf->ln(3);

        $pdf->Cell(95, 5, utf8_decode('ESCOLA DE SARGENTOS DAS ARMAS'), 1, 0, 'C');
        $pdf->Cell(95, 5, utf8_decode('RELATÓRIO DE APLICAÇÃO DE PROVA'), 1, 1, 'C');
        $pdf->ln(3);

        $pdf->SetFont('Arial', '', 10);
        if(strlen($esaAvaliacoesRap->esaAvaliacoes->esadisciplinas->nome_disciplina_abrev) > 20){
            $pdf->SetFont('Arial', '', 6);
        }
        $pdf->Cell(47.5, 5, utf8_decode(substr($esaAvaliacoesRap->esaAvaliacoes->esadisciplinas->nome_disciplina_abrev, 0, 40)), 1, 0, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(47.5, 5, utf8_decode($esaAvaliacoesRap->esaAvaliacoes->nome_avaliacao.' - '.$esaAvaliacoesRap->esaAvaliacoes->chamada.'ª chamada'), 1, 0, 'C');
        $pdf->Cell(47.5, 5, utf8_decode('C '.$esaAvaliacoesRap->esaTurma->qms->qms_sigla.' / '.$esaAvaliacoesRap->esaTurma->turma), 1, 0, 'C');
        $pdf->Cell(47.5, 5, utf8_decode($esaAvaliacoesRap->local_aplicacao), 1, 1, 'C');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(47.5, 5, utf8_decode('DISCIPLINA'), 1, 0, 'C');
        $pdf->Cell(47.5, 5, utf8_decode('PROVA'), 1, 0, 'C');
        $pdf->Cell(47.5, 5, utf8_decode('CURSO/TURMA'), 1, 0, 'C');
        $pdf->Cell(47.5, 5, utf8_decode('LOCAL'), 1, 1, 'C');
        $pdf->ln(3);

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(47.5, 5, FuncoesController::formatDateEntoBr($esaAvaliacoesRap->esaAvaliacoes->realizacao), 1, 0, 'C');
        $pdf->Cell(47.5, 5, date('H:i', strtotime($esaAvaliacoesRap->duracao)), 1, 0, 'C');
        $pdf->Cell(47.5, 5, date('H:i', strtotime($esaAvaliacoesRap->hora_inicio)), 1, 0, 'C');
        $pdf->Cell(47.5, 5, date('H:i', strtotime($esaAvaliacoesRap->hora_termino)), 1, 1, 'C');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(47.5, 5, utf8_decode('DATA DA APLICAÇÃO'), 1, 0, 'C');
        $pdf->Cell(47.5, 5, utf8_decode('DURAÇÃO PREVISTA'), 1, 0, 'C');
        $pdf->Cell(47.5, 5, utf8_decode('HORA DE INÍCIO'), 1, 0, 'C');
        $pdf->Cell(47.5, 5, utf8_decode('HORA DE TÉRMINO'), 1, 1, 'C');
        $pdf->ln(3);

        $pdf->Cell(95, 5, utf8_decode('ERROS DE IMPRESSÃO'), 1, 0, 'C');
        $pdf->Cell(95, 5, utf8_decode('ERROS DE INTERPRETAÇÃO'), 1, 1, 'C');
        $pdf->Cell(95, 5, utf8_decode('QUESTÃO(ÕES) OU ITEM(NS)'), 1, 0, 'C');
        $pdf->Cell(95, 5, utf8_decode('QUESTÃO(ÕES) OU ITEM(NS)'), 1, 1, 'C');
        
        $y = $pdf->getY();
        $pdf->SetFont('Arial', '', 8);
        $pdf->Rect($pdf->getX(), $pdf->getY(), 95, 35);
        $pdf->MultiCell(95, 5, utf8_decode($esaAvaliacoesRap->erros_impressao), 0, "L", false, 8);
        $pdf->setXY(105, $y);
        $pdf->Rect(105, $pdf->getY(), 95, 35);
        $pdf->MultiCell(95, 5, utf8_decode($esaAvaliacoesRap->erros_interpretacao), 0, "L", false, 8);

        $pdf->setY(100);

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(55, 5, utf8_decode('CONDIÇÕES DO LOCAL DE APLICAÇÃO'), 1, 0, 'C');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 5, "MB", 1, 0, 'C');
        $pdf->Cell(10, 5, "B", 1, 0, 'C');
        $pdf->Cell(10, 5, "R", 1, 0, 'C');
        $pdf->Cell(10, 5, "I", 1, 0, 'C');

        $pdf->setX(110);
        $pdf->MultiCell(90, 5, utf8_decode("FATORES QUE INFLUENCIARAM NA APLICAÇÃO"), 1, 'C');

        $y = $pdf->getY();
        $pdf->Cell(55, 5, utf8_decode('ADEQUAÇÃO'), 1, 0, 'C');
        $pdf->SetFont('Arial', '', 10);
        foreach($condicoesAplicacao as $condicao){
            $pdf->Cell(10, 5, ($esaAvaliacoesRap->cond_local_adequacao == $condicao) ? "X" : null, 1, 0, 'C');    
        }

        $pdf->ln(5);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(55, 5, utf8_decode('ARRUMAÇÃO'), 1, 0, 'C');
        $pdf->SetFont('Arial', '', 10);
        foreach($condicoesAplicacao as $condicao){
            $pdf->Cell(10, 5, ($esaAvaliacoesRap->cond_local_arrumacao == $condicao) ? "X" : null, 1, 0, 'C');    
        }

        $pdf->ln(5);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(55, 5, utf8_decode('SILÊNCIO'), 1, 0, 'C');
        $pdf->SetFont('Arial', '', 10);
        foreach($condicoesAplicacao as $condicao){
            $pdf->Cell(10, 5, ($esaAvaliacoesRap->cond_local_silencio == $condicao) ? "X" : null, 1, 0, 'C');    
        }

        $pdf->ln(5);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(55, 5, utf8_decode('ILUMINAÇÃO'), 1, 0, 'C');
        $pdf->SetFont('Arial', '', 10);
        foreach($condicoesAplicacao as $condicao){
            $pdf->Cell(10, 5, ($esaAvaliacoesRap->cond_local_iluminacao == $condicao) ? "X" : null, 1, 0, 'C');    
        }
        
        $pdf->Rect(($pdf->getX() + 5), $y, 90, 20);
        $pdf->setXY(110, $y);
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(90, 5, utf8_decode($esaAvaliacoesRap->fatores_influencia_aplicacao), 0, "L", false, 4);

        $pdf->setY(130);
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(140, 5, 'EFETIVO QUE REALIZOU A PROVA', 1, 0, 'R');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 5, $esaAvaliacoesRap->efetivo_realizou, 1, 1, 'C');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(140, 5, utf8_decode('EFETIVO NA SALA AO TÉRMINO DO TEMPO'), 1, 0, 'R');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 5, $esaAvaliacoesRap->efetivo_termino, 1, 1, 'C');

        $pdf->ln(5);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(90, 5, 'SEQUENCIA DE ENTREGA', 1, 0, 'C');
        $pdf->Cell(30, 5, 'TEMPO', 1, 0, 'C');
        $pdf->Cell(70, 5, 'DISCENTE', 1, 1, 'C');

        $pdf->Cell(90, 5, 'PRIMEIRO DISCENTE', 1, 0, 'R');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(30, 5, $esaAvaliacoesRap->primeiro_discente['tempo'], 1, 0, 'C');
        $pdf->Cell(70, 5, utf8_decode($esaAvaliacoesRap->primeiroDiscente->numero.' - '.$esaAvaliacoesRap->primeiroDiscente->nome_guerra), 1, 1, 'C');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(90, 5, 'SEGUNDO DISCENTE', 1, 0, 'R');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(30, 5, $esaAvaliacoesRap->segundo_discente['tempo'], 1, 0, 'C');
        $pdf->Cell(70, 5, utf8_decode($esaAvaliacoesRap->segundoDiscente->numero.' - '.$esaAvaliacoesRap->segundoDiscente->nome_guerra), 1, 1, 'C');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(90, 5, 'TERCEIRO DISCENTE', 1, 0, 'R');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(30, 5, $esaAvaliacoesRap->terceiro_discente['tempo'], 1, 0, 'C');
        $pdf->Cell(70, 5, utf8_decode($esaAvaliacoesRap->terceiroDiscente->numero.' - '.$esaAvaliacoesRap->terceiroDiscente->nome_guerra), 1, 1, 'C');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(90, 5, 'MAIORIA (META DA TURMA + 1)', 1, 0, 'R');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(30, 5, date('H:i', strtotime($esaAvaliacoesRap->maioria_efetivo)), 1, 0, 'C');
        $pdf->Cell(70, 5, '', 'R', 1, 'C');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(90, 5, 'TODO EFETIVO', 1, 0, 'R');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(30, 5, date('H:i', strtotime($esaAvaliacoesRap->todo_efetivo)), 1, 0, 'C');
        $pdf->Cell(70, 5, '', 'B,R', 1, 'C');

        $pdf->ln(5);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(95, 5, utf8_decode('FALTAS'), 1, 0, 'C');
        $pdf->Cell(95, 5, utf8_decode('MOTIVO'), 1, 1, 'C');
        
        $y = $pdf->getY();
        $pdf->SetFont('Arial', '', 8);
        $pdf->Rect($pdf->getX(), $pdf->getY(), 95, 60);

        $faltas = array(null, null);
        $i = 0;
        foreach($esaAvaliacoesRap->faltas->pluck('numero_nome_guerra')->toArray() as $aluno){
            $i++;
            if($i <= 12){
                $faltas[0] .= $i.'. '.$aluno.chr(13).chr(10);
            }else{
                $faltas[1] .= $i.'. '.$aluno.chr(13).chr(10);
            }
        }
        
        if($i >= 12){
            $pdf->MultiCell(47.5, 5, utf8_decode($faltas[0]), 0, 'L', false, 12);
            $pdf->Rect(57.5, $y, 47.5, 60);
            $pdf->SetXY(57.5, $y);
            $pdf->MultiCell(47.5, 5, utf8_decode($faltas[1]), 0, 'L', false, 12);
        }else{
            $pdf->MultiCell(95, 5, utf8_decode($faltas[0]), 0, 'L', false, 12);
        }

        $pdf->setXY(105, $y);
        $pdf->Rect(105, $pdf->getY(), 95, 60);

        if(isset($esaAvaliacoesRap->alunos_faltas)){
            $motivos = array(null, null);
            $i = 0;
            foreach($esaAvaliacoesRap->alunos_faltas as $alunofalta){
                $i++;
                if($i <= 12){
                    $motivos[0] .= $i.'. '.$alunofalta['motivo'].chr(13).chr(10);
                }else{
                    $motivos[1] .= $i.'. '.$alunofalta['motivo'].chr(13).chr(10);
                }
            }

            if($i >= 12){
                $pdf->MultiCell(47.5, 5, utf8_decode($motivos[0]), 0, 'L', false, 12);
                $pdf->Rect(152.5, $y, 47.5, 60);
                $pdf->SetXY(152.5, $y);
                $pdf->MultiCell(47.5, 5, utf8_decode($motivos[1]), 0, 'L', false, 12);
            }else{
                $pdf->MultiCell(95, 5, utf8_decode($motivos[0]), 0, 'L', false, 12);
            }
        }
        
        //$pdf->MultiCell(95, 5, $motivos, 0, "L", false, 12);

        $pdf->setY(250);
        $pdf->MultiCell(0, 5, utf8_decode('* Obs: no caso de faltas a 1ª Chm, o curso deverá informar ao Cmt CA, e este, à DE, até 2 dias após realização da prova, a justificativa ou não da(s) falta(s) à prova, conforme § 1º e 2º do Art 51 das NIAA/ESA.'), 0, "L");
        $pdf->Ln(20);
        $pdf->Cell(95, 5, '', 0, 0, 'C');
        $pdf->Cell(0, 5, 'APLICADOR', 'T', 1, 'C');

        $pdf->Output('I', utf8_decode('Relatório de Aplicação de Prova.pdf'));
        exit();
    }
}
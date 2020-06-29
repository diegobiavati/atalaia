<?php

namespace App\Http\Controllers\Relatorios;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Http\FPDF\PDF;
use App\Http\OwnClasses\ClassLog;
use App\Models\Alunos;
use App\Models\AlunosClassificacao;
use App\Models\AnoFormacao;
use App\Models\Areas;
use App\Models\ConteudoAtitudinal;
use App\Models\Disciplinas;
use App\Models\Fatd;
use App\Models\LancamentoFo;
use App\Models\Mencoes;
use App\Models\OMCT;
use App\Models\SituacaoMatricula;
use App\Models\SituacoesDiversas;
use Illuminate\Support\Facades\DB;

class RelatorioAlunoController extends Controller
{

    protected $classLog;
    protected $ownauthcontroller;

    public function __construct(OwnAuthController $ownauthcontroller, ClassLog $classLog)
    {
        $this->ownauthcontroller = $ownauthcontroller;
        $this->classLog = $classLog;
        $this->classLog->ip = $_SERVER['REMOTE_ADDR'];
    }

    public function ViewRelatorioGeral(Request $request)
    {

        if ($this->ownauthcontroller->PermissaoCheck(1)) {
            $uetes = OMCT::where('id', '<>', 1)->get(); //Remove a ESA
        } else {
            $uetes = OMCT::where('id', session()->get('login.omctID'))->get();
        }

        $anoFormacao = AnoFormacao::whereId($request->id_ano_formacao)->get()->first();
        $areas = Areas::get();

        $alunos = new Alunos();
        $colunas = $alunos->getFillableDescription();

        // Repassando para a view
        return view('relatorios.relacao-geral-alunos', compact('uetes', 'anoFormacao', 'areas', 'colunas'))
            ->with('ownauthcontroller', $this->ownauthcontroller);
    }

    public function ViewFichaIndividualAluno(Request $request)
    {
        if ($this->ownauthcontroller->PermissaoCheck(1)) {
            $uetes = OMCT::where('id', '<>', 1)->get(); //Remove a ESA
        } else {
            $uetes = OMCT::where('id', session()->get('login.omctID'))->get();
        }

        $anoFormacao = AnoFormacao::whereId($request->id_ano_formacao)->get()->first();

        //Repassando para a view
        return view('relatorios.relacao-ficha-individual-aluno', compact('uetes', 'anoFormacao'))
            ->with('ownauthcontroller', $this->ownauthcontroller);
    }

    public function ViewFradAluno(Request $request)
    {
        if ($this->ownauthcontroller->PermissaoCheck(1)) {
            $uetes = OMCT::where('id', '<>', 1)->get(); //Remove a ESA
        } else {
            $uetes = OMCT::where('id', session()->get('login.omctID'))->get();
        }

        $anoFormacao = AnoFormacao::whereId($request->id_ano_formacao)->get()->first();

        //Repassando para a view
        return view('relatorios.relacao-frad-aluno', compact('uetes', 'anoFormacao'))
            ->with('ownauthcontroller', $this->ownauthcontroller);
    }

    function RelacaoAlunosProntos(Request $request)
    {

        if (!$this->ownauthcontroller->PermissaoCheck(1) && $request->omctID != session()->get('login.omctID')) {
            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';
        } else {

            $arrayExcessao = ['ano_formacao_id', 'omctID', 'ordem', 'alteracao', 'ciente', 'relacao'];
            $arrayColunas = [];

            $ano_formacao = AnoFormacao::find($request->ano_formacao_id);

            if (isset($request->segmento)) {
                $segmento_array = $request->segmento;
            } else {
                $segmento_array = array('M', 'F');
            }

            if (isset($request->areas)) {
                $areas_array = $request->areas;
            } else {
                $areas_array = array();
            }

            $alunosSelect = Alunos::with('ano_formacao')
                ->with('turma')
                ->with('area')
                ->with('instrumento')
                ->with('uf_nascimento')
                ->with('situacao_anterior')
                ->with('situacao_matricula')
                //->with('situacao_atual')
                ->with('uf')
                ->with('profissao_mae')
                ->with('profissao_pai')
                ->with('raca')
                ->with('religiao')
                ->with('escolaridade')
                ->with('renda')
                ->with('estado_civil')
                ->whereIn('sexo', $segmento_array)
                ->whereIn('area_id', $areas_array)
                ->where('data_matricula', $request->ano_formacao_id)
                ->where('omcts_id', (($request->omctID == 'todas_omct') ? '<>' : '='), (($request->omctID == 'todas_omct') ? 1 : $request->omctID))
                ->orderBy('numero', 'desc')->get();

            switch ($request->omctID) {
                case 'todas_omct';
                    $omct = OMCT::where('id', '<>', 1)->get(); //Remove a ESA do select
                    break;
                default:
                    $omct = OMCT::where('id', $request->omctID)->get();
                    break;
            }

            $alunos = [];

            foreach ($alunosSelect as $aluno) {
                $alunos[$aluno->omcts_id][] = $aluno;
            }

            $aluno = new Alunos();

            //Pegar os campos dinâmicamente
            foreach ($request->request as $key => $valor) {
                if (!is_array($valor)) {
                    if (!in_array($key, $arrayExcessao)) {
                        $arrayColunas[$key] = $aluno->getFillableDescription()[$key];
                    }
                }
            }

            $aluno = null;

            $this->classLog->RegistrarLog('Acessou lista de alunos prontos', auth()->user()->email);

            return view('relatorios.new-relacao-alunos-prontos')
                ->with('ano_formacao', $ano_formacao)
                ->with('omct', $omct)
                ->with('alunos', $alunos)
                ->with('arrayColunas', $arrayColunas)
                ->with('relacao', $request->relacao);
        }
    }

    public function ViewRelacaoAlunos(Request $request)
    {
        $anoFormacao = AnoFormacao::whereId($request->ano_formacao_id)->get()->first();

        $rota = 'relatorios.relacao-ficha-individual-aluno';

        $alunos = Alunos::carregaAlunosVsAlunosSitDiv($anoFormacao->id);

        if ($request->omctID != 'todas_omct') {
            $alunos = Alunos::filtraAlunosOmct($alunos, $request->omctID);
        }

        if (isset($request->numero_aluno)) {
            $alunos = Alunos::filtraAlunosNumero($alunos, $request->numero_aluno);
        }

        if (isset($request->nome_aluno)) {
            $alunos = Alunos::filtraAlunosNome($alunos, $request->nome_aluno);
        }

        return view('relatorios/ficha-individual-do-aluno', compact('anoFormacao', 'rota', 'alunos'));
    }

    public function ViewRelacaoFradAlunos(Request $request)
    {
        $anoFormacao = AnoFormacao::whereId($request->ano_formacao_id)->get()->first();

        $rota = 'relatorios.relacao-frad-aluno';

        $rotaGeral = 'relatorios.relacao-frad-geral';
        $idOmct = $request->omctID;

        $where = '';
        if ($request->omctID != 'todas_omct') {
            $where .= ' AND alunos.omcts_id = ' . $request->omctID;
        }

        if (isset($request->numero_aluno)) {
            $where .= ' AND alunos.numero = ' . $request->numero_aluno;
        }

        if (isset($request->nome_aluno)) {
            $where .= " AND alunos.nome_completo LIKE '%$request->nome_aluno%'";
        }

        $alunos = $this->selectAlunosFrad($anoFormacao->id, $where);

        return view('relatorios/ficha-frad-do-aluno', compact('anoFormacao', 'rota', 'rotaGeral', 'alunos', 'idOmct'));
    }

    private function selectAlunosFrad($idAnoFormacao, $where)
    {
        return DB::select("SELECT alunos.id, alunos.numero, alunos.nome_guerra, alunos.nome_completo, alunos.data_matricula 
                                    FROM alunos
                                    INNER JOIN lancamento_fo ON (lancamento_fo.aluno_id = alunos.id)
                                        WHERE alunos.data_matricula = $idAnoFormacao
                                        $where
                                        AND ( lancamento_fo.fatd = 'S' OR lancamento_fo.frad = 'S')
                                    GROUP BY alunos.id");
    }

    public function RelatorioRelacaoAlunos(Request $request)
    {

        $valida = FuncoesController::validaSessao();

        if (isset($valida)) {
            return $valida;
        }

        $params = json_decode($request->params);

        $aluno = new Alunos(collect($params)->all());
        $aluno->load('imagem_aluno')->load('situacao_anterior')->load('situacao_matricula');
        $aluno->importaImagemAluno();

        //Se for Aluno em Situação Diversas tem que pegar de SituacoesDiversas::find($alunoSitDiv->situacoes_diversas_id)->situacao;
        //Senão SituacaoMatricula::find($aluno->id_situacao_matricula)->situacao_matricula;
        if (isset($params->situacoes_diversas_id)) {
            $situacaoAtual = SituacoesDiversas::find($params->situacoes_diversas_id)->situacao;
        } else {
            $situacaoAtual = SituacaoMatricula::find($aluno->id_situacao_matricula)->situacao_matricula;
        }

        return view('relatorios/relatorio-ficha-individual-aluno', compact('aluno', 'situacaoAtual'));
    }

    public function RelatorioFRADAlunos(Request $request)
    {

        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        $alunos = array();
        if (isset($request->omct)) {
            $where = '';
            if ($request->omct != 'todas_omct') {
                $where .= ' AND alunos.omcts_id = ' . $request->omct;
            }

            $alunos = $this->selectAlunosFrad($request->idAnoFormacao, $where);
        } else {
            array_push($alunos, (object) array('id' => $request->aluno));
        }

        $conteudoAtitudinal = ConteudoAtitudinal::all();

        $pdf = new PDF('L');
        $pdf->SetAutoPageBreak(false);

        foreach ($alunos as $key) {

            $aluno = Alunos::find($key->id);

            //Carrega os Lancamentos de FO
            $aluno->load('lancamento_fo');

            $pdf->AddPage();

            $pdf->SetFont('Times', 'B', 10);

            $pdf->SetXY(10, 11);
            $pdf->Cell(0, 4, utf8_decode('MINISTÉRIO DA DEFESA'), 0, 1, 'C', false);
            $pdf->Cell(0, 4, utf8_decode('EXÉRCITO BRASILEIRO'), 0, 1, 'C', false);
            $pdf->Cell(0, 4, 'ESCOLA DE SARGENTOS DAS ARMAS', 0, 1, 'C', false);
            $pdf->Cell(0, 4, '(ESCOLA SARGENTO MAX WOLF FILHO)', 0, 1, 'C', false);
            $pdf->SetFont('Times', 'B', 8);

            $pdf->Rect(250, 5, 25.2, 30.2);

            $pdf->Image(public_path() . '/storage/imagens_aluno/' . ((isset($aluno) && strlen($aluno->imagem_aluno->nome_arquivo) > 12) ? ($aluno->ano_formacao->formacao . '/' . $aluno->imagem_aluno->nome_arquivo) : 'no-image.jpg'), 250, 5, 25, 30);

            //Cria a Borda
            $pdf->Rect(10, 41, 278, 23);

            $pdf->Cell(0, 4, utf8_decode($aluno->omct->omct), 0, 1, 'C', false);

            $pdf->SetFont('Times', 'B', 12);
            $pdf->ln(5);
            $pdf->Cell(0, 4, utf8_decode('FICHA REGISTRO PARA ACOMPANHAMENTO DO DISCENTE'), 0, 1, 'C', false);

            $pdf->SetFont('Times', '', 10);
            //$pdf->Line(10, 41, 288, 41);
            $pdf->ln(2);
            $pdf->Cell(0, 4, utf8_decode('DADOS PESSOAIS DO DISCENTE'), 0, 1, 'L', false);
            $pdf->Cell(148, 8, utf8_decode('Nome: ' . $aluno->nome_completo), 'B', 1, 'L', false);
            $pdf->SetXY(160, 46);
            $pdf->Cell(48, 8, utf8_decode('Número: ' . $aluno->numero), 'B', 1, 'L', false);
            $pdf->SetXY(210, 46);
            $pdf->Cell(78, 8, utf8_decode('Curso: '), 'B', 1, 'L', false);

            $pdf->Cell(78, 8, utf8_decode('Pel/Turma: ' . $aluno->turma->turma), 'B', 1, 'L', false);

            //$pdf->Line(10, 65, 288, 65);

            $pdf->SetFont('Times', 'B', 10);
            $pdf->ln(5);
            $pdf->Cell(23, 7, 'Data', 1, 0, 'C');
            $pdf->Cell(80, 7, utf8_decode('Observações'), 1, 0, 'C');
            $pdf->Cell(40, 7, utf8_decode('Conteúdo Atitudinal'), 1, 0, 'C');
            $pdf->Cell(70, 7, utf8_decode('Providências e Orientações'), 1, 0, 'C');
            $pdf->Cell(25, 7, 'FO', 1, 0, 'C');
            $pdf->Cell(40, 7, utf8_decode('Observador/Orientador'), 1, 1, 'C');

            $pdf->SetFont('Times', '', 8);

            $pdf->SetWidths(array(23, 80, 40, 70, 25, 40));
            $pdf->SetAligns(array('C', 'L', 'L', 'L', 'C', 'C'));
            foreach ($aluno->lancamento_fo as $frad) {
                $fo = (($frad->tipo == 0) ? 'Negativo' : (($frad->tipo == 1) ? 'Neutro' : 'Positivo'));

                $conteudo_atitudinal = '';
                foreach (json_decode($frad->conteudo_atitudinal) as $atitudinal) {
                    foreach ($conteudoAtitudinal as $conteudo) {
                        if ($atitudinal == $conteudo->id) {
                            $conteudo_atitudinal .= $conteudo->descricao . ', ';
                        }
                    }
                }

                $conteudo_atitudinal = substr($conteudo_atitudinal, 0, (strlen($conteudo_atitudinal) - 2));
                if (isset($frad->fatdLancada)) {
                    $pdf->SetTextColor(255, 0, 0);
                }
                $pdf->Row(array(FuncoesController::formatDateEntoBr($frad->data_obs), utf8_decode($frad->observacao), utf8_decode($conteudo_atitudinal), utf8_decode($frad->providencia), $fo, utf8_decode($frad->operador->postograd->postograd_abrev . ' ' . $frad->operador->nome_guerra)));
                $pdf->SetTextColor(0, 0, 0);
            }
        }

        $pdf->Output('I', 'Ficha_FRAD.pdf');
        exit();
    }


    public function ViewRelacaoFDisciplinarAlunos(Request $request)
    {
        $anoFormacao = AnoFormacao::whereId($request->ano_formacao_id)->get()->first();

        $rota = 'relatorios.ficha-disciplinar-aluno';
        $rotaGeral = 'relatorios.ficha-disciplinar-geral';

        $idOmct = $request->omctID;

        $where = '';
        if ($request->omctID != 'todas_omct') {
            $where .= ' AND alunos.omcts_id = ' . $request->omctID;
        }

        if (isset($request->numero_aluno)) {
            $where .= ' AND alunos.numero = ' . $request->numero_aluno;
        }

        if (isset($request->nome_aluno)) {
            $where .= " AND alunos.nome_completo LIKE '%$request->nome_aluno%'";
        }

        $alunos = $this->selectAlunosFichaDisciplinar($request->ano_formacao_id, $where);

        return view('admin/consulta/relacao-ficha-disciplinar', compact('anoFormacao', 'rota', 'rotaGeral', 'alunos', 'idOmct'));
    }

    public function RelatorioFichaDisciplinarAlunos(Request $request)
    {

        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        $alunos = array();
        if (isset($request->omct)) {
            $where = '';
            if ($request->omct != 'todas_omct') {
                $where .= ' AND alunos.omcts_id = ' . $request->omct;
            }

            $alunos = $this->selectAlunosFichaDisciplinar($request->idAnoFormacao, $where);
        } else {
            array_push($alunos, (object) array('id' => $request->aluno));
        }

        $pdf = new PDF('L');
        $pdf->SetAutoPageBreak(false);

        foreach ($alunos as $key) {

            //$aluno = Alunos::find($key->id);

            $fatds = Fatd::whereHas('lancamentoFo', function ($query) use ($key) {
                $query->where(['aluno_id' => $key->id]);
            })->with('lancamentoFo')->get();

            $aluno = $fatds[0]->lancamentoFo->aluno;

            $pdf->AddPage();

            $pdf->SetFont('Times', 'B', 10);

            $pdf->SetXY(10, 11);
            $pdf->Cell(0, 4, utf8_decode('MINISTÉRIO DA DEFESA'), 0, 1, 'C', false);
            $pdf->Cell(0, 4, utf8_decode('EXÉRCITO BRASILEIRO'), 0, 1, 'C', false);
            $pdf->Cell(0, 4, 'ESCOLA DE SARGENTOS DAS ARMAS', 0, 1, 'C', false);
            $pdf->Cell(0, 4, '(ESCOLA SARGENTO MAX WOLF FILHO)', 0, 1, 'C', false);
            $pdf->SetFont('Times', 'B', 8);

            $pdf->Rect(229.6, 56.9, 36.0, 40.5);

            $pdf->Image(public_path() . '/storage/imagens_aluno/' . ((isset($aluno) && strlen($aluno->imagem_aluno->nome_arquivo) > 12) ? ($aluno->ano_formacao->formacao . '/' . $aluno->imagem_aluno->nome_arquivo) : 'no-image.jpg'), 230, 57, 35, 40);

            $pdf->Cell(0, 4, utf8_decode($aluno->omct->omct), 0, 1, 'C', false);

            $pdf->SetFont('Times', 'B', 12);
            $pdf->ln(5);
            $pdf->Cell(0, 4, utf8_decode('FICHA DISCIPLINAR INDIVIDUAL'), 0, 1, 'C', false);

            $pdf->SetFont('Times', 'B', 10);

            $pdf->ln(10);

            $pdf->Cell(0, 4, utf8_decode('1. IDENTIFICAÇÃO DO MILITAR'), 0, 1, 'L', false);

            $pdf->ln(5);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell(40, 6, utf8_decode('Número do Aluno'), 1, 0, 'L', true);
            $pdf->SetFont('Times', '', 10);
            $pdf->Cell(120, 6, utf8_decode($aluno->numero), 1, 1, 'L', true);
            $pdf->SetFont('Times', 'B', 10);
            $pdf->Cell(40, 6, utf8_decode('Nome Completo'), 1, 0, 'L', true);
            $pdf->SetFont('Times', '', 10);
            $pdf->Cell(120, 6, utf8_decode($aluno->nome_completo), 1, 1, 'L', true);
            $pdf->SetFont('Times', 'B', 10);
            $pdf->Cell(40, 6, utf8_decode('Nome do Pai'), 1, 0, 'L', true);
            $pdf->SetFont('Times', '', 10);
            $pdf->Cell(120, 6, utf8_decode($aluno->nome_pai), 1, 1, 'L', true);
            $pdf->SetFont('Times', 'B', 10);
            $pdf->Cell(40, 6, utf8_decode('Nome da Mãe'), 1, 0, 'L', true);
            $pdf->SetFont('Times', '', 10);
            $pdf->Cell(120, 6, utf8_decode($aluno->nome_mae), 1, 1, 'L', true);
            $pdf->SetFont('Times', 'B', 10);
            $pdf->Cell(40, 6, utf8_decode('Nº Identidade Militar'), 1, 0, 'L', true);
            $pdf->SetFont('Times', '', 10);
            $pdf->Cell(120, 6, utf8_decode($aluno->doc_idt_militar), 1, 1, 'L', true);
            $pdf->SetFont('Times', 'B', 10);
            $pdf->Cell(40, 6, utf8_decode('Prec-CP'), 1, 0, 'L', true);
            $pdf->SetFont('Times', '', 10);
            $pdf->Cell(120, 6, utf8_decode($aluno->doc_preccp), 1, 1, 'L', true);

            $pdf->ln(10);

            $pdf->SetFont('Times', 'B', 10);
            $pdf->Cell(0, 4, utf8_decode('2. PUNIÇÕES DISCIPLINARES'), 0, 1, 'L', false);

            $pdf->SetFont('Times', 'B', 8);
            $pdf->ln(5);
            $pdf->Cell(14, 7, utf8_decode('Nr Punição'), 1, 0, 'C');
            $pdf->Cell(13, 7, utf8_decode('Data'), 1, 0, 'C');
            $pdf->Cell(40, 7, utf8_decode('Punição (art.24 do RDE)'), 1, 0, 'C');
            $pdf->Cell(10, 7, utf8_decode('Nr Dias'), 1, 0, 'C');
            $pdf->Cell(100, 7, utf8_decode('Enquadramento (Anexo I do RDE)'), 1, 0, 'C');
            $pdf->Cell(70, 7, utf8_decode('BI e OM'), 1, 0, 'C');
            $pdf->Cell(25, 7, utf8_decode('Comportamento'), 1, 1, 'C');

            $pdf->SetFont('Times', '', 6);

            $pdf->SetWidths(array(14, 13, 40, 10, 100, 70, 25));
            $pdf->SetAligns(array('C', 'L', 'C', 'C', 'L', 'L', 'C'));

            foreach ($fatds as $fatd) {

                $justificado = (($fatd->justificado == 'N') ? 'Não' : (($fatd->justificado == 'S') ? 'Sim' : null));

                $pdf->Row(array(
                    $fatd->nr_processo, FuncoesController::formatDateEntoBr($fatd->lancamentoFO->data_obs), utf8_decode((isset($fatd->tipo_enquadramento) ? $fatd->tipo_enquadramento->enquadramento : null)), utf8_decode((isset($fatd->nr_dias) ? $fatd->nr_dias : 0)), utf8_decode($fatd->enquadramento), utf8_decode($fatd->bi_desc . ' do ' . $aluno->omct->sigla_omct), utf8_decode((isset($fatd->comportamento) ? $fatd->comportamento->comportamento : null))
                ));
            }

            $pdf->ln(10);

            $pdf->SetFont('Times', 'B', 10);
            $pdf->Cell(0, 4, utf8_decode('3. ANULAÇÃO OU CANCELAMENTO DE PUNIÇÕES DISCIPLINARES'), 0, 1, 'L', false);
            $pdf->SetFont('Times', '', 10);
            $pdf->ln(3);
            $pdf->Cell(0, 4, utf8_decode((isset($aluno->anulacaoCancelamento) ? $aluno->anulacaoCancelamento : 'Sem alterações ou cancelamentos de punições disciplinares')), 1, 1, 'L', false);
            $pdf->SetFont('Times', 'B', 8);


            $pdf->ln(10);

            $pdf->SetFont('Times', 'B', 10);
            $pdf->Cell(0, 4, utf8_decode('4. RECURSOS DISCIPLINARES'), 0, 1, 'L', false);
            $pdf->SetFont('Times', '', 10);
            $pdf->ln(3);
            $pdf->Cell(0, 4, utf8_decode((isset($aluno->recursosDisciplinares) ? $aluno->recursosDisciplinares : 'Sem recursos disciplinares')), 1, 1, 'L', false);
            $pdf->SetFont('Times', 'B', 8);

            $pdf->ln(10);

            $pdf->SetFont('Times', 'B', 10);
            $pdf->Cell(0, 4, utf8_decode('5. RECOMPENSAS'), 0, 1, 'L', false);
            $pdf->SetFont('Times', '', 10);
            $pdf->ln(3);
            $pdf->Cell(0, 4, utf8_decode((isset($aluno->recompensas) ? $aluno->recompensas : 'Sem recompensas')), 1, 1, 'L', false);
            $pdf->SetFont('Times', 'B', 8);
        }

        $pdf->Output('I', 'Ficha_Disciplinar.pdf');
        exit();
    }

    private function selectAlunosFichaDisciplinar($idAnoFormacao, $where)
    {
        return DB::select("SELECT alunos.id, alunos.numero, alunos.nome_guerra, alunos.nome_completo, alunos.data_matricula 
                                FROM alunos
                                INNER JOIN lancamento_fo ON (lancamento_fo.aluno_id = alunos.id)
                                INNER JOIN fatd ON (fatd.lancamento_fo_id = lancamento_fo.id)
                                WHERE alunos.data_matricula = $idAnoFormacao
                                $where
                                GROUP BY alunos.id");
    }

    public function ViewRelacaoAlunoUetePunido(Request $request)
    {

        $anoFormacao = AnoFormacao::whereId($request->ano_formacao_id)->get()->first();

        $rota = 'relatorios.ficha-aluno-punido';
        $rotaGeral = 'relatorios.ficha-aluno-punido-geral';
        $dadosRota = (object) array('punido' => $request->punicao, 'comportamento' => $request->comportamento);

        $idOmct = $request->omctID;

        $where = '';
        if ($request->omctID != 'todas_omct') {
            $where .= ' AND alunos.omcts_id = ' . $request->omctID;
        }

        if (isset($request->numero_aluno)) {
            $where .= ' AND alunos.numero = ' . $request->numero_aluno;
        }

        if (isset($request->nome_aluno)) {
            $where .= " AND alunos.nome_completo LIKE '%$request->nome_aluno%'";
        }

        if (isset($request->punicao)) {
            $where .= ' AND fatd.enquadramento_id = ' . $request->punicao;
        }

        if (isset($request->comportamento)) {
            $where .= ' AND fatd.comportamento_id = ' . $request->comportamento;
        }

        $alunos = $this->selectAlunosFichaDisciplinar($request->ano_formacao_id, $where);

        return view('admin/consulta/relacao-ficha-punido', compact('anoFormacao', 'rota', 'rotaGeral', 'alunos', 'idOmct', 'dadosRota'));
    }

    public function ViewRelacaoAlunoReprovado(Request $request)
    {

        /*if ($this->ownauthcontroller->PermissaoCheck(1)) {*/

            $anoFormacao = AnoFormacao::whereId($request->ano_formacao_id)->get()->first();

            $uetes = FuncoesController::retornaUetePerfil($this->ownauthcontroller);

            foreach($uetes as $uete){
                $ueteIds[] = $uete->id;
            }
            // SELECIONANDO TODAS AS AREAS
            $areas = Areas::whereNotIn('id', [4, 5])->get();

            // SELECIONANDO DISCIPLINAS
            $disciplinas = Disciplinas::where('ano_formacao_id', $anoFormacao->id)->get();

            $param['anoFormacao'] = $anoFormacao;
            $param['uetes'] = $ueteIds;

            $alunosClassificacao = AlunosClassificacao::where([['reprovado', '=', 'S']])->whereHas('aluno', function ($q) use ($param) {
                $q->where([['data_matricula', '=', $param['anoFormacao']->id]])
                    ->whereIn('omcts_id', $param['uetes']);
            })->orderBy('nota_final', 'desc')->get();

            foreach($alunosClassificacao as $nota){
                $notas_array[] = ''.$nota->nota_final.'';
                $notas_data_array[$nota->aluno_id] = array(
                    "NPB" => $nota->nota_final,
                    "data_demonstrativo" => unserialize($nota->data_demonstrativo)
                ); 
            }

            $mencoes = Mencoes::get();

            $this->classLog->RegistrarLog('Acessou o Relatório de Alunos Reprovados.', auth()->user()->email);

            return view('relatorios/relacao-aluno-reprovado', compact('anoFormacao', 'alunosClassificacao', 'areas', 'disciplinas', 'mencoes', 'notas_data_array'));

        /*} else {
            $this->classLog->RegistrarLog('Teve acesso negado ao tenter acessar o Relatório de Alunos Reprovados.', auth()->user()->email);
            return '<div style="text-align: center;">NÃO AUTORIZADO!</div>';
        }*/
    }

    public function RelatorioFichaAlunoPunidos(Request $request)
    {

        if (!is_null(FuncoesController::validaSessao())) {
            return;
        }

        $alunos = array();
        if (isset($request->omct)) {
            $where = '';
            if ($request->omct != 'todas_omct') {
                $where .= ' AND alunos.omcts_id = ' . $request->omct;
            }

            $alunos = $this->selectAlunosFichaDisciplinar($request->idAnoFormacao, $where);
        } else {
            array_push($alunos, (object) array('id' => $request->aluno));
        }

        $pdf = new PDF('L');
        $pdf->SetAutoPageBreak(true);
        $pdf->AddPage();

        $pdf->SetFont('Times', 'B', 10);

        $pdf->SetXY(10, 11);
        $pdf->Cell(0, 4, utf8_decode('MINISTÉRIO DA DEFESA'), 0, 1, 'C', false);
        $pdf->Cell(0, 4, utf8_decode('EXÉRCITO BRASILEIRO'), 0, 1, 'C', false);
        $pdf->Cell(0, 4, 'ESCOLA DE SARGENTOS DAS ARMAS', 0, 1, 'C', false);
        $pdf->Cell(0, 4, '(ESCOLA SARGENTO MAX WOLF FILHO)', 0, 1, 'C', false);

        $pdf->SetFont('Times', 'B', 12);
        $pdf->ln(5);
        $pdf->Cell(0, 4, utf8_decode('RELATÓRIO DE PUNIÇÕES DE ALUNOS NO PERÍODO BÁSICO DO CFGS'), 0, 1, 'C', false);

        $pdf->SetFont('Times', 'B', 6);
        $pdf->ln(5);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(14, 7, utf8_decode('Data'), 1, 0, 'C', 1);
        $pdf->Cell(13, 7, utf8_decode('UETE'), 1, 0, 'C', 1);
        $pdf->Cell(10, 7, utf8_decode('Número'), 1, 0, 'C', 1);
        $pdf->Cell(30, 7, utf8_decode('Nome de Guerra'), 1, 0, 'C', 1);
        $pdf->Cell(110, 7, utf8_decode('Punição'), 1, 0, 'C', 1);
        $pdf->Cell(20, 7, utf8_decode('Motivo'), 1, 0, 'C', 1);
        $pdf->Cell(10, 7, utf8_decode('Nº Dias'), 1, 0, 'C', 1);
        $pdf->Cell(40, 7, utf8_decode('Enquadramento'), 1, 0, 'C', 1);
        $pdf->Cell(10, 7, utf8_decode('BI'), 1, 0, 'C', 1);
        $pdf->Cell(20, 7, utf8_decode('Comportamento'), 1, 1, 'C', 1);

        $pdf->SetFont('Times', '', 6);

        $pdf->SetWidths(array(14, 13, 10, 30, 110, 20, 10, 40, 10, 20));
        $pdf->SetAligns(array('C', 'C', 'C', 'C', 'L', 'C'));

        $where = array();

        if (isset($request->punido)) {
            $where['enquadramento_id'] = $request->punido;
        }

        if (isset($request->comportamento)) {
            $where['comportamento_id'] = $request->comportamento;
        }

        foreach ($alunos as $key) {

            $fatds = Fatd::where($where)->whereHas('lancamentoFo', function ($query) use ($key) {
                $query->where(['aluno_id' => $key->id]);
            })->with('lancamentoFo')->get();

            $aluno = $fatds[0]->lancamentoFo->aluno;

            foreach ($fatds as $fatd) {
                $pdf->Row(array(
                    FuncoesController::formatDateEntoBr($fatd->lancamentoFO->data_obs), utf8_decode($aluno->omct->sigla_omct), $aluno->numero, utf8_decode($aluno->nome_guerra), utf8_decode((isset($fatd->tipo_enquadramento) ? $fatd->tipo_enquadramento->enquadramento : null)), null, $fatd->nr_dias, utf8_decode($fatd->enquadramento), utf8_decode($fatd->bi_desc), utf8_decode((isset($fatd->comportamento_id) ? $fatd->comportamento->comportamento : null))
                ));
            }
        }

        $pdf->Output('I', 'Ficha_Disciplinar.pdf');
        exit();
    }
}

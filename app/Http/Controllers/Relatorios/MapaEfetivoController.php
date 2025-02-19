<?php

namespace App\Http\Controllers\Relatorios;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Http\OwnClasses\ClassLog;
use App\Models\Alunos;
use App\Models\AlunosSitDiv;
use App\Models\AnoFormacao;
use App\Models\MapaOutrosDados;
use App\Models\Parametros;
use Khill\Lavacharts\Lavacharts;

class MapaEfetivoController extends Controller
{
    protected $classLog;
    protected $ownauthcontroller;

    public function __construct(OwnAuthController $ownauthcontroller, ClassLog $classLog)
    {
        $this->ownauthcontroller = $ownauthcontroller;
        $this->classLog = $classLog;
        $this->classLog->ip = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR']: null);
    }

    public function ViewMapaEfetivoGeral(Request $request)
    {
        $anoFormacao = AnoFormacao::whereId($request->id_ano_formacao)->get()->first();
        $rota = 'relatorios.relacao-mapa-efetivo-geral';

        // Repassando para a view
        return view('relatorios.mapa-efetivo-geral', compact('anoFormacao', 'rota'));
    }

    public function ViewMapaEfetivoDesligado(Request $request)
    {
        $anoFormacao = AnoFormacao::whereId($request->id_ano_formacao)->get()->first();
        $rota = 'relatorios.relacao-mapa-efetivo-desligado';

        // Repassando para a view
        return view('relatorios.mapa-efetivo-geral', compact('anoFormacao', 'rota'));
    }

    public function ViewMapaEvasaoEscolar(Request $request)
    {
        $anoFormacao = AnoFormacao::whereId($request->id_ano_formacao)->get()->first();
        $rota = 'relatorios.relacao-evasao-escolar';

        // Repassando para a view
        return view('relatorios.mapa-efetivo-geral', compact('anoFormacao', 'rota'));
    }

    public function RelacaoMapaEfetivoGeral(Request $request)
    {
        $valida = FuncoesController::validaSessao();

        if (isset($valida)) {
            return $valida;
        }

        $anoFormacao = AnoFormacao::whereId($request->ano_formacao_id)->get()->first();
        $relacao = $request->relacao;

        extract($this->infoMapaEfetivo($anoFormacao->id));

        if (session()->get('login')['omctID'] <> 1) {
            $alunosAdidos = AlunosSitDiv::where([['adido', '=', 'S']])->with('omct')->where('omcts_id', '=', session()->get('login')['omctID'])->get();
        } else {
            $alunosAdidos = AlunosSitDiv::where([['adido', '=', 'S']])->with('omct')->get();
        }

        $this->classLog->RegistrarLog('Visualizou Relatório Mapa Efetivo Geral', auth()->user()->email);

        $parametros = Parametros::where('ano_formacao_id', '=', $anoFormacao->id)->first();

        // Repassando para a view
        return view('relatorios.relacao-mapa-efetivo-geral', compact('anoFormacao', 'relacao', 'mapaEfetivo', 'alunosAdidos', 'evasaoUete', 'parametros'));
    }

    public function RelacaoMapaEfetivoDesligado(Request $request)
    {
        $valida = FuncoesController::validaSessao();

        if (isset($valida)) {
            return $valida;
        }

        $anoFormacao = AnoFormacao::whereId($request->ano_formacao_id)->get()->first();
        $relacao = $request->relacao;

        extract($this->resumoDesligamentoUetes($anoFormacao->id));

        $totalizadores = (object) [
            'total' => 'TOTAL', 'totDesligamentoApedido' => 0, 'totDesligamentoExOficio' => 0, 'totTrancamentoApedido' => 0, 'totTrancamentoExOficio' => 0, 'totTotal' => 0
        ];

        foreach ($resumoUetes as $uete) {
            $totalizadores->totDesligamentoApedido = ($totalizadores->totDesligamentoApedido + $uete->desligamentoApedido);
            $totalizadores->totDesligamentoExOficio = ($totalizadores->totDesligamentoExOficio + $uete->desligamentoExOficio);
            $totalizadores->totTrancamentoApedido = ($totalizadores->totTrancamentoApedido + $uete->trancamentoApedido);
            $totalizadores->totTrancamentoExOficio = ($totalizadores->totTrancamentoExOficio + $uete->trancamentoExOficio);
            $totalizadores->totTotal = ($totalizadores->totTotal + $uete->total);
        }

        array_push($resumoUetes, $totalizadores);

        $this->classLog->RegistrarLog('Visualizou Relatório Mapa Efetivo Desligado', auth()->user()->email);

        // Repassando para a view
        return view('relatorios.relacao-mapa-efetivo-desligado', compact('anoFormacao', 'relacao', 'alunosSituacoesDiversas', 'resumoUetes'));
    }

    public function RelacaoMapaEvasaoEscolar(Request $request)
    {
        $valida = FuncoesController::validaSessao();

        if (isset($valida)) {
            return $valida;
        }

        $anoFormacao = AnoFormacao::whereId($request->ano_formacao_id)->get()->first();
        $relacao = $request->relacao;

        extract($this->resumoDesligamentoUetes($anoFormacao->id));

        extract($this->infoMapaEfetivo($anoFormacao->id));

        //Inicia geração do gráfico
        $lava = new Lavacharts();
        $graficoComparacaoEvasao = $lava->DataTable();
        $graficoComparacaoEvasao->addStringColumn('UETE')->addNumberColumn('Evasão Escolar');

        foreach ($evasaoUete as $evasao) {
            if ($evasao->sigla_omct != 'TOTAL') {
                $graficoComparacaoEvasao->addRow([$evasao->sigla_omct, $evasao->quantidadeEvasao]);
            }
        }

        $lava->ColumnChart(
            'Evasao Escolar',
            $graficoComparacaoEvasao,
            [
                'title' => 'Comparativo de Evasão Escolar por UETE',
                'titleTextStyle' => [
                    'color'    => '#eb6b2c',
                    'fontSize' => 14
                ]
            ]
        );
        //Fim geração gráfico

        $this->classLog->RegistrarLog('Visualizou Relatório de Evasão Escolar', auth()->user()->email);

        // Repassando para a view
        return view('relatorios.relacao-mapa-evasao-escolar', compact('anoFormacao', 'relacao', 'resumoUetes', 'evasaoUete', 'mapaEfetivo', 'lava'));
    }

    private function resumoDesligamentoUetes(int $idAnoFormacao)
    {

        if ((session()->get('login')['omctID'] <> 1)) {
            $alunosSituacoesDiversas = AlunosSitDiv::where([
                                                                ['data_matricula', '=', $idAnoFormacao]
                                                              , ['omcts_id', '=', session()->get('login')['omctID']]
                                                          ])
                                                    ->with('omct')
                                                    ->with('situacao')
                                                    ->with('motivos')
                                                    ->with('situacaoDivHistorico')
                                                    ->whereNotNull('solicitacao_situacao')
                                                    //->whereNull('qms_id')
                                                    ->orderBy('omcts_id', 'asc')->get();

        } else {
            $alunosSituacoesDiversas = AlunosSitDiv::where([['data_matricula', '=', $idAnoFormacao]])
                                                    ->with('omct')
                                                    ->with('situacao')
                                                    ->with('motivos')
                                                    ->with('situacaoDivHistorico')
                                                    ->whereNotNull('solicitacao_situacao')
                                                    //->whereNull('qms_id')
                                                    ->orderBy('omcts_id', 'asc')->get();

/*
FuncoesController::getSQLEloquent(AlunosSitDiv::where([['data_matricula', '=', $idAnoFormacao]])
->with('omct')
->with('situacao')
->with('motivos')
->with('situacaoDivHistorico')
->whereNotNull('solicitacao_situacao')
//->whereNull('qms_id')
->orderBy('omcts_id', 'asc'));
*/
        }

        $resumoUetes = array();
        foreach ($alunosSituacoesDiversas as $value) {

            $data = unserialize($value->situacaoDivHistorico->data);

            //Senão tiver turma no segundo ano
            //if(!isset($data['turma_esa_id'])){

                if (!isset($resumoUetes[$value->omct->id])) {
                    $resumoUetes[$value->omct->id] = (object) array(
                        'sigla_uete' => $value->omct->omct,
                        'desligamentoApedido' => 0,
                        'desligamentoExOficio' => 0,
                        'trancamentoApedido' => 0,
                        'trancamentoExOficio' => 0,
                        'total' => 0
                    );
                }
    
                switch ($value->situacoes_diversas_id) {
                    case 1: //Trancamento
                        switch ($value->solicitacao_situacao) {
                            case 'AP': //A pedido
                                $resumoUetes[$value->omct->id]->trancamentoApedido++;
                                break;
                            case 'EO': //Ex Oficio
                                $resumoUetes[$value->omct->id]->trancamentoExOficio++;
                                break;
                        }
                        break;
                    case 3: //Desligamento
                        switch ($value->solicitacao_situacao) {
                            case 'AP': //A pedido
                                $resumoUetes[$value->omct->id]->desligamentoApedido++;
                                break;
                            case 'EO': //Ex Oficio
                                $resumoUetes[$value->omct->id]->desligamentoExOficio++;
                                break;
                        }
                        break;
                }
    
                $resumoUetes[$value->omct->id]->total = ((($resumoUetes[$value->omct->id]->trancamentoApedido + $resumoUetes[$value->omct->id]->trancamentoExOficio) + $resumoUetes[$value->omct->id]->desligamentoApedido) + $resumoUetes[$value->omct->id]->desligamentoExOficio);                
            //}
        }

        $retorno = array('alunosSituacoesDiversas' => $alunosSituacoesDiversas, 'resumoUetes' => $resumoUetes);
        return $retorno;
    }

    public function infoMapaEfetivo(int $idAnoFormacao)
    {
        $alunos = Alunos::carregaAlunosVsAlunosSitDiv($idAnoFormacao);

        if ((session()->get('login')['omctID'] <> 1)) {
            $mapaEfetivo = MapaOutrosDados::where('ano_formacao_id', '=', $idAnoFormacao)->where('omct_id', '=', session()->get('login')['omctID'])->get();
        } else {
            $mapaEfetivo = MapaOutrosDados::where('ano_formacao_id', '=', $idAnoFormacao)->get();
        }

        $evasaoUete = array();

        $contador = count($mapaEfetivo);
        for ($i = 0; $i < $contador; $i++) {

            $alunosFiltro = Alunos::filtraAlunosOmctAreaSeg($alunos, $mapaEfetivo[$i]->omct_id, $mapaEfetivo[$i]->area_id, $mapaEfetivo[$i]->sexo);
            
            $alunosSituacoesDiversas = AlunosSitDiv::where([['data_matricula', '=', $idAnoFormacao], ['omcts_id', '=', $mapaEfetivo[$i]->omct_id], ['area_id', '=', $mapaEfetivo[$i]->area_id], ['sexo', '=', $mapaEfetivo[$i]->sexo]])->get();

            $mapaEfetivo[$i]->pb_om_sigla = $mapaEfetivo[$i]->uete->sigla_omct;
            $mapaEfetivo[$i]->area = $mapaEfetivo[$i]->area->area;
            $mapaEfetivo[$i]->previsto_mtcl = $mapaEfetivo[$i]->qtdade_previstomtcl;
            $mapaEfetivo[$i]->qtdade_designadomtcl = $mapaEfetivo[$i]->qtdade_designadomtcl;
            $mapaEfetivo[$i]->qtdade_adiamentomtcl = $mapaEfetivo[$i]->qtdade_adiamentomtcl;

            $mapaEfetivo[$i]->em_1mtcl = 0;
            $mapaEfetivo[$i]->em_mtcl_adiam = 0;
            $mapaEfetivo[$i]->em_2mtcl = 0;
            $mapaEfetivo[$i]->em_repetente = 0;
            $mapaEfetivo[$i]->em_mtcl_ord_jud = 0;

            foreach ($alunosFiltro as $aluno) {
                switch ($aluno->id_situacao_matricula) {
                    case 100:
                        $mapaEfetivo[$i]->em_1mtcl++;
                        break;
                    case 101:
                        $mapaEfetivo[$i]->em_mtcl_adiam++;
                        break;
                    case 102:
                        $mapaEfetivo[$i]->em_2mtcl++;
                        break;
                    case 103:
                        $mapaEfetivo[$i]->em_mtcl_ord_jud++;
                        break;
                    case 104:
                        $mapaEfetivo[$i]->em_repetente++;
                        break;
                }
            }

            $mapaEfetivo[$i]->desl_apedido = 0;
            $mapaEfetivo[$i]->desl_ExOf = 0;
            $mapaEfetivo[$i]->tranc_apedido = 0;
            $mapaEfetivo[$i]->tranc_ExOf = 0;

            foreach ($alunosSituacoesDiversas as $situacoesDiversas) {
                switch ($situacoesDiversas->situacoes_diversas_id) {
                    case 1: //Trancamento
                        switch ($situacoesDiversas->solicitacao_situacao) {
                            case 'AP': //A pedido
                                $mapaEfetivo[$i]->tranc_apedido++;
                                break;
                            case 'EO': //Ex Oficio
                                $mapaEfetivo[$i]->tranc_ExOf++;
                                break;
                        }
                        break;
                    case 3: //Desligamento
                        switch ($situacoesDiversas->solicitacao_situacao) {
                            case 'AP': //A pedido
                                $mapaEfetivo[$i]->desl_apedido++;
                                break;
                            case 'EO': //Ex Oficio
                                $mapaEfetivo[$i]->desl_ExOf++;
                                break;
                        }
                        break;
                }
            }

            $mapaEfetivo[$i]->em_total = ((($mapaEfetivo[$i]->em_1mtcl + $mapaEfetivo[$i]->em_mtcl_adiam) + $mapaEfetivo[$i]->em_2mtcl) + $mapaEfetivo[$i]->em_mtcl_ord_jud);
            $mapaEfetivo[$i]->ed_total = ((($mapaEfetivo[$i]->desl_apedido + $mapaEfetivo[$i]->desl_ExOf) + $mapaEfetivo[$i]->tranc_apedido) + $mapaEfetivo[$i]->tranc_ExOf);

            $mapaEfetivo[$i]->qtdade_pqessa = 0;
            $mapaEfetivo[$i]->qtdade_pqesslog = 0;
            $mapaEfetivo[$i]->qtdade_pqciavex = 0;
            $mapaEfetivo[$i]->ee_cfs = $mapaEfetivo[$i]->em_total - $mapaEfetivo[$i]->ed_total;

            if (!isset($evasaoUete[$mapaEfetivo[$i]->uete->id])) {
                $evasaoUete[$mapaEfetivo[$i]->uete->id] = (object) array(
                    'sigla_omct' => $mapaEfetivo[$i]->pb_om_sigla,
                    'quantidadeEvasao' => 0,
                    'quantidadeEvasaoPorCent' => 0,
                    'totalEvasao' => 0,
                    'totalEvasaoPorc' => 0
                );
            }

            $evasaoUete[$mapaEfetivo[$i]->uete->id]->quantidadeEvasao += $mapaEfetivo[$i]->ed_total;
        }

        $totalizadores = (object) [
            'totOmct' => 'TODAS', 'totArea' => 'TODAS', 'totSexo' => 'TODOS', 'totPrevisto' => 0, 'totDesMtr' => 0, 'totAdiMtr' => 0, 'tot1Mtr' => 0, 'tot2Mtr' => 0, 'totRepetente' => 0, 'totMtrPorAdi' => 0, 'totMtrOrdJud' => 0, 'totEfeMtr' => 0, 'totDeslPed' => 0, 'totDeslExOf' => 0, 'totTrancMtrPed' => 0, 'totTrancMtrExOf' => 0, 'totEfetDesl' => 0, 'totMtrEsa' => 0, 'totMtrEsSLog' => 0, 'totMtrCIAvEx' => 0, 'totEECFS' => 0
        ];

        foreach ($mapaEfetivo as $informacao) {
            $totalizadores->totPrevisto = ($totalizadores->totPrevisto + $informacao->qtdade_previstomtcl);
            $totalizadores->totDesMtr = ($totalizadores->totDesMtr + $informacao->qtdade_designadomtcl);
            $totalizadores->totAdiMtr = ($totalizadores->totAdiMtr + $informacao->qtdade_adiamentomtcl);
            $totalizadores->tot1Mtr = ($totalizadores->tot1Mtr + $informacao->em_1mtcl);
            $totalizadores->tot2Mtr = ($totalizadores->tot2Mtr + $informacao->em_2mtcl);
            $totalizadores->totRepetente = ($totalizadores->totRepetente + $informacao->em_repetente);
            $totalizadores->totMtrPorAdi = ($totalizadores->totMtrPorAdi + $informacao->em_mtcl_adiam);
            $totalizadores->totMtrOrdJud = ($totalizadores->totMtrOrdJud + $informacao->em_mtcl_ord_jud);
            $totalizadores->totEfeMtr = ($totalizadores->totEfeMtr + $informacao->em_total);
            $totalizadores->totDeslPed = ($totalizadores->totDeslPed + $informacao->desl_apedido);
            $totalizadores->totDeslExOf = ($totalizadores->totDeslExOf + $informacao->desl_ExOf);
            $totalizadores->totTrancMtrPed = ($totalizadores->totTrancMtrPed + $informacao->tranc_apedido);
            $totalizadores->totTrancMtrExOf = ($totalizadores->totTrancMtrExOf + $informacao->tranc_ExOf);
            $totalizadores->totEfetDesl = ($totalizadores->totEfetDesl + $informacao->ed_total);
            $totalizadores->totMtrEsa = ($totalizadores->totMtrEsa + $informacao->qtdade_pqessa);
            $totalizadores->totMtrEsSLog = ($totalizadores->totMtrEsSLog + $informacao->qtdade_pqesslog);
            $totalizadores->totMtrCIAvEx = ($totalizadores->totMtrCIAvEx + $informacao->qtdade_pqciavex);
            $totalizadores->totEECFS = ($totalizadores->totEECFS + $informacao->ee_cfs);
        }

        $mapaEfetivo->push($totalizadores);

        $totalQuantidadeEvasao = 0;
        $totalQuantidadeEvasaoPorc = 0;
        foreach ($evasaoUete as $evasao) {
            $evasao->quantidadeEvasaoPorCent = number_format(($evasao->quantidadeEvasao / $totalizadores->totEfeMtr) * 100, 2, ',', '');
            $evasao->totalEvasao += $evasao->quantidadeEvasao;

            $totalQuantidadeEvasao = $totalQuantidadeEvasao + $evasao->quantidadeEvasao;
            //$totalQuantidadeEvasaoPorc = $totalQuantidadeEvasaoPorc + $evasao->quantidadeEvasaoPorCent;
        }

        $evasaoUete[9999] = (object) array(
            'sigla_omct' => 'TOTAL',
            'quantidadeEvasao' => $totalQuantidadeEvasao,
            'quantidadeEvasaoPorCent' => 100,
            'totalEvasao' => 0,
            'totalEvasaoPorc' => 0
        );

        $retorno = array('mapaEfetivo' => $mapaEfetivo, 'evasaoUete' => $evasaoUete);
        return $retorno;
    }
}

<?php

namespace App\Http\Controllers\Relatorios;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Http\OwnClasses\ClassLog;
use App\Models\Alunos;
use App\Models\AlunosDependente;
use App\Models\AnoFormacao;
use App\Models\Escolaridade;
use App\Models\EstadoCivil;
use App\Models\OMCT;
use App\Models\Profissao;
use App\Models\Raca;
use App\Models\Religiao;
use App\Models\Renda;
use App\Models\SituacaoAnterior;
use App\Models\SituacaoMatricula;
use App\Models\Uf;

class DadosEstatisticosGeraisController extends Controller
{
    protected $classLog;
    protected $ownauthcontroller;

    public function __construct(OwnAuthController $ownauthcontroller, ClassLog $classLog)
    {
        $this->ownauthcontroller = $ownauthcontroller;
        $this->classLog = $classLog;
        $this->classLog->ip = $_SERVER['REMOTE_ADDR'];
    }

    public function ViewDadosEstatisticosGerais(Request $request)
    {
        $anoFormacao = AnoFormacao::whereId($request->id_ano_formacao)->get()->first();

        if ($this->ownauthcontroller->PermissaoCheck(1)) {
            $uetes = OMCT::where('id', '<>', 1)->get(); //Remove a ESA
        } else {
            $uetes = OMCT::where('id', session()->get('login.omctID'))->get();
        }

        $rota = 'relatorios.relacao-dados-estatisticos-geral';

        // Repassando para a view
        return view('relatorios.dados-estatisticos-gerais', compact('anoFormacao', 'rota', 'uetes'))->with('ownauthcontroller', $this->ownauthcontroller);
    }

    public function RelacaoEstatistica(Request $request)
    {
        $valida = FuncoesController::validaSessao();

        if (isset($valida)) {
            return $valida;
        }

        $anoFormacao = AnoFormacao::whereId($request->ano_formacao_id)->get()->first();

        $alunos = Alunos::carregaAlunosVsAlunosSitDiv($anoFormacao->id);

        if ($request->omctID == 'todas_omct') {
            $siglaUete = 'TODAS UETES';
        } else {
            $uete = OMCT::where('id', $request->omctID)->get()->first();
            $siglaUete = $uete->sigla_omct;

            $alunos = Alunos::filtraAlunosOmct($alunos, $uete->id);
        }

        foreach ($alunos as $aluno) {

            if (isset($aluno->omcts_id)) { //Se existir UETE cadastrado
                $idSituacaoAnterior = md5($aluno->id_situacao_matricula . '_' . $aluno->id_situacao_anterior);
                //a. Efetivo total (no ato da matrícula)
                if (($aluno->id_situacao_matricula != null) && !isset($efetivoTotal[$aluno->id_situacao_matricula])) {
                    $situacaoMatricula = SituacaoMatricula::find($aluno->id_situacao_matricula);

                    $efetivoTotal[$aluno->id_situacao_matricula] = (object) ['situacao_matricula' => $situacaoMatricula['situacao_matricula'], 'quantidade' => 0];
                }

                //b. Situação Anterior (no ato da matrícula)
                if (($aluno->id_situacao_anterior != null) && ($aluno->id_situacao_matricula != null) && !isset($efetivoSituacaoAnterior[$idSituacaoAnterior])) {
                    $situacaoAnterior = SituacaoAnterior::find($aluno->id_situacao_anterior);

                    $efetivoSituacaoAnterior[$idSituacaoAnterior] = (object) ['situacao_matricula' => $situacaoMatricula['situacao_matricula'], 'situacao_anterior' => $situacaoAnterior['situacao_anterior'], 'quantidade' => 0];
                }

                //c. Grau de escolaridade dos alunos (no ato da matrícula)
                if (($aluno->id_escolaridade != null) && !isset($efetivoEscolaridade[$aluno->id_escolaridade])) {
                    $escolaridade = Escolaridade::find($aluno->id_escolaridade);

                    $efetivoEscolaridade[$aluno->id_escolaridade] = (object) ['grau_escolaridade' => $escolaridade['escolaridade'], 'quantidade' => 0];
                }

                //d. Naturalidade de alunos (no ato da matrícula)
                if (($aluno->nasc_id_uf != null) && !isset($efetivoNaturalidade[$aluno->nasc_id_uf])) {
                    $uf = Uf::find($aluno->nasc_id_uf);

                    if (($uf->uf_regiao != null) && !isset($efetivoNaturalRegiao[$uf->uf_regiao])) {
                        $efetivoNaturalRegiao[$uf->uf_regiao] = (object) ['regiao' => $uf['uf_regiao'], 'quantidade' => 0];
                    }
                    $efetivoNaturalidade[$aluno->nasc_id_uf] = (object) ['estado' => $uf['uf_sigla'], 'quantidade' => 0];
                }

                $idade = FuncoesController::retornaIdadePelaDataNascimento($aluno->data_nascimento);
                //e. Faixa etária de alunos (na data de matrícula)
                if (isset($aluno->data_nascimento) && !isset($efetivoFaixaEtaria[$idade])) {
                    $efetivoFaixaEtaria[$idade] = (object) ['idade' => $idade, 'quantidade' => 0];
                }

                $religiao = Religiao::find($aluno->id_religiao);
                //f. Religião de alunos (no ato da matrícula)
                if (isset($aluno->id_religiao) && !isset($efetivoReligiao[$religiao->categoria])) {

                    $efetivoReligiao[$religiao->categoria] = (object) ['religiao' => $religiao->categoria, 'quantidade' => 0];
                }

                //g. Renda familiar de alunos (no ato da matrícula)
                if (isset($aluno->id_renda) && !isset($efetivoRenda[$aluno->id_renda])) {
                    $renda = Renda::find($aluno->id_renda);

                    $efetivoRenda[$aluno->id_renda] = (object) ['renda' => $renda->descricao_renda, 'quantidade' => 0];
                }

                //h. Profissão paterna de alunos (no ato da matrícula)
                if (isset($aluno->id_profissao_pai) && !isset($efetivoProfissaoPai[$aluno->id_profissao_pai])) {
                    $profissao = Profissao::find($aluno->id_profissao_pai);

                    $efetivoProfissaoPai[$aluno->id_profissao_pai] = (object) ['profissao' => $profissao->profissao, 'situacaoMilitar' => $profissao->militar_postograd, 'quantidade' => 0];
                }

                //i. Profissão materna de alunos (no ato da matrícula)
                if (isset($aluno->id_profissao_mae) && !isset($efetivoProfissaoMae[$aluno->id_profissao_mae])) {
                    $profissao = Profissao::find($aluno->id_profissao_mae);

                    $efetivoProfissaoMae[$aluno->id_profissao_mae] = (object) ['profissao' => $profissao->profissao, 'situacaoMilitar' => $profissao->militar_postograd, 'quantidade' => 0];
                }

                //j. Estado civil de alunos (no ato da matrícula)
                if (isset($aluno->id_estado_civil) && !isset($efetivoEstadoCivil[$aluno->id_estado_civil])) {
                    $estadoCivil = EstadoCivil::find($aluno->id_estado_civil);

                    $efetivoEstadoCivil[$aluno->id_estado_civil] = (object) ['estadoCivil' => $estadoCivil->estado_civil, 'quantidade' => 0];
                }

                $alunosDependente = AlunosDependente::where('id_aluno', $aluno->id)->with('parentesco')->get();
                //k. Dependentes de alunos (no ato da matrícula)
                foreach ($alunosDependente as $dependente) {
                    if (isset($dependente->id_parentesco) && !isset($efetivoDependentes[$dependente->id_parentesco])) {
                        $efetivoDependentes[$dependente->id_parentesco] = (object) ['parentesco' => $dependente->parentesco->parentesco, 'quantidade' => 0];
                    }
                }

                //l. Cor/Raça (no ato da matrícula)
                if (isset($aluno->id_raca) && !isset($efetivoSegmento[$aluno->id_raca])) {
                    $segmento = Raca::find($aluno->id_raca);

                    $efetivoSegmento[$aluno->id_raca] = (object) ['segmento' => $segmento->descricao, 'quantidade' => 0];
                }


                if (isset($aluno->id_situacao_matricula)) {
                    $efetivoTotal[$aluno->id_situacao_matricula]->quantidade++;

                    if (isset($aluno->id_situacao_anterior) && isset($aluno->id_situacao_matricula)) {
                        $efetivoSituacaoAnterior[$idSituacaoAnterior]->quantidade++;
                    }
                    if (isset($aluno->id_escolaridade)) {
                        $efetivoEscolaridade[$aluno->id_escolaridade]->quantidade++;
                    }
                    if (isset($aluno->nasc_id_uf)) {
                        $efetivoNaturalidade[$aluno->nasc_id_uf]->quantidade++;

                        $efetivoNaturalRegiao[Uf::find($aluno->nasc_id_uf)->uf_regiao]->quantidade++;
                    }
                    if (isset($aluno->data_nascimento)) {
                        $efetivoFaixaEtaria[$idade]->quantidade++;
                    }
                    if (isset($aluno->id_religiao)) {
                        $efetivoReligiao[$religiao->categoria]->quantidade++;
                    }
                    if (isset($aluno->id_renda)) {
                        $efetivoRenda[$aluno->id_renda]->quantidade++;
                    }
                    if (isset($aluno->id_profissao_pai)) {
                        $efetivoProfissaoPai[$aluno->id_profissao_pai]->quantidade++;
                    }
                    if (isset($aluno->id_profissao_mae)) {
                        $efetivoProfissaoMae[$aluno->id_profissao_mae]->quantidade++;
                    }
                    if (isset($aluno->id_estado_civil)) {
                        $efetivoEstadoCivil[$aluno->id_estado_civil]->quantidade++;
                    }
                    foreach ($alunosDependente as $dependente) {
                        if (isset($dependente->id_parentesco)) {
                            $efetivoDependentes[$dependente->id_parentesco]->quantidade++;
                        }
                    }
                    if (isset($aluno->id_raca)) {
                        $efetivoSegmento[$aluno->id_raca]->quantidade++;
                    }
                }
            }
        }

        sort($efetivoFaixaEtaria);

        /* Totalizadores */
        $total = (object) ['descricao' => 'TODAS', 'totQuantidade' => 0];

        foreach ($efetivoTotal as $efetivo) {
            $total->totQuantidade += $efetivo->quantidade;
        }

        $efetivoTotal[9999] = $total;

        $total = (object) ['descricao' => 'TODAS', 'totQuantidade' => 0];

        foreach ($efetivoSituacaoAnterior as $efetivo) {
            $total->totQuantidade += $efetivo->quantidade;
        }

        $efetivoSituacaoAnterior[9999] = $total;

        $total = (object) ['descricao' => 'TOTAL', 'totQuantidade' => 0];

        foreach ($efetivoEscolaridade as $efetivo) {
            $total->totQuantidade += $efetivo->quantidade;
        }

        $efetivoEscolaridade[9999] = $total;

        $total = (object) ['descricao' => 'TOTAL', 'totQuantidade' => 0];

        foreach ($efetivoNaturalidade as $efetivo) {
            $total->totQuantidade += $efetivo->quantidade;
        }

        $efetivoNaturalidade[9999] = $total;

        $total = (object) ['descricao' => 'TOTAL', 'totQuantidade' => 0];

        foreach ($efetivoNaturalRegiao as $efetivo) {
            $total->totQuantidade += $efetivo->quantidade;
        }

        $efetivoNaturalRegiao[9999] = $total;

        $total = (object) ['descricao' => 'TOTAL', 'totQuantidade' => 0];

        foreach ($efetivoFaixaEtaria as $efetivo) {
            $total->totQuantidade += $efetivo->quantidade;
        }

        $efetivoFaixaEtaria[9999] = $total;

        $total = (object) ['descricao' => 'TOTAL', 'totQuantidade' => 0];

        foreach ($efetivoReligiao as $efetivo) {
            $total->totQuantidade += $efetivo->quantidade;
        }

        $efetivoReligiao[9999] = $total;

        $total = (object) ['descricao' => 'TOTAL', 'totQuantidade' => 0];

        foreach ($efetivoRenda as $efetivo) {
            $total->totQuantidade += $efetivo->quantidade;
        }

        $efetivoRenda[9999] = $total;

        $total = (object) ['descricao' => 'TODAS', 'totQuantidade' => 0];

        foreach ($efetivoProfissaoPai as $efetivo) {
            $total->totQuantidade += $efetivo->quantidade;
        }

        $efetivoProfissaoPai[9999] = $total;

        $total = (object) ['descricao' => 'TODAS', 'totQuantidade' => 0];

        foreach ($efetivoProfissaoMae as $efetivo) {
            $total->totQuantidade += $efetivo->quantidade;
        }

        $efetivoProfissaoMae[9999] = $total;

        $total = (object) ['descricao' => 'TOTAL', 'totQuantidade' => 0];

        foreach ($efetivoEstadoCivil as $efetivo) {
            $total->totQuantidade += $efetivo->quantidade;
        }

        $efetivoEstadoCivil[9999] = $total;

        $total = (object) ['descricao' => 'TOTAL', 'totQuantidade' => 0];

        if (isset($efetivoDependentes)) {
            foreach ($efetivoDependentes as $efetivo) {
                $total->totQuantidade += $efetivo->quantidade;
            }
        }

        $efetivoDependentes[9999] = $total;

        $total = (object) ['descricao' => 'TOTAL', 'totQuantidade' => 0];

        foreach ($efetivoSegmento as $efetivo) {
            $total->totQuantidade += $efetivo->quantidade;
        }

        $efetivoSegmento[9999] = $total;

        /* Fim Totalizadores */

        $this->classLog->RegistrarLog('Visualizou Relatório de Dados Estatísticos Gerais.', auth()->user()->email);

        // Repassando para a view
        return view('relatorios.relacao-dados-estatisticos-gerais', compact(
            'anoFormacao',
            'siglaUete',
            'efetivoTotal',
            'efetivoSituacaoAnterior',
            'efetivoEscolaridade',
            'efetivoNaturalidade',
            'efetivoNaturalRegiao',
            'efetivoFaixaEtaria',
            'efetivoReligiao',
            'efetivoRenda',
            'efetivoProfissaoPai',
            'efetivoProfissaoMae',
            'efetivoEstadoCivil',
            'efetivoDependentes',
            'efetivoSegmento'
        ));
    }
}

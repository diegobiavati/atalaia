<?php

namespace App\Http\Controllers\Relatorios;

/* MODELS */

//use App\Models\AnoFormacao;
use App\Models\Alunos;
use App\Models\AlunosClassificacao;
use App\Models\AlunosConselhoEscolar;
use App\Models\AlunosSitDivHistorico;
use App\Models\AlunosSitDiv;
use App\Models\AlunosVoluntAv;
use App\Models\AnoFormacao;
use App\Models\Areas;
use App\Models\AvaliacaoTaf;
use App\Models\Avaliacoes;
use App\Models\AvaliacoesNotas;
use App\Models\AvaliacoesProntoFaltas;
use App\Models\AvaliacoesProntoFaltasStatus;
use App\Models\Disciplinas;
use App\Models\EscolhaQMSAlunosOpcoes;
use App\Models\Mencoes;
use App\Models\OMCT;
use App\Models\QMS;
use App\Models\TelegramAlunoAuth;
use App\Models\ConfDemonstrativos;

/* CONTROLLERS */

use App\Http\Controllers\OwnAuthController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utilitarios\FuncoesController;
use Illuminate\Support\Facades\DB;
use Khill\Lavacharts\Lavacharts;


class AnalisesNotasController extends Controller
{
    public function AnaliseParcialProvas(Request $request){

        $ano_formacao = AnoFormacao::find($request->ano_formacao_id);
        $ano_selecionado = (isset($ano_formacao->formacao))? $ano_formacao->formacao:'---';

        //RECUPERANDO OS DADOS DA AVALIAÇÃO

        $avaliacao = Avaliacoes::find($request->avaliacaoID);

        list($ano,$mes,$dia) = explode('-', $avaliacao->data);

        $avaliacao_data = array(
            "disciplina" => $avaliacao->disciplinas->nome_disciplina,
            "nome" => $avaliacao->nome_completo,
            "data" => $dia.'/'.$mes.'/'.$ano,
            "periodo" => 'Básico'
        );
        
        // SELECIONANDO ALUNOS

        if($request->omctID=='todas_omct'){
            $alunosIDs = Alunos::where('data_matricula', $request->ano_formacao_id)->get(['id']);
            $omct = 'TODAS';
        } else {
            $alunosIDs = Alunos::where('data_matricula', $request->ano_formacao_id)->where('omcts_id', $request->omctID)->get(['id']);
            $omct = OMCT::find($request->omctID);
            $omct = $omct->sigla_omct;
        }

        
        if($alunosIDs){
            
            // CONTANDO ALUNOS QUE FALTARAM ESTA AVALIAÇÃO
            $avaliacao_data['faltas'] = AvaliacoesProntoFaltas::whereIn('aluno_id', $alunosIDs)->where('avaliacao_id', $request->avaliacaoID)->count();

            $mencoes = Mencoes::get();
            $avaiacoes_notas = AvaliacoesNotas::whereIn('alunos_id', $alunosIDs)->where('avaliacao_id', $request->avaliacaoID)->get();

            $avaliacoes_notas_novo = FuncoesController::recalculaNotaAluno($avaiacoes_notas);

            foreach($avaliacoes_notas_novo as $key => $aval_nota){
                if($key != 'alunosID'){
                    if($aval_nota['media_disciplina'] > 0){
                        $avaliacao_data['media_aritmetica'] = number_format($aval_nota['media_disciplina'], 3, ',', '');
                        $avaliacao_data['maior'] = $aval_nota['max_disciplina'];
                        $avaliacao_data['menor'] = $aval_nota['min_disciplina'];
    
                        foreach($aval_nota as $keyInfo => $info){
    
                            if(is_numeric($keyInfo)){
                                if($info['media']>=5){
                                    $com_media[] = 1;
                                } else {
                                    $sem_media[] = 1;
                                }
                                foreach($mencoes as $menc){
                                    if($info['media']>=$menc->inicio && $info['media']<=$menc->fim) {
                                        $mencao[$menc->mencao][] = 1;
                                    }
                                }    
    
                            }
                        }
                    }else{
                        $avaliacao_data['media_aritmetica'] = number_format($aval_nota['media_disciplina_s_peso'], 3, ',', '');
                        $avaliacao_data['maior'] = $aval_nota['max_disciplina_s_peso'];
                        $avaliacao_data['menor'] = $aval_nota['min_disciplina_s_peso'];

                        foreach($aval_nota as $keyInfo => $info){

                            if(is_numeric($keyInfo)){
                                if($info['media_sem_peso']>=5){
                                    $com_media[] = 1;
                                } else {
                                    $sem_media[] = 1;
                                }
                                foreach($mencoes as $menc){
                                    if($info['media_sem_peso']>=$menc->inicio && $info['media_sem_peso']<=$menc->fim) {
                                        $mencao[$menc->mencao][] = 1;
                                    }
                                }    

                            }
                        }
                    }
                    
                }
            }

            $avaliacao_data['efetivo'] = $avaiacoes_notas->count();
            
            $avaliacao_data['amplitude'] = number_format(($avaliacao_data['maior'] - $avaliacao_data['menor']), 3, ',', '');
            
            $avaliacao_data['maior'] =  number_format($avaliacao_data['maior'], 3, ',', '');
            $avaliacao_data['menor'] =  number_format($avaliacao_data['menor'], 3, ',', '');

            foreach($mencoes as $menc){
                if(isset($mencao[$menc->mencao])){
                    $faixas_mencao[$menc->mencao] = array_sum($mencao[$menc->mencao]);
                }
            }

            if(isset($faixas_mencao)){
                $avaliacao_data['mencoes'] = $faixas_mencao;   
            } else {
                $avaliacao_data['mencoes'] = array(null);   
            }

            $com_media = (isset($com_media))?array_sum($com_media):0;
            $sem_media = (isset($sem_media))?array_sum($sem_media):0;

            $avaliacao_data['com_media'] = $com_media;
            $avaliacao_data['sem_media'] = $sem_media;

            // NOTAS OBTIDAS

            for($i=0;$i<=8;$i++){
                $intervalo[$i] = number_format($i, 1, ',', '').' à '.number_format(($i+0.9), 1, ',', '');
                
                foreach($avaliacoes_notas_novo as $key => $aval_nota){
                    
                    if($key != 'alunosID'){
                        if($aval_nota['media_disciplina'] > 0){
                            foreach($aval_nota as $keyInfo => $info){
                                if(is_numeric($keyInfo)){
                                    if($info['media']>=$i && $info['media']<=($i+0.999)){
                                        $notas_obtidas[$i][] = 1;    
                                    }
                                }
                            }
                        }else{
                            foreach($aval_nota as $keyInfo => $info){
                                if(is_numeric($keyInfo)){
                                    if($info['media_sem_peso']>=$i && $info['media_sem_peso']<=($i+0.999)){
                                        $notas_obtidas[$i][] = 1;    
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $intervalo[9] = "9,0 à 9,499";
            $intervalo[10] = "9,5 à 10";
            
            foreach($avaliacoes_notas_novo as $key => $aval_nota){
                if($key != 'alunosID'){
                    if($aval_nota['media_disciplina'] > 0){
                        foreach($aval_nota as $keyInfo => $info){
                            if(is_numeric($keyInfo)){
                                if($info['media']>=9 && $info['media']<=9.499){
                                    $notas_obtidas[9][] = 1;    
                                } else if($info['media']>=9.5 && $info['media']<=10){
                                    $notas_obtidas[10][] = 1;
                                }
                            }
                        }
                    }else{
                        foreach($aval_nota as $keyInfo => $info){
                            if(is_numeric($keyInfo)){
                                if($info['media_sem_peso']>=9 && $info['media_sem_peso']<=9.499){
                                    $notas_obtidas[9][] = 1;    
                                } else if($info['media_sem_peso']>=9.5 && $info['media_sem_peso']<=10){
                                    $notas_obtidas[10][] = 1;
                                }
                            }
                        }
                    }
                }
            }

        }

        // INSTANCIANO OS GRÁFICOS DO RELATÓIO

        $mencao_graph = \Lava::DataTable();
        $mencao_graph->addStringColumn('Reasons');
        $mencao_graph->addNumberColumn('Percent');
        //$mencao_graph->addRow(['Check Reviews', 5]);
        
        foreach($avaliacao_data['mencoes'] as $key => $item){
            $mencao_graph->addRow([$key, $item]);
        }
      
        \Lava::PieChart('IMDB', $mencao_graph, [
            'is3D'   => true
        ]);
        
        $range_notes = \Lava::DataTable();
        $range_notes->addStringColumn('Faixa');
        $range_notes->addNumberColumn('Frequência');
        foreach($intervalo as $key => $item){
            if(isset($notas_obtidas[$key])){
                $range_notes->addRow([$item, array_sum($notas_obtidas[$key])]);                            
            }
        }

        \Lava::ColumnChart('faixas', $range_notes, [
            'width' => 480,
            'height' => 300
        ]);


        if(!isset($notas_obtidas)){
            return '<div style="text-align: center; margin-top: 24px;">NÃO É POSSÍVEL ANALISAR OS RESULTADOS À PARTIR DAS INFORMAÇÕES RECUPERADAS</div>';
        } else {
            return view('relatorios.analise-resultados-provas')->with('ano_selecionado', $ano_selecionado)
                                                               ->with('omct', $omct)
                                                               ->with('intervalo', $intervalo)
                                                               ->with('notas_obtidas', $notas_obtidas)
                                                               ->with('avaliacao_data', $avaliacao_data);            
        }

    }

    public function AnaliseParcialDisciplinas(Request $request){

        $ano_formacao = AnoFormacao::find($request->ano_formacao_id);
        $ano_selecionado = (isset($ano_formacao->formacao))? $ano_formacao->formacao:'---';

        // SELECIONANDO ALUNOS

        if($request->omctID=='todas_omct'){
            $alunos = Alunos::where('data_matricula', $request->ano_formacao_id)->get();
            $omct = 'TODAS';
        } else {
            $alunos = Alunos::where('data_matricula', $request->ano_formacao_id)->where('omcts_id', $request->omctID)->get();
            $omct = OMCT::find($request->omctID);
            $omct = $omct->sigla_omct;
        }

        

        /*     
        ano_formacao_id: "1",
        omctID: "2",
        disciplinaID: "2"
        */

        

        // SEPARANDO O RELATÓRIO CASO NÃO SEJA PARA O TFM

        if($request->disciplinaID!='taf'){
        
            if($alunos){

                foreach($alunos as $item){
                    $alunosIDs[] = $item->id;
                }

                $disciplinas = Disciplinas::find($request->disciplinaID);

                $avaliacoes = Avaliacoes::where([
                    ['disciplinas_id', '=', $request->disciplinaID],
                    ['nome_abrev', '<>', 'AF'],
                    ['data', '<', date('Y-m-d')]
                ])->get();

                foreach($alunosIDs as $alunoID){
                    $disciplina_resultados[] = $disciplinas->getNotasAluno2023($alunoID);
                }
                
                $avaliacoes = $avaliacoes->unique('nome_completo');

                $retorno_excel = null;
                foreach($disciplina_resultados as $item){
                    if($item['ND']!=null){
                        $array_nds[] = $item['ND'];
                    }

                    if($request->relacao == 'excel'){
                        $alunoID = $item['alunoID'];

                    
                        $item['aluno'] = $alunos->first(function($value, $key) use($alunoID){
                            return $value->id == $alunoID;
                        });

                        $retorno_excel[$alunoID] = $item;
                    }
                }

                if($request->relacao == 'excel'){
                    $relacao = $request->relacao;
                    return view('ajax.relatorios.analise-parcial-notas-disciplinas', compact('relacao', 'alunos', 'retorno_excel'))
                    ->with('anoFormacao', $ano_formacao)
                    ->with('uete', OMCT::find($request->omctID));
                }
//dd(array_sum($array_nds), $disciplina_resultados, $array_nds);

                if($array_nds){
                    $disciplina_data['nome'] = $disciplinas->nome_disciplina;
                    //$disciplina_data['periodo'] = 'Básico';
                    $disciplina_data['periodo'] = '1º Ano <br>CFGS '.$disciplinas->ano_formacao->ano_cfs;
                    rsort($array_nds);
                    $disciplina_data['maior'] = $array_nds[0];
                    sort($array_nds);
                    $disciplina_data['menor'] = $array_nds[0];
                    $disciplina_data['amplitude'] = number_format($disciplina_data['maior'] - $disciplina_data['menor'], 3, ',', '');                
                    $disciplina_data['media_aritmetica'] = number_format(array_sum($array_nds)/count($array_nds), 3, ',', '');
                    $disciplina_data['efetivo'] = count($array_nds);
                    $disciplina_data['faltas'] = count($alunos) - count($array_nds);

                }

                $mencoes = Mencoes::get();

                foreach($array_nds as $item){
                    if($item>=5){
                        $com_media[] = 1;
                    } else {
                        $sem_media[] = 1;
                    }
                    foreach($mencoes as $menc){
                        if($item>=$menc->inicio && $item<=$menc->fim) {
                            $mencao[$menc->mencao][] = 1;
                        }
                    }
                }

                foreach($mencoes as $menc){
                    if(isset($mencao[$menc->mencao])){
                        $faixas_mencao[$menc->mencao] = array_sum($mencao[$menc->mencao]);
                    }
                }

                if(isset($faixas_mencao)){
                    $disciplina_data['mencoes'] = $faixas_mencao;   
                } else {
                    $disciplina_data['mencoes'] = array(null);   
                }

                $com_media = (isset($com_media))?array_sum($com_media):0;
                $sem_media = (isset($sem_media))?array_sum($sem_media):0;
                
                $disciplina_data['com_media'] = $com_media;
                $disciplina_data['sem_media'] = $sem_media;
                
                // NOTAS OBTIDAS

                for($i=0;$i<=8;$i++){
                    $intervalo[$i] = number_format($i, 1, ',', '').' à '.number_format(($i+0.9), 1, ',', '');
                    
                    foreach($array_nds as $item){
                        if($item>=$i && $item<=($i+0.999)){
                            $notas_obtidas[$i][] = 1;    
                        }
                    }   
                }
                
                $intervalo[9] = "9,0 à 9,499";
                $intervalo[10] = "9,5 à 10";

                foreach($array_nds as $item){
                    if($item>=9 && $item<=9.499){
                        $notas_obtidas[9][] = 1;    
                    } else if($item>=9.5 && $item<=10){
                        $notas_obtidas[10][] = 1;    
                    }
                }

            }

            // INSTANCIANO OS GRÁFICOS DO RELATÓIO

            $mencao_graph = \Lava::DataTable();
            $mencao_graph->addStringColumn('Reasons');
            $mencao_graph->addNumberColumn('Percent');
            //$mencao_graph->addRow(['Check Reviews', 5]);
            
            foreach($disciplina_data['mencoes'] as $key => $item){
                $mencao_graph->addRow([$key, $item]);
            }
        
            \Lava::PieChart('IMDB', $mencao_graph, [
                'is3D'   => true
            ]);
            
            $range_notes = \Lava::DataTable();
            $range_notes->addStringColumn('Faixa');
            $range_notes->addNumberColumn('Frequência');
            foreach($intervalo as $key => $item){
                if(isset($notas_obtidas[$key])){
                    $range_notes->addRow([$item, array_sum($notas_obtidas[$key])]);                            
                }
            }

            \Lava::ColumnChart('faixas', $range_notes, [
                'width' => 480,
                'height' => 300
            ]);


            

            if(!isset($array_nds)){
                return '<div style="text-align: center; margin-top: 24px;">NÃO É POSSÍVEL ANALISAR OS RESULTADOS À PARTIR DAS INFORMAÇÕES RECUPERADAS</div>';
            } else {
                return view('relatorios.analise-resultados-disciplinas')->with('ano_selecionado', $ano_selecionado)
                                                                        ->with('omct', $omct)
                                                                        ->with('avaliacoes', $avaliacoes)
                                                                        ->with('intervalo', $intervalo)
                                                                        ->with('notas_obtidas', $notas_obtidas)
                                                                        ->with('disciplina_data', $disciplina_data);            


            // ANALISE PARCIAL PARA CASO DE TFM

            }
            
        } else {

            if($alunos){

                $taf = AvaliacaoTaf::whereIn('aluno_id', $alunos)->get();
                $max = AvaliacaoTaf::whereIn('aluno_id', $alunos)->max('media');
                $min = AvaliacaoTaf::whereIn('aluno_id', $alunos)->min('media');
                $media_aritmetica = AvaliacaoTaf::whereIn('aluno_id', $alunos)->avg('media');
                $amplitude = $max-$min;
                $taf_data = array(
                    "maior" => number_format($max, 3, ',', ''),
                    "menor" => number_format($min, 3, ',', ''),
                    "media_aritmetica" => number_format($media_aritmetica, 3, ',', ''),
                    "amplitude" => number_format($amplitude, 3, ',', ''),
                    "faltas" => count($alunos) - count($taf),
                    "periodo" => '1º Ano <br>CFGS '.$taf->first()->aluno->ano_formacao->ano_cfs,
                    "nome" => "TESTE DA APTIDÃO FÍSICA",
                    "efetivo" => count($alunos)
                );

                foreach($taf as $item){
                    $array_nds[] = $item->media;   
                }

                $mencoes = Mencoes::get();

                foreach($array_nds as $item){
                    if($item>=5){
                        $com_media[] = 1;
                    } else {
                        $sem_media[] = 1;
                    }
                    foreach($mencoes as $menc){
                        if($item>=$menc->inicio && $item<=$menc->fim) {
                            $mencao[$menc->mencao][] = 1;
                        }
                    }
                }

                foreach($mencoes as $menc){
                    if(isset($mencao[$menc->mencao])){
                        $faixas_mencao[$menc->mencao] = array_sum($mencao[$menc->mencao]);
                    }
                }

                if(isset($faixas_mencao)){
                    $taf_data['mencoes'] = $faixas_mencao;   
                } else {
                    $taf_data['mencoes'] = array(null);   
                }

                $com_media = (isset($com_media))?array_sum($com_media):0;
                $sem_media = (isset($sem_media))?array_sum($sem_media):0;
                
                $taf_data['com_media'] = $com_media;
                $taf_data['sem_media'] = $sem_media;
                
                // NOTAS OBTIDAS

                for($i=0;$i<=8;$i++){
                    $intervalo[$i] = number_format($i, 1, ',', '').' à '.number_format(($i+0.9), 1, ',', '');
                    foreach($array_nds as $item){
                        if($item>=$i && $item<=($i+0.999)){
                            $notas_obtidas[$i][] = 1;    
                        }
                    }   
                }

                $intervalo[9] = "9,0 à 9,499";
                $intervalo[10] = "9,5 à 10";

                foreach($array_nds as $item){
                    if($item>=9 && $item<=9.499){
                        $notas_obtidas[9][] = 1;    
                    } else if($item>=9.5 && $item<=10){
                        $notas_obtidas[10][] = 1;    
                    }
                }

            }

            // INSTANCIANO OS GRÁFICOS DO RELATÓIO

            $mencao_graph = \Lava::DataTable();
            $mencao_graph->addStringColumn('Reasons');
            $mencao_graph->addNumberColumn('Percent');
            //$mencao_graph->addRow(['Check Reviews', 5]);
            
            foreach($taf_data['mencoes'] as $key => $item){
                $mencao_graph->addRow([$key, $item]);
            }
        
            \Lava::PieChart('IMDB', $mencao_graph, [
                'is3D'   => true
            ]);
            
            $range_notes = \Lava::DataTable();
            $range_notes->addStringColumn('Faixa');
            $range_notes->addNumberColumn('Frequência');
            foreach($intervalo as $key => $item){
                if(isset($notas_obtidas[$key])){
                    $range_notes->addRow([$item, array_sum($notas_obtidas[$key])]);                            
                }
            }

            \Lava::ColumnChart('faixas', $range_notes, [
                'width' => 480,
                'height' => 300
            ]);

            if(!isset($array_nds)){
                return '<div style="text-align: center; margin-top: 24px;">NÃO É POSSÍVEL ANALISAR OS RESULTADOS À PARTIR DAS INFORMAÇÕES RECUPERADAS</div>';
            } else {
                return view('relatorios.analise-resultados-taf')->with('ano_selecionado', $ano_selecionado)
                                                                        ->with('omct', $omct)
                                                                        ->with('intervalo', $intervalo)
                                                                        ->with('notas_obtidas', $notas_obtidas)
                                                                        ->with('taf_data', $taf_data);            


            }
        }
    }

    public function AnaliseParcialNPB(Request $request){

        $ano_formacao = AnoFormacao::find($request->ano_formacao_id);
        $ano_selecionado = (isset($ano_formacao->formacao))? $ano_formacao->formacao:'---';

        if($request->omctID=='todas_omct'){
            $alunosIDs = Alunos::where('data_matricula', $request->ano_formacao_id)->get(['id']);
            $omct = 'TODAS';
        } else {
            $alunosIDs = Alunos::where('data_matricula', $request->ano_formacao_id)->where('omcts_id', $request->omctID)->get(['id']);
            $omct = OMCT::find($request->omctID);
            $omct = $omct->sigla_omct;
        }        

        $notas = AlunosClassificacao::whereIn('aluno_id', $alunosIDs)->where('ano_formacao_id', $request->ano_formacao_id)->get();
        $max = AlunosClassificacao::whereIn('aluno_id', $alunosIDs)->where('ano_formacao_id', $request->ano_formacao_id)->max('nota_final_arredondada');
        $min = AlunosClassificacao::whereIn('aluno_id', $alunosIDs)->where('ano_formacao_id', $request->ano_formacao_id)->min('nota_final_arredondada');
        $media_aritmetica = AlunosClassificacao::whereIn('aluno_id', $alunosIDs)->where('ano_formacao_id', $request->ano_formacao_id)->avg('nota_final_arredondada');
        $amplitude = $max-$min;

        if(count($notas)>0){

            foreach($notas as $item){
                $array_npb[] = $item->nota_final_arredondada;
            }
            
            $npb_data = array(
                "maior" => number_format($max, 3, ',', ''),
                "menor" => number_format($min, 3, ',', ''),
                "media_aritmetica" => number_format($media_aritmetica, 3, ',', ''),
                "amplitude" => number_format($amplitude, 3, ',', ''),
                "efetivo" => count($alunosIDs),
                "periodo" => 'Básico'
            );

            $mencoes = Mencoes::get();

            foreach($array_npb as $item){
                if($item>=5){
                    $com_media[] = 1;
                } else {
                    $sem_media[] = 1;
                }
                foreach($mencoes as $menc){
                    if($item>=$menc->inicio && $item<=$menc->fim) {
                        $mencao[$menc->mencao][] = 1;
                    }
                }
            }

            foreach($mencoes as $menc){
                if(isset($mencao[$menc->mencao])){
                    $faixas_mencao[$menc->mencao] = array_sum($mencao[$menc->mencao]);
                }
            }

            if(isset($faixas_mencao)){
                $npb_data['mencoes'] = $faixas_mencao;   
            } else {
                $npb_data['mencoes'] = array(null);   
            }

            $com_media = (isset($com_media))?array_sum($com_media):0;
            $sem_media = (isset($sem_media))?array_sum($sem_media):0;
            
            $npb_data['com_media'] = $com_media;
            $npb_data['sem_media'] = $sem_media;
            
            // NOTAS OBTIDAS

            for($i=0;$i<=8;$i++){
                $intervalo[$i] = number_format($i, 1, ',', '').' à '.number_format(($i+0.9), 1, ',', '');
                foreach($array_npb as $item){
                    if($item>=$i && $item<=($i+0.999)){
                        $notas_obtidas[$i][] = 1;    
                    }
                }   
            }

            $intervalo[9] = "9,0 à 9,499";
            $intervalo[10] = "9,5 à 10";

            foreach($array_npb as $item){
                if($item>=9 && $item<=9.499){
                    $notas_obtidas[9][] = 1;    
                } else if($item>=9.5 && $item<=10){
                    $notas_obtidas[10][] = 1;    
                }
            }
        

        // INSTANCIANO OS GRÁFICOS DO RELATÓIO

        $mencao_graph = \Lava::DataTable();
        $mencao_graph->addStringColumn('Reasons');
        $mencao_graph->addNumberColumn('Percent');
        //$mencao_graph->addRow(['Check Reviews', 5]);
        
        foreach($npb_data['mencoes'] as $key => $item){
            $mencao_graph->addRow([$key, $item]);
        }
    
        \Lava::PieChart('IMDB', $mencao_graph, [
            'is3D'   => true
        ]);
        
        $range_notes = \Lava::DataTable();
        $range_notes->addStringColumn('Faixa');
        $range_notes->addNumberColumn('Frequência');
        foreach($intervalo as $key => $item){
            if(isset($notas_obtidas[$key])){
                $range_notes->addRow([$item, array_sum($notas_obtidas[$key])]);                            
            }
        }

        \Lava::ColumnChart('faixas', $range_notes, [
            'width' => 480,
            'height' => 300
        ]);

        //dd($npb_data);

        return view('relatorios.analise-resultados-npb')->with('ano_selecionado', $ano_selecionado)
                                                        ->with('omct', $omct)
                                                        ->with('intervalo', $intervalo)
                                                        ->with('notas_obtidas', $notas_obtidas)
                                                        ->with('npb_data', $npb_data);            
        } else {
            return '<div style="text-align: center; margin-top: 24px;">NÃO É POSSÍVEL ANALISAR OS RESULTADOS À PARTIR DAS INFORMAÇÕES RECUPERADAS</div>';
        }

    }
}


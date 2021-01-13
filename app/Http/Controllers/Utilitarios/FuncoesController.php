<?php

namespace App\Http\Controllers\Utilitarios;

use App\Http\Controllers\OwnAuthController;
use App\Models\Alunos;
use App\Models\AnoFormacao;
use App\Models\AvaliacoesNotas;
use App\Models\OMCT;
use App\Models\QMS;
use DateTime;
use Illuminate\Database\Eloquent\Collection;

class FuncoesController
{

    public static function formatDateBrtoEn($date)
    {

        if (!isset($date)) {
            return null;
        }

        @list($day, $month, $year) = @explode('/', $date);

        return $year . '-' . $month . '-' . $day;
    }

    public static function formatDateEntoBr($date)
    {

        if (!isset($date)) {
            return null;
        }

        @list($year, $month, $day) = @explode('-', $date);

        return $day . '/' . $month . '/' . $year;
    }

    public static function validDate($date)
    {
        if (!preg_match("/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $date)) {
            return false;
        }

        @list($year, $month, $day) = @explode('-', $date); // Padrão Americano yyyy/mm/dd

        // verifica se a data é válida!
        // 1 = true (válida)
        // 0 = false (inválida)
        $res = checkdate($month, $day, $year);

        if ($res == 1) {
            return true;
        } else {
            return false;
        }
    }

    public static function LimpaPastaTemp()
    {

        $caminho = storage_path() . '/app/public/temp/';
        $dir = opendir($caminho);

        while ($nome = readdir($dir)) {
            if ($nome != '.' && $nome != '..') {
                if (date('d-m-Y', filemtime($caminho . $nome)) != date('d-m-Y')) {
                    unlink($caminho . $nome);
                }
            }
        }
        closedir($dir);
    }

    public static function validaSessao()
    {
        if (!auth()->check()) {
            return redirect('/');
        }
        return null;
    }

    public static function retornaBotaoAnoFormacao($id_ano_formacao = null)
    {
        $anos_formacao = AnoFormacao::orderBy('formacao', 'desc')->get();
        if (count($anos_formacao) > 0) {
            $response_ano_formacao[] = '<div style="text-align: center; margin-top: 52px;"><div class="btn-group btn-group-toggle" data-toggle="buttons">';
            $i = 0;
            foreach ($anos_formacao as $ano_formacao) {
                if (isset($id_ano_formacao)) {
                    if ($id_ano_formacao == $ano_formacao->id) {
                        $status_active_label = ($id_ano_formacao == $ano_formacao->id) ? 'active' : '';
                        $status_checked_input = ($id_ano_formacao == $ano_formacao->id) ? 'checked' : '';

                        $response_ano_formacao[] = '<label class="btn btn-secondary ' . $status_active_label . ' " style="text-align: center;" ><input type="radio" name="ano_formacao" value="' . $ano_formacao->id . '" ' . $status_checked_input . ' /> ' . $ano_formacao->ano_cfs . '</label>';
                    }
                } else {
                    $status_active_label = ($i == 0) ? 'active' : '';
                    $status_checked_input = ($i == 0) ? 'checked' : '';
                    $response_ano_formacao[] = '<label class="btn btn-secondary ' . $status_active_label . ' " style="text-align: center;" ><input type="radio" name="ano_formacao" value="' . $ano_formacao->id . '" ' . $status_checked_input . ' /> ' . $ano_formacao->ano_cfs . '</label>';
                }

                $i++;
            }
            $response_ano_formacao[] = '</div></div>';
        } else {
            $response_ano_formacao[] = '<div style="text-align: center;">NÃO HÁ ANO DE FORMAÇÃO CADASTRADO NO SISTEMA</div>';
        }
        return implode('', $response_ano_formacao);
    }

    public static function retornaIdadePelaDataNascimento($dataNascimento)
    { //Formato Y-m-d

        $date = new DateTime($dataNascimento);
        $interval = $date->diff(new DateTime(date('Y-m-d')));
        return $interval->format('%Y');
    }

    public static function retornaUetePerfil(OwnAuthController $ownAuthController){
        if ($ownAuthController->PermissaoCheck(1)) {
            $uetes = OMCT::where('id', '<>', 1)->get(); //Remove a ESA
        } else {
            $uetes = OMCT::where('id', session()->get('login.omctID'))->get();
        }
        return $uetes;
    }

    public static function retornaCursoPerfilAnoFormacao(AnoFormacao $anoFormacao){

        $param['anoFormacao_id'] = $anoFormacao->id;
        
        if((session()->has('qms_selecionada') && session()->get('qms_selecionada') == 9999) || (session()->get('login.qmsID.0.qms_matriz_id') == 9999)){//ESA
            $qmsMatriz = array(1,2,3,4,5);
        }else if(session()->has('qms_selecionada')){
            $qmsMatriz = array(session()->get('qms_selecionada'));
        }else{
            $qmsMatriz = array(session()->get('login.qmsID.0.qms_matriz_id'));
        }

        $cursos = QMS::whereIn('qms_matriz_id', $qmsMatriz)->whereHas('escolhaQms', function($q) use ($param){
            $q->where('ano_formacao_id', $param['anoFormacao_id']);
        })->get();

        return $cursos;
    }

    public static function recalculaNotaAluno(Collection $avaliacoesNotas)
    {

        foreach ($avaliacoesNotas as $nota) {
            if (!is_null($nota->alunos_id)) {

                $alunosID[] = $nota->alunos_id;

                $aluno_notas[$nota->avaliacao->disciplinas_id][$nota->alunos_id]['notas'][] = $nota->getNota();
                $aluno_notas[$nota->avaliacao->disciplinas_id][$nota->alunos_id]['avaliacoes'][$nota->avaliacao->nome_abrev . ' - ' . $nota->avaliacao->chamada . 'ª chamada'] = (object) array('indice_notas' => array_key_last($aluno_notas[$nota->avaliacao->disciplinas_id][$nota->alunos_id]['notas']), 'nota' => $nota->getNota(), 'nome_abrev' => $nota->avaliacao->nome_abrev, 'peso' => $nota->avaliacao->peso);
                $aluno_notas[$nota->avaliacao->disciplinas_id][$nota->alunos_id]['disciplina_id'] = $nota->avaliacao->disciplinas_id;
                $aluno_notas[$nota->avaliacao->disciplinas_id][$nota->alunos_id]['disciplina_nome'] = $nota->avaliacao->disciplinas->nome_disciplina; //$disciplina_nome[$nota->avaliacao->disciplinas_id];
                $aluno_notas[$nota->avaliacao->disciplinas_id][$nota->alunos_id][$nota->avaliacao->nome_abrev . ' - ' . $nota->avaliacao->chamada . 'ª chamada'] = $nota->getNota();

                /*if($nota->alunos_id == 3156){
                    dd($aluno_notas[$nota->avaliacao->disciplinas_id][3156]);
                }*/
            }
        }

        if (isset($aluno_notas)) {
            foreach ($aluno_notas as $disciplina_id => $disciplina) {
                
                $aluno_notas[$disciplina_id]['media_disciplina'] = 0.0;
                $aluno_notas[$disciplina_id]['max_disciplina'] = 0.0;
                $aluno_notas[$disciplina_id]['min_disciplina'] = 0.0;
                $contador_media = 0;

                foreach ($disciplina as $aluno_id => $aluno) {
                    $quantidadeAvaliacao = 0;
                    foreach ($aluno['avaliacoes'] as $key => $aval) {
                        
                        $quantidadeAvaliacao += $aval->peso;
                        if ($aval->peso > 0) {
                            $aluno_notas[$disciplina_id][$aluno_id]['notas'][$aval->indice_notas] = ($aval->nota * $aval->peso);
                            $aluno['notas'][$aval->indice_notas] = $aluno_notas[$disciplina_id][$aluno_id]['notas'][$aval->indice_notas];
                        }else{
                            unset($aluno_notas[$disciplina_id][$aluno_id]['notas'][$aval->indice_notas]);

                            //Para puxar fazer a média do demonstrativo...
                            $aluno_notas[$disciplina_id][$aluno_id]['notas_sem_peso'][] = $aval->nota;

                            $aluno_notas[$disciplina_id][$aluno_id]['avaliacoes'][$key]->indice_notas = null;
                            $aluno['avaliacoes'][$key]->indice_notas = null;
                        }
                    }
                    $aluno_notas[$disciplina_id][$aluno_id]['disciplina_razao'] = $quantidadeAvaliacao;
                    if($quantidadeAvaliacao > 0){
                        $aluno_notas[$disciplina_id][$aluno_id]['media'] = array_sum($aluno_notas[$disciplina_id][$aluno_id]['notas']) / $quantidadeAvaliacao;
                    }else{
                        $aluno_notas[$disciplina_id][$aluno_id]['media'] = 0;
                        $aluno_notas[$disciplina_id][$aluno_id]['media_sem_peso'] = array_sum($aluno_notas[$disciplina_id][$aluno_id]['notas_sem_peso']) / count($aluno_notas[$disciplina_id][$aluno_id]['notas_sem_peso']);
                    }
                    
                    $aluno_notas[$disciplina_id]['media_disciplina'] += $aluno_notas[$disciplina_id][$aluno_id]['media'];
                    $aluno_notas[$disciplina_id]['max_disciplina'] = ($aluno_notas[$disciplina_id][$aluno_id]['media'] > $aluno_notas[$disciplina_id]['max_disciplina'] 
                                                                                    ? $aluno_notas[$disciplina_id][$aluno_id]['media'] 
                                                                                    : $aluno_notas[$disciplina_id]['max_disciplina']);
                    $aluno_notas[$disciplina_id]['min_disciplina'] = ((($aluno_notas[$disciplina_id][$aluno_id]['media'] < $aluno_notas[$disciplina_id]['min_disciplina']) 
                                                                                    || ($aluno_notas[$disciplina_id]['min_disciplina'] == 0)) 
                                                                                    ? $aluno_notas[$disciplina_id][$aluno_id]['media'] 
                                                                                    : $aluno_notas[$disciplina_id]['min_disciplina']);

                    //Caso seja sem peso a avaliação e só existir uma nota lançada...
                    if($aluno_notas[$disciplina_id][$aluno_id]['media'] == 0 && $quantidadeAvaliacao == 0){
                        $aluno_notas[$disciplina_id][$aluno_id]['media'] = '-';
                    }else{
                        $contador_media++;
                    }
                }

                if($contador_media > 0){
                    $aluno_notas[$disciplina_id]['media_disciplina'] = $aluno_notas[$disciplina_id]['media_disciplina'] / $contador_media;
                }
            }
            $aluno_notas['alunosID'] = $alunosID;
        }else{
            $aluno_notas = array();
        }
        
        //dd($aluno_notas[24][2188]);
        return $aluno_notas;
    }

    public static function  ArrayMergeKeepKeys() {
            $arg_list = func_get_args();
            foreach((array)$arg_list as $arg){
                foreach((array)$arg as $K => $V){
                    $Zoo[$K]=$V;
                }
            }
        return $Zoo;
    }
 
    public static function retornaAnoFormacaoAtivoQualificacao(){
        return AnoFormacao::where('per_ativo_qualificacao', 'S')->first();
    }

}
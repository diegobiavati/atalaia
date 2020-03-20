<?php

namespace App\Http\Controllers;

use App\Models\Alunos;
use App\Models\AlunosCurso;
use App\Models\AlunosSitDiv;
use App\Models\Areas;
use App\Models\ImagemAluno;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Route;
use View;

class TesteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        /*$alunos = Alunos::carregaAlunosVsAlunosSitDiv();

        foreach($alunos as $aluno){
            echo $aluno['id'].' -> '.$aluno['numero'].' -> '.$aluno['nome_completo'].' -> '.$aluno['omcts_id'].' omct id session ->'.session()->get('login.omctID');
            echo '<br>';
        }*/
        //dd($alunos[2190], $alunos[2195]);

        /*$aluno = Alunos::with('ano_formacao')->with('dependentes')->with('imagem_aluno')->find(2146);

        if(!isset($aluno)){
            $alunoSitDiv = AlunosSitDiv::with('situacaoDivHistorico')->find(2146);

            $unserialize = unserialize($alunoSitDiv->situacaoDivHistorico->data);
            
            dd(new Alunos($unserialize['cadastro']));
            return response()->json($unserialize['cadastro']);
        }
        return response()->json($aluno);*/
        //return substr_replace('B0', '0', 1, 0);
        //$aluno = Alunos::with('ano_formacao')->with('dependentes')->with('instrumento')->with('uf_nascimento')->find(2247);

        //return response()->json($aluno);
        //return view("rotas", ['resource' => Route::getRoutes()->getRoutes()]);
        //return view("exportar/exportarExcel");

        //$imagemAluno = ImagemAluno::with('aluno')->where('id_aluno', 21)->get();
        //$imagemAluno = AlunosCurso::with('alunos')->where('al_inscricao', 40120636)->get();
        //$imagemAluno = Alunos::with('alunos_curso')->where('al_inscricao', '30601264')->get();
        /*$param = '30601264';
        //$param = '70000026';
        $imagemAluno = AlunosCurso::whereHas('alunos', function ($query) use ($param) {
            $query->where('al_inscricao', $param);
        })->get();*/


        //$aluno = Alunos::get();

        /*$param = '2019';
        $aluno = Alunos::whereHas('ano_formacao', function ($query) use ($param) {
            $query->where('ano_per_basico', $param);
        })->where('al_inscricao', '40800013')->get()->first();

        $aluno->classif_cacfs = 170;
        $aluno->save();*/
        /*dd(count(AlunosCurso::whereHas('alunos', function ($query) use ('$param') {
            $query->where('al_inscricao', $param);
        })->get()));*/

        //return response()->json($aluno);

        /*$html = view('admin.aluno.alunoSitDiversas')->render();

        $list[] = $html;
        
        return response()->json($list);*/

        $arquivoConcurso = file(storage_path('app/public/temp/Alunos_Concurso.CSV'), FILE_TEXT);
        $arquivoAtalaia = file(storage_path('app/public/temp/Alunos_Atalaia.csv'), FILE_TEXT);

        $contadorConcurso = count($arquivoConcurso);
        $contadorAtalaia = count($arquivoAtalaia);
        echo '<table border="1">
        <tr>
        <td>ano</td>
        <td>inscricao</td>
        <td>nome</td>
        <td>data_nascimento</td>
        <td>naturalidade_cidade</td>
        <td>naturalidade_uf</td>
        <td>naturalidade_pais</td>
        <td>data_incorporacao</td>
        <td>cod_sitanterior</td>
        <td>cod_sit2</td>
        <td>endereco</td>
        <td>bairro</td>
        <td>cidade</td>
        <td>uf</td>
        <td>cep</td>
        <td>tel_residencial</td>
        <td>tel_comercial</td>
        <td>celular</td>
        <td>celular2</td>
        <td>email</td>
        <td>sca_notadou</td>
        <td>senha</td>
        <td>sca_omct</td>
        <td>area</td>
        <td>sca_classfinal</td>
        <td>sexo</td>
        </tr>';
        for ($i = 1; $i < $contadorAtalaia; $i++) {

            $explode = explode(';', $arquivoAtalaia[$i]);

            for ($l = 1; $l < $contadorConcurso; $l++) {
                $explodeConcurso = explode(';', utf8_encode($arquivoConcurso[$l]));

                if ($explode[0] === $explodeConcurso[1]) { //Se o número da inscricao existir no arquivo do Concurso
                    //echo $arquivoConcurso[$l] . '<hr>';
                    echo '<tr>
                             <td>' . $explodeConcurso[0] . '</td>
                             <td>' . $explodeConcurso[1] . '</td>
                             <td>' . $explodeConcurso[7] . '</td>
                             <td>' . date('Y-m-d', strtotime($explodeConcurso[11])) . '</td>
                             <td>' . $explodeConcurso[17] . '</td>
                             <td>' . $explodeConcurso[18] . '</td>
                             <td>' . $explodeConcurso[19] . '</td>
                             <td>' . $explodeConcurso[33] . '</td>
                             <td>null</td>
                             <td>null</td>
                             <td>' . $explodeConcurso[20] . '</td>
                             <td>' . $explodeConcurso[23] . '</td>
                             <td>' . $explodeConcurso[24] . '</td>
                             <td>' . $explodeConcurso[25] . '</td>
                             <td>' . $explodeConcurso[26] . '</td>
                             <td>' . $explodeConcurso[27] . '</td>
                             <td>' . $explodeConcurso[28] . '</td>
                             <td>' . $explodeConcurso[29] . '</td>
                             <td>null</td>
                             <td>' . $explodeConcurso[30] . '</td>
                             <td>' . $explodeConcurso[70] . '</td>
                             <td>' . $explodeConcurso[3] . '</td>
                             <td>' . $explodeConcurso[72] . '</td>
                             <td>' . $explodeConcurso[4] . '</td>
                             <td>' . $explodeConcurso[66] . '</td>
                             <td>' . $explodeConcurso[14] . '</td>
                        </tr>';
                    break;
                }
            }
        }
        echo '</table>';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

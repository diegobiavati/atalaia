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

        $html = view('admin.aluno.alunoSitDiversas')->render();

        $list[] = $html;
        
        return response()->json($list);
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

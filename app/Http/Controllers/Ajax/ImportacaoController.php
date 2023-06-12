<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Http\OwnClasses\ClassLog;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificaBoletim;
use App\Models\Alunos;
use App\Models\AlunosCurso;
use App\Models\Militar;

class ImportacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('importar.importadorIndex');
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

        $retorno['status'] = 'err';
        $retorno['response'] = 'Houve um Erro';

        if ($request->hasFile('arquivo')) {
            $retorno['status'] = 'ok';
            $retorno['response'] = 'Upload Feito';
        } else {
            $error[] = 'Não Foi Enviado Um Arquivo';
            $retorno['error'] = implode('<br />', $error);
            return response()->json($retorno);
        }

        if ($request->arquivo->extension() != 'xls' && $request->arquivo->getMimeType() != 'application/vnd.ms-excel') {
            $error[] = 'Por favor, utilize arquivos do tipo Excel(.xls).';
        }

        if (isset($error)) {
            $retorno['status'] = 'err';
            $retorno['response'] = 'Houve um Erro';
            $retorno['error'] = implode('<br />', $error);

            return response()->json($retorno);
        }

        FuncoesController::LimpaPastaTemp();

        if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid()) {

            $extension = $request->arquivo->extension();

            $name = uniqid('excel_' . date('His'));
            $nameFile = "{$name}.{$extension}";

            if ($request->arquivo->storeAs('temp/', $nameFile)) {

                switch ($request->tipo) {
                    case 'aluno':
                        $alunos = new Alunos();
                        return $alunos->importInsert('temp/' . $nameFile);
                    case 'alunoCurso':
                        $alunosCurso = new AlunosCurso();
                        return $alunosCurso->import('temp/' . $nameFile);
                }
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['response'] = '<form id="importar_concurso">
                                <input type="hidden" name="_token" value="' . csrf_token() . '"/>
                                <div style="margin-top: 64px; text-align: center; color: #696969;">
                                    <label for="arquivo">Selecione o Arquivo do Tipo Excel :</label>                        
                                    <input type="file" id="arquivo" name="arquivo" accept=".xls">
                                </div>
                                <div style="margin: 32px auto; width: 50%; text-align: center; ">
                                    <button type="button" id="btnConcurso" class="btn btn-success" onclick="enviaArquivoExcel()">Enviar Planilha e Inserir Informações</button>
                                </div>';
        switch ($id) {
            case 'alunoConcurso':
                $data['modalTitle'] = 'Importação de Dados do Aluno (Concurso)';
                $data['response'] = $data['response'] . '<input type="hidden" name="tipo" value="aluno"/>';
                break;
            case 'alunoCursoConcurso':
                $data['modalTitle'] = 'Importação de Dados do Aluno Curso (Concurso)';
                $data['response'] = $data['response'] . '<input type="hidden" name="tipo" value="alunoCurso"/>';
                break;
            default:
                break;
        }

        $data['response'] = $data['response'] . '</form>';

        return response()->json($data);
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

    public static function ImportaMSAccessCapitaniMysql(){
        $sql = "SELECT AnoQ, Nr_Alu, GUERRA, Grupo, Disciplina, [AA/AA1] as AA_A1, AA2, AA3, AA4, AC_AI, AC2, AR, AF1, AF2, AD, NFC, Bonus, NDC, NPBARRED, NQ, NACP, NAA, NFC, mencao, QR, ClasF, NFC_DIZ FROM 2anocfgs;";
        
        $output = null;

        exec('java -jar '.app_path('Imports/').'SQLMSAccess.jar "'.$sql.'"', $output);

        $classLog = new ClassLog();
        $classLog->RegistrarLog('Fez Importação de Dados Capitani '.$output[0], 'Sistema');
    }
	
	public static function verificaNomeBoletim(){
	
        if(($militar = Militar::where([['bol_index', 'like', '%JOÃO VICTOR GOMES DA SILVA%']])
        ->whereRaw('data_documento BETWEEN DATE_SUB(NOW(), INTERVAL 1 DAY) AND CURRENT_DATE')->get()) && $militar->count() > 0){
			Mail::to('jvgs_o.o@live.com')->send(new VerificaBoletim($militar));		
            return true;
        }
        
        if(($militar = Militar::where([['bol_index', 'like', '%MUNIR CHEIK KALED%']])
        ->whereRaw('data_documento BETWEEN DATE_SUB(NOW(), INTERVAL 1 DAY) AND CURRENT_DATE')->get()) && $militar->count() > 0){
            Mail::to('munir.cheik@gmail.com')->send(new VerificaBoletim($militar));		
            return true;
        }

        
        return false;
    }

}
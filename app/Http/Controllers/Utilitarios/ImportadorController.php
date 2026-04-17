<?php

namespace App\Http\Controllers\Utilitarios;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Alunos;
use App\Models\AlunosDependente;

class ImportadorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->check()) {
            return view('importar.SispbExcelAluno');
        } else {
            return "Faça o Login no Sistema";
        }
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

        if ($request->hasFile('arquivo_excel')) {
            $retorno['status'] = 'ok';
            $retorno['response'] = 'Upload Feito';
        } else {
            $error[] = 'Não Foi Enviado Um Arquivo ';
            $retorno['error'] = implode('<br />', $error);
            return response()->json($retorno);
        }

        if ($request->arquivo_excel->extension() != 'xls' && $request->arquivo_excel->getMimeType() != 'application/vnd.ms-excel') {
            $error[] = 'Por favor, utilize arquivos do tipo Excel(.xls).';
        }

        if (isset($error)) {
            $retorno['status'] = 'err';
            $retorno['response'] = 'Houve um Erro';
            $retorno['error'] = implode('<br />', $error);

            return response()->json($retorno);
        }

        if ($request->hasFile('arquivo_excel') && $request->file('arquivo_excel')->isValid()) {
            $extension = $request->arquivo_excel->extension();

            $name = uniqid('excel_' . date('His'));
            $nameFile = "{$name}.{$extension}";

            if ($request->arquivo_excel->storeAs('temp/', $nameFile)) {
                FuncoesController::LimpaPastaTemp();

                //Implementar a importação
                if (isset($request->radio) && $request->radio == 'alunos') {
                    $alunos = new Alunos();
                    $alunos->import('temp/' . $nameFile);

                    $retorno['status'] = 'ok';
                    $retorno['response'] = 'Alunos Atualizados';
                } elseif (isset($request->radio) && $request->radio == 'dependentes') {
                    $dependentes = new AlunosDependente();
                    $dependentes->import('temp/' . $nameFile);

                    $retorno['status'] = 'ok';
                    $retorno['response'] = 'Dependentes Atualizados';
                }
            }
        }

        return response()->json($retorno);
    }
}

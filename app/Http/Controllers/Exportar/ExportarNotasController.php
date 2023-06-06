<?php

namespace App\Http\Controllers\Exportar;

use App\Exports\AlunosNotasExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExportarNotasController extends Controller
{
    public function exportarNotasAlunos(Request $request){
        return new AlunosNotasExport($request->id_ano_formacao);
    }
}

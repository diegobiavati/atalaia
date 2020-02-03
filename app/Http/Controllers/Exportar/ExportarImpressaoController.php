<?php

namespace App\Http\Controllers\Exportar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TafPortarias;
use App\Models\TafCorrida;
use App\Models\TafFlexaoBraco;
use App\Models\TafFlexaoBarra;
use App\Models\TafAbdominal;
use App\Models\TafBonusAtletas;
use App\Models\TafConfiguracoes;

class ExportarImpressaoController extends Controller
{
    public function ImprimirPortaria(Request $request){
        return view('exportar.portaria_taf')->with('id', $request->id);
    }
}

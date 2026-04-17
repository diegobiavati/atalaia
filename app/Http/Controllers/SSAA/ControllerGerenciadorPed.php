<?php

namespace App\Http\Controllers\SSAA;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OwnAuthController;

class ControllerGerenciadorPed extends Controller
{
    private $_ownauthcontroller = null;
    private $_request = null;

    public function __construct(Request $request, OwnAuthController $ownauthcontroller)
    {
        $this->_ownauthcontroller = $ownauthcontroller;
        $this->_request = $request;
    }

    public function index()
    {

        $esaPedAno = \App\Models\EsaPedAnos::all();

        return view('ssaa.ped.form', [
            'ownauthcontroller' => $this->_ownauthcontroller,
            'esaPedAno' => $esaPedAno,
            'criptografia' => true,

            'urlPedExercicio' => route('gaviao.ajax.ssaa.get-ped-exercicios', [
                'id_ped' => null,
            ]),
        ]);
    }

    public function getPedExercicios(Request $request)
    {
        $idPed = explode('_', decrypt($request->id_ped))[1];

        $pedExercicios = \App\Models\EsaPedAnos::where('id', $idPed)->first();

        return response()->json($pedExercicios->esapedexercicio);
    }
}

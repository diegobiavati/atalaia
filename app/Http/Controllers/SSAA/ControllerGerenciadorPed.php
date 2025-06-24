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
        return view('ssaa.ped.form', [
            'ownauthcontroller' => $this->_ownauthcontroller,
            'urlIndiceDisciplinas' => route('gaviao.ajax.ssaa.gerenciador-ped.index'),
            'urlIndiceDisciplinasProvas' => route('gaviao.ajax.ssaa.gerenciador-ped.index')
        ]);
    }
}

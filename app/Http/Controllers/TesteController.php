<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utilitarios\SmbClientPhp;
use App\Models\Alunos;
use App\Models\AlunosCurso;
use App\Models\AlunosSitDiv;
use App\Models\Areas;
use App\Models\ImagemAluno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
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
        $headers = array(
            'Content-Type: application/pdf',
        );
        //Response::download(storage_path().'/CitrixHypervisor-8.1.0-install-cd.iso', 'CitrixHypervisor-8.1.0-install-cd.iso', ['content-type' => 'application/iso']);
        Response::download(storage_path().'/CitrixHypervisor-8.1.0-install-cd.iso', 'teste.iso', $headers);
        
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

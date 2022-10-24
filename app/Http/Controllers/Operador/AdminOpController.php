<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Ajax\AjaxAdminGaviaoController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utilitarios\FuncoesController;
use App\Models\Imagens;
use App\Models\Operadores;
use App\Models\OperadoresTipo;
use App\Models\QMSMatriz;
use App\User;
use Illuminate\Http\Request;

class AdminOpController extends Controller
{
    public function ShowHome(\App\Http\Controllers\OwnAuthController $ownauthcontroller){
        
        $user = User::find(auth()->id());
        $operador = Operadores::where('email', '=', $user->email)->first();
        if(!$img = Imagens::find($user->imagens_id)){
            $img = Imagens::find(1);
        }
        $funcoesOperadores = OperadoresTipo::get();
        foreach($funcoesOperadores as $funcao){
            $data[$funcao->id] = $funcao->funcao_abrev;
        }
        
        if($ownauthcontroller->PermissaoCheck([1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,24,25,26,27])){
        
            return view('operador.admin')->with('img_perfil', ($img->imagem)??'')
                                        ->with('operador', $operador)
                                        ->with('ownauthcontroller', $ownauthcontroller)
                                        ->with('data', $data);

        } else {
            return 'Não autorizado!<br/><a href="/sair">Clique aqui para sair</a>';
        }
    }

    public function DashboardGaviao(\App\Http\Controllers\OwnAuthController $ownauthcontroller){
        $user = User::find(auth()->id());
        $operador = Operadores::where('email', '=', $user->email)->first();
        if(!$img = Imagens::find($user->imagens_id)){
            $img = Imagens::find(1);
        }

        $funcoesOperadores = OperadoresTipo::get();
        foreach($funcoesOperadores as $funcao){
            $data[$funcao->id] = $funcao->funcao_abrev;
        }

        $qmsMatriz = array(1,2,3,4,5,9999);
        $cursos = QMSMatriz::whereIn('id', $qmsMatriz)->get();
        
        if(session()->has('login.qmsID')){

            switch(session()->get('login.qmsID')[0]['qms_matriz_id']){
                case 1://Infantaria
                    $backgroundColor = FuncoesController::getQmsColor(1)->backgroundColor;
                    $backgroundVisaoGeral = $backgroundColor;
                    $backgroundMenuLateral = 'linear-gradient(135deg, rgb(0, 168, 89), rgb(45, 72, 59));';
                break;
                case 2://Cavalaria
                    $backgroundColor = FuncoesController::getQmsColor(2)->backgroundColor;
                    $backgroundVisaoGeral = $backgroundColor;
                    $backgroundMenuLateral = 'linear-gradient(135deg, rgb(237, 50, 55), rgb(98, 25, 27));';
                break;
                case 3://Artilharia
                    $backgroundColor = FuncoesController::getQmsColor(3)->backgroundColor;
                    $backgroundVisaoGeral = $backgroundColor;
                    $backgroundMenuLateral = 'linear-gradient(135deg, rgb(0, 100, 166), rgb(0, 47, 79));';
                break;
                case 4://Engenharia
                    $backgroundColor = FuncoesController::getQmsColor(4)->backgroundColor;
                    $backgroundVisaoGeral = $backgroundColor;
                    $backgroundMenuLateral = 'linear-gradient(135deg, rgb(145, 216, 247), rgb(48, 68, 77))';
                break;
                case 5://Comunicações
                    $backgroundColor = FuncoesController::getQmsColor(5)->backgroundColor;
                    $backgroundVisaoGeral = $backgroundColor;
                    $backgroundMenuLateral = 'linear-gradient(135deg, rgb(0, 152, 218), rgb(0, 41, 60))';
                break;
                case 9999://ESA
                    $backgroundColor = FuncoesController::getQmsColor(9999)->backgroundColor;
                    $backgroundVisaoGeral = '#0094D3;';
                    $backgroundMenuLateral = 'linear-gradient(135deg, rgb(40, 139, 179), rgb(19, 92, 115));';

                    if(!session()->has('qms_selecionada')){//Para setar a Qms no Login
                        session()->put('qms_selecionada', 9999);
                    }
                break;
            }
            
            if(session()->has('qms_selecionada')){
                switch(session()->get('qms_selecionada')){
                    case 1:
                        $backgroundVisaoGeral = 'rgb(0,168,89);';
                        $backgroundMenuLateral = 'linear-gradient(135deg, rgb(0, 168, 89), rgb(45, 72, 59));';
                    break;
                    case 2:
                        $backgroundVisaoGeral = 'rgb(237,50,55);';
                        $backgroundMenuLateral = 'linear-gradient(135deg, rgb(237, 50, 55), rgb(98, 25, 27));';
                    break;
                    case 3:
                        $backgroundVisaoGeral = 'rgb(0,100,166);';
                        $backgroundMenuLateral = 'linear-gradient(135deg, rgb(0, 100, 166), rgb(0, 47, 79));';
                    break;
                    case 4:
                        $backgroundVisaoGeral = 'rgb(145,216,247);';
                        $backgroundMenuLateral = 'linear-gradient(135deg, rgb(145, 216, 247), rgb(48, 68, 77))';
                    break;
                    case 5:
                        $backgroundVisaoGeral = 'rgb(0,152,218);';
                        $backgroundMenuLateral = 'linear-gradient(135deg, rgb(0, 152, 218), rgb(0, 41, 60))';
                    break;
                }
            }

            session()->put('backgroundColor', $backgroundColor.';');
            session()->put('backgroundVisaoGeral', $backgroundVisaoGeral);
            session()->put('backgroundMenuLateral', $backgroundMenuLateral);            
            
            return view('operador.adminGaviao')->with('img_perfil', ($img->imagem)??'')
                                        ->with('operador', $operador)
                                        ->with('ownauthcontroller', $ownauthcontroller)
                                        ->with('data', $data)
                                        ->with('cursos', $cursos);

        } else {
            return 'Usuário Logado!<br/><a href="'.route('gaviao.logout').'">Clique aqui para sair</a>';
        }
    }
}
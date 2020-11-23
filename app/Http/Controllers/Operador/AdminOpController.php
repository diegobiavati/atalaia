<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;

use App\Models\Imagens;
use App\Models\Operadores;
use App\Models\OperadoresTipo;
use App\User;

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
        
        if($ownauthcontroller->PermissaoCheck([1])){

            switch(session()->get('login.qmsID')[0]['qms_matriz_id']){
                case 1://Infantaria
                    $backgroundColor = 'rgb(0,168,89);';
                break;
                case 2://Cavalaria
                    $backgroundColor = 'rgb(237,50,55);';
                break;
                case 3://Artilharia
                    $backgroundColor = 'rgb(0,100,166);';
                break;
                case 4://Engenharia
                    $backgroundColor = 'rgb(145,216,247);';
                break;
                case 5://Comunicações
                    $backgroundColor = 'rgb(0,152,218);';
                break;
                case 9999://ESA
                    $backgroundColor = 'linear-gradient(#EC2125 47%, #E0B22E 50%,#0094D3 53%);';
                break;
            }
            
            return view('operador.adminGaviao')->with('img_perfil', ($img->imagem)??'')
                                        ->with('operador', $operador)
                                        ->with('ownauthcontroller', $ownauthcontroller)
                                        ->with('data', $data)
                                        ->with('backgroundColor', $backgroundColor);

        } else {
            return 'Usuário Logado!<br/><a href="'.route('gaviao.logout').'">Clique aqui para sair</a>';
        }
    }
}

<?php

namespace App\Http\Controllers\Operador;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Imagens;
use App\Models\PostoGrad;
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
        
        if($ownauthcontroller->PermissaoCheck([1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21])){
        
            return view('operador.admin')->with('img_perfil', ($img->imagem)??'')
                                        ->with('operador', $operador)
                                        ->with('ownauthcontroller', $ownauthcontroller)
                                        ->with('data', $data);

        } else {
            return 'Não autorizado!<br/><a href="/sair">Clique aqui para sair</a>';
        }
    }
}

<?php

namespace App\Http\Controllers\Aluno;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Alunos;
use App\Models\Imagens;
use App\User;


class PainelAlunoController extends Controller
{
    public function ShowHome(\App\Http\Controllers\OwnAuthController $ownauthcontroller){
        $user = User::find(auth()->id());
        $aluno = Alunos::where('email', '=', $user->email)->first();

        return view('aluno.painel-aluno')->with('aluno', $aluno)
                                         ->with('ownauthcontroller', $ownauthcontroller);
    
    }
}

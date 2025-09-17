<?php

namespace App\Http\Controllers\Aluno;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Alunos;
use App\Models\Imagens;
use App\User;
use Illuminate\Support\Facades\Log;

class PainelAlunoController extends Controller
{
    public function ShowHome(\App\Http\Controllers\OwnAuthController $ownauthcontroller){
        
        $user = User::find(auth()->id());
        $aluno = Alunos::where('email', '=', $user->email)->first();

        Log::channel('daily')->info("Aluno Fez Login No Sistema ", ['aluno' => $aluno->id, 'email' => $aluno->email, 'ip' => request()->ip(), 'user_agent' => request()->userAgent() ]);   

        return view('aluno.painel-aluno')->with('aluno', $aluno)
                                         ->with('ownauthcontroller', $ownauthcontroller);
    
    }
}

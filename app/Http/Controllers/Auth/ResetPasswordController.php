<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
/* INJETANDO minha classe que faz validações */

use App\Http\OwnClasses\OwnValidator;
/*
    Classes utilizadas pelo método reset() que sobrescreve
    o método reset em (verdor/laravel/frameworl/src/Illuminate/Auth)

*/

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

/*

    fim da injeção das classes Request e Password

*/


class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/atalaia';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /*

        SOBRESCREVI O MÉTODO reset (verdor/laravel/frameworl/src/Illuminate/Auth)
        e injetei no início do arquivo as classes utilizadas pelo método:
        use Illuminate\Http\Request;
        use Illuminate\Support\Facades\Password;

    */

    public function reset(Request $request)
    {

        if (OwnValidator::ValidarPW($request->password) != 'ok') {
            $data['status'] = 'err';
            $data['msg'] = OwnValidator::ValidarPW($request->password);
        } else {
            $this->validate($request, $this->rules(), $this->validationErrorMessages());

            $response = $this->broker()->reset(
                $this->credentials($request),
                function ($user, $password) {
                    $this->resetPassword($user, $password);
                }
            );

            if ($response == Password::PASSWORD_RESET) {
                $data['status'] = 'ok';
                $data['msg'] = 'SENHA ALTERADA COM SUCESSO!';
            } else {
                $data['status'] = $response; // passwords.token
                $data['msg'] = 'Token inválido. Por favor, solicite um novo email de recuperação de senha.';
            }
        }

        return $data;
    }
}

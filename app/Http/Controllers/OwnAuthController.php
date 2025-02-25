<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\PasswordBroker;
//use Illuminate\Support\Facades\DB;
use App\User;

/* MODELS */

use App\Models\Alunos;
use App\Models\Operadores;
use App\Models\OperadoresPermissoes;
//use App\Models\UsersTopicMQTT;

use App\Http\Controllers\MQTTController;
use App\Http\OwnClasses\ClassLog;
use App\Models\QMS;

class OwnAuthController extends Controller
{

    protected $classLog;

    public function __construct(ClassLog $classLog, Request $request){
        $this->classLog = $classLog;
        $this->classLog->ip = $request->ip();      
        
    }

    public function PerfilCheck($perfils){
        if(session()->has('login')){
            
            $session_data = session()->get('login');

            if(is_array($perfils)){
                foreach($perfils as $perfil){
                    if(in_array($perfil, $session_data['perfil'])){
                        if(isset($not_authorized)){
                            unset($not_authorized);
                        }
                        break;
                    } else {
                        $not_authorized = true;
                    }
                }

                return (isset($not_authorized))?false:true;

            } else {                
                if(in_array($perfils, $session_data['perfil'])){
                    return true;
                } else {
                    return false;   
                }
            }
        } else {
            return false;
        }
    }
    
    public function PermissaoCheck($permissao){
        
        if(session()->has('login')){
            
            $session_data = session()->get('login');
            
            if(is_array($permissao)){
                foreach($permissao as $permissoes){
                    if(in_array($permissoes, $session_data['permissoes'])){
                        if(isset($not_authorized)){
                            unset($not_authorized);
                        }
                        break;
                    } else {
                        $not_authorized = true;
                    }
                }

                return (isset($not_authorized))?false:true;

            } else {                
                if(in_array($permissao, $session_data['permissoes'])){
                    return true;
                } else {
                    return false;   
                }
            }
        } else {
            return false;
        }
    }
    
    public function UserLogin(Request $request){
        
        if($request->is('gaviao') || $request->is('gaviao/*')){
            if(auth()->check() && session()->get('login.qmsID') !== null ){
                return redirect()->route('gaviaoRouter');
            } else {
                return view('loginGaviao');
            }   
        }else{
            if(auth()->check() && session()->get('login.omctID') !== null ){
                
                return redirect()->route('atalaia');
            } else {
                return view('login');
            }    
        }
    }

    public function UserRouter(Request $request){

        if($request->is('gaviaoRouter')){
            if(auth()->check()){
                if($this->PermissaoCheck([1,29])){
                    return redirect()->route('gaviao.dashboard');
                } else {
                    return redirect()->route('gaviao.logout');
                }
            } else {
                return redirect()->route('loginGaviao');
            }    
        }else{
            if(auth()->check()){
                if($this->PermissaoCheck([1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,24,25,26,27])){
                    return redirect()->route('operador.admin');
                } else if($this->PermissaoCheck('47un0')){
                    return redirect()->route('aluno.painel');
                } else {
                    auth()->logout();
                    return redirect()->route('login');                
                }
            } else {
                return redirect()->route('login');
            }    
        }
    }

    public function AuthUser(Request $request){
        
        if(auth()->attempt([
            'email'=>$request->login,
            'password'=>$request->senha
        ])){

            $data['status'] = 'ok';
            $data['msgOK'] = 'ACESSO AUTORIZADO';

            // CRIANDO A SEÇÃO DO USUÁRIO COM AS RESPECTIVAS PERMISSOES

            $operadores = Operadores::where([['email', '=', $request->login], ['ativo', '=', 'S']])->first();
            $alunos = Alunos::where('email', '=', $request->login)->first();
            
            if(isset($operadores->id)){
                $this->classLog->RegistrarLog('Operador realizou login com sucesso', auth()->user()->email); 
                if(empty($operadores->id_funcao_operador)){
    
                    $permissoes[] = null;
    
                } else {
    
                    // SELECIONANDO TODAS AS PERMISSOES
                    /*
                        Cada loop abaixo deverá ficar registrado Ex: $operador_tipo[1] = '1,2,3,4,5,6,7,8,9,10,11,12,13,14'; 
                    */
    
                    $operadores_tipo_permissoes = OperadoresPermissoes::get();
                    foreach($operadores_tipo_permissoes as $permissoes_operador){
                        $operador_tipo[$permissoes_operador->operadores_tipo_id] = $permissoes_operador->permissoes;
                    }
    
                    /*
                    
                        Após o loop acima, cada função (Op Sç UETE ESA, Cmt Cia UETE...) já esta com suas permissões gravadas por índices
    
                    */
    
                    $array_funcoes = explode(',', $operadores->id_funcao_operador);
                    foreach($array_funcoes as $funcaoID){
                        $permissoes[] = $operador_tipo[$funcaoID];
                    }
    
                    $permissoes = array_unique(explode(',', implode(',', $permissoes)));
    
                }
    
                $data_user_session =    array(  'userID' => auth()->id(),
                                                'operadorID' => $operadores->id,
                                                'perfil' => $array_funcoes,
                                                'omctID' => $operadores->omcts_id,
                                                'permissoes' => array_unique(explode(',', implode(',', $permissoes)))
                                            );
    
                session()->put('login',  $data_user_session);
                
                $mqttcontroller = new MQTTController;
                $mqttcontroller->NotifyOnlineUser($operadores->postograd->postograd.' '.$operadores->nome_guerra, $operadores->usuario->id);

            } else if(isset($alunos->id)){
                $this->classLog->RegistrarLog('Aluno realizou login com sucesso', auth()->user()->email); 
                $data_user_session =    array('userID' => auth()->id(),
                                              'omctID' => $alunos->omcts_id,
                                              'permissoes' => array('47un0'));
                
                session()->put('login',  $data_user_session);
            }else{
                $this->classLog->RegistrarLog('Usuário Não Localizado ou Inativo: '. $request->login);
                $data['status'] = 'err';
                $data['msgErr'] = 'USUÁRIO NÃO LOCALIZADO OU INATIVO';
                $data['statusErr'] = 0;
            }

        } else {
            $this->classLog->RegistrarLog('Usuário errou login/senha, digitou: '. $request->login);
            $userMail = User::where('email', '=', $request->login)->count();
            $data['status'] = 'err';
            $data['msgErr'] = ($userMail==0)?'USUÁRIO NÃO LOCALIZADO':'SENHA INVÁLIDA';
            $data['statusErr'] = ($userMail==0)?0:1;
        }

        return $data;
    }

    public function AuthGaviaoUser(Request $request){
        
        if(auth()->attempt([
            'email'=>$request->login,
            'password'=>$request->senha
        ])){
            
            $data['status'] = 'ok';
            $data['msgOK'] = 'ACESSO AUTORIZADO';

            // CRIANDO A SEÇÃO DO USUÁRIO COM AS RESPECTIVAS PERMISSOES

            $operadores = Operadores::where([['email', '=', $request->login], ['ativo', '=', 'S']])->whereNotNull('qms_matriz_id')->first();

            if(isset($operadores->id)){
                $this->classLog->RegistrarLog('Operador realizou login no Gavião com sucesso', auth()->user()->email); 
                if(empty($operadores->id_funcao_operador)){
    
                    $permissoes[] = null;
    
                } else {
    
                    // SELECIONANDO TODAS AS PERMISSOES
                    // Cada loop abaixo deverá ficar registrado Ex: $operador_tipo[1] = '1,2,3,4,5,6,7,8,9,10,11,12,13,14'; 
                    $operadores_tipo_permissoes = OperadoresPermissoes::get();
                    foreach($operadores_tipo_permissoes as $permissoes_operador){
                        $operador_tipo[$permissoes_operador->operadores_tipo_id] = $permissoes_operador->permissoes;
                    }
    
                    //Após o loop acima, cada função já está com suas permissões gravadas por índices
                    $array_funcoes = explode(',', $operadores->id_funcao_operador);
                    foreach($array_funcoes as $funcaoID){
                        $permissoes[] = $operador_tipo[$funcaoID];
                    }
    
                    $permissoes = array_unique(explode(',', implode(',', $permissoes)));

                    if($operadores->qms_matriz_id == 9999){//Operador Admin ESA
                        $qms_operador[] = array('id' => 0, 'qms_matriz_id' => $operadores->qms_matriz_id);
                    }else{
                        // Seleciona as QMS disponiveis para a QMS MAtriz no Sistema Ordenando do último para a o primeiro para ser pego na primeira posição.
                        $qms_operador = QMS::where([['qms_matriz_id', '=', $operadores->qms_matriz_id]])->select('id', 'qms_matriz_id')->orderBy('id', 'DESC')->get()->toArray();
                    }

                }
    
                $data_user_session = array('userID' => auth()->id(),
                                           'operadorID' => $operadores->id,
                                           'perfil' => $array_funcoes,
                                           'qmsID' => $qms_operador,
                                           'permissoes' => array_unique(explode(',', implode(',', $permissoes))));
    
                session()->put('login',  $data_user_session);
                
                $mqttcontroller = new MQTTController;
                $mqttcontroller->NotifyOnlineUser($operadores->postograd->postograd.' '.$operadores->nome_guerra, $operadores->usuario->id);

            } else{
                $this->classLog->RegistrarLog('Usuário Não Localizado ou Inativo: '. $request->login);
                $data['status'] = 'err';
                $data['msgErr'] = 'USUÁRIO NÃO LOCALIZADO OU INATIVO';
                $data['statusErr'] = 0;
            }
        } else {
            $this->classLog->RegistrarLog('Usuário errou login/senha, digitou: '. $request->login);
            $userMail = User::where('email', '=', $request->login)->count();
            $data['status'] = 'err';
            $data['msgErr'] = ($userMail==0)?'USUÁRIO NÃO LOCALIZADO':'SENHA INVÁLIDA';
            $data['statusErr'] = ($userMail==0)?0:1;
        }

        return $data;
    }

    public function SendRecoveryPassword(Request $request, PasswordBroker $passwords)
    {
        if( $request->ajax() ){
            $this->validate($request, ['email' => 'required|email']);

            $response = $passwords->sendResetLink($request->only('email'));

            switch ($response)
            {
                case PasswordBroker::RESET_LINK_SENT:
                   return[
                       'status'=>'ok',
                       'msg'=>'Um link de recuperação de senha foi enviado para seu e-mail.'
                   ];

                case PasswordBroker::INVALID_USER:
                   return[
                       'status'=>'err',
                       'msg'=>'O email informado não pertence a um usuário cadastrado.'
                   ];
            }
        }
        
    }
    
    public function DialogRecoveryPassword(Request $request){
        return view('login')->with('token', $request->token)->with('email', $request->email);
    }


}
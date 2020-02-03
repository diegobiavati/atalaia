<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\OwnClasses\phpMQTT;
use App\User;

/* MODELS */

use App\Models\UsersTopicMQTT;
use App\Models\Operadores;

class MQTTController extends Controller
{
    public function RegisterUserMQTT(){
        if(auth()->check()){
            $data['status'] = 'ok';        
            $registerMQTT = UsersTopicMQTT::where('user_id', '=', auth()->id())->first();
            if($registerMQTT){
                $data['hasTopic'] = true;
                $data['topic'] = $registerMQTT->topic;
            } else {
                $data['hasTopic'] = false;
                $data['topic'] = auth()->user()->email.'/'.hash('sha256', auth()->user()->email.time().uniqid());
            }            
        } else {
            $data['status'] = 'err';        
        }

        return $data;
    }

    public function NotifyOnlineUser($userOnlineName, $userID){

        $mqtt = new phpMQTT();
        if ($mqtt->connect()) {

            $operadores = Operadores::get(['id_funcao_operador', 'email']);
            foreach($operadores as $operador){
                if($operador->id_funcao_operador!=''){
                    $funcoes_array = explode(',', $operador->id_funcao_operador);
                    if(in_array(1, $funcoes_array)){
                        
                        $topic_query = UsersTopicMQTT::where('user_id', '=', $operador->usuario->id)->first();
                            
                        if(isset($topic_query->topic)){                    
                            
                            $msg = array(   
                                            "msg" => '  <div class="notification_mqtt" id="userID_'.$userID.'" style="padding: 3px 6px;">
                                                            '.$userOnlineName. " está online agora!
                                                        </div>",
                                            "seletor"=>"div#popover_content_notification",
                                            "callback"=>"
                                                <script>
                                                    if($('div#empty-notification').length){
                                                        $('div#empty-notification').remove();
                                                    }
                                                    $('#notification_online').trigger('play');
                                                    $('span.ion-android-notifications').css('color', '#B40404');
                                                </script>"
                                        );

                            $mqtt->publish($topic_query->topic, ''.json_encode($msg).'', 0);
                            
                        }
                        
                    }
                }
            }

            $mqtt->close();
        }
        
    }

    public function NotifyOfflineUser(Request $request){

        $mqtt = new phpMQTT();
        if ($mqtt->connect()) {

            $operadores = Operadores::get(['id_funcao_operador', 'email']);
            foreach($operadores as $operador){
                if($operador->id_funcao_operador!=''){
                    $funcoes_array = explode(',', $operador->id_funcao_operador);
                    if(in_array(1, $funcoes_array)){
                        
                        $topic_query = UsersTopicMQTT::where('user_id', '=', $operador->usuario->id)->first();
                            
                        if(isset($topic_query->topic)){                    
                            
                            $msg = array(   
                                            "msg" => null,
                                            "seletor"=>null,
                                            "callback"=>"
                                                <script>
                                                    $('div#userID_".$request->id."').remove();
                                                    if(!$('div.notification_mqtt').length){
                                                        $('#notification_finish').trigger('play');
                                                        $('div.top-notificacao-icons span').eq(0).css('color', '#696969');
                                                        $('div#popover_content_notification').html('<div id=\"empty-notification\">Nenhuma notificação recente</div>');
                                                    }
                                                </script>"
                                        );

                            $mqtt->publish($topic_query->topic, ''.json_encode($msg).'', 0);
                            
                        }
                        
                    }
                }
            }

            $mqtt->close();
        }
        
    }

}

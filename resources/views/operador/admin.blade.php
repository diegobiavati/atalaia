@extends('headers')
@section('js-includes')
<script src="/js/jquery.mask.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.1/mqttws31.js" type="text/javascript"></script>
<script>

// Create a client instance
client = new Paho.MQTT.Client('{{$_SERVER['SERVER_NAME']}}', 3000, '{{auth()->user()->email}}');

// set callback handlers
client.onConnectionLost = onConnectionLost;
client.onMessageArrived = onMessageArrived;

lastWill = new Paho.MQTT.Message('{"id":"{{auth()->id()}}", "msg":'+null+'}'); // Mensagem
lastWill.destinationName = 'status/connection/disconnected'; // topico 

// connect the client

function conectClient(){
    client.connect({
        onSuccess:onConnect, 
        onFailure:function(){
            setTimeout(function(){
                conectClient();
            }, 5*1000);
        },
        willMessage: lastWill 
    });
}

//conectClient();

// called when the client connects

function onConnect() {
    $.ajax({
        type:'GET',
        dataType: 'json',
        url: '{{route('register_user_mqtt')}}',
        success: function(data){
            if(data.status=='ok'){
                client.subscribe(data.topic);
                //var dataMsg = '{"id":"{{auth()->id()}}", "msg":"Bem-vindo"}';
                if(data.hasTopic==false){
                    client.send('status/connection/connected', '{"id":"{{auth()->id()}}", "msg":\"'+data.topic+'\"}');
                }
            }
        }
    });
}

// called when the client loses its connection
function onConnectionLost(responseObject) {
    client.send('status/connection/desconnected', '{"id":"{{auth()->id()}}", "msg":'+null+'}');   
    setTimeout(function(){
        conectClient();
    }, 2000);    
  if (responseObject.errorCode !== 0) {
    //console.log("onConnectionLost:"+responseObject.errorMessage);    
  }
}

// called when a message arrives
function onMessageArrived(message) {
  //console.log("onMessageArrived:"+message.payloadString);
  var jsonMsg = JSON.parse(message.payloadString);
  $(jsonMsg.seletor).append(jsonMsg.msg);
  $('body').append('<div id="temp">' + jsonMsg.callback + '</div>');
  $('div#temp').remove();
}

</script>

@stop
@section('css-includes')
<link href="/css/ionicons.css" rel="stylesheet" type="text/css" />
<link href="/css/menu_style.css" rel="stylesheet" type="text/css" />
<link href="/css/style.css" rel="stylesheet" type="text/css" />

@stop

@section('title', 'Atalaia :: Admin Operadores')

@section('content')

<audio id="notification_online" controls preload="none" style="display: none;"> 
    <source src="/sounds/notification/light.mp3" type="audio/mpeg">
</audio>

 <audio id="notification_finish" controls preload="none" style="display: none;"> 
    <source src="/sounds/notification/quiet-knock.mp3" type="audio/mpeg">
 </audio>

<div id="menu-lateral">
    <div id="lateral-top">
        <img class="logo_sistema" src="/images/logo_atalaia_pb.png" />        
    </div>
    <div id="lateral-profile-top">
        <div class="lateral-profile-top-content">
            <h4>SISTEMA ATALAIA
                <small class="text-muted" style="font-size: 14px;">
                    <br />GESTÃO ACADÊMICA MILITAR
                    <br />ESCOLHA DE QMS
                </small>
            </h4>
        </div>

        <div style="border-top: 1px solid #ccc; height: auto; padding: 0 3px 12px 3px;">
            <div class="data_user_imagem_perfil" style="position: absolute; margin: -32px 0 0 101px; background: url({{$img_perfil}}) no-repeat center center; background-size: cover; width: 64px; height: 64px; border-radius: 50%;"></div> 
            <div style=" text-align: center; margin-top: 36px; font-size: 14px;">
            <span id="postograd_user"><b>{{$operador->postograd->postograd_abrev }}</b></span> <span id="nome_guerra_user"><b>{{$operador->nome_guerra}}</b></span>
            <br /><span id="omct_user">{{$operador->omcts->sigla_omct }}</span>
                @if($operador->id_funcao_operador!='' && is_array(explode(',', $operador->id_funcao_operador)))
                    <br />
                    <div style="padding: 0 8px;">
                        <span id="funcao_user" style="color: #088A4B; font-size: 12px;">
                            @foreach(explode(',', $operador->id_funcao_operador) as $permissao)
                                {{$data[$permissao]}}
                                @if(!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </span>    
                    </div>
                @else
                    <br />
                    <span id="funcao_user" style="color: #DF0101; font-size: 12px;">
                        NÃO CONFIGURADA
                    </span>
                @endif
            </div>
            <div class="clear"></div>
            <div style="text-align: center; margin: 10px 8px 0 0">
                <button type="button" class="btn btn-success btn-sm" onclick="dialogEditarMeuPerfil({{auth()->id()}});"><i class="ion-edit"></i> Editar perfil</button>
            </div>
            <div style="margin-top: 8px; font-size: 14px;">
                <a class="no-style" href="{{route('atalaia.logout')}}">Sair</a>
            </div>
        </div>

    </div>
    
    <div class="box-ul-menu scroll_style_01" style="color: #fff; overflow-y: auto;">
        <ul class="list-group list-group-flush">

            @if($ownauthcontroller->PermissaoCheck(1))
                @include('menu_admin_operadores')            
            @else
            
                @if($ownauthcontroller->PermissaoCheck(18)) <!-- Visão geral restrita a respectiva UETE -->
                    <li class="list-group-item justify-content-between align-items-center menu-list-01">
                        <a id="visao-geral-omct" href="javascript: void(0);">                
                            <i class="ion-ios-eye"></i>
                            Visão geral da UETE
                            <span class="badge badge-primary badge-pill"></span>
                        </a>
                    </li>
                @endif
                @if($ownauthcontroller->PermissaoCheck(10)) <!-- Visão geral restrita a respectiva UETE -->
                    <li class="list-group-item justify-content-between align-items-center menu-list-01">
                            <a id="alunos" href="javascript: void(0);">
                                <i class="ion-android-contacts"></i>
                                Alunos
                                <span class="badge badge-primary badge-pill"></span>
                            </a>                
                    </li>
                @endif
                @if($ownauthcontroller->PermissaoCheck(3)) <!-- Visão geral restrita a respectiva UETE -->
                    <li class="list-group-item justify-content-between align-items-center menu-list-01">
                        <a id="avaliacoes" href="javascript: void(0);">
                            <i class="ion-ios-compose"></i> 
                            Avaliações
                            <span class="badge badge-primary badge-pill"></span>
                        </a>
                    </li>
                @endif
                @if($ownauthcontroller->PermissaoCheck(3)) <!-- Visão geral restrita a respectiva UETE -->
                    <li class="list-group-item justify-content-between align-items-center menu-list-01">
                        <!--<a id="lancar-taf-aluno" href="javascript: void(0);">-->
                        <a id="menu-tfm-aluno" href="javascript: void(0);">
                            <i class="ion-android-walk"></i> 
                            <!--Lançar TFM aluno-->
                            TFM do aluno
                            <span class="badge badge-primary badge-pill"></span>
                        </a>
                    </li>
                @endif
                @if($ownauthcontroller->PermissaoCheck(25)) <!-- Visão geral restrita para SPPA UETE -->
                    <li class="list-group-item justify-content-between align-items-center menu-list-01">
                        <a id="voluntarios-para-aviacao" href="javascript: void(0);">                
                            <i class="ion-plane"></i>
                            Voluntários para Aviação
                            <span class="badge badge-primary badge-pill"></span>
                        </a>                
                    </li>
                @endif
                @if($ownauthcontroller->PermissaoCheck(21))
                    <li class="list-group-item justify-content-between align-items-center menu-list-01">
                        <a id="fale-com-aluno" href="javascript: void(0);">                
                            <i class="ion-chatbubbles"></i>
                                Fale com aluno
                            <span class="badge badge-primary badge-pill"></span>
                        </a>
                    </li>
                @endif
                @if($ownauthcontroller->PermissaoCheck(24))
                    <li class="list-group-item justify-content-between align-items-center menu-list-01">
                        <a id="viewLancamentos" href="javascript: void(0);">
                            <i class="ion-paper-airplane"></i>
                            Lançamentos
                            <span class="badge badge-primary badge-pill"></span>
                        </a>
                    </li>
                @endif
                @if($ownauthcontroller->PermissaoCheck(1) || $ownauthcontroller->PermissaoCheck(20) || $ownauthcontroller->PermissaoCheck(22))
                    <li class="list-group-item justify-content-between align-items-center menu-list-01">
                        <a id="relatorios" href="javascript: void(0);">                
                            <i class="ion-ios-pie"></i>
                                Documentação e relatórios
                            <span class="badge badge-primary badge-pill"></span>
                        </a>
                    </li>
                @endif
                    
            @endif

        </ul>    
    </div> 
</div>

<div id="top">
    <div class="top-notificacao-icons" style="margin: 20px 330px; font-size: 24px; width: 74px;">
        <a href="javascript: void(0);" class="no-style" data-toggle="popover_notification" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
            <!--span class="ion-android-notifications" onclick="$(this).css('color', '#696969');"></span-->  
        </a>
        <!--a href="javascript: void(0);" class="no-style" data-toggle="popover_mensagens" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
            <span class="ion-android-chat" style="margin-left: 8px" onclick="$(this).css('color', '#696969');"></span>   
        </a-->

        <div id="popover_content_notification" style="display: none;">Sem notificações recentes</div>

        <div id="popover_content_mensagens" style="display: none;">Não há novas mensagens</div>

    </div>
    <div style="float: right; width: 300px; margin-top: -78px">
        <div style="float: right; margin: 0 4px 0 10px;">
            <img class="logo_organizacao" src="/images/logo_esa.png"  />
        </div>
        <div style="float: right; margin: 10px 12px 0 0;">
            Escola de Sargentos das Armas<br/>
            Escola Sargento Max Wolf Filho
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>

<div id="hack_top"></div>

<div id="content">
    <div class="inside-content"></div>
</div>

<!-- MODALS CONTENTE DINAMICA-->
    
    <div class="modal fade" id="modalDinamica" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header" style="background-color: #F2F2F2">
                <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                <!--button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button-->
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer" style="text-align: right;">
                  <!--button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary">Save changes</button-->
                </div>
              </div>
            </div>
    </div>  
    
<!-- FINAL MODAL CONTENTE DINAMICA-->

<!-- MODALS CONTENTE DINAMICA NO STYLE -->
    
<div class="modal fade" id="modalDinamica_no_style" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <!--div class="modal-header" style="background-color: #F2F2F2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div-->
        <div class="modal-body" style="padding: 0"></div>
        <!--div class="modal-footer" style="text-align: right;">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div-->
      </div>
    </div>
</div>  

<!-- FINAL MODAL CONTENTE DINAMICA NO STYLE-->

<!-- MODALS confirmAcao DINAMICA-->

    <div class="modal fade bd-example-modal-sm" id="modalConfirmAcao" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
          <div class="modal-content" style="border-radius: 0; padding: 10px;">
                <div style="border-bottom: 1px solid #ccc;"><i class="ion-android-hand" style="font-size: 22px; vertical-align: middle;"></i><span style="margin-left: 10px;"><b>ATENÇÃO!</b></span></div>
                <div class="content-confirmAcao" style="margin: 6px 0;"></div>
                <div style="text-align: right;">
                    <div style="display: inline-block;">
                        <a id="confirm-buttom" class="no-style" href="javascript: void(0);"><span style="color: #363636;"><strong>SIM</strong></span></a>
                    </div>
                    <div style="display: inline-block; margin-left: 8px;">
                        <a class="no-style" href="javascript: void(0);" data-dismiss="modal"><span style="color: #2E64FE;"><strong>NÃO</strong></span></a>
                    </div>
                </div>              
          </div>
        </div>
    </div>

<!-- FINAL MODAL confirmAcao DINAMICA-->



<script>
        $.ajaxSetup({
            error: function(jqxhr){
                if(jqxhr.status==500){
                    //location.href='{{route('atalaia')}}';
                }
            }
        });

        function b64EncodeUnicode(str) {
            // first we use encodeURIComponent to get percent-encoded UTF-8,
            // then we convert the percent encodings into raw bytes which
            // can be fed into btoa.
            return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
                function toSolidBytes(match, p1) {
                    return String.fromCharCode('0x' + p1);
            }));
        }

        function b64DecodeUnicode(str) {
            // Going backwards: from bytestream, to percent-encoding, to original string.
            return decodeURIComponent(atob(str).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));
        }


        $("[data-toggle=popover_notification]").popover({
                trigger: 'focus',
                html: true,
                delay: { "show": 80, "hide": 100 }, 
                content: function() {
                    return $('#popover_content_notification').html();
                }
            }); 
            
            $("[data-toggle=popover_mensagens]").popover({
                trigger: 'focus',
                html: true, 
                delay: { "show": 80, "hide": 100 }, 
                content: function() {
                    return $('#popover_content_mensagens').html();
                }
        });    

        (function($){
        $.fn.confirmAcao = function(content, callback) {
            return this.each(function() {
                $('div.content-confirmAcao').html(content);
                $('#modalConfirmAcao').modal('show');
                $('#confirm-buttom').click(function(){
                    callback();
                    
                    $('#modalConfirmAcao').modal('hide');
                                        
                    // DESASSOCIA EVENTOS DE CLIQUE ANTERIORES

                    $(this).off(); 
                })
            });
        };
        })(jQuery);

        function toogleContent(element, val_1, val_2){
            var val_0 = $(element).text();
            if(val_0==val_1){
                $(element).text(val_2);    
            } else if(val_0==val_2) {
                $(element).text(val_1);
            }
        }

        function toogleContent2(elementCheckBox, target_elementID){
            if($(elementCheckBox).val()==0){
                $(elementCheckBox).val(1);    
                $(target_elementID).slideDown();
            } else {
                $(elementCheckBox).val(0);    
                $(target_elementID).slideUp();
            }
        }



        /* AJAX - EDITAR DADOS OPERADOR */

        function AtualizarOperador(id){
            var style_bg_inicial = $('tr#operador_' + id).css('background-color');
            var dataForm = $('form#atualiza_dados_operador').serialize();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/atualizar-operador/' + id,
                beforeSend: function(){
                    $('div.errors-cadastro-operador ul').remove().parent().hide();
                    $('tr#operador_' + id).css('background-color', '#81F7BE');
                },

                success: function(data){
                    $('div#modalDinamica').modal('hide');
                    $('tr#operador_' + id).css('background-color', style_bg_inicial);
                    $('tr#operador_' + id + ' td').eq(0).html(data.content_tr_eq0);
                    $('tr#operador_' + id + ' td').eq(1).html(data.content_tr_eq1);
                    $('tr#operador_' + id + ' td').eq(2).html(data.content_tr_eq2);
                    $('tr#operador_' + id + ' td').eq(3).html(data.content_tr_eq3 + '<br />' + data.content_tr_eq4);
                    if(data.typeUser==1){
                        $('span#postograd_user').html('<b>' + data.content_1 + '</b>');
                        $('span#nome_guerra_user').html('<b>' + data.nomeGuerra + '</b>');
                        $('span#omct_user').html(data.content_tr_eq2);
                        $('span#funcao_user').html(data.content_tr_eq1);
                    }

                },

                error: function(jqxhr){
                    $('tr#operador_' + id).css('background-color', style_bg_inicial);
                    if(jqxhr.status==500){
                        alert('Erro interno! Verifique se o email informado já se encontra cadastrado.');
                    } else if(jqxhr.status==422){
                        $('div.errors-cadastro-operador').slideDown(100);
                        var errors = $.parseJSON(jqxhr.responseText);
                        $('div.errors-cadastro-operador').prepend('<ul style="margin: 0 6px;"></ul>');                            
                        $.each(errors.errors, function (index, value) {
                            $('div.errors-cadastro-operador ul').append('<li>' + value + '</li>');
                        });                           
                    }
                }                
            });
        }

        /* MOSTRA DIALOGO EDITAR OPERADORES */

        function dialogEditarOperador(id){
             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-editar-operador/' + id,
                beforeSend: function(){
                    loadingModalDinamica('show', 'lg');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    loadingModalDinamica('hide', 'lg');
                }
            });
        }

        /* AJAX - EDITAR MEU PERFIL */

        function AtualizarMeuPerfil(){
            var dataForm = $('form#atualizar_meu_perfil').serialize();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/atualizar-meu-perfil/' + {{auth()->id()}},
                beforeSend: function(){
                    $('div.errors-meu-cadastro ul').remove().parent().hide();
                },
                success: function(data){
                    if(data.status=='ok'){
                        $('span#postograd_user').html('<b>' + data.postoGrad + '</b>');
                        $('div#modalDinamica').modal('hide');
                    }

                },
                error: function(jqxhr){
                    if(jqxhr.status==500){
                        alert('Erro interno! Verifique se o email informado já se encontra cadastrado.');
                    } else if(jqxhr.status==422){
                        $('div.errors-meu-cadastro').slideDown(100);
                        var errors = $.parseJSON(jqxhr.responseText);
                        $('div.errors-meu-cadastro').prepend('<ul style="margin: 0 6px;"></ul>');                            
                        $.each(errors.errors, function (index, value) {
                            $('div.errors-meu-cadastro ul').append('<li>' + value + '</li>');
                        });
                    }
                }                
            });
        }          

        /* MOSTRA DIALOGO EDITAR MEU PERFIL */

        function dialogEditarMeuPerfil(){
             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-editar-meu-perfil',
                beforeSend: function(){
                    loadingModalDinamica('show', 'sm');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    loadingModalDinamica('hide', 'sm');
                }
            });
        }

        /* AJAX - ADICIONAR OPERADOR */
        function AdicionarOperador(){
            var dataForm = $('form#adicionar_dados_operador').serialize();
            
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/adicionar-operador',
                beforeSend: function(){
                    $('div.errors-cadastro-operador ul').remove().parent().hide();
                },
                success: function(data){
                    if(data.status=='ok'){
                        $('div#modalDinamica').modal('hide');
                        $('a#gerenciar-operadores').trigger('click');
                        $('div.errors-cadastro-operador ul').remove().parent().hide();
                    }
                },
                error: function(jqxhr){
                    if(jqxhr.status==500){
                        alert('Erro interno! Verifique se o email informado já se encontra cadastrado.');
                    } else if(jqxhr.status==422){
                        $('div.errors-cadastro-operador').slideDown(100);
                        var errors = $.parseJSON(jqxhr.responseText);
                        $('div.errors-cadastro-operador').prepend('<ul style="margin: 0 6px;"></ul>');                            
                        $.each(errors.errors, function (index, value) {
                            $('div.errors-cadastro-operador ul').append('<li>' + value + '</li>');
                        });                           
                    }
                }                    
            });
        }

        /* AJAX - ADICIONAR ANO DE FORMAÇAO */

        function AdicionarAnoFormacao(){
            var dataForm = $('form#adicionar_ano_formacao').serialize();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/adicionar-ano-formacao',
                beforeSend: function(){
                    //$('div.errors-cadastro-operador ul').remove().parent().hide();
                },
                success: function(data){
                    if(data.status=='ok'){
                        $('div#modalDinamica').modal('hide');
                        $('a#anos-de-formacao').trigger('click');
                        //$('div.errors-cadastro-operador ul').remove().parent().hide();
                    } else {
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-cadastro-ano-formacao').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar inserir um novo ano de formação. Certifique-se de que os dados foram preenchidos corretamente!').slideDown();    
                    }
                },
                error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-cadastro-ano-formacao').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar inserir um novo ano de formação. Certifique-se de que este ano não está inserido no sistema ou que  o ano informado esteja no formato AAAA (Ex. 2020).').slideDown();    
                    }
                }                    
            });
        }

        /* MOSTRA DIALOGO ADICIONAR AVALIAÇÃO */

        function dialogAdicionarAvaliacao(){
            $('div.errors-adicionar-avaliacoes2').slideUp();
            $('.collapse').collapse('hide');
             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-adicionar-avaliacao',
                beforeSend: function(){
                    loadingModalDinamica('show', 'lg');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    loadingModalDinamica('hide', 'lg');
                    $('.carousel').carousel({
                        interval: false
                    });                    
                    
                }
            });            
        }

        /* MOSTRA DIALOGO ADICIONAR AVALIAÇÃO RECUPERAÇÃO*/

        function dialogAdicionarAvaliacaoRec(){
            $('div.errors-adicionar-avaliacoes2').slideUp();
            $('.collapse').collapse('hide');
             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-adicionar-avaliacao-recuperacao',
                beforeSend: function(){
                    loadingModalDinamica('show', 'lg');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    loadingModalDinamica('hide', 'lg');
                    $('.carousel').carousel({
                        interval: false
                    });                    
                    
                }
            });            
        }

        /* BOX DINAMICO REFERENCIA DA CHAMADA */

        function showDialogReferenciaAvaliacao(select_value){
            if($(select_value).val()!=1){
                $('.dialog-referencia-avaliacao').slideDown();
            } else {
                $('.dialog-referencia-avaliacao').slideUp();
            }
        }

        /* CARREGA DINAMICAMENTE DIALOGO DAS CHAMADAS (DENTRO DA MODAL ADICIONAR AVALIAÇÃO) */

        function loadDialogChamadas(select_value){
            var disciplina_id = $(select_value).val();
             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-chamadas/' + disciplina_id,
                beforeSend: function(){
                    $('.box-dialog-chamadas').html('<div style="text-align: center;"><img src="/images/loadings/loading_01.svg" style="width: 20px;"> Carregando opções...</div>');
                },
                success: function(data){
                    $('.box-dialog-chamadas').html(data.result);
                }
            });                        
        }

        /* CARREGA PORTARIA AO MUDAR SELECT */

        function loadConfigPortaria(select_value){
            $('div.errors-adicionar-portaria2').empty().slideUp();
            var portaria_id = $(select_value).val();
            if(portaria_id!=0){
                $.ajax({
                    type:'GET',
                    dataType: 'json',
                    url: '/ajax/load-content-portaria/' + portaria_id,
                    beforeSend: function(){
                        $('div#box-content-portarias').html('<div style="text-align: center;"><img src="/images/loadings/loading_01.svg" style="width: 20px;"> Carregando portaria...</div>');
                    },
                    success: function(data){
                        $('div#box-content-portarias').html(data.result);
                    }
                });                
            }
        }

        /* ADICIONAR AVALIAÇÃO */

        function AdicionarAvaliacao(){
            var dataForm = $('form#adicionar_avaliacao').serialize();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/adicionar-avaliacao',
                beforeSend: function(){
                    $('div.errors-adicionar-avaliacoes ul').remove().parent().hide();
                },
                success: function(data){
                    if(data.data_prova=='err'){
                        $('html, body').animate({scrollTop: 10}, 200);
                        $('div.errors-adicionar-avaliacoes').html('<strong>ATENÇÃO: </strong> A data da prova não deve ser menor que o prazo para lançamento do Pronto de Faltas e Grau escolar. Por favor, aumente o prazo para UETE lançar os resultados ou altere a data da avaliação.').slideDown();     
                    } else if(data.data_prova=='err1'){
                        $('html, body').animate({scrollTop: 10}, 200);
                        $('div.errors-adicionar-avaliacoes').html('<strong>ATENÇÃO: </strong> A avaliação deve ser criada mais próxima de sua realização.').slideDown();     
                    } else if(data.data_prova=='err2'){
                        $('html, body').animate({scrollTop: 10}, 200);
                        $('div.errors-adicionar-avaliacoes').html('<strong>ATENÇÃO: </strong> A data/hora informada é inválida.').slideDown();     
                    } else {
                        if(data.err_chamada=='err'){
                        $('html, body').animate({scrollTop: 10}, 200);
                            $('div.errors-adicionar-avaliacoes').html('<strong>ATENÇÃO: </strong> Novas chamadas devem ter uma outra avaliação de referência.').slideDown();    
                        } else {
                            if(data.status=='ok'){
                                $('div#modalDinamica').modal('hide');
                                $('a#avaliacoes').trigger('click');
                                setTimeout(function(){
                                    $('button#disciplina_' + data.disciplinaID).trigger('click');
                                    $('blockquote#disciplina_' + data.disciplinaID).show();
                                    $('blockquote#disciplina_' + data.disciplinaID + ' footer').html('Uma avaliação criada agora mesmo!');
                                    setTimeout(function(){
                                        $('blockquote#disciplina_' + data.disciplinaID).fadeOut();
                                        $('blockquote#disciplina_' + data.disciplinaID + ' footer').empty();
                                    }, 10000);
                                }, 460);
                            } else {
                                $('div#modalDinamica').modal('hide');
                                $('div.errors-adicionar-avaliacoes2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar inserir uma avaliação').slideDown();    
                            }
                        }
                    }
                },
                error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-avaliacoes2').html('<strong>ATENÇÃO: </strong> Houve um erro interno ao tentar inserir uma nova avaliação. Por favor, repita a operação.').slideDown();    
                    } else if(jqxhr.status==422){
                        $('div.errors-adicionar-avaliacoes').slideDown(100);
                        var errors = $.parseJSON(jqxhr.responseText);
                        $('div.errors-adicionar-avaliacoes').prepend('<ul style="margin: 0 6px;"></ul>');                            
                        $.each(errors.errors, function (index, value) {
                            $('div.errors-adicionar-avaliacoes ul').append('<li>' + value + '</li>');
                        });  
                    }
                }                    
            });            
        }

        /* EDITAR AVALIAÇÃO */

        function EditarAvaliacao(id){
            var dataForm = $('form#editar_avaliacao').serialize();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/editar-avaliacao/' + id,
                beforeSend: function(){
                    $('div.errors-editar-avaliacoes ul').remove().parent().hide();
                },
                success: function(data){
                    //errors-editar-avaliacoes
                    if(data.data_prova=='err'){
                        $('div.errors-editar-avaliacoes').html('<strong>ATENÇÃO: </strong> A data da prova não deve ser menor que o prazo para lançamento do Pronto de Faltas e Grau escolar. Por favor, aumente o prazo para UETE lançar os resultados ou altere a data da avaliação.').slideDown();     
                    } else if(data.data_prova=='err1'){
                        $('div.errors-editar-avaliacoes').html('<strong>ATENÇÃO: </strong> A avaliação deve ser criada mais próxima de sua realização.').slideDown();     
                    } else if(data.data_prova=='err2'){
                        $('div.errors-editar-avaliacoes').html('<strong>ATENÇÃO: </strong> A data/hora informada é inválida.').slideDown();     
                    } else {
                        if(data.status=='ok'){
                            $('div#modalDinamica').modal('hide');
                            $('a#avaliacoes').trigger('click');
                            setTimeout(function(){
                                $('button#disciplina_' + data.disciplinaID).trigger('click');
                                $('blockquote#disciplina_' + data.disciplinaID).show();
                                $('blockquote#disciplina_' + data.disciplinaID + ' footer').html('Uma avaliação atualizada agora mesmo!');
                                setTimeout(function(){
                                    $('blockquote#disciplina_' + data.disciplinaID).fadeOut();
                                    $('blockquote#disciplina_' + data.disciplinaID + ' footer').empty();
                                }, 10000);
                            }, 460);
                        } else {
                            $('div#modalDinamica').modal('hide');
                            $('div.errors-adicionar-avaliacoes2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar editar a avaliação').slideDown();    
                        }
                    }
                },
                error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-avaliacoes2').html('<strong>ATENÇÃO: </strong> Houve um erro interno ao tentar inserir uma nova avaliação. Por favor, repita a operação.').slideDown();    
                    } else if(jqxhr.status==422){
                        $('div.errors-editar-avaliacoes').slideDown(100);
                        var errors = $.parseJSON(jqxhr.responseText);
                        $('div.errors-editar-avaliacoes').prepend('<ul style="margin: 0 6px;"></ul>');                            
                        $.each(errors.errors, function (index, value) {
                            $('div.errors-editar-avaliacoes ul').append('<li>' + value + '</li>');
                        });  
                    }
                }                    
            });            
        }

        /* ADICIONAR AVALIAÇÃO DE RECUPERAÇÃO*/

        function AdicionarAvaliacaoRec(){
            var dataForm = $('form#adicionar_avaliacao_recuperacao').serialize();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/adicionar-avaliacao-recuperacao',
                beforeSend: function(){
                    $('div.errors-adicionar-avaliacoes ul').remove().parent().hide();
                },
                success: function(data){
                    if(data.data_prova=='err'){
                        $('div.errors-adicionar-avaliacoes').html('<strong>ATENÇÃO: </strong> A data da prova não deve ser menor que o prazo para lançamento do Pronto de Faltas e Grau escolar. Por favor, aumente o prazo para UETE lançar os resultados ou altere a data da avaliação.').slideDown();     
                    } else if(data.data_prova=='err1'){
                        $('div.errors-adicionar-avaliacoes').html('<strong>ATENÇÃO: </strong> A avaliação deve ser criada mais próxima de sua realização.').slideDown();     
                    } else if(data.data_prova=='err2'){
                        $('div.errors-adicionar-avaliacoes').html('<strong>ATENÇÃO: </strong> A data/hora informada é inválida.').slideDown();     
                    } else {                    
                        if(data.err_chamada=='err'){
                            $('div.errors-adicionar-avaliacoes').html('<strong>ATENÇÃO: </strong> Novas chamadas devem ter uma outra avaliação de referência.').slideDown();    
                        } else {
                            if(data.status=='ok'){
                                $('div#modalDinamica').modal('hide');
                                $('a#avaliacoes').trigger('click');
                                setTimeout(function(){
                                    $('button#disciplina_' + data.disciplinaID).trigger('click');
                                    $('blockquote#disciplina_' + data.disciplinaID).show();
                                    $('blockquote#disciplina_' + data.disciplinaID + ' footer').html('Avaliação de recuperação criada agora mesmo!');
                                    setTimeout(function(){
                                        $('blockquote#disciplina_' + data.disciplinaID).fadeOut();
                                        $('blockquote#disciplina_' + data.disciplinaID + ' footer').empty();
                                    }, 10000);
                                }, 460);
                            } else {
                                $('div#modalDinamica').modal('hide');
                                $('div.errors-adicionar-avaliacoes2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar inserir uma avaliação').slideDown();    
                            }
                        }
                    }                        
                },
                error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-avaliacoes2').html('<strong>ATENÇÃO: </strong> Houve um erro interno ao tentar inserir uma nova avaliação. Por favor, repita a operação.').slideDown();    
                    } else if(jqxhr.status==422){
                        $('div.errors-adicionar-avaliacoes').slideDown(100);
                        var errors = $.parseJSON(jqxhr.responseText);
                        $('div.errors-adicionar-avaliacoes').prepend('<ul style="margin: 0 6px;"></ul>');                            
                        $.each(errors.errors, function (index, value) {
                            $('div.errors-adicionar-avaliacoes ul').append('<li>' + value + '</li>');
                        });  
                    }
                }                    
            });            
        }

        /* MOSTRA DIALOGO REMOVER AVALIAÇÃO */

        function dialogRemoverAvaliacao(id){
            $('div.errors-adicionar-avaliacoes2').hide();
            $(document).confirmAcao('Deseja realmente excluir esta <i>Avaliação</i>?', function(){
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/ajax/remover-avaliacao/' + id,
                    beforeSend: function(){
                    },
                    success: function(data){
                        if(data.status=='ok'){
                            $('a#avaliacoes').trigger('click');
                            setTimeout(function(){
                                $('button#disciplina_' + data.disciplinaID).trigger('click');
                                $('blockquote#disciplina_' + data.disciplinaID).show();
                                $('blockquote#disciplina_' + data.disciplinaID + ' footer').html('Uma avaliação excluída agora mesmo!');
                                setTimeout(function(){
                                    $('blockquote#disciplina_' + data.disciplinaID).fadeOut();
                                    $('blockquote#disciplina_' + data.disciplinaID + ' footer').empty();
                                }, 10000);
                            }, 460);                            
                        } else if(data.status=='err') {
                            $('div.errors-adicionar-avaliacoes2').html('Houve um erro ao tentar excluir a avaliação. Verifique se esta avaliação não possui uma chamada vinculada e tente novamente').slideDown();    
                        } else {
                            $('div.errors-adicionar-avaliacoes2').html('Houve um erro interno ao tentar excluir a avaliação.').slideDown();    
                        }
                    },
                    error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div.errors-adicionar-avaliacoes2').html('Houve um erro interno ao tentar excluir a avaliação.').slideDown();    
                    }
                }  
                });                
            });            
        }
        
        /* DIALOG EDITAR AVALIAÇÃO */

        function dialogEditarAvaliacao(id){
            $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-editar-avaliacao/' + id,
                beforeSend: function(){
                    loadingModalDinamica('show', 'lg');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    loadingModalDinamica('hide', 'lg');
                }
            });
        }

        /* MOSTRA DIALOGO IMPORTAR DICIPLINAS */

        function dialogImportarDisciplinas(){
            $('div.errors-adicionar-disciplinas2').slideUp();
             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-importar-disciplina',
                beforeSend: function(){
                    loadingModalDinamica('show', 'lg');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    loadingModalDinamica('hide', 'lg');
                    $('.carousel').carousel({
                        interval: false
                    });                    
                    
                }
            });            
        }

        /* AJAX - ADICIONAR DISCIPLINA */

        function AdicionarDisciplina(){
            var dataForm = $('form#adicionar_disciplina').serialize();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/adicionar-disciplina',
                beforeSend: function(){
                    $('div.errors-adicionar-disciplinas ul').remove().parent().hide();
                },
                success: function(data){
                    if(data.status=='ok'){
                        $('div#modalDinamica').modal('hide');
                        $('a#gerenciar-disciplinas').trigger('click');
                    } else {
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-disciplinas2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar inserir uma disciplina').slideDown();    
                    }
                },
                error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-disciplinas2').html('<strong>ATENÇÃO: </strong> Houve um erro interno ao tentar inserir uma nova disciplina.').slideDown();    
                    } else if(jqxhr.status==422){
                        $('div.errors-adicionar-disciplinas').slideDown(100);
                        var errors = $.parseJSON(jqxhr.responseText);
                        $('div.errors-adicionar-disciplinas').prepend('<ul style="margin: 0 6px;"></ul>');                            
                        $.each(errors.errors, function (index, value) {
                            $('div.errors-adicionar-disciplinas ul').append('<li>' + value + '</li>');
                        });  
                    }
                }                    
            });
        }

        /* AJAX - IMPORTAR DISCIPLINA */

        function ImportarDisciplinas(){
            var dataForm = $('form#importar_disciplinas').serialize();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/importar-disciplinas',
                beforeSend: function(){
                    $('div.errors-importar-disciplinas ul').remove().parent().hide();
                },
                success: function(data){
                    if(data.status=='ok'){
                        $('div#modalDinamica').modal('hide');
                        $('a#gerenciar-disciplinas').trigger('click');
                    } else {
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-disciplinas2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar inserir uma disciplina').slideDown();    
                    }
                },
                error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-disciplinas2').html('<strong>ATENÇÃO: </strong> Houve um erro interno ao tentar inserir uma nova disciplina. Por favor, verifique se a <i>Disciplina</i> já está cadastrada.').slideDown();    
                    } else if(jqxhr.status==422){
                        $('div.errors-importar-disciplinas').slideDown(100);
                        var errors = $.parseJSON(jqxhr.responseText);
                        $('div.errors-importar-disciplinas').prepend('<ul style="margin: 0 6px;"></ul>');                            
                        $.each(errors.errors, function (index, value) {
                            $('div.errors-importar-disciplinas ul').append('<li>' + value + '</li>');
                        });  
                    }
                }                    
            });
        }

        /* MOSTRA DIALOGO ADICIONAR DICIPLINAS */          

        function dialogAdicionarDisciplina(){
            $('div.errors-adicionar-disciplinas2').slideUp();
             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-adicionar-disciplina',
                beforeSend: function(){
                    loadingModalDinamica('show', 'sm');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    loadingModalDinamica('hide', 'sm');
                }
            });
        }

        /* MOSTRA DIALOGO EDITAR DISCIPLINAS */

        function dialogEditarDisciplina(id){
             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-editar-disciplina/' + id,
                beforeSend: function(){
                    loadingModalDinamica('show', 'sm');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    loadingModalDinamica('hide', 'sm');
                }
            });
        }

        /* AJAX - EDITAR DADOS DISCIPLINA */

        function AtualizarDisciplina(id){
            var style_bg_inicial = $('tr#operador_' + id).css('background-color');
            var dataForm = $('form#editar_disciplina').serialize();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/atualizar-disciplina/' + id,
                beforeSend: function(){
                    $('div.errors-adicionar-disciplinas ul').remove().parent().hide();
                },
                success: function(data){
                    if(data.status=='ok'){
                        $('div#modalDinamica').modal('hide');
                        $('a#gerenciar-disciplinas').trigger('click');
                    } else {
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-disciplinas2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar inserir uma disciplina').slideDown();    
                    }
                },
                error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-disciplinas2').html('<strong>ATENÇÃO: </strong> Houve um erro interno ao tentar inserir uma nova disciplina. Por favor, verifique se a <i>Disciplina</i> já está cadastrada.').slideDown();    
                    } else if(jqxhr.status==422){
                        $('div.errors-adicionar-disciplinas').slideDown(100);
                        var errors = $.parseJSON(jqxhr.responseText);
                        $('div.errors-adicionar-disciplinas').prepend('<ul style="margin: 0 6px;"></ul>');                            
                        $.each(errors.errors, function (index, value) {
                            $('div.errors-adicionar-disciplinas ul').append('<li>' + value + '</li>');
                        });  
                    }
                }                    
            });
        }

        /* MOSTRA DIALOGO REMOVER DICIPLINAS */          

        function dialogRemoverDisciplina(id) {
            var style_bg_inicial = $('div#disciplina_'+id).css('background-color');
            $(document).confirmAcao('Para remover este item certifique-se que a disciplina não está vinculada com nenhuma <strong>avaliação</strong>.<p>Deseja realmente excluir esta <i>Disciplina</i>?</p>', function(){
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/ajax/remover-disciplina/' + id,
                    beforeSend: function(){
                        $('div#disciplina_'+id).css('background-color', '#F6CECE');
                    },
                    success: function(data){
                        if(data.status=='ok'){
                            $('a#gerenciar-disciplinas').trigger('click');       
                        } else {
                            $('div#disciplina_'+id).css('background-color', style_bg_inicial);
                        }
                    },
                    error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div#disciplina_'+id).css('background-color', style_bg_inicial);
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-disciplinas2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar remover a <i>Disciplina</i> do sistema. Verifique se não há avaliações vinculadas a esta <i>Disciplina</i>').slideDown();    
                    }
                }  
                });                
            });
        }                 

        function dialogAdicionarAnoFormacao(){
            $('div.errors-cadastro-ano-formacao').hide();
             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-adicionar-ano-formacao',
                beforeSend: function(){
                    loadingModalDinamica('show', 'sm');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    loadingModalDinamica('hide', 'sm');
                }
            });
        } 

        /* DIALOG ADICIONAR PORTARIA */

        function dialogAdicionarPortaria(){
            $('div.errors-adicionar-portaria2').empty().slideUp();
            $('div.errors-adicionar-portaria').hide();
             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-adicionar-portaria',
                beforeSend: function(){
                    loadingModalDinamica('show', 'sm');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    loadingModalDinamica('hide', 'sm');
                }
            });
        }

        /* ADICIONAR PORTARIA */

        function AdicionarPortaria(){
            var dataForm = $('form#adicionar_portaria').serialize();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/adicionar-portaria',
                beforeSend: function(){
                    $('div.errors-adicionar-portaria ul').remove().parent().hide();
                },
                success: function(data){
                    if(data.status=='ok'){
                        $('div#modalDinamica').modal('hide');
                        $('a#indices-taf-atletas').trigger('click');
                    } else if(data.status=='err1') {
                        $('div.errors-adicionar-portaria').html('<strong>ATENÇÃO: </strong> Portaria é inválida!').slideDown();    
                    } else {
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Houve um erro interno ao tentar inserir a portaria.').slideDown();
                    }
                },
                error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Houve um erro interno ao tentar inserir a portaria.').slideDown();    
                    } else if(jqxhr.status==422){
                        $('div.errors-adicionar-portaria').slideDown(100);
                        var errors = $.parseJSON(jqxhr.responseText);
                        $('div.errors-adicionar-portaria').prepend('<ul style="margin: 0 6px;"></ul>');                            
                        $.each(errors.errors, function (index, value) {
                            $('div.errors-adicionar-portaria ul').append('<li>' + value + '</li>');
                        });  
                    }
                }                    
            });
        }

        /* INCLUIR INDICE CORRIDA */       

        function incluirIndiceCorrida(){
            var dataForm = $('form#data-insert-indices-corrida').serialize();
            $('div.errors-adicionar-portaria2').empty().hide();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/incluir-indice-corrida',
                beforeSend: function(){
                    $('table.indices-corrida').append('<tr id="tr-temp" style="text-align: center; background-color: #81F7BE;">Aguarde...<tr>');
                },
                success: function(data){
                    $('tr#tr-temp').remove();
                    if(data.status=='ok'){
                        $('table.indices-corrida').append(data.content);
                        $('input[name="tempo_inicial"], input[name="tempo_final"], input[name="grau"]').val('');
                        $('input[name="tempo_inicial"]').focus();
                    } else if(data.status=='err' || data.status=='err1'){
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> ' + data.erros).slideDown();
                    } else {
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse índice não se encontra criado.').slideDown();
                    }
                },
                error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse índice não se encontra criado.').slideDown();   
                    } else if(jqxhr.status==422){
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse índice não se encontra criado.').slideDown();
                    } else {
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse índice não se encontra criado.').slideDown();
                    }
                }                    
            });       
        }

        /* DIALOGO REMOVER INDICE CORRIDA */

        function dialogRemoverIndiceCorrida(id){
            var style_bg_inicial = $('tr#tr_indice_corrida_'+id).css('background-color');
            $('div.errors-adicionar-portaria2').empty().slideUp();
            $(document).confirmAcao('<strong>ATENÇÃO: </strong> Se esta portaria estiver sendo utilizada, a remoção deste índice pode ocasionar <span style="color: #B40404;"><strong>erros graves</strong></span> nas notas finais dos alunos. Tem certeza que deseja continuar?', function(){
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/ajax/remover-indice-corrida/' + id,
                    beforeSend: function(){
                        $('tr#tr_indice_corrida_'+id).css('background-color', '#F6CECE');
                    },
                    success: function(data){
                        if(data.status=='ok'){
                            $('tr#tr_indice_corrida_'+id).remove();
                        } else {
                            $('tr#tr_indice_corrida_'+id).css('background-color', style_bg_inicial);
                            $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar remover o <i>Índice</i>. Se o problema persistir, informe ao admnistrador.').slideDown();    
                        }
                    },
                    error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('tr#tr_indice_corrida_'+id).css('background-color', style_bg_inicial);
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar remover o <i>Índice</i>. Se o problema persistir, informe ao admnistrador.').slideDown();    
                    }
                }  
                });                  
            });
        }

        /* INCLUIR INDICE FLEXAÕ DE BRAÇO */       

        function incluirIndiceFlexBra(){
            var dataForm = $('form#data-insert-indices-flexbra').serialize();
            $('div.errors-adicionar-portaria2').empty().hide();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/incluir-indice-flexbra',
                beforeSend: function(){
                    $('table.indices-flexao-braco').append('<tr id="tr-temp" style="text-align: center; background-color: #81F7BE;">Aguarde...<tr>');
                },
                success: function(data){
                    $('tr#tr-temp').remove();
                    if(data.status=='ok'){
                        $('table.indices-flexao-braco').append(data.content);
                        $('input[name="valor_inicial"], input[name="valor_final"], input[name="grau"]').val('');
                        $('input[name="valor_inicial"]').focus();
                    } else if(data.status=='err' || data.status=='err1'){
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> ' + data.erros).slideDown();
                    } else {
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse índice não se encontra criado.').slideDown();
                    }
                },
                error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse índice não se encontra criado.').slideDown();   
                    } else if(jqxhr.status==422){
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse índice não se encontra criado.').slideDown();
                    } else {
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse índice não se encontra criado.').slideDown();
                    }
                }                    
            });       
        }

        /* DIALOGO REMOVER INDICE FLEXÃO DE BRAÇO */

        function dialogRemoverIndiceFlexBra(id){
            var style_bg_inicial = $('tr#tr_indice_flexbra_'+id).css('background-color');
            $('div.errors-adicionar-portaria2').empty().slideUp();
            $(document).confirmAcao('<strong>ATENÇÃO: </strong> Se esta portaria estiver sendo utilizada, a remoção deste índice pode ocasionar <span style="color: #B40404;"><strong>erros graves</strong></span> nas notas finais dos alunos. Tem certeza que deseja continuar?', function(){
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/ajax/remover-indice-flexbra/' + id,
                    beforeSend: function(){
                        $('tr#tr_indice_flexbra_'+id).css('background-color', '#F6CECE');
                    },
                    success: function(data){
                        if(data.status=='ok'){
                            $('tr#tr_indice_flexbra_'+id).remove();
                        } else {
                            $('tr#tr_indice_flexbra_'+id).css('background-color', style_bg_inicial);
                            $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar remover o <i>Índice</i>. Se o problema persistir, informe ao admnistrador.').slideDown();    
                        }
                    },
                    error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('tr#tr_indice_flexbra_'+id).css('background-color', style_bg_inicial);
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar remover o <i>Índice</i>. Se o problema persistir, informe ao admnistrador.').slideDown();    
                    }
                }  
                });                  
            });
        }

        /* INCLUIR INDICE FLEXÃO NA BARRA */       

        function incluirIndiceFlexBar(){
            var dataForm = $('form#data-insert-indices-flexbar').serialize();
            $('div.errors-adicionar-portaria2').empty().hide();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/incluir-indice-flexbar',
                beforeSend: function(){
                    $('table.indices-flexao-barra').append('<tr id="tr-temp" style="text-align: center; background-color: #81F7BE;">Aguarde...<tr>');
                },
                success: function(data){
                    $('tr#tr-temp').remove();
                    if(data.status=='ok'){
                        $('table.indices-flexao-barra').append(data.content);
                        $('input[name="valor_inicial"], input[name="valor_final"], input[name="grau"]').val('');
                        $('input[name="valor_inicial"]').focus();
                    } else if(data.status=='err' || data.status=='err1'){
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> ' + data.erros).slideDown();
                    } else {
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse índice não se encontra criado.').slideDown();
                    }
                },
                error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse índice não se encontra criado.').slideDown();   
                    } else if(jqxhr.status==422){
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse índice não se encontra criado.').slideDown();
                    } else {
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse índice não se encontra criado.').slideDown();
                    }
                }                    
            });       
        }

        /* DIALOGO REMOVER INDICE FLEXÃO NA BARRA */

        function dialogRemoverIndiceFlexBar(id){
            var style_bg_inicial = $('tr#tr_indice_flexbar_'+id).css('background-color');
            $('div.errors-adicionar-portaria2').empty().slideUp();
            $(document).confirmAcao('<strong>ATENÇÃO: </strong> Se esta portaria estiver sendo utilizada, a remoção deste índice pode ocasionar <span style="color: #B40404;"><strong>erros graves</strong></span> nas notas finais dos alunos. Tem certeza que deseja continuar?', function(){
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/ajax/remover-indice-flexbar/' + id,
                    beforeSend: function(){
                        $('tr#tr_indice_flexbar_'+id).css('background-color', '#F6CECE');
                    },
                    success: function(data){
                        if(data.status=='ok'){
                            $('tr#tr_indice_flexbar_'+id).remove();
                        } else {
                            $('tr#tr_indice_flexbar_'+id).css('background-color', style_bg_inicial);
                            $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar remover o <i>Índice</i>. Se o problema persistir, informe ao admnistrador.').slideDown();    
                        }
                    },
                    error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('tr#tr_indice_flexbar_'+id).css('background-color', style_bg_inicial);
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar remover o <i>Índice</i>. Se o problema persistir, informe ao admnistrador.').slideDown();    
                    }
                }  
                });                  
            });
        }

        /* INCLUIR INDICE ABDOMINAL */       

        function incluirIndiceAbdominal(){
            var dataForm = $('form#data-insert-indices-abdomin').serialize();
            $('div.errors-adicionar-portaria2').empty().hide();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/incluir-indice-abdomin',
                beforeSend: function(){
                    $('table.indices-abdominal').append('<tr id="tr-temp" style="text-align: center; background-color: #81F7BE;">Aguarde...<tr>');
                },
                success: function(data){
                    $('tr#tr-temp').remove();
                    if(data.status=='ok'){
                        $('table.indices-abdominal').append(data.content);
                        $('input[name="valor_inicial"], input[name="valor_final"], input[name="grau"]').val('');
                        $('input[name="valor_inicial"]').focus();
                    } else if(data.status=='err' || data.status=='err1'){
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> ' + data.erros).slideDown();
                    } else {
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse índice não se encontra criado.').slideDown();
                    }
                },
                error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse índice não se encontra criado.').slideDown();   
                    } else if(jqxhr.status==422){
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse índice não se encontra criado.').slideDown();
                    } else {
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse índice não se encontra criado.').slideDown();
                    }
                }                    
            });       
        }

        /* DIALOGO REMOVER INDICE ABDOMINAL */

        function dialogRemoverIndiceAbdominal(id){
            var style_bg_inicial = $('tr#tr_indice_abdominal_'+id).css('background-color');
            $('div.errors-adicionar-portaria2').empty().slideUp();
            $(document).confirmAcao('<strong>ATENÇÃO: </strong> Se esta portaria estiver sendo utilizada, a remoção deste índice pode ocasionar <span style="color: #B40404;"><strong>erros graves</strong></span> nas notas finais dos alunos. Tem certeza que deseja continuar?', function(){
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/ajax/remover-indice-abdomin/' + id,
                    beforeSend: function(){
                        $('tr#tr_indice_abdominal_'+id).css('background-color', '#F6CECE');
                    },
                    success: function(data){
                        if(data.status=='ok'){
                            $('tr#tr_indice_abdominal_'+id).remove();
                        } else {
                            $('tr#tr_indice_abdominal_'+id).css('background-color', style_bg_inicial);
                            $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar remover o <i>Índice</i>. Se o problema persistir, informe ao admnistrador.').slideDown();    
                        }
                    },
                    error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('tr#tr_indice_abdominal_'+id).css('background-color', style_bg_inicial);
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar remover o <i>Índice</i>. Se o problema persistir, informe ao admnistrador.').slideDown();    
                    }
                }  
                });                  
            });
        }

        /* INCLUIR BONUS ATLETA */       

        function incluirBonusAtleta(){
            var dataForm = $('form#data-insert-bonus-atletas').serialize();
            $('div.errors-adicionar-portaria2').empty().hide();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/incluir-bonus-atletas',
                beforeSend: function(){
                    $('table.indices-bonus-atletas').append('<tr id="tr-temp" style="text-align: center; background-color: #81F7BE;">Aguarde...<tr>');
                },
                success: function(data){
                    $('tr#tr-temp').remove();
                    if(data.status=='ok'){
                        $('table.indices-bonus-atletas').append(data.content);
                        $('input[name="valor_min"], input[name="valor_max"], input[name="bonus"]').val('');
                        $('input[name="valor_min"]').focus();
                    } else if(data.status=='err' || data.status=='err1'){
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> ' + data.erros).slideDown();
                    } else {
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse bonus já está cadastrado.').slideDown();
                    }
                },
                error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse bonus já está cadastrado.').slideDown();   
                    } else if(jqxhr.status==422){
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse bonus já está cadastrado.').slideDown();
                    } else {
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Erro interno. Verifique se esse bonus já está cadastrado.').slideDown();
                    }
                }                    
            });       
        }

        /* DIALOGO REMOVER BONUS ATLETA */

        function dialogRemoverBonusAtleta(id){
            var style_bg_inicial = $('tr#tr_indice_bonus_atletas_'+id).css('background-color');
            $('div.errors-adicionar-portaria2').empty().slideUp();
            $(document).confirmAcao('<strong>ATENÇÃO: </strong> Se esta portaria estiver sendo utilizada, a remoção desta regra pode ocasionar <span style="color: #B40404;"><strong>erros graves</strong></span> nas notas finais dos alunos. Tem certeza que deseja continuar?', function(){
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/ajax/remover-bonus-atletas/' + id,
                    beforeSend: function(){
                        $('tr#tr_indice_bonus_atletas_'+id).css('background-color', '#F6CECE');
                    },
                    success: function(data){
                        if(data.status=='ok'){
                            $('tr#tr_indice_bonus_atletas_'+id).remove();
                        } else {
                            $('tr#tr_indice_bonus_atletas_'+id).css('background-color', style_bg_inicial);
                            $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar remover o <i>Índice</i>. Se o problema persistir, informe ao admnistrador.').slideDown();    
                        }
                    },
                    error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('tr#tr_indice_bonus_atletas_'+id).css('background-color', style_bg_inicial);
                        $('div.errors-adicionar-portaria2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar remover o <i>Índice</i>. Se o problema persistir, informe ao admnistrador.').slideDown();    
                    }
                }  
                });                  
            });
        }

        /* CLONAR PORTARIA */

        function clonarPortaria(aLink, id){
            $('div.status-opcoes-portaria').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
            var aLinkElement = $(aLink).children('div');
            var aLinkContent = aLinkElement.html();
            $.ajax({
                type: 'GET',
                dataType: 'json',
                url: '/ajax/clonar-portaria/' + id,
                beforeSend: function(){
                    aLinkElement.html('<img src="/images/loadings/loading_01.svg" style="width: 22px; margin-top: 16px;" />');
                },
                success: function(data){
                    aLinkElement.html(aLinkContent);
                    if(data.status=='ok'){
                        $('select[name="portaria_id"]').append(data.contentOption);
                        $('div.status-opcoes-portaria').addClass('alert-success').slideDown().html('Portaria foi clonada com sucesso! Para começar a configurá-la, utilize a caixa de seleção acima.');
                    } else {
                        $('div.status-opcoes-portaria').addClass('alert-danger').slideDown().html('Houve um erro ao tentar clonar a portaria.');
                    }
                    setTimeout(function(){
                        $('div.status-opcoes-portaria').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
                    }, 8000);
                },
                error: function(jqxhr){
                    aLinkElement.html(aLinkContent);
                    $('div.status-opcoes-portaria').addClass('alert-danger').slideDown().html('Houve um erro interno ao tentar clonar a portaria.');
                    setTimeout(function(){
                            $('div.status-opcoes-portaria').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
                    }, 8000);                    
                }                
            });            
        }

        /* MOSTRA DIALOGO EDITAR NOME DA PORTARIA */

        function dialogEditarNomePortaria(id){
            $('div.status-opcoes-portaria').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-editar-nome-portaria/' + id,
                beforeSend: function(){
                    loadingModalDinamica('show', 'sm');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    $('input[name="nome_portaria"]').focus();
                    loadingModalDinamica('hide', 'sm');
                }
            });
        }

        /* MOSTRA DIALOGO ADICIONAR PERIODO ESCOLHA QMS */

        function dialogAdicionarPeriodoEscolhaQMS(){
            $('div.errors-adicionar-escolha-qms2').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-adicionar-periodo-escolha-qms',
                beforeSend: function(){
                    loadingModalDinamica('show', 'lg');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    $('.carousel').carousel({
                        interval: false
                    });
                    setTimeout(function(){
                        $('input[name="identificador"]').focus();
                    }, 600);
                    loadingModalDinamica('hide', 'lg');
                }
            });
        }

        /* ADICIONAR PERÍODO DE ESCOLHA DE QMS */

        function AdicionarPeriodoEscolhaQMS(){
            var dataForm = $('form#adicionar_periodo_escolha_qms').serialize();
            var modalBodyContent = $('div#modalDinamica div.modal-body').html();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/adicionar-periodo-escolha-qms',
                beforeSend: function(){
                    $('div.errors-adicionar-escolha-qms').hide().empty();
                    $('div#modalDinamica div.modal-body').html('<div id="modal-dinamica-box-loading"><img src="/images/loadings/loading_01.svg"> Aguarde, criando registro...</div>');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-body').html(modalBodyContent);
                    if(data.status=='ok'){
                        $('div#modalDinamica').modal('hide');
                        $('a#escolha-de-qms').trigger('click');
                    } else if(data.status=='err1') {
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-escolha-qms2').html('Não foi possível criar um novo período de escolha. Por favor informe ao desenvolvedor.').slideDown();    
                    } else if(data.status=='err2'){
                        $('div.errors-adicionar-escolha-qms').html(data.erroPreencimento).slideDown();
                    } else {
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-escolha-qms2').addClass('alert-danger').html('<strong>ATENÇÃO: </strong> Houve um erro interno ao tentar inserir o período de escolha. Por favor informe ao desenvolvedor.').slideDown();
                    }
                },
                error: function(jqxhr){
                    $('div#modalDinamica').modal('hide');
                    $('div.errors-adicionar-escolha-qms2').addClass('alert-danger').html('<strong>ATENÇÃO: </strong> Houve um erro interno('+ jqxhr.status +') ao tentar inserir o período de escolha. Por favor informe ao desenvolvedor.').slideDown();
                }                    
            });            
        }

        function dialogEditarEscolhaQMS(id){
            $('div.errors-adicionar-escolha-qms2').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-editar-periodo-escolha-qms/' + id,
                beforeSend: function(){
                    loadingModalDinamica('show', 'lg');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    $('.carousel').carousel({
                        interval: false
                    });
                    loadingModalDinamica('hide', 'lg');
                }
            });            
        }

        function AtualizarPeriodoEscolhaQMS(id){
            var dataForm = $('form#editar_periodo_escolha_qms').serialize();
            var modalBodyContent = $('div#modalDinamica div.modal-body').html();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/atualizar-periodo-escolha-qms/' + id,
                beforeSend: function(){
                    $('div.errors-adicionar-escolha-qms').hide().empty();
                    $('div#modalDinamica div.modal-body').html('<div id="modal-dinamica-box-loading"><img src="/images/loadings/loading_01.svg"> Aguarde, criando registro...</div>');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-body').html(modalBodyContent);
                    if(data.status=='ok'){
                        $('div#modalDinamica').modal('hide');
                        $('a#escolha-de-qms').trigger('click');
                    } else if(data.status=='err1') {
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-escolha-qms2').html('Não foi possível criar um novo período de escolha. Por favor informe ao desenvolvedor.').slideDown();    
                    } else if(data.status=='err2'){
                        $('div.errors-adicionar-escolha-qms').html(data.erroPreencimento).slideDown();
                    } else {
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-escolha-qms2').addClass('alert-danger').html('<strong>ATENÇÃO: </strong> Houve um erro interno ao tentar inserir o período de escolha. Por favor informe ao desenvolvedor.').slideDown();
                    }
                },
                error: function(jqxhr){
                    $('div#modalDinamica').modal('hide');
                    $('div.errors-adicionar-escolha-qms2').addClass('alert-danger').html('<strong>ATENÇÃO: </strong> Houve um erro interno('+ jqxhr.status +') ao tentar inserir o período de escolha. Por favor informe ao desenvolvedor.').slideDown();
                }                    
            });             
        }

        function dialogRemoverEscolhaQMS(id){
            $(document).confirmAcao('<strong>ATENÇÃO: </strong>Os registros de escolhas dos alunos juntamente com este periodo será removido. Tem certeza que deseja excluir? ', function(){
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/ajax/remover-periodo-escolha-qms/' + id,
                    beforeSend: function(){
                        $('div#escolhaQMSID_' + id).css('background-color', '#F6CECE');
                    },
                    success: function(data){
                        if(data.status=='ok'){
                            $('a#escolha-de-qms').trigger('click');    
                        } else if(data.status=='err'){
                            $('div.errors-adicionar-escolha-qms2').slideDown().html('<strong>ATENÇÃO: </strong> ' + data.response);
                            setTimeout(function(){
                                $('div.errors-adicionar-escolha-qms2').slideUp().empty();
                            }, 8000);                            
                        } else if(data.status=='err1'){
                            alert(data.response);
                        }
                    }
                }); 
            });
        }        

        /* RENOMEAR PORTARIA */

        function renomearPortaria(id){
            $('div.status-opcoes-portaria').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');          
            var dataForm = $('form#renomear_portaria').serialize();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/renomear-portaria/' + id,
                beforeSend: function(){
                    $('div.errors-adicionar-portaria ul').remove().parent().hide();
                },
                success: function(data){
                    if(data.status=='ok'){
                        $('div#modalDinamica').modal('hide');
                        $('select option[value="'+ id +'"]').text(data.contentOption);
                        $('div.status-opcoes-portaria').addClass('alert-success').slideDown().html('Nome da portaria foi alterado com sucesso agora mesmo!');
                    } else if(data.status=='err1') {
                        $('div.status-opcoes-portaria').addClass('alert-danger').slideDown().html('<strong>ATENÇÃO: </strong> Portaria é inválida! Nome da portaria muito curto.');
                    } else {
                        $('div#modalDinamica').modal('hide');
                        $('div.status-opcoes-portaria').addClass('alert-danger').slideDown().html('<strong>ATENÇÃO: </strong> Houve um erro interno ao tentar renomear a portaria.');
                    }
                    setTimeout(function(){
                        $('div.status-opcoes-portaria').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
                    }, 8000);                    
                },
                error: function(jqxhr){
                    $('div#modalDinamica').modal('hide');
                    $('div.status-opcoes-portaria').addClass('alert-danger').slideDown().html('<strong>ATENÇÃO: </strong> Houve um erro interno ao tentar renomear a portaria. Verifique se existe uma portaria cadastrada com este mesmo nome.');
                    setTimeout(function(){
                        $('div.status-opcoes-portaria').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
                    }, 12000);                    
                }                    
            });
        }

        /* DIALOGO REMOVER PORTARIA */        

        function dialogRemoverPortaria(aLink, id) {
            $('div.status-opcoes-portaria').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
            var aLinkElement = $(aLink).children('div');
            var aLinkContent = aLinkElement.html();               
            $(document).confirmAcao('Para remover este item certifique-se que esta <strong>Portaria</strong> não está vinculada a nenhum <i>Ano de Formação</i>.<p>Deseja realmente continuar?</p>', function(){
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/ajax/remover-portaria/' + id,
                    beforeSend: function(){
                        aLinkElement.html('<img src="/images/loadings/loading_01.svg" style="width: 22px; margin-top: 8px;" />');
                    },
                    success: function(data){
                        aLinkElement.html(aLinkContent);
                        if(data.status=='ok'){
                            $('a#indices-taf-atletas').trigger('click');
                        } else {
                            $('div.status-opcoes-portaria').addClass('alert-danger').slideDown().html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar remover a portaria. Verifique se ela não está sendo utilizada.');
                        }
                        setTimeout(function(){
                            $('div.status-opcoes-portaria').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
                        }, 8000);                         
                    },
                    error: function(jqxhr){
                        aLinkElement.html(aLinkContent);
                        $('div.status-opcoes-portaria').addClass('alert-danger').slideDown().html('<strong>ATENÇÃO: </strong> Houve um erro interno ao tentar remover a portaria. Verifique se ela não está sendo utilizada.');
                        setTimeout(function(){
                            $('div.status-opcoes-portaria').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
                        }, 8000); 
                    }
                });  
                                
            });
        }

        function marcarSuficiencia(aLink, modo, id){
            var aLinkElement = $(aLink).children('div');
            var aLinkContent = aLinkElement.html();             
            $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/ajax/marcar-suficiencia/' + modo  +'/' + id,
                    beforeSend: function(){
                        aLinkElement.html('<img src="/images/loadings/loading_01.svg" style="width: 16px; margin-top: 9px;" />');
                    },
                    success: function(data){
                        if(data.status=='ok'){
                            if(data.changestatusCheckTo=='on'){
                                aLinkElement.html('<i style="color: #0B6121; font-size: 22px; margin-top: 8px;" class="ion-android-done"></i>');
                            } else {
                                aLinkElement.empty();
                            }
                        } else {
                            aLinkElement.html(aLinkContent);
                        }                      
                    },
                    error: function(jqxhr){
                        aLinkElement.html(aLinkContent);
                    }
                });             
        }

        function marcarExercicioAvaliado(aLink, modo, id){
            var aLinkElement = $(aLink).children('div');
            var aLinkContent = aLinkElement.html();             
            $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/ajax/marcar-exercicio-avaliado/' + modo  +'/' + id,
                    beforeSend: function(){
                        aLinkElement.html('<img src="/images/loadings/loading_01.svg" style="width: 16px; margin-top: 9px;" />');
                    },
                    success: function(data){
                        if(data.status=='ok'){
                            if(data.changestatusCheckTo=='on'){
                                aLinkElement.html('<i style="color: #0B6121; font-size: 22px; margin-top: 8px;" class="ion-android-done"></i>');
                            } else {
                                aLinkElement.empty();
                            }
                        } else {
                            aLinkElement.html(aLinkContent);
                        }                      
                    },
                    error: function(jqxhr){
                        aLinkElement.html(aLinkContent);
                    }
                });             
        }

        function marcarUniverso(aLink, modo, val, id){
            var aLinkElement = $(aLink).children('div');
            var aLinkContent = aLinkElement.html();             
            $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/ajax/marcar-universo/' + modo  +'/' + val + '/' + id,
                    beforeSend: function(){
                        aLinkElement.html('<img src="/images/loadings/loading_01.svg" style="width: 16px; margin-top: 9px;" />');
                    },
                    success: function(data){
                        if(data.status=='ok'){
                            if(modo=='ano-formacao'){
                                $('div.anoFormacaoID').empty();
                                if(data.changestatusCheckTo=='on'){
                                    $('div#anoFormacaoID_' + val).html('<i style="color: #0B6121; font-size: 22px; margin-top: 8px;" class="ion-android-done"></i>');
                                }
                            } else {

                                if(data.changestatusCheckTo=='on'){
                                    aLinkElement.html('<i style="color: #0B6121; font-size: 22px; margin-top: 8px;" class="ion-android-done"></i>');
                                } else {
                                    aLinkElement.empty();
                                }

                            }
                        } else {
                            aLinkElement.html(aLinkContent);
                        }                      
                    },
                    error: function(jqxhr){
                        aLinkElement.html(aLinkContent);
                    }
                });             
        }


        function marcarVoluntarioAviacao(aLink){
            var aLinkElement = $(aLink).children('div');
            var aLinkContent = aLinkElement.html();
            $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/ajax/marcar-voluntario-aviacao',
                    beforeSend: function(){
                        aLinkElement.html('<img src="/images/loadings/loading_01.svg" style="width: 16px; margin-top: 11px;" />');
                    },
                    success: function(data){
                        if(data.status=='ok'){
                            $('a#voluntarios-para-aviacao').trigger('click');
                        }                    
                    },
                    error: function(jqxhr){
                        aLinkElement.html(aLinkContent);
                    }
                });               
        }        

        function dialogRemoverAnoFormacao(id) {
            var style_bg_inicial = $('div#ano_de_formacao_'+id).css('background-color');
            $(document).confirmAcao('Para remover este item certifique-se que não há vínculos de <strong>alunos</strong> e/ou <strong>disciplinas</strong> com este ano de formação.<p>Deseja realmente excluir este <i>Ano de Formação?</i></p>', function(){
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/ajax/remover-ano-formacao/' + id,
                    beforeSend: function(){
                        $('div#ano_de_formacao_'+id).css('background-color', '#F6CECE');
                    },
                    success: function(data){
                        if(data.status=='ok'){
                            $('a#anos-de-formacao').trigger('click');       
                        } else {
                            $('div#ano_de_formacao_'+id).css('background-color', style_bg_inicial);
                        }
                    },
                    error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div#ano_de_formacao_'+id).css('background-color', style_bg_inicial);
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-cadastro-ano-formacao').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar remover o <i>Ano de Formação</i> do sistema. Verifique se não há alunos e/ou disciplinas vinculadas a este <i>Ano de Formação</i>').slideDown();    
                    }
                }  
                });                
            });
        }

        /* DIALOGO EDITAR ANO FORMAÇÃO */

        function dialogEditarAnoFormacao(id){
             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-editar-ano-formacao/' + id,
                beforeSend: function(){
                    loadingModalDinamica('show', 'sm');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    setTimeout(function(){
                        $('input[name="data_matricula"]').select();
                    }, 600);
                    loadingModalDinamica('hide', 'sm');
                }
            });
        }

        /* ATUALIZAR ANO DE FORMAÇÃO */

        function AtualizarAnoFormacao(id) {         
            var dataForm = $('form#atualizar_ano_formacao').serialize();
            var style_bg_inicial = $('div#ano_de_formacao_'+id).css('background-color');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/atualizar-ano-formacao/' + id,
                beforeSend: function(){
                    $('div#modalDinamica').modal('hide');
                    $('div#ano_de_formacao_'+id).css('background-color', '#81F7BE');
                },
                success: function(data){
                    $('div#ano_de_formacao_'+id).css('background-color', style_bg_inicial);
                    if(data.status=='ok'){
                        $('span#ano_de_formacao_data_matricula_' + id).html(data.content)
                    } else {
                        $('div.errors-cadastro-ano-formacao').html('Houve um erro ao tentar atualizar a data inicial de matrícula. Verifique se foi informada uma data válida no formato DD/MM/AAAA.').slideDown();
                        setTimeout(function(){
                            $('div.errors-cadastro-ano-formacao').slideUp(200, function(){
                                $(this).empty();    
                            });
                        }, 10000);
                    }
                },
                error: function(jqxhr){
                    $('div#ano_de_formacao_'+id).css('background-color', style_bg_inicial);
                    $('div.errors-cadastro-ano-formacao').html('Erro interno ao tentar atualizar a data inicial de matrícula.').slideDown();
                    setTimeout(function(){
                            $('div.errors-cadastro-ano-formacao').slideUp(200, function(){
                                $(this).empty();    
                            });
                        }, 10000);                    
                }                    
            });            
        }                       

        /* MOSTRA DIALOGO ADICIONAR OPERADORES */          

        function dialogAdicionarOperador(){
             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-adicionar-operador',
                beforeSend: function(){
                    loadingModalDinamica('show', 'lg');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    loadingModalDinamica('hide', 'lg');
                }
            });
        }

        function dialogRemoverOperador(id, nome) {
            $(document).confirmAcao('Tem certeza que deseja remover o(a) operador(a) '+ nome +' do sistema?', function(){
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/ajax/remover-operador/' + id,
                    beforeSend: function(){
                        $('tr#operador_'+id).css('background-color', '#F6CECE');
                    },
                    success: function(data){
                        if(data.total_op==0){
                            $('a#gerenciar-operadores').trigger('click');
                        } else {
                            $('tr#operador_'+id).fadeOut(320);       
                        }
                    }
                });                
            });
        }

        /* CHAMADA AJAX */

        function loadAdminAjaxContent(routeAs){
            if(routeAs != undefined){
                $.ajax({
                    type: 'GET',
                    url: '/ajax/' + routeAs,
                    beforeSend: function(){
                        $('div.inside-content').html('<div id="temp" style="text-align: center; margin-top: 120px;"><img src="/images/loadings/loading_04.svg" style="width: 28px; margin-right: 8px;" /><br />Aguarde, carregando...</div>');
                    },
                    success: function(data){
                        $('div.inside-content').html(data);    
                    }
                });
            }
        } 

        function loadRelatorioAjaxContent(routeAs){
            var idANoFormacao = $('.btn.btn-secondary.active input[name="ano_formacao"]').val();
            $.ajax({
                type: 'GET',
                url: '/ajax/' + routeAs + '/' + idANoFormacao,
                beforeSend: function(){
                    $('div#relatorios-content').html('<div id="temp" style="text-align: center; margin-top: 24px;"><img src="/images/loadings/loading_04.svg" style="width: 28px; margin-right: 8px;" /><br />Aguarde, carregando...</div>');    
                },
                success: function(data){
                    $('div#relatorios-content').html(data);    
                }
            });
        }

        $(document).on('click', 'li.opcoes-relatorios', function(){
            $('li.opcoes-relatorios').removeClass('active');
            $('li.opcoes-relatorios a').css('color', '#696969');
            $(this).addClass('active');
            $(this).children('a').css('color', '#ffffff');
        }) 

        /* CARREGA LOADING DEFAULT MODAL DINAMICA */

        function loadingModalDinamica(modo, tamanho){
            $('div#modalDinamica div.modal-dialog').removeClass('modal-lg');
            $('div#modalDinamica div.modal-dialog').removeClass('modal-lgg');
            
            if(tamanho=='lg'){
                $('div#modalDinamica div.modal-dialog').addClass('modal-lg');   
            } else {
                $('div#modalDinamica div.modal-dialog').addClass(tamanho);   
            }

            if (modo=='show'){
                $('div#modalDinamica div.modal-header h5').empty();
                $('div#modalDinamica div.modal-footer').empty();
                $('div#modalDinamica div.modal-body').empty();
                $('div#modalDinamica').modal('show');
                $('div#modalDinamica div.modal-body').html('<div id="modal-dinamica-box-loading"><img src="/images/loadings/loading_01.svg"> Aguarde, carregando...</div>');
            
                // MODO hide NÃO ESCONDE O LOADIND, PORÉM MOSTRA O TITLE E O FOOTER DA MODAL
            
            } else if (modo=='hide') {
                $('div#modalDinamica div.modal-header').slideDown(400);
                $('div#modalDinamica div.modal-footer').slideDown(400); 
                             
            }
        }

        $(document).on('click', '.open_input_file', function(){
            $('div.erro-upload').slideUp(200, function(){
                $(this).empty();
            });
            $('input[type="file"]').trigger('click');            
        });

        function submitForm(formID, action) {
            var input = $('form#' + formID + ' input[type="file"]')[0];
            var fileSize = (input.files[0].size/1000);
            if(fileSize>1024){
                $('div.erro-upload').html('O arquivo a ser enviado não deve ser maior que 1024Kb').slideDown();
            } else {
                var fd = new FormData(document.getElementById(formID));
                $.ajax({
                    cache: false,
                    dataType: 'json',
                    url: action,
                    type: "POST",
                    data: fd,
                    enctype: 'multipart/form-data',
                    xhr: function() {
                        var xhr = $.ajaxSettings.xhr();
                        xhr.upload.onprogress = function(e) {
                            $('div.progress div').css('width', (Math.floor(e.loaded / e.total *100))-(1) + '%');
                        };
                        return xhr;
                    },            
                    beforeSend: function() {
                        $('div.progress').slideDown(100);                            
                    },
                    success: function(data) {
                        if(data.uploadType=='imgPefil'){
                            $('div.progress div').css('width', '100%');
                            setTimeout(function(){
                                $('div.progress').slideUp(100, function(){
                                    $('div.progress div').css('width', '0%');
                                });    
                            }, 400);
                            if(data.status=='ok'){
                                if(!$('div#box-dialog-remover-imagem-perfil').is(':visible')){
                                    $('div#box-dialog-remover-imagem-perfil').slideDown(100);    
                                }
                                $('div.imagem_perfil').css('background-image', 'url(\''+ data.src_image +'\')');
                                if(data.tipo=='minha-imagem'){
                                    $('div.data_user_imagem_perfil').css('background-image', 'url(\''+ data.src_image +'\')');
                                }
                            } else {
                                $('div.erro-upload').html(data.error).slideDown();                            
                            }
                        }
                    },
                    error: function(jqxhr){
                        $('div.erro-upload').html('Houve um erro ao tentar enviar o arquivo').slideDown();
                    },                                                             
                    processData: false,  // tell jQuery not to process the data
                    contentType: false   // tell jQuery not to set contentType
                });
            }

        }

        function removerImgPerfil(id){
            var divContent = $('div#box-dialog-remover-imagem-perfil').html();
            $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/remover-img-perfil/' + id,
                beforeSend: function(){
                    $('div#box-dialog-remover-imagem-perfil').html('<img src="/images/loadings/loading_01.svg" style="width: 20px;"> Aguarde...');
                },
                success: function(data){
                    if(data.status=='ok'){
                        $('div#box-dialog-remover-imagem-perfil').slideUp(200, function(){
                            $('div#box-dialog-remover-imagem-perfil').html(divContent);
                            $('div.imagem_perfil').css('background-image', 'url(\''+ data.src_image +'\')');
                            if(data.typeUser==1){
                                $('div.data_user_imagem_perfil').css('background-image', 'url(\''+ data.src_image +'\')');
                            }
                        });
                    } else {
                        $('div#box-dialog-remover-imagem-perfil').html(divContent);
                    }

                }
            });            
        }

        /* MOSTRA DIALOGO IMPLANTAR ALUNO */

        function dialogImplantarAluno(){
            $('div#response').slideUp(600, function(){
                $(this).empty().show();
            });
            $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-implantar-aluno',
                beforeSend: function(){
                    loadingModalDinamica('show', 'modal-lgg');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    loadingModalDinamica('hide', 'modal-lgg');
                }
            });            
        }

        /* IMPLANTAR ALUNO */

        function ImplantarAluno(){

            var dataForm = $('form#implantar_aluno').serialize();
            
            $.ajax({
                cache: false,
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/admin/aluno',
                enctype: 'multipart/form-data',
                xhr: function() {
                        var xhr = $.ajaxSettings.xhr();
                        xhr.upload.onprogress = function(e) {
                            $('div.progress div').css('width', (Math.floor(e.loaded / e.total *100))-(1) + '%');
                        };
                        return xhr;
                },            
                beforeSend: function(){
                    $('div.errors-implantar-aluno').empty().hide();
                    $('div.progress').slideDown(100);     
                },
                success: function(data){
                    
                    if(data.status=='ok'){
                        $('div.success-implantar-aluno').html(data.response).slideDown();
                        setTimeout(function(){
                            $('div.success-implantar-aluno').slideUp(200, function(){
                                $(this).removeClass('alert-success').empty();
                            });
                        }, 3000);
                        

                        //Reseta os Campos
                        loadAdminAjaxContent('admin/aluno');
                    } else {
                        $('div.errors-implantar-aluno').html('<strong>ATENÇÃO:</strong><br />').slideDown();
                         $.each(data.response, function(key, value){
                            $('div.errors-implantar-aluno').append('<li>' + value + '</li>');
                        });
                    }
                },
                error: function(jqxhr){
                     $('div.errors-implantar-aluno').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown(); 
                }           
            });

        }

        function AtualizarAluno(id){

            var dataForm = $('form#implantar_aluno').serialize();
            
            $.ajax({
                cache: false,
                dataType: 'json',
                url: '/ajax/admin/aluno/' + id,
                type: "PUT",
                data: dataForm,
                enctype: 'multipart/form-data',
                xhr: function() {
                        var xhr = $.ajaxSettings.xhr();
                        xhr.upload.onprogress = function(e) {
                            $('div.progress div').css('width', (Math.floor(e.loaded / e.total *100))-(1) + '%');
                        };
                        return xhr;
                },
                beforeSend: function(){
                    $('div.errors-implantar-aluno').empty().hide();
                },
                success: function(data){
                    console.log(data);
                    if(data.status=='ok'){
                        $('div.success-implantar-aluno').html(data.response).slideDown();
                        setTimeout(function(){
                            $('div.success-implantar-aluno').slideUp(200, function(){
                                $(this).removeClass('alert-success').empty();
                            });

                            //Reseta os Campos
                            loadAdminAjaxContent('admin/aluno');
                        }, 3000);
                    } else {
                        $('div.errors-implantar-aluno').html('<strong>ATENÇÃO:</strong><br />').slideDown();
                         $.each(data.response, function(key, value){
                            $('div.errors-implantar-aluno').append('<li>' + value + '</li>');
                        });
                    }
                },
                error: function(jqxhr){
                     $('div.errors-implantar-aluno').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown(); 
                }           
            });
        }

        function dialogRemoverAluno(id){
            $(document).confirmAcao('<strong>ATENÇÃO: </strong>.<p>Está certo disso? Todos os dados deste aluno será PERMANENTEMENTE perdido.</p>', function(){
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    url: '/ajax/remover-aluno/' + id,
                    beforeSend: function(){
                        $('tr#alunoID_'+id).css('background-color', '#F6CECE');
                    },
                    success: function(data){
                        if(data.response=='ok'){
                            $('tr#alunoID_'+id).fadeOut(320);
                        } else {
                            alert('Você não tem permissão para remover alunos');
                        }
                    }
                });
            });
        }          

        /* CHAMA EDIÇÃO DO ALUNO APOS PRESSIONAR ENTER NO INPUT */

        $(document).on('keypress', 'input#input_busca_rapida_aluno', function(e) {
            if (e.which==13) {
                //var alunoNome = $(this).val();
                var alunoId = $(this).val();

                loadAdminAjaxContent('admin/aluno/' + alunoId);          
                //Original Julião...
                //dialogEditarCadastroAluno(alunoNome, 'byName');       
            }
        });
        
        /* FUNÇÃO AJAX QUE CHAMA MODAL PARA EDIÇÃO DOS DADOS DO ALUNO  */

        function dialogEditarCadastroAluno(pesquisa, tipo) {
            //errors-editar-cadastro-aluno
             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-editar-cadastro-aluno/' + pesquisa + '/' + tipo,
                beforeSend: function(){
                    loadingModalDinamica('show', 'modal-lgg');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    loadingModalDinamica('hide', 'modal-lgg');
                },
                error: function(jqxhr){
                    if(jqxhr.status==404){
                        setTimeout(function(){
                            $('div#modalDinamica_no_style').modal('show');
                            $('div#modalDinamica_no_style div.modal-body').html('<div style="padding: 12px 18px; margin-top: 10px; text-transform: uppercase;">\
                                                                                    <b>Para editar este aluno, localize-o na lista e clique sobre o lápis para fazer a edição</b>\
                                                                                </div>\
                                                                                <div style="padding: 8px 24px; text-align: right;">\
                                                                                    <a href="javascript: void(0)" class="no-style" data-dismiss="modal" style="color: #2E64FE; font-size: 12px;">\
                                                                                        <b>OK</b>\
                                                                                    </a>\
                                                                                </div>'); 
                            $('div#modalDinamica').modal('hide');
                        }, 1000);
                    } else {
                        setTimeout(function(){
                            $('div#modalDinamica').modal('hide');
                            alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                        }, 1000);
                    }
                }                    
            });            
        }

        /* ATUALIZAR CADASTRO DO ALUNO */

        function AtualizarCadastroAluno(dataButton, id) {         
            var dataForm = $('form#atualizar_cadastro_aluno').serialize();
            var dataButtonContent = $(dataButton).html();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/atualizar-cadastro-aluno/' + id,
                beforeSend: function(){
                    $(dataButton).html('<img src="/images/loadings/loading_03.svg" style="margin-right: 3px; width: 22px;" />Atualizando...').addClass('disabled');
                },
                success: function(data){
                    $(dataButton).html(dataButtonContent).removeClass('disabled');
                    if(data.status=='ok'){
                        if($('tr#alunoID_' + id).length){
                            $('tr#alunoID_' + id).html(data.tr_aluno);    
                        }
                        $('div.alert-alunos').html('Os dados do aluno foram atualizados com sucesso').slideDown().addClass('alert-success');
                        setTimeout(function(){
                            $('div.alert-alunos').slideUp(200, function(){
                                $(this).removeClass('alert-success').empty();
                            });
                        }, 6000);
                        $('div#modalDinamica').modal('hide');    
                    } else {                   
                        $('div.errors-editar-cadastro-aluno').html('<strong>ATENÇÃO: </strong><br />' + data.response).slideDown();
                        $('.modal').animate({
                            scrollTop: 0
                        }, 200);                        
                    }

                },
                error: function(jqxhr){
                    $(dataButton).html(dataButtonContent);
                    setTimeout(function(){
                        $('div#modalDinamica').modal('hide');
                        alert('ERRO INTERNO');
                    }, 1000);                  
                }                    
            });            
        }

        /* CARREGA TAB de alunos em situações diversas */

        function loadAlunosSitDiv(){
/*             $('div#response').slideUp(600, function(){
                $(this).empty().show();
            }); */            
            $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/load-alunos-situacoes-diversas',
                beforeSend: function(){
                    $('div#response2').html('<div id="temp" style="text-align: center;"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
                },
                success: function(data){
                    $('div#temp').remove();
                    $('div#response2').html(data.response);
                },
                error: function(jqxhr){
                    $('div#temp').remove();
                    $('div#response2').html('<div style="text-align: center; color: #DF0101">ERRO INTERNO</div>');    
                }
            }); 
        }

        /* DIALOGO INFO DO USUARIO */

        function dialogInfoUser(id_usr, tipo) {
            $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-info-user/' + tipo + '/' + id_usr,
                beforeSend: function(){
                    $('div#modalDinamica_no_style').modal('show');
                },
                success: function(data){
                    $('div#modalDinamica_no_style div.modal-body').html(data.body);
                }
            });  
        }

        //Original Julião...
        /*function dialogAdicionarAluSitDiv(id){
            $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-adicionar-aluno-situacao-diversa/' + id,
                beforeSend: function(){
                    loadingModalDinamica('show', 'sm');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    loadingModalDinamica('hide', 'sm');
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        $('div#modalDinamica').modal('hide');
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            });              
        }*/

        function DialogAdicionarAluSitDivNovo(id){
            $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/admin/alunoSitDiversas/' + id,
                beforeSend: function(){
                    loadingModalDinamica('show', 'sm');
                },
                success: function(data){
                    $('#modalDinamica div.modal-content').empty();

                    $(data).each(function(key,value){
                        $('div#modalDinamica div.modal-content').append(value);
                    });
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        $('div#modalDinamica').modal('hide');
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            });              
        }

        /*function dialogEditarCadastroAlunoSitDiv(id){
            $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-editar-cadastro-aluno-situacoes-diversas/' + id,
                beforeSend: function(){
                    loadingModalDinamica('show', 'sm');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    loadingModalDinamica('hide', 'sm');
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        $('div#modalDinamica').modal('hide');
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            });             
        }*/

        function dialogEditarCadastroAlunoSitDivNovo(id){
            $.ajax({
            	type:'GET',
                dataType: 'json',
                //url: '/ajax/dialog-editar-cadastro-aluno-situacoes-diversas/' + id,
                url: '/ajax/admin/alunoSitDiversas/' + id,
                beforeSend: function(){
                    loadingModalDinamica('show', 'sm');
                },
                success: function(data){
                    $('#modalDinamica div.modal-content').empty();

                    $(data).each(function(key,value){
                        $('div#modalDinamica div.modal-content').append(value);
                    });
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        $('div#modalDinamica').modal('hide');
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            });             
        }

        /*function reintegrarAlunoSituDiversas(id){

            $.ajax({
            	type:'PUT',
                dataType: 'json',
                data: {
                        '_token' : '{{ csrf_token() }}',
                        'requisicao' : 'reintegrar'
                },
                url: '/ajax/admin/alunoSitDiversas/' + id,
                beforeSend: function(){
                },
                success: function(data){
                    $('#modalDinamica div.modal-content').empty();

                    $(data).each(function(key,value){
                        alert(value);
                    });
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        $('div#modalDinamica').modal('hide');
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            });             
        }*/

        function AdicionarAluSitDiv(dataButton, id){
            var dataButtonContent = $(dataButton).html();
            var dataForm = $('form#incluir_aluno_situacao_diversa').serialize();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/incluir_aluno_situacao_diversa/' + id,
                beforeSend: function(){
                    $(dataButton).html('<img src="/images/loadings/loading_03.svg" style="margin-right: 3px; width: 24px;" />Alterando registro...').addClass('disabled');
                    $('div.error_aluno_situacao_diversa').empty().hide();
                },
                success: function(data){
                    $(dataButton).html(dataButtonContent).removeClass('disabled');
                    if(data.status=='ok'){
                        $('div#modalDinamica').modal('hide');
                        $('tr#alunoID_' + id).html(data.response);
                    } else if(data.status=='err'){
                        $('div.error_aluno_situacao_diversa').html(data.response).slideDown();
                    } else if(data.status=='not_authorized'){
                        alert('Você não tem permissão para transferir alunos para situaçãoes diversas');
                        $('div#modalDinamica').modal('hide');
                    }
                },
                error: function(jqxhr){
                    $(dataButton).html(dataButtonContent).removeClass('disabled');
                    $('div.error_aluno_situacao_diversa').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();    
                }                    
            });            
        }

        function AtualizarAluSitDiv(dataButton, id){
            var dataButtonContent = $(dataButton).html();
            var dataForm = $('form#atualizar_aluno_situacao_diversa').serialize();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/atualizar_aluno_situacao_diversa/' + id,
                beforeSend: function(){
                    $(dataButton).html('<img src="/images/loadings/loading_03.svg" style="margin-right: 3px; width: 24px;" />Alterando registro...').addClass('disabled');
                },
                success: function(data){
                    $(dataButton).html(dataButtonContent).removeClass('disabled');
                    if(data.status=='ok' && data.rematricula=='n'){
                        $('td#situacao_alunoID_' + id).html(data.response);
                        $('div#modalDinamica').modal('hide');
                    }
                    if(data.status=='ok' && data.rematricula=='y'){
                        $('tr#alunoID_' + id).html(data.response);
                        $('div#modalDinamica').modal('hide');
                    }
                },
                error: function(jqxhr){
                    $(dataButton).html(dataButtonContent).removeClass('disabled');   
                }                    
            });            
        }

        function OpcoesdeListagemSelecaoAlunos(dataButton){
            var dataButtonContent = $(dataButton).html();
            var dataForm = $('form#opcoes-listagem-selecao-alunos').serialize();
            $('div.opcoes-de-listagem').empty().hide();
            $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/opcoes-listagem-selecao-alunos?' + dataForm,
                beforeSend: function(){
                    $(dataButton).html('<img src="/images/loadings/loading_03.svg" style="margin-right: 3px; width: 32px;" />Buscando registros...').addClass('disabled');
                },
                success: function(data){
                    $(dataButton).html(dataButtonContent).removeClass('disabled');
                    $('div#response').empty();
                    if(data.status=='ok'){
                        $('html, body').animate({
                            scrollTop: ($('button#submitFiltroListagem').offset().top - 80)
                        }, 1000);
                        $('div#response').html(data.response);
                    } else {                        
                        $('div.opcoes-de-listagem').html(data.response).slideDown();
                    }
                    //$('div#modalDinamica_no_style div.modal-body').html(data.body);
                }
            });                
        }

        function dialogRelacaoVoluntariosAviacao(){
            $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/relacao-voluntarios-aviacao',
                beforeSend: function(){
                    $('div#content-voluntarios-aviacao').html('<div id="temp" style="text-align: center; margin: 24px; padding: 24px;"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
                },
                success: function(data){
                    $('div#temp').fadeOut(300, function(){
                        $(this).remove();
                        $('div#content-voluntarios-aviacao').html(data.response);
                    });
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            });             
            
        }

        function enviarRelacaoAptosAviacao(dataButton){
            $('div.status-aptos-aviacao').attr('class', 'alert status-aptos-aviacao').hide();
            var dataForm = $('form#relacao-aptos-aviacao').serialize();
            var dataButtonContent = $(dataButton).html();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/relacao-aptos-aviacao',
                beforeSend: function(){
                    $(dataButton).html('<img src="/images/loadings/loading_03.svg" style="margin-right: 3px; width: 32px;" />Registrando...').addClass('disabled');
                },
                success: function(data){
                    $(dataButton).html(dataButtonContent).removeClass('disabled');
                    $('html, body').animate({
                            scrollTop: 80
                    }, 1000);                    
                        if(data.status=='ok'){
                            $('div.status-aptos-aviacao').addClass('alert-success').html(data.response).slideDown();
                        } else if(data.status=='err'){
                            $('div.status-aptos-aviacao').addClass('alert-danger').html(data.response).slideDown();
                        }
                },
                error: function(jqxhr){
                    $(dataButton).html(dataButtonContent).removeClass('disabled');
                    setTimeout(function(){
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }  
            });            
        }        

        function dialogRemoveProntoFaltas(dataButton, id, csrf_token){
            $(document).confirmAcao('<strong>ATENÇÃO: </strong>.<p>Após remover o pronto de faltas todas as informações referente ao mesmo não será mais possível recuperá-las. Deseja continuar?</p>', function(){
                
                var dataButtonContent = $(dataButton).html();
                var uete = $('#removeProntoFaltas_'+id+' .custom-select[name="omcts_id"]').val();

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_token' : csrf_token,
                        'id_uete': uete
                    },
                    url: '/ajax/remover-pronto-faltas/' + id,
                    beforeSend: function(){
                        $(dataButton).html('<img src="/images/loadings/loading_03.svg" style="margin-right: 3px; width: 32px;" />Buscando registros...').addClass('disabled');
                        $('div#content-opcoes-avaliacaoID_' + id).empty();
                    },
                    success: function(data){
                        $(dataButton).html(dataButtonContent).removeClass('disabled');
                        /*$('span#deleta-pronto-faltasID_' + id).html(data.response);*/
                        $('div#content-opcoes-avaliacaoID_' + id).html(data.response);
                    },
                    error: function(jqxhr){
                        $(dataButton).html(dataButtonContent).removeClass('disabled');
                        setTimeout(function(){
                            alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                        }, 1000);
                    }  
                });                
            });      
        }

        function dialogProntoFaltas(id){
            $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-pronto-de-faltas/' + id,
                beforeSend: function(){
                    $('div#content-opcoes-avaliacaoID_' + id).html('<div id="temp" style="text-align: center; margin: 24px; padding: 24px;"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
                },
                success: function(data){
                    $('div#temp').fadeOut(300, function(){
                        $(this).remove();
                        $('div#content-opcoes-avaliacaoID_' + id).html(data.response);
                    });
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            });              
        }
        
        function enviarProntoFaltas(dataButton, id){
            $(document).confirmAcao('<strong>ATENÇÃO: </strong>.<p>Após o envio do pronto de faltas não será mais possível alterá-lo. Deseja continuar?</p>', function(){
                var dataForm = $('form#ponto-de-faltas').serialize();
                var dataButtonContent = $(dataButton).html();
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: dataForm,
                    url: '/ajax/enviar-pronto-faltas/' + id,
                    beforeSend: function(){
                        $(dataButton).html('<img src="/images/loadings/loading_03.svg" style="margin-right: 3px; width: 32px;" />Buscando registros...').addClass('disabled');
                    },
                    success: function(data){
                        $(dataButton).html(dataButtonContent).removeClass('disabled');
                        $('span#pronto-faltasID_' + id).html(data.response);
                        $('div#content-opcoes-avaliacaoID_' + id).empty();
                    },
                    error: function(jqxhr){
                        $(dataButton).html(dataButtonContent).removeClass('disabled');
                        setTimeout(function(){
                            alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                        }, 1000);
                    }  
                });                
            });            
        }

        function dialogLancarGraus(id){
            $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-lancar-graus/' + id,
                beforeSend: function(){
                    $('div#content-opcoes-avaliacaoID_' + id).html('<div id="temp" style="text-align: center; margin: 24px; padding: 24px;"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
                },
                success: function(data){
                    $('div#temp').fadeOut(300, function(){
                        $(this).remove();
                        $('div#content-opcoes-avaliacaoID_' + id).html(data.response);
                        //$('[data-toggle="tooltip"]').tooltip();   
                    });
/*                     if(data.porcento_preenchido>5){
                        setTimeout(function(){
                            $('div#status_top').show().animate({
                                'margin-top': '-84px' 
                            }, 400);
                        }, 800);
                    } */
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            });              
        }

        function dialogConfiguracoesRelatorio(){
            $('div.documentacao-relatorios').hide();
            $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-configuracoes-relatorios',
                beforeSend: function(){
                    loadingModalDinamica('show', 'sm');
                },
                success: function(data){
                    if(data.status=='ok'){
                        $('div#modalDinamica div.modal-header h5').html(data.header);
                        $('div#modalDinamica div.modal-body').html(data.body);
                        $('div#modalDinamica div.modal-footer').html(data.footer);
                        loadingModalDinamica('hide', 'sm');
                    } else {
                        alert('Você não tem permissão para relizar estas configurações');
                        $('div#modalDinamica').modal('hide');
                    }
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        $('div#modalDinamica').modal('hide');
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            });            
        }

        function dialogPeriodoLancamentoTAF(){
            $('div.documentacao-relatorios').hide();
            $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-periodo-lancamento-taf',
                beforeSend: function(){
                    loadingModalDinamica('show', 'sm');
                },
                success: function(data){
                    if(data.status=='ok'){
                        $('div#modalDinamica div.modal-header h5').html(data.header);
                        $('div#modalDinamica div.modal-body').html(data.body);
                        $('div#modalDinamica div.modal-footer').html(data.footer);
                        loadingModalDinamica('hide', 'sm');
                    } else {
                        alert('Você não tem permissão para relizar estas configurações');
                        $('div#modalDinamica').modal('hide');
                    }
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        $('div#modalDinamica').modal('hide');
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            });            
        }

        /* AJAX - ATUALIZAR PERIODO LANÇAMENTO TFM */

        function AtualizarPeriodoLancamentoTAF(){
            var dataForm = $('form#editar_periodo_lanca_taf').serialize();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/editar-periodo-lanca-taf',
                beforeSend: function(){
                    $('div.errors-adicionar-disciplinas ul').remove().parent().hide();
                },
                success: function(data){
                    if(data.status=='ok'){
                        $('div#modalDinamica').modal('hide');
                        $('a#avaliacoes').trigger('click');
                        $('html, body').animate({
                            scrollTop: 10*1000
                        }, 1000);
                        setTimeout(function(){
                                $('button#disciplina_99999').trigger('click');
                                $('blockquote#disciplina_99999').show();
                                $('blockquote#disciplina_99999 footer').html('Período de lançamento do TFM atualizado agora mesmo!');
                                setTimeout(function(){
                                    $('blockquote#disciplina_99999').fadeOut();
                                    $('blockquote#disciplina_99999 footer').empty();
                                }, 10000);
                            }, 460);                        
                    } else {
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-disciplinas2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar atualizar o período de lançamento do TFM').slideDown();    
                    }
                },
                error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-disciplinas2').html('<strong>ATENÇÃO: </strong> Houve um erro interno ao tentar editar o período de lançamento do TFM.').slideDown();    
                    } else if(jqxhr.status==422){
                        $('div.errors-adicionar-disciplinas').slideDown(100);
                        var errors = $.parseJSON(jqxhr.responseText);
                        $('div.errors-adicionar-disciplinas').prepend('<ul style="margin: 0 6px;"></ul>');                            
                        $.each(errors.errors, function (index, value) {
                            $('div.errors-adicionar-disciplinas ul').append('<li>' + value + '</li>');
                        });  
                    }
                }                    
            });
        }        

        //var dataButtonContent = $(dataButton).html();
        //$(dataButton).html('<img src="/images/loadings/loading_03.svg" style="margin-right: 3px; width: 32px;" />Buscando registros...').addClass('disabled');
        
        function configurarRelatorios(dataButton){
            var dataForm = $('form#configuracoes_relatorios').serialize();
            var dataButtonContent = $(dataButton).html();
            
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/configurar-relatorios',
                beforeSend: function(){
                    $(dataButton).html('<img src="/images/loadings/loading_03.svg" style="margin-right: 3px; width: 32px;" />Registrando...').addClass('disabled');
                },
                success: function(data){
                    $('div#modalDinamica').modal('hide');
                    $('html, body').animate({
                            scrollTop: 80
                    }, 1000);                    
                    if(data.status=='ok'){
                        $('div.documentacao-relatorios').addClass('alert-success').html(data.response).slideDown();
                    } else {
                        $('div.documentacao-relatorios').addClass('alert-danger').html(data.response).slideDown();
                    }
                    
                    setTimeout(function(){
                        $('div.documentacao-relatorios').slideUp();
                    }, 8000);

                },
                error: function(jqxhr){
                    $(dataButton).html(dataButtonContent).removeClass('disabled');
                    setTimeout(function(){
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }  
            });
        }

        function registrarGrauAluno(alunoID, avaliacaoID){
            var gbo = $('input[name="nota_aluno_id_' + alunoID +'"]').val();
            var contentTr = $('td#col_aluno_' + alunoID).html();
            $.ajax({
                type:'GET',
                dataType: 'json',
                url: '/ajax/registrar-grau-aluno/' + alunoID + '/' + avaliacaoID + '/' + gbo ,
                beforeSend: function(){
                    $('td#col_aluno_' + alunoID).html('<div style="text-align: center;"><img src="/images/loadings/loading_04.svg" style="width: 24px; margin-top: 12px;" /></div>');
                },
                success: function(data){
                    //$('div#status_top').removeClass('visible');
                    if(data.status=='ok'){
                        $('td#col_aluno_' + alunoID).html(data.response);
                        if(data.porcento_preenchido>5){
                            $('div#porcentagem-preenchimento div').css('width', data.porcento_preenchido + '%').html(data.porcento_preenchido + '%');
                            setTimeout(function(){
/*                                 if(!$('div#status_top').is(':visible')){
                                    $('div#status_top').show().animate({
                                        'margin-top': '-84px' 
                                    }, 400);                                
                                } */
                            }, 800);
                        }
                        //$('[data-toggle="tooltip"]').tooltip();                        
                    } else if (data.status=='err'){
                        $('td#col_aluno_' + alunoID).html(contentTr);
                        $('div#modalDinamica_no_style').modal('show');
                        $('div#modalDinamica_no_style div.modal-body').html(data.response);
                    }
                 
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        $('td#col_aluno_' + alunoID).html(contentTr);
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            }); 
        }


        function editarRegistroGrauAluno(alunoID, avaliacaoID){
            var contentTr = $('td#col_aluno_' + alunoID).html();
            $.ajax({
                type:'GET',
                dataType: 'json',
                url: '/ajax/editar-registro-grau-aluno/' + alunoID + '/' + avaliacaoID,
                beforeSend: function(){
                    $('td#col_aluno_' + alunoID).html('<div style="text-align: center;"><img src="/images/loadings/loading_04.svg" style="width: 24px; margin-top: 12px;" /></div>');
                },
                success: function(data){
                    //$('div#status_top').removeClass('visible');
                    if(data.status=='ok'){
                        $('td#col_aluno_' + alunoID).html(data.response);                        
                        $('[data-toggle="tooltip"]').tooltip('hide');                        
                        if(data.porcento_preenchido>5){
                            $('div#porcentagem-preenchimento div').css('width', data.porcento_preenchido + '%').html(data.porcento_preenchido + '%');
                            setTimeout(function(){
/*                                 if(!$('div#status_top').is(':visible')){
                                    $('div#status_top').show().animate({
                                        'margin-top': '-84px' 
                                    }, 400);                                
                                } */
                            }, 800);
                        } else {
                            setTimeout(function() {
/*                                 if($('div#status_top').is(':visible')){
                                    $('div#status_top').animate({
                                        'margin-top': '84px' 
                                    }, 400);                                
                                } */
                            }, 800);                            
                        }                        
                    } else if (data.status=='err'){
                        $('td#col_aluno_' + alunoID).html(contentTr);
                        $('div#modalDinamica_no_style').modal('show');
                        $('div#modalDinamica_no_style div.modal-body').html(data.response);
                    }
                 
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        $('td#col_aluno_' + alunoID).html(contentTr);
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            }); 
        }

        function toogleConfirmGrau(inputField){
            if($.isNumeric($(inputField).val())){
                $(inputField).nextAll('a').first().show();
                $(inputField).nextAll('span').first().hide();
            } else {
                $(inputField).nextAll('a').first().hide();
                $(inputField).nextAll('span').first().show();                
            }
        }

        function carregaOpcoesRelatorio(item){
            
            $('#full-modal').modal('show');
            $.ajax({
                type:'GET',
                dataType: 'json',
                url: '/ajax/carrega-opcoes-relatorio/' + item,
                beforeSend: function(){
                    $('div#title-full-modal').empty(); 
                    $('div#body-full-modal').empty();                    
                    $('div#title-full-modal').html('<div id="temp"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
                },
                success: function(data){
                    $('div#temp').fadeOut(300, function(){
                        $(this).remove();
                        $('div#title-full-modal').html(data.modalTitle); 
                        $('div#body-full-modal').html(data.response);  
                    });
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            });  
        }

        function selectAlunosFizeramAvaliacao(selectElement){
            var id = $(selectElement).val(); 
            $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/select-alunos-fizeram-avaliacao/' + id,
                beforeSend: function(){
                    $('div#lista_complementar').html('<div id="temp" style="text-align: center; margin: 24px; padding: 24px;"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
                },
                success: function(data){
                    $('div#temp').fadeOut(300, function(){
                        $(this).remove();
                        $('div#lista_complementar').html(data.response);
                    });
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            });
        }

        function alterarPrecedencia(tipo, id){
            var precedenciaVal = $('div#precedencia_' + id).text();
            if(tipo=='decrement' && precedenciaVal>0){
                precedenciaVal--;
                $('div#precedencia_' + id).text(precedenciaVal);
            }
            if(tipo=='increment'){
                precedenciaVal++;
                $('div#precedencia_' + id).text(precedenciaVal);
            }
            $.ajax({
            	type:'GET',
                url: '/ajax/alterar-precedencia/' + id + '/' + precedenciaVal,
                beforeSend: function(){
                    $('div#precedencia_' + id).html('<div id="temp"><img src="/images/loadings/loading_02.svg" style="width: 18px;" /></div>');
                },
                success: function(data){
                    $('div#temp').fadeOut(300, function(){
                        $(this).remove();
                        $('div#precedencia_' + id).html(data);
                    });
                }                   
            });          
        }

        $(document).ready(function(){
            
            /* LARGURA AUTO DO CONTENT */

            var docWidth = $(document).width();
            var menuWidth = $('#menu-lateral').width();
            $('#content').width(docWidth - menuWidth).fadeIn();

             /* SCROLL AUTO DO MENU */

            var lateral_top_height =  $('#lateral-top').height();
            var lateral_profile_top_height =  $('#lateral-profile-top').height();
            var docHeight = $(document).height();
            $('div.box-ul-menu').height(docHeight - (lateral_top_height + lateral_profile_top_height));

            /* CARREGANDO PRIMEIRO ITEM DO MENU */

            var id = $('div.box-ul-menu a').eq(0).attr('id');
            $('div.box-ul-menu  li').eq(0).addClass('menu-list-01-active');
            loadAdminAjaxContent(id);
            
            /* EVENTO PARA CARREGAR CONTEUDO DO MENU VIA AJAX */

            $(document).on('click', 'div.box-ul-menu li', function(evt) {
                evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
        
                var id = $(this).find('a').attr('id');
                $('div.box-ul-menu li').removeClass('menu-list-01-active');
                $(this).addClass('menu-list-01-active');
                loadAdminAjaxContent(id);
            });            
            
            /* REMOVENDO CLASSE visible NATIVA DO BOOTSTRAP */

            $('input.pesquisar-in-card-title').removeClass('visible');

            /* EFEITO PARA MOSTRAR O INPUT DE PESQUISA */

            $(document).on('click', 'div.box-pesquisar-in-card-title a', function(){
                $('input.pesquisar-in-card-title').removeClass('visible');
                if($('input.pesquisar-in-card-title').is(':visible')){
                    $('input.pesquisar-in-card-title').slideUp('fast');
                    $(this).parent().css('border-bottom', 'none');
                } else {
                    $('input.pesquisar-in-card-title').slideDown('fast', function(){
                        $(this).focus();
                    });
                    $(this).parent().css('border-bottom', '1px solid #ccc');
                }
            });

            $(document).on('blur', 'input.pesquisar-in-card-title', function(){
                $(this).slideUp('fast');
                $('div.box-pesquisar-in-card-title a').parent().css('border-bottom', 'none');
                $(this).val('');
            });

            /* PESQUISA NA TABELA DE OPERADORES */

            $(document).on('keyup', 'input.busca-operador', function(){
                if($('td.operador-nome').is(':visible')){
                    $('.box-pesquisa-not-found').hide();
                    $("td.operador-nome:not(:contains('"+ $(this).val() +"'))").parent().hide();
                    $("td.operador-nome:contains('"+ $(this).val() +"')").parent().show();
                } else {
                    $("td.operador-nome:contains('"+ $(this).val() +"')").parent().show();
                    if($('td.operador-nome').is(':visible')){
                        $('.box-pesquisa-not-found').hide(); 
                    } else {
                        $('.box-pesquisa-not-found').show();    
                    }
                }
            });

        });
            
        // FALE COM ALUNO

        $(document).on('click', 'div.box-aluno-list', function(evt){
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
        
            var this_element = $(this).children('div.aluno_checked');
            if(this_element.is(':visible')){
                $(this).children('div.aluno_checked').hide();    
                $(this).children('input[type="checkbox"]').prop('checked', false);
                loadColConversasOnSelectAluno();
            } else {
                $(this).children('input[type="checkbox"]').prop('checked', true);
                $(this).children('div.aluno_checked').show();    
                loadColConversasOnSelectAluno();
            }
        });            

        function loadColConversasOnSelectAluno(){
            var checkboxElement = $('input:checked[name="alunosCheck[]"]');
            if(checkboxElement.length==0){
                $('button#enviar_mensagem_fale_com_aluno').prop('disabled', true);
                $('div.lista_destinatarios').slideUp(80);
                $('div.box-mensagens').html('<i class="ion-chatbubble-working" style=" font-size: 38px;"></i><br />SELECIONE UM ALUNO PARA ENVIAR UMA MENSAGEM');

            } else if(checkboxElement.length==1){

                $('div.lista_destinatarios, div.dialog-envio-msg').slideDown(80);
                $('textarea[name="text_msg_fale_com_aluno"]').focus();
                if($('textarea[name="text_msg_fale_com_aluno"]').val()==''){
                    $('button#enviar_mensagem_fale_com_aluno').prop('disabled', true);
                } else {
                    $('button#enviar_mensagem_fale_com_aluno').prop('disabled', false);
                }

                loadMsgAlunos();

            } else {

                $('div.lista_destinatarios, div.dialog-envio-msg').slideDown(80);
                $('textarea[name="text_msg_fale_com_aluno"]').focus();
                if($('textarea[name="text_msg_fale_com_aluno"]').val()==''){
                    $('button#enviar_mensagem_fale_com_aluno').prop('disabled', true);
                } else {
                    $('button#enviar_mensagem_fale_com_aluno').prop('disabled', false);
                }

                loadMsgAlunos();

            }
        }

        function loadMsgAlunos(){
            var checkboxElement = $('input[name="alunosCheck[]"], input[name="_token"]').serialize();
            $.ajax({
            	type:'POST',
                dataType: 'json',
                data: checkboxElement,
                url: '/ajax/carregar-mensagens',
                beforeSend: function(){
                    $('div.lista_destinatarios .lista').html('<div id="temp" style="text-align: center;"><img src="/images/loadings/loading_02.svg" style="width: 28px; margin-right: 8px;" /></div>');
                    $('div.box-aluno-list').css('opacity', 0.375);
                },
                success: function(data){
                    $('div.box-aluno-list').css('opacity', 1);
                    if(data.conversa_com){
                        $('div.lista_destinatarios .lista').html(data.conversa_com);    
                    }
                    if(data.box_mensagens){
                        $('div.box-mensagens').html(data.box_mensagens);
                        setTimeout(function(){
                            $('.box-mensagens').animate({scrollTop: 2*100000}, 1200);
                        }, 400)    
                    }
                }
            });
        }

        $(document).on('keyup', 'textarea[name="text_msg_fale_com_aluno"]', function(){
            if($(this).val().length>0 && $('input:checked[name="alunosCheck[]"]').length>0){
                $('button#enviar_mensagem_fale_com_aluno').prop('disabled', false);    
            } else {
                $('button#enviar_mensagem_fale_com_aluno').prop('disabled', true);
            }
        });

        function enviarMsgAluno(dataButton){
            var dataForm = $('input[name="alunosCheck[]"], input[name="_token"], textarea[name="text_msg_fale_com_aluno"]').serialize();
            var dataButtonContent = $(dataButton).html();
            $.ajax({
            	type:'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/enviar-mensagem-aluno',
                beforeSend: function(){
                    $(dataButton).html('<img src="/images/loadings/loading_03.svg" style="margin-right: 3px; width: 24px;" />Enviando...').prop('disabled', true);
                    $('div.box-aluno-list').css('opacity', 0.375);
                },
                success: function(data){
                    if($('div.empty_box').length){
                        $('div.empty_box').remove();    
                    }
                    $('textarea[name="text_msg_fale_com_aluno"]').val('').focus();
                    $('div.box-aluno-list').css('opacity', 1);
                    $(dataButton).html(dataButtonContent).prop('disabled', false);
                    $('div.box-mensagens').append(data.box_mensagens);
                    $('.box-mensagens').animate({scrollTop: 2*100000}, 1200);
                }
            });            
        }

        function dialogMensagensEspeciais(){
            $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-mensagens-especiais',
                beforeSend: function(){
                    loadingModalDinamica('show', 'sm');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    loadingModalDinamica('hide', 'sm');
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        $('div#modalDinamica').modal('hide');
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            });            
        } 

        function enviarMsgEspecialAluno(dataButton){
            if($('select[name="avaliacaoID"]').val()!=0){
                var dataForm = $('input[name="alunosCheck[]"], input[name="_token"], select[name="avaliacaoID"]').serialize();
                var dataButtonContent = $(dataButton).html();
                $.ajax({
                    type:'POST',
                    data: dataForm,
                    url: '/ajax/enviar-mensagem-especial-aluno/',
                    beforeSend: function(){
                        $(dataButton).html('<img src="/images/loadings/loading_03.svg" style="margin-right: 3px; width: 24px;" />Enviando...').prop('disabled', true);
                    },
                    success: function(data){
                        if(data=='ok'){
                            $('a#desmarcar-todas').trigger('click');
                            $('div#modalDinamica').modal('hide');
                        }
                    }
                });                 
            } else {
                $('select[name="avaliacaoID"]').focus();
            }
           
        }

        function lancarTafAluno(id, aLinkElement){
            //alert('Deu certo Berna cagão. O ID desse aluno é' + id);
            var dataForm = $('form#Form_lancarTaf_' + id).serialize();
            //alert(data);
            $.ajax({
                    dataType: 'json',
                    type:'POST',
                    data: dataForm + '&id=' + id,
                    url: '/ajax/gravar-taf-aluno',
                    beforeSend: function(){
                        $('div#media_nota_taf_' + id).html('<img src="/images/loadings/loading_03.svg" style="margin-right: 3px; width: 24px;" />');
                    },
                    success: function(data){
                        if(data.status=='ok'){
                            $('div#media_nota_taf_' + id).html(data.media);
                            $(aLinkElement).removeClass('btn-success').addClass('btn-warning').html('Atualizar');
                        } else {
                            $('div#media_nota_taf_' + id).html('0,000');
                        }

                            
                    }
                }); 
        }

        function lancarRecuperacaoTafAluno(id, aLinkElement){
            
            var dataForm = $('form#Form_lancarTaf_' + id).serialize();
            
            $.ajax({
                dataType: 'json',
                type:'POST',
                data: dataForm + '&id=' + id,
                url: '/ajax/gravar-taf-recuperacao-aluno',
                beforeSend: function(){
                    $('div#media_nota_taf_recuperacao_' + id).html('<img src="/images/loadings/loading_03.svg" style="margin-right: 3px; width: 24px;" />');
                },
                success: function(data){
                    if(data.status=='ok'){
                        $('div#media_nota_taf_recuperacao_' + id).html(data.media);
                        //$(aLinkElement).removeClass('btn-success').addClass('btn-warning').html('Atualizar');
                    } else {
                        $('div#media_nota_taf_recuperacao_' + id).html('0,000');
                    }

                        
                }
            }); 
        }
    
</script>

@stop
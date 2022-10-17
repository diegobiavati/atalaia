@extends('headers')
@section('js-includes')

<script src="/js/jquery.mask.min.js"></script>
@stop
@section('css-includes')

<link href="/css/ionicons.css" rel="stylesheet" type="text/css" />
<link href="/css/menu_style_gaviao.css" rel="stylesheet" type="text/css" />
<link href="/css/style.css" rel="stylesheet" type="text/css" />
<link href="/css/font-exo.css" rel="stylesheet" type="text/css" />

<?php
    $backgroundColor = session()->get('backgroundColor');
    $backgroundVisaoGeral = session()->get('backgroundVisaoGeral');
    $backgroundMenuLateral = session()->get('backgroundMenuLateral');
?>

<style type="text/css">
    body {
      font-family: 'Exo', sans-serif;
    }

    #top{
        background: {{ $backgroundColor }}
    }

    #lateral-top {
        background: {{ $backgroundColor }}
    }

    div#back_to_top{
        background: {{ $backgroundColor }}
    }

    #menu-lateral {
        background-image: {{$backgroundMenuLateral}}
    }

    li.menu-list-01:hover {
        background-color: {{$backgroundVisaoGeral}}
    }

    li.menu-list-01-active {
        background-color: {{$backgroundVisaoGeral}}
    }

    #menu-lateral {
        background-color: {{ $backgroundVisaoGeral }}
    }

    .modal-header {
        font-weight: bold;
        background-color: {{$backgroundVisaoGeral}}
    }

</style>

@if($ownauthcontroller->PermissaoCheck(1))
<style type="text/css"> 

    .btn.btn-success.btn-sm{
        background: #0094D3; 
        border-color: #0094D3; 
    }

    #menu-lateral {
        background-color: #0094D3; 
    }

</style>
@else
<style type="text/css">

    .btn.btn-success.btn-sm{
        background-color:{{$backgroundColor}}
        border-color:{{$backgroundColor}}
    }

</style>
@endif

@stop

@section('title', 'Gavião :: Admin Operadores')

@section('content')

<!--<audio id="notification_online" controls preload="none" style="display: none;"> 
    <source src="/sounds/notification/light.mp3" type="audio/mpeg">
</audio>

<audio id="notification_finish" controls preload="none" style="display: none;"> 
    <source src="/sounds/notification/quiet-knock.mp3" type="audio/mpeg">
</audio>-->

<div id="menu-lateral">
    <div id="lateral-top">
        <img class="logo_sistema" src="/images/logo_esa.png" />        
    </div>
    <div id="lateral-profile-top">
        <div class="lateral-profile-top-content">
            <h4>SISTEMA GAVIÃO
                <small class="text-muted" style="font-size: 14px;">
                    <br />GESTÃO ESCOLAR MILITAR
                </small>
            </h4>
            @if($ownauthcontroller->PermissaoCheck(30))
            <div>
                <select class="custom-select" name="seletor_qms" style="text-align: center;">
                    @foreach($cursos as $curso)
                        @if(session()->has('qms_selecionada'))
                            <option value="{{$curso->id}}" {{ ((session()->get('qms_selecionada') == $curso->id) ? 'selected': '') }}>{{$curso->qms}}</option>
                        @else
                            <option value="{{$curso->id}}" {{ (($curso->id == 9999) ? 'selected': '') }}>{{$curso->qms}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            @endif
        </div>
        

        <div style="border-top: 1px solid #ccc; height: auto; padding: 0 3px 12px 3px;">
            <div class="data_user_imagem_perfil" style="position: absolute; margin: -32px 0 0 101px; background: url({{$img_perfil}}) no-repeat center center; background-size: cover; width: 64px; height: 64px; border-radius: 50%;"></div> 
            <div style=" text-align: center; margin-top: 36px; font-size: 14px;">
            <span id="postograd_user"><b>{{$operador->postograd->postograd_abrev }}</b></span> <span id="nome_guerra_user"><b>{{$operador->nome_guerra}}</b></span>
            <br /><span id="omct_user">{{ $operador->qms->qms }}</span>
                @if($operador->id_funcao_operador!='' && is_array(explode(',', $operador->id_funcao_operador)))
                    <br />
                    <div style="padding: 0 8px;">
                        <span id="funcao_user" style="color: {{$backgroundColor}}; font-size: 12px;">
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
                <a class="no-style" href="{{route('gaviao.logout')}}">Sair</a>
            </div>
        </div>

    </div>
    
    <div class="box-ul-menu scroll_style_01" style="color: #fff; overflow-y: auto;">
        <ul class="list-group list-group-flush">

            @if($ownauthcontroller->PermissaoCheck(1))
                @include('menu_admin_operadores_gaviao')
            @else
                <li class="list-group-item justify-content-between align-items-center menu-list-01">
                    <a id="visao-geral-gaviao" href="javascript: void(0);">
                        <i class="ion-ios-eye"></i>
                        Visão geral
                        <span class="badge badge-primary badge-pill"></span>
                    </a>
                </li>
                @if($ownauthcontroller->PermissaoCheck([13, 14]))
                <li class="list-group-item justify-content-between align-items-center menu-list-01">
                    <a id="gerenciar-operadores-gaviao" href="javascript: void(0);">
                        <i class="ion-ios-people"></i> 
                        Gerenciar Operadores
                        <span class="badge badge-primary badge-pill"></span>
                    </a>
                </li>
                @endif
                @if($ownauthcontroller->PermissaoCheck(29))
                <li class="list-group-item justify-content-between align-items-center menu-list-01">
                    <a id="alunos-gaviao" href="javascript: void(0);">
                        <i class="ion-android-contacts"></i>
                        Gerenciar Alunos
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
                <li class="list-group-item justify-content-between align-items-center menu-list-01">
                    <a id="view-relatorios" href="javascript: void(0);">                
                        <i class="ion-ios-pie"></i>
                        Documentação e Relatórios
                        <span class="badge badge-primary badge-pill"></span>
                    </a>
                </li>
                @if($ownauthcontroller->PermissaoCheck(27))
                <li class="list-group-item justify-content-between align-items-center menu-list-01">
                    <a id="view-ssaa" href="javascript: void(0);">                
                        <i class="ion-android-clipboard"></i>
                        SSAA
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
        <a href="javascript: void(0);" class="no-style" data-toggle="popover_notification" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;"></a>
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
                <div class="modal-header" style="background-color: {{ $backgroundColor }}">
                <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer" style="text-align: right;">
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

    <!--<div class="modal fade bd-example-modal-sm" id="modalConfirmAcao" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
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
    </div>-->

<!-- FINAL MODAL confirmAcao DINAMICA-->



<script>
        //Corrige o bug de perda do controle de modal sobre modal quando fecha
        $(document).on('hidden.bs.modal', function(event){
            if($('.modal:visible').length){
                $('body').addClass('modal-open');
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

        $(document).ready(function(){
            
            // LARGURA AUTO DO CONTENT 
            var docWidth = $(document).width();
            var menuWidth = $('#menu-lateral').width();
            $('#content').width(docWidth - menuWidth).fadeIn();

            // SCROLL AUTO DO MENU
            var lateral_top_height =  $('#lateral-top').height();
            var lateral_profile_top_height =  $('#lateral-profile-top').height();
            var docHeight = $(document).height();
            $('div.box-ul-menu').height(docHeight - (lateral_top_height + lateral_profile_top_height));

            // CARREGANDO PRIMEIRO ITEM DO MENU 
            var id = $('div.box-ul-menu a').eq(0).attr('id');
            $('div.box-ul-menu  li').eq(0).addClass('menu-list-01-active');
            loadAdminAjaxContent(id);
            
            //EVENTO PARA CARREGAR CONTEUDO DO MENU VIA AJAX 
            $(document).on('click', 'div.box-ul-menu li', function(evt) {
                evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
        
                var id = $(this).find('a').attr('id');
                $('div.box-ul-menu li').removeClass('menu-list-01-active');
                $(this).addClass('menu-list-01-active');
                loadAdminAjaxContent(id);
            });            
            
            // REMOVENDO CLASSE visible NATIVA DO BOOTSTRAP
            $('input.pesquisar-in-card-title').removeClass('visible');

            // EFEITO PARA MOSTRAR O INPUT DE PESQUISA

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

            // Seleciona a QMS que será utilizada no sistema
            $(document).on('change', 'select[name="seletor_qms"]', function(){
                $.ajax({
                    type: 'GET',
                    url: '/gaviao/ajax/seletorQms/' + $('select[name="seletor_qms"] option:selected').val(),
                    beforeSend: function(){
                        loadingModalDinamica('show', 'lg');
                    },
                    success: function(data){
                        if(data.success){
                            window.location.reload();
                        }
                        loadingModalDinamica('hide');
                    }
                });
            });

        });

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
                $('div#modalDinamica div.modal-header i').empty();
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

        /* CHAMADA AJAX */

        function loadAdminAjaxContent(routeAs){
            if(routeAs != undefined){
                $.ajax({
                    type: 'GET',
                    url: '/gaviao/ajax/' + routeAs,
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
                url: '/gaviao/ajax/' + routeAs + '/' + idANoFormacao,
                beforeSend: function(){
                    $('div#relatorios-content').html('<div id="temp" style="text-align: center; margin-top: 24px;"><img src="/images/loadings/loading_04.svg" style="width: 28px; margin-right: 8px;" /><br />Aguarde, carregando...</div>');    
                },
                success: function(data){
                    $('div#relatorios-content').html(data);    
                }
            });
        }

        function openWindow(url, titulo){
            window.open(url, titulo);
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

        // DIALOGO INFO DO USUARIO 
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
        /*$.ajaxSetup({
            error: function(jqxhr){
                if(jqxhr.status==500){
                    //location.href='{{route('atalaia')}}';
                }
            }
        });*/

        function downloadPDF(urlVisualizacao, dados){

            $.ajax({
                type: 'POST',
                url: urlVisualizacao,
                data: dados,
                beforeSend: function() {
                    $('div#parametros-content').empty();
                    $('div#parametros-content').html('<div id=\"temp\"><img src=\"/images/loadings/loading_01.svg\" style=\"width: 24px; margin-right: 8px;\" /> Aguarde, carregando...</div>');
                },
                success: function(data) {
                    $('div#temp').fadeOut(300, function() {
                        $(this).remove();
                        
                        window.open(data.rota+'/'+data.arquivo);
                    });
                },
                error: function(jqxhr) {
                    setTimeout(function() {
                        $('div#parametros-content').empty();
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }
            });
        }
    
</script>

@stop
<?php
    $backgroundVisaoGeral = (session()->has('backgroundVisaoGeral') ? session()->get('backgroundVisaoGeral') : '#0A2A1B').';';
?>
<style>
    .modal-full {
        min-width: 100%;
        margin: 0;
    }

    .modal-full .modal-content {
        min-height: 100vh;
    }
</style>

<div style="text-align: center; margin-top: 22px; color:  #696969;">
    <h4>SSAA</h4>
</div>
<div class="alert documentacao-relatorios" role="alert" style="margin-top: 44px"></div>
<div style="margin: 44px auto; width: 530px; text-align: center; margin-top: 84px;">

    @if($ownauthcontroller->PermissaoCheck(34))
    <div class="box-option-menu-docs-rels">
        <a href="javascript: void(0);" class="no-style" id="gerenciar-disciplinas">
            <div style="border-radius: 36px; width: 84px; height: 84px; background-color: {{$backgroundVisaoGeral}} margin: 0 auto; margin-bottom: 6px; line-height: 86px; box-shadow: 2px 2px 5px #888888; color: #ffffff;">
                <i class="ion-android-calendar" style="font-size: 44px;"></i>
            </div>
            <span>Gerenciador de Disciplinas</span>
        </a>
    </div>
    @endif

    @if($ownauthcontroller->PermissaoCheck([32,33]))
    <div class="box-option-menu-docs-rels">
        <a href="javascript: void(0);" class="no-style" id="calendario-ssaa">
            <div style="border-radius: 36px; width: 84px; height: 84px; background-color: {{$backgroundVisaoGeral}} margin: 0 auto; margin-bottom: 6px; line-height: 86px; box-shadow: 2px 2px 5px #888888; color: #ffffff;">
                <i class="ion-android-calendar" style="font-size: 44px;"></i>
            </div>
            <span>Calendário de Avaliações</span>
        </a>
    </div>
    @endif

</div>

<div id="temp" style="width: 70%; margin: 22px auto; text-align: center;"></div>

<div class="modal" id="ssaa-modal" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="alert alert-success" role="alert" style="margin-bottom:0px;"></div>
    <div class="alert alert-danger" role="alert" style="margin-bottom:0px;"></div>

    <div class="modal-dialog modal-full" role="document">
        <div class="modal-content" style="background-color: rgba(255,255,255,0.925); border-radius: 0;">
            <div class="modal-header">
                <div style="width: 100%;">
                    <div id="title-ssaa-modal" style="margin-top: 10px; font-size: 16px; text-align: center;"></div>
                    <div style="position: absolute; left: 100%; font-size: 18px; margin: -32px 0 0 -38px;">
                        <a href="javascript: void(0);" class="no-style" data-dismiss="modal">
                            <i class="ion-close"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div id="body-ssaa-modal"></div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function() {
        
        $('#calendario-ssaa').click(function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
            carregaOpcaoAjaxContent('calendario', 'index', 'Modal');
        });

        $('#gerenciar-disciplinas').click(function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
            carregaOpcaoAjaxContent('gerenciar-disciplinas', 'index', 'Modal');
        });

    });

    function carregaOpcaoAjaxContent(tipo, item, modo) {

        $('div.alert-success').html(null).slideUp();
        $('div.alert-danger').html(null).slideUp();

        $('div#temp').empty();
        $('div#body-ssaa-modal').empty();

        $.ajax({
            type: 'GET',
            url: '/gaviao/ajax/' + tipo + '/' + item,
            beforeSend: function() {
                //$('div#temp').empty();
                if (modo == 'Modal') {
                    $('#ssaa-modal').modal('show');
                    $('div#body-ssaa-modal').empty();
                    $('div#title-ssaa-modal').html('<div id="temp"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
                }
            },
            success: function(data) {

                if (modo == 'Modal') {
                    $('div#temp').fadeOut(300, function() {
                        $(this).remove();
                        $('div#body-ssaa-modal').empty();
                        $('div#body-ssaa-modal').html(data);
                    });
                } else {
                    $('div#temp').html(data);
                }

            },
            error: function(jqxhr) {
                setTimeout(function() {
                    alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                }, 1000);
            }
        });
    }
</script>
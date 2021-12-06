<?php
$backgroundVisaoGeral = session()->get('backgroundVisaoGeral');
?>

<div class="card bg-light mb-3">
    <div class="card-header">
        <i class="ion-university"></i><strong>DIPLOMA DIGITAL</strong>
        <div style="float: right">

            <a href="javascript: void(0);" data-toggle="popover" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
                <i class="ion-android-more-vertical" style="color: #696969;"></i>
            </a>
            <div id="popover-content" style="display: none;">
                <div class="menu_inside_popover">
                    <i class="ion-plus"></i><a href="javascript: void(0);" onclick="dialogAdicionarPeriodoDiplomacao();">Criar novo período</a><br />
                </div>
            </div>

        </div>
    </div>

    <div class="card-body">
        <div class="alert errors-diplomacao" role="alert"></div>
        <h5 class="card-title" style="text-align: center; margin: 18px 0 44px 0;">
            Mensagem a Definir <span style="font-size: 24px;"><b>2021</b></span>
        </h5>
        <p class="card-text">

        <!--<div class="card-header" style="background-color: {{$backgroundVisaoGeral}} color: #fff; padding: 16px; font-size: 16px;">-->
        <div class="box-registro-not-found">
            <i class="ion-social-snapchat-outline" style="font-size: 32px"></i><br />
            <span style="color: brown">Não há períodos de diplomação configurados.</span>
        </div>

        </p>

    </div>

</div>

<script>
    $("[data-toggle=popover]").popover({
        trigger: 'focus',
        html: true,
        delay: {
            "show": 100,
            "hide": 400
        },
        content: function() {
            return $('#popover-content').html();
        }
    });

    function dialogAdicionarPeriodoDiplomacao() {
        $('div.errors-diplomacao').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');

        $.ajax({
            type: 'GET',
            url: '{{ $url_diploma_periodo }}',
            beforeSend: function() {
                loadingModalDinamica('show', 'lg');
                $('div#modalDinamica div.modal-body').html('<div id="temp"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
            },
            success: function(data) {

                $('div#modalDinamica div.modal-content').html(data);
                /*$('.carousel').carousel({
                    interval: false
                });
                setTimeout(function() {
                    $('input[name="identificador"]').focus();
                }, 600);*/


                loadingModalDinamica('hide', 'lg');
            }
        });
    }
</script>
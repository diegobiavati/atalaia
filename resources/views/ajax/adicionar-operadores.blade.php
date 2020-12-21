<div class="modal-header">
    <h5 class="modal-title">
        <i class="ion-android-create" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i>
        Adicionar operador
    </h5>
</div>

<div class="modal-body">
    <div class="alert alert-danger errors-cadastro-operador" role="alert"></div>
    <form id="adicionar_dados_operador">
        <input type="hidden" name="_token" value="{{csrf_token()}}">
        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
            <div style="float: left;">
                <i class="ion-android-person" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <input class="no-style" name="nome" type="text" autocomplete="off" placeholder="Nome completo" style="width: 100%;" />
            </div>
            <div class="clear"></div>
        </div>

        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
            <div style="float: left;">
                <i class="ion-android-contact" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <input class="no-style" name="nome_guerra" autocomplete="off" placeholder="Nome de Guerra" style="width: 100%;" />
            </div>
            <div class="clear"></div>
        </div>

        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
            <div style="float: left;">
                <i class="ion-bookmark" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 15px; padding: 0 0 10px 6px; ">
                <input class="no-style" name="idt_militar" autocomplete="off" placeholder="Identidade Militar" style="width: 100%;" />
            </div>
            <div class="clear"></div>
        </div>

        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
            <div style="float: left;">
                <i class="ion-bookmark" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 15px; padding: 0 0 10px 6px; ">
                <input class="no-style" name="idt_militar_o_exp" autocomplete="off" placeholder="Org Expd (Idt Mil)" style="width: 100%;" />
            </div>
            <div class="clear"></div>
        </div>

        <div style="margin: 24px auto; width: 80%; max-width: 380px;">
            <div style="float: left; margin-top: 3px;">
                <i class="ion-ios-pricetag" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float:right; width: 93%;">
                <select class="custom-select" name="postograd_id">{!! implode('', $options_postograd) !!}</select>
            </div>
            <div class="clear"></div>
        </div>

        <div style="margin: 18px auto; width: 80%; max-width: 380px;">
            <div style="float: left; margin-top: 3px;">
                <i class="ion-ios-home" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float:right; width: 93%;">
                <select class="custom-select" name="qms_id">{!! implode('', $options_qms) !!}</select>
            </div>
            <div class="clear"></div>
        </div>

        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
            <div style="float: left;">
                <i class="ion-ios-telephone" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <input class="no-style telefone_mask" name="tel_pronto_atendimento" type="text" pattern="[0-9]{11}" maxlength="16" autocomplete="off" placeholder="Telefone com DDD" style="width: 100%;" />
            </div>
            <div class="clear"></div>
        </div>

        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
            <div style="float: left;">
                <i class="ion-email" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <input class="no-style" name="email" type="text" maxlength="60" autocomplete="off" placeholder="Email" style="width: 100%;" />
            </div>
            <div class="clear"></div>
        </div>

        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
            <div style="float: left;">
                <i class="ion-locked" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <div style="margin: 2px 0 16px 0;">Tipo de conta do operador</div>
                {!! implode('<br />', $tipos) !!}
            </div>
            <div class="clear"></div>
        </div>
    </form>
</div>

<div class="modal-footer" style="text-align: right;">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
    <button type="button" class="btn btn-primary" onclick="AdicionarOperador();">Salvar</button>
</div>

<script>
    $(document).ready(function() {
        $('.telefone_mask').mask('(00)00000-0000');
    });

    // AJAX - ADICIONAR OPERADOR 
    function AdicionarOperador() {
        var dataForm = $('form#adicionar_dados_operador').serialize();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: dataForm,
            url: '/ajax/adicionar-operador',
            beforeSend: function() {
                $('div.errors-cadastro-operador ul').remove().parent().hide();
            },
            success: function(data) {
                if (data.status == 'ok') {
                    $('div#modalDinamica').modal('hide');
                    $('a#gerenciar-operadores').trigger('click');
                    $('div.errors-cadastro-operador ul').remove().parent().hide();
                }
            },
            error: function(jqxhr) {
                if (jqxhr.status == 500) {
                    alert('Erro interno! Verifique se o email informado já se encontra cadastrado.');
                } else if (jqxhr.status == 422) {
                    $('div.errors-cadastro-operador').slideDown(100);
                    var errors = $.parseJSON(jqxhr.responseText);
                    $('div.errors-cadastro-operador').prepend('<ul style="margin: 0 6px;"></ul>');
                    $.each(errors.errors, function(index, value) {
                        $('div.errors-cadastro-operador ul').append('<li>' + value + '</li>');
                    });
                }
            }
        });
    }
</script>
<div class="modal-header">
    <h5 class="modal-title">
        <i class="ion-android-create" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i>
        Editar Operador
    </h5>
</div>

<div class="modal-body">
    <div class="alert alert-danger errors-cadastro-operador erro-upload" role="alert"></div>
    <form id="img_perfil" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{csrf_token()}}">
        <input type="file" name="imagem" onchange="submitForm('img_perfil', '/ajax/upload/img-perfil/operador/{{$operador->id}}');" style="display: none;" />
    </form>
    
    <form id="atualiza_dados_operador">
        <input type="hidden" name="_token" value="{{csrf_token()}}">

        <div style="display: grid; grid-template-columns: 0.8fr 2.2fr; margin: 0 auto; width: 80%; max-width: 380px; border-bottom: 1px solid #ccc; padding: 0 0 20px 6px;">
            <div>
                <div class="imagem_perfil" style="background: url('{{$imagens->imagem}}') no-repeat center center; background-size: cover;">
                    <div>
                        <a class="no-style open_input_file" href="javascript: void(0);">
                            <span style="color: #696969;"><i class="ion-ios-camera"></i></span>
                        </a>
                    </div>
                </div>
            </div>
            <div padding: 6px;">
                <p><input class="line-bottom" type="text" name="nome" value="{{$operador->nome}}" autocomplete="off" style="width: 100%;" placeholder="Nome completo" /></p>
                <p><input class="line-bottom" type="text" name="nome_guerra" value="{{$operador->nome_guerra}}" autocomplete="off" style="width: 100%;" placeholder="Nome de guerra" /></p>
            </div>
        </div>

        <div id="box-dialog-remover-imagem-perfil" style="font-size: 12px; left: 60%; margin-top: -24px; position: absolute; {{$style_display}}">
            <a href="javascript: void(0);" class="no-style" onclick="removerImgPerfil({{$user->id}});">
                <i class="ion-android-delete" style="font-size: 16px;"></i>
                Remover imagem
            </a>
        </div>
        
        <div style="margin: 24px auto; width: 80%; max-width: 380px;">
            <div class="progress" style="margin-top: 36px; display: none;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
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
                <i class="ion-power" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="ativo" class="custom-control-input" id="ativoCheck" {{ (($operador->ativo == 'S') ? 'checked' : '' ) }}>
                    <label class="custom-control-label" for="ativoCheck">Ativo</label><br />
                    <span style="font-size: 12px; color: #FE2E2E;">Desmarcando essa opção o operador não será mais listado</span>
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
            <div style="float: left;">
                <i class="ion-key" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="resetar_senha" class="custom-control-input" id="customCheck1">
                    <label class="custom-control-label" for="customCheck1">Resetar senha</label><br />
                    <span style="font-size: 12px; color: #FE642E;">Marcando a opção acima, a senha será alterada para o prefixo do email até o arroba</span>
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
            <div style="float: left;">
                <i class="ion-bookmark" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <input class="no-style" name="idt_militar" value="{{$operador->idt_militar}}" autocomplete="off" placeholder="Identidade Militar" style="width: 100%;" />
            </div>
            <div class="clear"></div>
        </div>

        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
            <div style="float: left;">
                <i class="ion-bookmark" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <input class="no-style" name="idt_militar_o_exp" value="{{$operador->idt_militar_o_exp}}" autocomplete="off" placeholder="Org Expd (Idt Mil)" style="width: 100%;" />
            </div>
            <div class="clear"></div>
        </div>

        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
            <div style="float: left;">
                <i class="ion-ios-telephone" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <input class="no-style telefone_mask" name="tel_pronto_atendimento" type="text" pattern="[0-9]{11}" value="{{$operador->tel_pronto_atendimento}}" maxlength="14" autocomplete="off" placeholder="Telefone com DDD" />
            </div>
            <div class="clear"></div>
        </div>

        <div style="margin: 14px auto; width: 80%; max-width: 380px;">
            <div style="float: left;">
                <i class="ion-email" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <input class="no-style" name="email" type="text" value="{{$operador->email}}" maxlength="60" autocomplete="off" placeholder="Email" style="width: 100%;" />
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
    <button type="button" class="btn btn-primary" onclick="AtualizarOperador({{ $operador->id }});">Salvar</button>
</div>

<script>
    $(document).ready(function() {
        $('.telefone_mask').mask('(00)00000-0000');
    });

    // AJAX - EDITAR DADOS OPERADOR 
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
</script>
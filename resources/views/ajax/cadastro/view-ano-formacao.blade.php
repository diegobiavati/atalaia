<?php
$backgroundColor = session()->get('backgroundColor');
?>

<div class="modal-header" style="background-color: {{ $backgroundColor }}">
    <h5 class="modal-title">
        <i class="ion-android-create" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i>
        Editar ano de formação
    </h5>
</div>
<div class="modal-body">
    <div class="alert alert-danger info-ano-formacao" role="alert"></div>

    <form id="atualizar_ano_formacao">
        <input type="hidden" name="_token" value="{{csrf_token()}}">                                     
        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
            <p>Informe a nova data de matrícula</p>
            <div style="float: left;">
                <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <input class="no-style" style="width: 100%;" name="data_matricula" type="text" value="{{ strftime('%d/%m/%Y', strtotime($ano_formacao->data_matricula)) }}" maxlength="10" autocomplete="off" placeholder="Data de matrícula (DD/MM/AAAA)." />
            </div>
            <div class="clear"></div>
            @if(session()->get('login.qmsID'))
                <div style="margin: 14px auto; width: 80%; max-width: 380px;">
                    <div class="custom-control custom-checkbox" style="margin-top: 20px;">
                        <input id="customCheck1" name="per_ativo_qualificacao" type="checkbox" value="0" class="custom-control-input" {{$checked}} />
                        <label class="custom-control-label" for="customCheck1">Período Ativo (Qualificação)</label>
                    </div>
                </div>
            @endif
            <div class="clear"></div>                                            
        </div>                                        
    </form>

</div>
<div class="modal-footer" style="text-align: right;">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        Cancelar
    </button>
    <button type="button" class="btn btn-primary" onclick="AtualizarAnoFormacao({{$ano_formacao->id}});">
        Salvar
    </button>
</div>
<script>

    $('div.info-ano-formacao').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');

    $('input[name="data_matricula"]').mask('00/00/0000');
</script>
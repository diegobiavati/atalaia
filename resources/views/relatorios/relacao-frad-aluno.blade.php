<?php
    $url = ( (session()->get('login.omctID')) ? '/ajax/consulta-frad-aluno' : '/gaviao/ajax/consulta-frad-aluno');
?>
<form id="fradAluno">
    <input type="hidden" name="ano_formacao_id" value="{{$anoFormacao->id or old('ano_formacao_id')}}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    @include('ajax.componenteSelectUeteCurso')

    <div style="width: 90%; margin: 22px auto; text-align: left; border-bottom: 1px solid #ccc;">
        <h4 style="text-align: center; margin-bottom: 12px;">Consulta de FRAD(s)</h4>
        <div class="custom-control" style="display: inline-block; margin: 0 10px 12px 0;">
            <label class="labelDescricao" for="numero_aluno">Número do Aluno</label>
            <input class="no-style" style="width: 30%;margin-top:10px;margin-left: 10px;border-bottom: 1px solid #ccc;" type="text" pattern="[0-9]+$" id="numero_aluno" name="numero_aluno" type="text" autocomplete="off" placeholder="" />
        </div>
        <div class="clear"></div>
        <div class="custom-control" style="display: inline-block; margin: 0 10px 32px 0;width: 100%;">
            <label class="labelDescricao" for="nome_aluno">Nome do Aluno</label>
            <input class="no-style" style="width: 50%;margin-top:10px;margin-left: 10px;border-bottom: 1px solid #ccc;" id="nome_aluno" name="nome_aluno" type="text" autocomplete="off" placeholder="" />
        </div>
    </div>

    <div style="margin-top: 24px;">
        <button id="btn-frad-aluno" type="button" class="btn btn-primary" style="display: none;">Consultar Aluno(s)</button>
    </div>
</form>
<div id="parametros-content" style="margin: 46px auto; text-align: center;"></div>

<script>
    $(document).ready(function() {

        $('form#fradAluno select.required_to_show_button').change(function() {
            $('form#fradAluno select.required_to_show_button').each(function(index, element) {

                if ($(element).val() == 99) {
                    $('button#btn-frad-aluno').slideUp(100);
                    return false;
                }
                $('button#btn-frad-aluno').slideDown(100);
            });
        });

        $('button#btn-frad-aluno').click(function() {
            var dados = $('#fradAluno').serialize();

            $.ajax({
                type: 'GET',
                url: '{{ $url }}',
                data: dados,
                beforeSend: function() {
                    $('div#parametros-content').empty();
                    $('div#parametros-content').html('<div id=\"temp\"><img src=\"/images/loadings/loading_01.svg\" style=\"width: 24px; margin-right: 8px;\" /> Aguarde, carregando...</div>');
                },
                success: function(data) {
                    $('div#temp').fadeOut(300, function() {
                        $(this).remove();
                        $('div#parametros-content').empty();
                        $('div#parametros-content').html(data);
                    });
                },
                error: function(jqxhr) {
                    setTimeout(function() {
                        $('div#parametros-content').empty();
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }
            });
        });
    });
</script>
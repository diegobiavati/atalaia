<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/bootstrap-datepicker.pt-BR.min.js"></script>
<link href="/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />

<form id="relatorio-audiencia-fo">
    <input type="hidden" name="ano_formacao_id" value="{{$anoFormacao->id or old('ano_formacao_id')}}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    @include('ajax.componenteSelectUeteCurso')

    <div style="width: 90%; margin: 22px auto; text-align: left; border-bottom: 1px solid #ccc;">
        <h4 style="text-align: center; margin-bottom: 12px;">Consulta de Audiência de Fato Observado</h4>

        <div style="float: right; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; text-align: center;">
            <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
            <input class="no-style data" style="margin-top:10px;margin-left: 10px;border-bottom: 1px solid #ccc;" name="data_inicial" type="text" maxlength="10" autocomplete="off" placeholder="Data Inicial" />
            <label class="labelDescricao"> á</label>
            <input class="no-style data" style="margin-top:10px;margin-left: 10px;border-bottom: 1px solid #ccc;" name="data_final" type="text" maxlength="10" autocomplete="off" placeholder="Data Final" />
        </div>
        
        <div class="clear"></div>
    </div>

    <div style="margin-top: 24px;">
        <button id="btn-consulta-relatorio" type="button" class="btn btn-primary" style="display: none;">Relatório para Audiência(s)</button>
    </div>
</form>
<div id="parametros-content" style="margin: 46px auto; text-align: center;"></div>

<script>

    $('.data').mask('00/00/0000');

    $(document).ready(function() {

        $('[name="data_inicial"]')
        .datepicker({
            autoclose: true, // It is false, by default
            format: 'dd/mm/yyyy'
        });

        $('[name="data_final"]')
        .datepicker({
            autoclose: true, // It is false, by default
            format: 'dd/mm/yyyy'
        });

        $(document).on('change', 'select.required_to_show_button', function() {
            $('select.required_to_show_button').each(function(index, element) {

                if ($(element).val() == 99) {
                    $('button#btn-consulta-relatorio').slideUp(100);
                    return false;
                }
                $('button#btn-consulta-relatorio').slideDown(100);
            });
        });

        $('button#btn-consulta-relatorio').click(function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

            var dados = $('#relatorio-audiencia-fo').serialize();

            $.ajax({
                type: 'POST',
                url: '{{$rota}}',
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
        });
    });
</script>
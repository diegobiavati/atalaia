<form id="relatorio-audiencia-fo">
    <input type="hidden" name="ano_formacao_id" value="{{$anoFormacao->id or old('ano_formacao_id')}}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    @include('ajax.componenteSelectUeteCurso')

    <div id="cia-content" style="display: none;margin-top: 24px;">
        @include('ajax.componenteSelectCiaInfantaria')
    </div>
    

    <div style="width: 90%; margin: 22px auto; text-align: left;">
        <h4 style="text-align: center; margin-bottom: 12px;">Consulta de Audiência de Fato Observado</h4>

        @include('ajax.componenteDataInicialDataFinal')
        
        <div class="clear"></div>
    </div>

    <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
        <h4 style="text-align: center; margin-bottom: 12px;">Tipos de FO</h4>
        <div class="custom-control custom-radio" style="display: inline-block; margin: 0 10px 12px 0;">
            <input type="radio" class="custom-control-input" id="todos" name="relacao" value="todos" checked />
            <label class="custom-control-label" for="todos">Todos</label>
        </div>

        <div class="custom-control custom-radio" style="display: inline-block; margin: 0 10px 12px 0;">
            <input type="radio" class="custom-control-input" id="negativos" name="relacao" value="0"/>
            <label class="custom-control-label" for="negativos">Negativos</label>
        </div>

        <div class="custom-control custom-radio" style="display: inline-block; margin: 0 10px 12px 0;">
            <input type="radio" class="custom-control-input" id="neutros" name="relacao" value="1"/>
            <label class="custom-control-label" for="neutros">Neutros</label>
        </div>

        <div class="custom-control custom-radio" style="display: inline-block; margin: 0 10px 12px 0;">
            <input type="radio" class="custom-control-input" id="positivos" name="relacao" value="2"/>
            <label class="custom-control-label" for="positivos">Positivos</label>
        </div>
    </div>

    <div style="margin-top: 24px;">
        <button id="btn-consulta-relatorio" type="button" class="btn btn-primary" style="display: none;">Relatório para Audiência(s)</button>
    </div>
</form>
<div id="parametros-content" style="margin: 46px auto; text-align: center;"></div>

<script>

    $(document).ready(function() {

        $(document).on('change', 'select.required_to_show_button', function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

            $('div#cia-content').slideUp(100);
            if($(this).children("option:selected").html() == 'Infantaria'){
                $('div#cia-content').slideDown(100);
            }

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
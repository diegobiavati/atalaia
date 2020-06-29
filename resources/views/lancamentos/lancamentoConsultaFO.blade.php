<form id="consultaFatoObservado">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="rotaConsulta" value="{{$rotaConsulta}}" />

    <div style="width: 50%; margin: 22px auto; text-align: left; border-bottom: 1px solid #ccc;">
        <h4 style="text-align: center; margin-bottom: 12px;">Consulta de Fatos Observados</h4>

        {!! App\Http\Controllers\Utilitarios\FuncoesController::retornaBotaoAnoFormacao() !!}

        <select name="omctID" class="custom-select required_to_show_button" style="margin-top: 32px;">
            <option value="0" disabled selected hidden>Selecione uma UETE</option>
            @if($ownauthcontroller->PermissaoCheck(1))
            <option value="todas_omct">TODAS AS UETE</option>
            @endif
            @foreach ($uetes as $uete)
            <option value={{$uete->id}}>{{ $uete->omct }}</option>
            @endforeach
        </select>

        <select name="opcaoRel" class="custom-select" style="margin-top: 32px;">
            <option value="0" disabled selected hidden>Selecione uma Opção</option>
            <option value="1">Listar Todos</option>
            <option value="2">Listar Resolvidos</option>
            <option value="3">Listar Resolvidos com FATD</option>
            <option value="4">Listar Não Resolvidos</option>
        </select>

        <div class="custom-control" style="display: inline-block; margin: 20px 10px 12px 0;">
            <label class="labelDescricao" for="numero_aluno">Número do Aluno</label>
            <input class="no-style" style="width: 30%;margin-top:10px;margin-left: 10px;border-bottom: 1px solid #ccc;" type="text" pattern="[0-9]+$" id="numero_aluno" name="numero_aluno" type="text" autocomplete="off" placeholder="" />
        </div>
        <div class="clear"></div>
        <div class="custom-control" style="display: inline-block; margin: 0 10px 32px 13px;width: 100%;">
            <label class="labelDescricao" for="nome_aluno">Nome do Aluno</label>
            <input class="no-style" style="width: 50%;margin-top:10px;margin-left: 10px;border-bottom: 1px solid #ccc;" id="nome_aluno" name="nome_aluno" type="text" autocomplete="off" placeholder="" />
        </div>
    </div>

    <div style="margin-top:24px;">
        <button id="btnConsultaFO" type="button" class="btn btn-primary" >Consultar Fato Observado</button>
    </div>
    <div id="container-temp" style="margin-top:24px;"></div>
    <script>
        $(document).ready(function() {

            $(document).on('change', 'input[name="ano_formacao"]', function(evt) {
                evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

                $('div#container-temp').empty();
            });

            $('#btnConsultaFO.btn.btn-primary').click(function(evt) {
                evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

                var formData = $('form#consultaFatoObservado').serialize();
                //formData = formData + '&ano_formacao=' + $('.btn.btn-secondary.active input[name="ano_formacao"]').val();
                
                $.ajax({
                    url: $('input[name="rotaConsulta"]').val(),
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('div#container-temp').empty();
                        $('div#container-temp').html('<div id="temp"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
                    },
                    success: function(data) {

                        $('div#container-temp').empty();
                        $('div#container-temp').html(data);

                    },
                    error: function(jqxhr) {
                        $('div.errors-lancamento-fo').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
                    }
                });
            });

            
        });
    </script>
</form>
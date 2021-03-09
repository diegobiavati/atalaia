<form id="consultaFATD">

    <input type="hidden" name="_token" value="{{csrf_token()}}" />

    {!! App\Http\Controllers\Utilitarios\FuncoesController::retornaBotaoAnoFormacao((isset($ano_formacao) ? $ano_formacao : null)) !!}

    <div id="container">

        <script>

            function carregaContainerFATD(url) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    beforeSend: function() {
                        $('form#consultaFATD div#container').empty();
                    },
                    success: function(data) {
                        $('form#consultaFATD div#container').html(data);
                    },
                    error: function(jqxhr) {
                        $('form#consultaFATD div#container').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
                    }
                });
            }

            $(document).ready(function() {
                $('form#consultaFATD .btn.btn-secondary input[name="ano_formacao"]').change(function(evt) {
                    evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

                    url = '/ajax/lancamentos/viewConsultarFATD_' + evt.target.value;
		//  $('form#consultaFATD').get(0).reset();
                    $('select.custom-select[name="turmaID"]').empty();

                    //Carrega a combo com Uetes ou Cursos
                    carregaContainerFATD(url);
                });
                //carrega o container com data selecionada padrão
                carregaContainerFATD('/ajax/lancamentos/viewConsultarFATD_' + $('form#consultaFATD .btn.btn-secondary.active input[name="ano_formacao"]').val());
            });

        </script>
    </div>

    
</form>


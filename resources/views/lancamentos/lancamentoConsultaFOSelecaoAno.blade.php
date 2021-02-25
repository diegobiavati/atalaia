<form id="consultaFatoObservado">

    <input type="hidden" name="_token" value="{{csrf_token()}}" />

    {!! App\Http\Controllers\Utilitarios\FuncoesController::retornaBotaoAnoFormacao((isset($ano_formacao) ? $ano_formacao : null)) !!}

    <div id="container">

        <script>
            function carregaContainer(url) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    beforeSend: function() {
                        $('form#consultaFatoObservado div#container').empty();
                    },
                    success: function(data) {
                        $('form#consultaFatoObservado div#container').html(data);
                    },
                    error: function(jqxhr) {
                        $('form#consultaFatoObservado div#container').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
                    }
                });
            }

            $(document).ready(function() {
                $(document).on('change', 'form#consultaFatoObservado .btn.btn-secondary input[name="ano_formacao"]', function(evt) {
                    evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

                    url = '/ajax/lancamentos/viewConsultarFO_' + evt.target.value;

                    $('form#consultaFatoObservado').get(0).reset();
                    $('select.custom-select[name="turmaID"]').empty();

                    //Carrega a combo com Uetes ou Cursos
                    carregaContainer(url);
                });
                //carrega o container com data selecionada padrão
                carregaContainer('/ajax/lancamentos/viewConsultarFO_' + $('form#consultaFatoObservado .btn.btn-secondary.active input[name="ano_formacao"]').val());
            });

        </script>
    </div>

    
</form>


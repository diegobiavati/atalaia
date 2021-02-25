

<form id="lancamentoFatoObservado">

    <input type="hidden" name="_token" value="{{csrf_token()}}" />

    {!! App\Http\Controllers\Utilitarios\FuncoesController::retornaBotaoAnoFormacao((isset($ano_formacao) ? $ano_formacao : null)) !!}

    <div id="container">

        <script>
            
            function carregaContainer(url) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    beforeSend: function() {
                        $('div#container').empty();
                    },
                    success: function(data) {
                        $('div#container').html(data);
                    },
                    error: function(jqxhr) {
                        $('div#container').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
                    }
                });
            }

            

            $(document).ready(function() {
                $('form#lancamentoFatoObservado .btn.btn-secondary input[name="ano_formacao"]').change(function(evt) {
                //$(document).on('change', 'form#lancamentoFatoObservado .btn.btn-secondary input[name="ano_formacao"]', function(evt) {
                    evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

                    url = '/ajax/lancamentos/lancarFO_' + evt.target.value;

                    $('form#lancamentoFatoObservado').get(0).reset();
                    $('div#container-turma').empty();
                    $('div#datepicker').datepicker('clearDates');
                    $('select.custom-select[name="turmaID"]').empty();

                    //Carrega a combo com Uetes ou Cursos
                    carregaContainer(url);
                });
                
                //carrega o container com data selecionada padrão
                carregaContainer('/ajax/lancamentos/lancarFO_' + $('form#lancamentoFatoObservado .btn.btn-secondary.active input[name="ano_formacao"]').val());
            });

        </script>
    </div>

    
</form>


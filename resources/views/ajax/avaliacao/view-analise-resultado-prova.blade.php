<div style="padding: 0px 180px; margin-bottom: 10px;">
    @include('ajax.componenteSelectUeteCurso')
</div>

<div id="parametros-content" style="padding: 0px 180px; margin-bottom: 10px;"></div>

<div style="margin: 32px auto; text-align: center; ">
    <button id="submit-analise-resultado-prova" type="button" class="btn btn-primary" style="display: none;">Visualizar Análise de Resultado de Prova</button>
</div>

<script>
    $(document).ready(function() {

        $(document).on('change', 'select[name=qmsID].required_to_show_button', function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

            //Filtrar Disciplina;
            $.ajax({
                url: '{{$urlGetDisciplinas}}' + evt.target.value,
                method: 'GET',
                beforeSend: function(){
                    $('div#parametros-content').empty();
                }
            }).done(function(data) {
                $('div#parametros-content').html(data +'<div class="provas"></div>');

                //Filtrar Avaliação;
                $('select[name=disciplinaID].selectpicker').change(function(evt) {
                    evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

                    $.ajax({
                        url: '{{$urlGetAvaliacoes}}' + evt.target.value,
                        method: 'GET',
                        beforeSend: function(){
                            $('div#parametros-content .provas').empty();
                        }
                    }).done(function(data) {
                        $('div#parametros-content .provas').html('<div style="margin-top: 10px;">' + data + '</div>');

                        $('select[name=provasID].selectpicker').change(function(evt) {
                            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

                            $.ajax({
                                url: '{{$urlValidaVisualizacao}}',
                                method: 'POST',
                                dataType: "json",
                                data: {'_token' : '{{ csrf_token() }}', 'avaliacaoID': evt.target.value},
                                beforeSend: function(){
                                    $('button#submit-analise-resultado-prova').slideUp();
                                }
                            }).done(function(data){
                                if(data.success){
                                    if(data.warning){
                                        $('div#relatorios-ssaa').fadeOut();

                                        $(document).confirmAcao('<strong>' + data.message + '</strong>.<p><i>Deseja visualizar com a pendência </i>?</p>', function(s) {
                                            $('button#submit-analise-resultado-prova').slideDown(100);
                                        });
                                    }else{
                                        $('button#submit-analise-resultado-prova').slideDown(100);
                                    }
                                }
                            }).fail(function(data){

                            });
                            
                        });

                    }).fail(function(data) {
                        $('div.parametros-content').slideDown().addClass('alert-danger').html('Erro Interno!!' + data);
                    });
                });

            }).fail(function(data) {
                $('div.parametros-content').slideDown().addClass('alert-danger').html('Erro Interno!!' + data);
            });
            
            
        });

        $('button#submit-analise-resultado-prova').click(function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
            $('button#submit-analise-resultado-prova').slideUp();

            window.open('{{$urlVisualizacao}}/' + $('#relatorios-content #parametros-content select[name=provasID]').val());
        });
    });
</script>
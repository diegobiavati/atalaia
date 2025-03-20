<div style="padding: 0px 180px; margin-bottom: 10px;">
    @include('ajax.componenteSelectUeteCurso')
</div>

<div id="parametros-content" style="padding: 0px 180px; margin-bottom: 10px;"></div>

<div style="margin: 32px auto; text-align: center; ">
    <button id="submit-avaliacao-recuperacao" type="button" class="btn btn-primary" style="display: none;">Visualizar Alunos em Recuperação</button>
</div>

<script>
    $(document).ready(function() {

        $('select[name=qmsID].required_to_show_button').change(function(evt){
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

                    $('button#submit-avaliacao-recuperacao').slideDown(100);
                });

            }).fail(function(data) {
                $('div.parametros-content').slideDown().addClass('alert-danger').html('Erro Interno!!' + data);
            });
            
            
        });

        $('button#submit-avaliacao-recuperacao').click(function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
            $('button#submit-avaliacao-recuperacao').slideUp();

            window.open('{{$urlVisualizacao}}/' + $('#relatorios-content #parametros-content select[name=disciplinaID]').val());
        });
    });
</script>
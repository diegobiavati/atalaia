<div id="disciplinas_ssaa">
    {!! App\Http\Controllers\Utilitarios\FuncoesController::retornaBotaoAnoFormacao((isset($ano_formacao) ? $ano_formacao : null)) !!}
    <div class="container_disciplinas"></div> <!-- /container -->
</div>
<script>
    
    function carregaContainerDisciplinas(url) {
        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() {
                $('div.container_disciplinas').empty();
            },
            success: function(data) {
                $('div.container_disciplinas').html(data);
            },
            error: function(jqxhr) {
                $('div.container_disciplinas').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
            }
        });
    }

    $(document).ready(function() {
        $('div#disciplinas_ssaa .btn.btn-secondary input[name="ano_formacao"]').change(function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

            url = '/gaviao/ajax/gerenciar-disciplinas/index/' + evt.target.value;
            
            $('div.container_disciplinas').empty();

            //Carrega a combo com Uetes ou Cursos
            carregaContainerDisciplinas(url);
        });

        //carrega o container com data selecionada padrão
        carregaContainerDisciplinas('/gaviao/ajax/gerenciar-disciplinas/index/' + $('div#disciplinas_ssaa .btn.btn-secondary.active input[name="ano_formacao"]').val());
    });
</script>
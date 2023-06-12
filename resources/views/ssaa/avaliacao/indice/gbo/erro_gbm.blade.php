<style>
    div.alert.alertas-lancamento-gbo {
        font-weight: bold;
    }
</style>
<div id="lancamento-gbo">
    <div class="container-lancamento-gbo">
        <div class="alert alertas-lancamento-gbo" role="alert"></div>

        <div class="container-side">
            <button type="button" class="btn btn-secondary" id="btn-lancamento-cancelamento" data-dismiss="modal">Fechar</button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('div.alertas-lancamento-gbo').slideDown().addClass('alert-danger').html('{{ $mensagem }}');

        //Remover Modal com FadeIn
        $(document).on('click', '[data-dismiss="modal"]', function(evt){
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
            $('div.container_indice_dificuldades').fadeIn(1000);
        });
    });
</script>
@include('ajax.componenteSelectUeteCurso')

<div style="margin: 32px auto; text-align: center; ">
    <button id="submit-demonstrativo" type="button" class="btn btn-primary" style="display: none;" onclick="visualizarDemonstrativo('{{$urlVisualizacao}}', 'Visualização do Demonstrativo');">Visualizar Demonstrativo</button>
</div>

<script>
    $(document).ready(function() {
        $(document).on('change', 'select.required_to_show_button', function() {
            $('button#submit-demonstrativo').slideDown(100);
        });
    });

    function visualizarDemonstrativo(url, titulo){
        url += $('select[name="qmsID"]').val() ;
        openWindow(url, titulo);
    }
</script>
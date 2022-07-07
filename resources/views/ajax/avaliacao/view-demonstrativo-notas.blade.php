@include('ajax.componenteSelectUeteCurso')

<div style="margin: 32px auto; text-align: center; ">
    <button id="submit-demonstrativo" type="button" class="btn btn-primary" style="display: none;">Visualizar Demonstrativo</button>
</div>

<div id="parametros-content"></div>

<script>
    $(document).ready(function() {
        $(document).on('change', 'select.required_to_show_button', function() {
            $('button#submit-demonstrativo').slideDown(100);
        });

        $('button#submit-demonstrativo').click(function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

            var dados = {"_token": "{{ csrf_token() }}"};
            var url = '{{$urlVisualizacao}}' + $('select[name="qmsID"]').val();
            
            downloadPDF(url, dados);
        });
    });

</script>
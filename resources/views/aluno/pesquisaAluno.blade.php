<div class="box-pesquisar-in-card-title" style="display: inline-block; padding:0; height: 36px;">
    <input id="input_busca_rapida_aluno" class="pesquisar-in-card-title" type="text" placeholder="Busca rápida"
        style="width:300px;">
    <a class="no-style" href="javascript: void(0);">
        <i class="ion-android-search" style="color: #696969;"></i>
    </a>
</div>
<script>
$(document).ready(function() {
    $('#input_busca_rapida_aluno').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{ route('ajax.alunos.busca-ajax') }}",
                data: {
                    termo: request.term
                },
                success: function(data) {
                    response(data.map(function(aluno) {
                        return {
                            label: aluno.numero + " - " + aluno
                                .nome_completo,
                            value: aluno.nome_completo,
                            id: aluno.id
                        };
                    }));
                }
            });
        },
        minLength: 0,
        select: function(event, ui) {
            loadAdminAjaxContent('admin/aluno/' + ui.item.id);
        }
    }).focus(function() {
        $(this).autocomplete("search", ""); // abre igual datalist ao focar
    });
});
</script>
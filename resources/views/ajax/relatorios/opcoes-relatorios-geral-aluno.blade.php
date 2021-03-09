
{!! $botaoAnoFormacao !!}

@switch($requisicao)
  @case('alunos'):
    
    <div style="margin: 46px auto; text-align: center; width: 780px;">
        <ul class="list-group list-group-flush" style="margin-top: 44px;">
            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent('relacao-geral-alunos');">Relação geral de alunos</a></li>
            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent('ficha-individual-aluno');">Ficha Individual do Aluno(a)s</a></li>
            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent('view-relacao-punidos');">Relação de Aluno(a)s Punidos</a></li>
            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent('view-frad-aluno');">Ficha Registro para Acompanhamento do Discente <b>(FRAD)</b></a></li>
            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent('view-rod-aluno');">Relatório de Observação do Discente <b>(ROD)</b></a></li>
            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent('view-ficha-disciplinar');">Ficha Disciplinar do Aluno(a)s <b>(FATD)</b></a></li>
        </ul>
        <div id="relatorios-content" style="margin-top: 24px;"></div>
    </div>

  @break
@endswitch

<script>

$(document).on('change', '.btn.btn-secondary input[name="ano_formacao"]', function(evt) {
    evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
    evt.preventDefault();
    
    $('div#relatorios-content').empty();
});

</script>
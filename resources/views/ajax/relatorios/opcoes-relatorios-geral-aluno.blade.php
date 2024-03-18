
{!! $botaoAnoFormacao !!}

<div id="relatorios-ssaa" style="margin: 46px auto; text-align: center; width: 780px;">
@switch($requisicao)
  @case('alunos'):
        <ul class="list-group list-group-flush" style="margin-top: 44px;">
            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent('relacao-geral-alunos');">Relação Geral de Aluno(a)s</a></li>
            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="abrirRelacaoAlunoSemFoto('ajax/relacao-alunos-sem-foto', 'Relação Aluno(a)s Sem Foto');">Relação Aluno(a)s Sem Foto</a></li>
            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent('ficha-individual-aluno');">Ficha Individual do Aluno(a)s</a></li>
            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent('view-relacao-punidos');">Relação de Aluno(a)s Punidos</a></li>
            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent('view-frad-aluno');">Ficha Registro para Acompanhamento do Discente <b>(FRAD)</b></a></li>
            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent('view-rod-aluno');">Relatório de Observação do Discente <b>(ROD)</b></a></li>
            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent('view-ficha-disciplinar');">Ficha Disciplinar Individual do Aluno(a)s <b>(FDI)</b></a></li>
            <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent('view-audiencia-fo');">Audiência de Fato Observado <b>(Audiência de FO)</b></a></li>
        </ul>
        

  @break

  @case('avaliacoes'):
        <ul class="list-group list-group-flush" style="margin-top: 44px;">
          <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent('demonstrativo-notas');">Demonstrativo de notas</a></li>
          <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent('analise-resultado-prova');">Análise de Resultado de Prova</a></li>
          <li class="list-group-item opcoes-relatorios"><a href="javascript: void(0);" class="no-style" onclick="loadRelatorioAjaxContent('avaliacoes-por-disciplina');">Avaliações por Disciplina</a></li>
        </ul>
  @break
    
@endswitch
  <div id="relatorios-content" style="margin-top: 24px;"></div>
</div>

<!-- MODALS confirmAcao DINAMICA-->

<div class="modal fade bd-example-modal-sm" id="modalConfirmAcao" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; padding: 10px;min-height: 23vh;min-width: 500px;">
            <div style="border-bottom: 1px solid #ccc;"><i class="ion-android-hand" style="font-size: 22px; vertical-align: middle;"></i><span style="margin-left: 10px;"><b>ATENÇÃO!</b></span></div>
            <div class="content-confirmAcao" style="margin: 6px 0;"></div>
            <div style="text-align: right;">
                <div style="display: inline-block;">
                    <a id="confirm-buttom" class="no-style" href="javascript: void(0);"><span style="color: #363636;"><strong>SIM</strong></span></a>
                </div>
                <div style="display: inline-block; margin-left: 8px;">
                    <a id="cancel-buttom" class="no-style" href="javascript: void(0);"><span style="color: #2E64FE;"><strong>NÃO</strong></span></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FINAL MODAL confirmAcao DINAMICA-->
<script>

(function($) {
    $.fn.confirmAcao = function(content, callback) {
        return this.each(function() {
            $('div.content-confirmAcao').html(content);
            $('#modalConfirmAcao').modal('show');
            $('#confirm-buttom').click(function() {
                callback();
                $('#modalConfirmAcao').modal('hide');
                $('div#relatorios-ssaa').fadeIn(1000);
                // DESASSOCIA EVENTOS DE CLIQUE ANTERIORES
                $(this).off();
            });
            $('#cancel-buttom').click(function() {
                $('#modalConfirmAcao').modal('hide');
                $('div#relatorios-ssaa').fadeIn(1000);
                // DESASSOCIA EVENTOS DE CLIQUE ANTERIORES
                $(this).off();
            })
        });
    };
})(jQuery);

$(document).on('change', '.btn.btn-secondary input[name="ano_formacao"]', function(evt) {
    evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
    evt.preventDefault();
    
    $('div#relatorios-content').empty();
});

function abrirRelacaoAlunoSemFoto(url, titulo){
    url += '/'+$('.btn.btn-secondary.active input[name="ano_formacao"]').val();
    openWindow(url, titulo);
}

</script>
<style>
    .center {
        margin: auto;
        width: 30%;
        padding: 20px;
    }
</style>

<div class="center">
    <div class="fomr-group">
        <label style="padding: 5px;width: 100%;background-color:rgb(121, 161, 212);margin-bottom: 0px;text-align:center;">
            <font style="color:rgb(255, 255, 255);">Curso</font>
        </label>
        @include('ajax.componenteSelectUeteCurso')
    </div>
</div>

@isset($load)
<div class="card bg-light mb-3 center" style="width:80%;">
    <div class="card-header">
        <i class="ion-ios-bookmarks"></i><strong>Gerenciador de Disciplinas da <font style="color:{{ App\Http\Controllers\Utilitarios\FuncoesController::getQmsColor($cursoSelecionado->qms_matriz_id)->backgroundColor }};font-size: large;">{{$cursoSelecionado->qms}}</font></strong>
        <div style="float: right">
            @if(count($cursos)>0)
            <a href="javascript: void(0);" data-toggle="popover" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
                <i class="ion-android-add-circle" style="color: #f00;"></i>
            </a>
            <div id="popover-content" style="display: none;">
                <div class="menu_inside_popover">
                    <i class="ion-ios-bookmarks"></i><a href="javascript: void(0);" onclick="dialogAdicionaDisciplina({{$cursoSelecionado->id}});">Adicionar disciplina no sistema</a><br />
                </div>
                <!--<div class="menu_inside_popover">
                    <i class="ion-android-arrow-down"></i><a href="javascript: void(0);" onclick="dialogImportarDisciplinas();">Importar disciplinas</a><br />                 
                </div>-->
            </div>
            @endif
        </div>
    </div>

    <div class="card-body">
        <div class="alert alertas-gerenciador-disciplinas" role="alert"></div>
        <h5 class="card-title" style="text-align: center; margin: 18px 0 44px 0;">
            @if(count($cursos)>0)
            RELAÇÃO DE DISCIPLINAS CADASTRADAS NO ANO DE FORMAÇÃO <span style="font-size: 24px;"><b>{{$cursoSelecionado->escolhaQms->anoFormacao->formacao}}</b></span>
            @endif
        </h5>
        <p class="card-text">
            @if(count($cursos)>0)

            @forelse($disciplinas as $disciplina)

        <div id="disciplina_{{$disciplina->id}}" style="width: 480px; margin: 0 auto; border-bottom: 1px solid #ccc; padding: 4px;">
            <li style="font-size: 18px; float: left;">
                <strong>{{$disciplina->nome_disciplina_abrev}}</strong><br />
                <div style="font-size: 14px; color: #696969; margin-left: 26px;">{{$disciplina->nome_disciplina}}
                    <br /><span style="color: #696969; font-style: italic; font-size: 12px;">{{ $disciplina->getTipoDisciplinas() }} {!! $disciplina->getDescricaoTFM() !!}</span>
                </div>
            </li>
            @if($ownauthcontroller->PermissaoCheck([34]))
            <div style="float: right; font-size: 18px; margin-top: 22px;">
                <a href="javascript: void(0);" class="no-style" onclick="dialogEditarDisciplina({{$disciplina->id}});" title="Editar configurações da disciplina">
                    <i class="ion-android-create"></i>
                </a>
                <a href="javascript: void(0);" onclick="dialogRemoverDisciplina({{$disciplina->id}});" class="no-style" style="margin-left: 10px;">
                    <i class="ion-android-delete"></i>
                </a>
            </div>
            @endif
            <div class="clear"></div>
        </div>

        @empty

        <div class="box-registro-not-found">
            <i class="ion-social-snapchat-outline" style="font-size: 32px"></i><br />
            <span style="color: brown">Não há disciplinas configuradas para o ano de {{$cursoSelecionado->escolhaQms->anoFormacao->formacao}}</span>
        </div>

        @endforelse

        @else
        <div class="alert alert-danger" role="alert" style="width: 80%; margin: 0 auto; text-align: center;">
            <strong>ATENÇÃO</strong><br />
            <p>Para configurar novas disciplinas adicione primeiro um <i>Ano de Formação</i>.<br />As disciplina serão cadastrardas no <i>Ano de Formação</i> mais recente.</p>
        </div>
        @endif
        </p>
    </div>

</div>

<!-- MODALS confirmAcao DINAMICA-->

<div class="modal fade bd-example-modal-sm" id="modalConfirmAcao" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius: 0; padding: 10px;min-height: 23vh;">
            <div style="border-bottom: 1px solid #ccc;"><i class="ion-android-hand" style="font-size: 22px; vertical-align: middle;"></i><span style="margin-left: 10px;"><b>ATENÇÃO!</b></span></div>
            <div class="content-confirmAcao" style="margin: 6px 0;"></div>
            <div style="text-align: right;">
                <div style="display: inline-block;">
                    <a id="confirm-buttom" class="no-style" href="javascript: void(0);"><span style="color: #363636;"><strong>SIM</strong></span></a>
                </div>
                <div style="display: inline-block; margin-left: 8px;">
                    <a class="no-style" href="javascript: void(0);" onclick="$('#modalConfirmAcao').modal('hide');"><span style="color: #2E64FE;"><strong>NÃO</strong></span></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FINAL MODAL confirmAcao DINAMICA-->

@endisset
<script>
    (function($) {
        $.fn.confirmAcao = function(content, callback) {
            return this.each(function() {
                $('div.content-confirmAcao').html(content);
                $('#modalConfirmAcao').modal('show');
                $('#confirm-buttom').click(function() {
                    callback();
                    $('#modalConfirmAcao').modal('hide');
                    // DESASSOCIA EVENTOS DE CLIQUE ANTERIORES
                    $(this).off();
                })
            });
        };
    })(jQuery);

    function dialogAdicionaDisciplina(cursoId) {

        $.ajax({
            type: 'GET',
            url: '/gaviao/ajax/gerenciar-disciplinas/form/' + cursoId,
            beforeSend: function() {
                $('div.alertas-gerenciador-disciplinas').slideUp();
                loadingModalDinamica('show', 'lg');
            },
            success: function(data) {
                $('div.container_disciplinas').fadeOut();
                $('div#modalDinamica div.modal-content').html(data);
            }
        });
    }

    function dialogRemoverDisciplina(id) {
        var style_bg_inicial = $('div#disciplina_' + id).css('background-color');
        $(document).confirmAcao('Para remover este item certifique-se que a disciplina não está vinculada com nenhuma <strong>avaliação</strong>.<p>Deseja realmente excluir esta <i>Disciplina</i>?</p>', function() {
            $.ajax({
                type: 'DELETE',
                dataType: 'json',
                url: '/gaviao/ajax/gerenciar-disciplinas/' + id,
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    $('div#disciplina_' + id).css('background-color', '#F6CECE');

                    $('div.alertas-gerenciador-disciplinas').empty().hide();
                    $('div.alertas-gerenciador-disciplinas').removeClass('alert-success').empty();
                    $('div.alertas-gerenciador-disciplinas').removeClass('alert-danger').empty();
                },
                success: function(data) {
                    if (data.status == 'success') {
                        //Ativa o gatilho da caixa de seleção para carregar o gerenciador de disciplinas...
                        $('div.container_disciplinas select.custom-select[name="qmsID"]').change();
                    } else {
                        $('div#disciplina_' + id).css('background-color', style_bg_inicial);
                        $('div.alertas-gerenciador-disciplinas').addClass('alert-danger').slideDown();
                    }
                },
                error: function(jqxhr) {
                    if (jqxhr.status == 500) {
                        $('div#disciplina_' + id).css('background-color', style_bg_inicial);
                        $('div#modalDinamica').modal('hide');
                        $('div.alertas-gerenciador-disciplinas').addClass('alert-danger').slideDown();
                        $('div.alertas-gerenciador-disciplinas').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar remover a <i>Disciplina</i> do sistema. Verifique se não há avaliações vinculadas a esta <i>Disciplina</i>').slideDown();
                    }
                }
            });
        });
    }

    function dialogEditarDisciplina(id) {
        $.ajax({
            type: 'GET',
            url: '/gaviao/ajax/gerenciar-disciplinas/' + id,
            beforeSend: function() {
                $('div.alertas-gerenciador-disciplinas').slideUp();
                loadingModalDinamica('show', 'lg');
            },
            success: function(data) {
                $('div#modalDinamica div.modal-content').html(data);
            }
        });
    }

    $(document).ready(function() {

        $('div.alertas-gerenciador-disciplinas').slideUp();

        $('div.container_disciplinas select.custom-select[name="qmsID"]').change(function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

            url = '/gaviao/ajax/gerenciar-disciplinas/load/' + $('div#disciplinas_ssaa .btn.btn-secondary.active input[name="ano_formacao"]').val() + '/' + evt.target.value;

            $('div.container_disciplinas').empty();
            //Carrega a combo com Uetes ou Cursos
            carregaContainerDisciplinas(url);
        });

        $("[data-toggle=popover]").popover({
            trigger: 'focus',
            html: true,
            delay: {
                "show": 100,
                "hide": 400
            },
            content: function() {
                return $('#popover-content').html();
            }
        });

    });

</script>
@php
$color = App\Http\Controllers\Utilitarios\FuncoesController::getQmsColor($cursoSelecionado->qms_matriz_id)->backgroundColor;

$readOnly = (count($turmasRapPendente) == 0) ? 'readOnly': '';

@endphp
<div class="fomr-group" style="width: 410px;">
    <label>Disciplina</label>
    <select name="disciplinaID" class="selectpicker custom-select required_to_show_button" {{ $readOnly }}>
        <option value="0" disabled selected hidden>Selecione uma Disciplina</option>
        @foreach ($disciplinas as $disciplina)
        <option {{ ((isset($esaAvaliacoes) && $disciplina->id == $esaAvaliacoes->id_esa_disciplinas) ? 'selected' : '') }} value={{$disciplina->id}}>{{ $disciplina->nome_disciplina }}</option>
        @endforeach
    </select>
</div>

<div class="fomr-group" style="width: 210px;">
    <label>Nome da avaliação</label>
    <select name="nome_avaliacao" class="selectpicker custom-select" {{ $readOnly }}>
        <option value="0" disabled selected hidden>Selecione uma avaliação</option>
        @foreach ($nomeAvaliacoes as $avaliacao)
        <option {{ ((isset($esaAvaliacoes) && $avaliacao->id == $esaAvaliacoes->nome_avaliacao) ? 'selected' : '') }} value={{$avaliacao->id}}>{{ $avaliacao->id.' - '.$avaliacao->descricao }}</option>
        @endforeach
    </select>
</div>
<div class="fomr-group" style="width: 183px;">
    <label>Tipo de avaliação</label>
    <select name="tipo_avaliacao" class="selectpicker custom-select" {{ $readOnly }}>
        <option value="0" disabled selected hidden>Selecione um Tipo</option>
        @foreach ($tipoAvaliacao as $tipo)
        <option {{ ((isset($esaAvaliacoes) && $tipo->id == $esaAvaliacoes->tipo_avaliacao) ? 'selected' : '') }} value={{$tipo->id}}>{{ $tipo->descricao }}</option>
        @endforeach
    </select>
</div>
<div class="fomr-group" style="width: 140px;">
    <label>Chamada</label>
    <select name="chamada" class="selectpicker custom-select" {{ $readOnly }}>
        <option value="0" disabled selected hidden>Selecione</option>
        @foreach ($chamadas as $chamada)
        <option {{ ((isset($esaAvaliacoes) && $chamada->id == $esaAvaliacoes->chamada) ? 'selected' : '') }} value={{$chamada->id}}>{{ $chamada->descricao }}</option>
        @endforeach
    </select>
</div>
<div class="fomr-group" style="width: 80px;" data-toggle="tooltip" data-placement="right" data-html="true" title="Informe o peso que está avaliação utilizará para o cálculo.">
    <label>Peso</label>
    <input type="number" value="{{ $esaAvaliacoes->peso or 0 }}" class="form-control" min="0" max="2" step="0.5" name="peso" {{ $readOnly }}>
</div>
<div class="fomr-group" style="width: 150px;">
    <label>Proposta</label>
    <input type="date" class="form-control" value="{{ $esaAvaliacoes->proposta or old('proposta') }}" name="proposta" min="{{ $cursoSelecionado->escolhaQms->anoFormacao->ano_per_qualificacao.'-02-01' }}" max="{{ $cursoSelecionado->escolhaQms->anoFormacao->ano_per_qualificacao.'-12-31' }}" required {{ $readOnly }}>
</div>
<div class="fomr-group" style="width: 150px;">
    <label>Realização</label>
    <input type="date" class="form-control" value="{{ $esaAvaliacoes->realizacao or old('realizacao') }}" name="realizacao" min="{{$cursoSelecionado->escolhaQms->anoFormacao->ano_per_qualificacao.'-02-01' }}" max="{{ $cursoSelecionado->escolhaQms->anoFormacao->ano_per_qualificacao.'-12-31' }}" required {{ $readOnly }}>
</div>

<div class="fomr-group" style="width: 150px;">
    <label>Devolução</label>
    <input type="date" class="form-control" value="{{ $esaAvaliacoes->devolucao or old('devolucao') }}" name="devolucao" min="{{$cursoSelecionado->escolhaQms->anoFormacao->ano_per_qualificacao.'-02-01' }}" max="{{ $cursoSelecionado->escolhaQms->anoFormacao->ano_per_qualificacao.'-12-31' }}" required {{ $readOnly }}>
</div>

<div class="fomr-group" style="width: 150px;">
    <div class="custom-control custom-checkbox" style="margin-top: 20px;">
        <input id="customCheckAvl1" name="avl1" type="checkbox" value="0" class="custom-control-input" {{ (isset($esaAvaliacoes) && ($esaAvaliacoes->avl_1_ano == 'S')) ? 'checked' : '' }} {{ ((isset($readOnly)) ? 'disabled="disabled"' : '' ) }}>
        <label class="custom-control-label" for="customCheckAvl1" style="width:200px;margin-top: 0.1rem;font-weight:bold;">Avaliação do 1º Ano?</label>
    </div>
</div>

@if(count($turmasRapPendente) == 0)
@isset($esaAvaliacoes)
<div class="fomr-group" style="width: 150px;">
    <div class="custom-control custom-checkbox" style="margin-top: 20px;">
        <input id="customCheckCiente" name="ciente" type="checkbox" value="0" class="custom-control-input" {{ (isset($esaAvaliacoes) && ($esaAvaliacoes->retorno_aluno == 'S')) ? 'checked' : '' }} {{ (!$ownauthcontroller->PermissaoCheck(41) || ($esaAvaliacoes->retorno_aluno == 'S')) ? 'disabled=\'disabled\'' : '' }}>
        <label class="custom-control-label" for="customCheckCiente" style="width:500px;margin-top: 0.1rem;font-weight:bold;">Retorno do Ciente <font style="color:#AA2EFE">* Irá calcular a NDs dos Aluno(a)s</font></label>
    </div>
</div>
<br>
@endisset
@endif

<div style="margin-top: 24px;">
    @if(!isset($readOnly))
    <button type="button" class="btn btn btn-success" onclick="javascript:void(0);">{{ isset($esaAvaliacoes) ? 'Modificar' : 'Salvar' }}</button>
    @endif

    @if($ownauthcontroller->PerfilCheck([9006]) && count($turmasRapPendente) > 0)
    <!-- Libera Lançar a RAP -->
    @if(!isset($colecaoFaltas) || $colecaoFaltas->count() > 0)
    <button type="button" class="btn btn btn-primary" onclick="javascript:void(0);">Lançar o RAP</button>
    @endif

    @endif

    <button type="button" class="btn btn-warning" onclick="javascript:void(0);">Cancelar</button>
    @if(isset($esaAvaliacoes) && (!isset($readOnly)))
    <button type="button" class="btn btn-danger float-right" onclick="javascript:void(0);">Excluir</button>
    @endif
</div>
<!-- MODALS confirmAcao DINAMICA-->

<div class="modal fade bd-example-modal-sm" id="modalConfirmAcao" style="background-color: rgb(119, 119, 119);" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius: 0; padding: 10px;min-height: 15vh;">
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

<!-- FINAL MODAL confirmacao DINAMICA-->

<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    });

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

    $('form#form-avaliacoes select.selectpicker.custom-select').change(function() {
        $('form#form-avaliacoes select.selectpicker.custom-select :selected').each(function(i) {
            if ($(this).val() != 0) {
                $(this).parent().css('color', '{{$color}}');
                $(this).parent().find('option').css('font-weight', 'normal')
                $(this).parent().find('option').css('color', 'black');
            }
        });
    });

    $('form#form-avaliacoes button.btn.btn-danger.float-right').click(function(evt) {
        $(document).confirmAcao('Para remover este item certifique-se que a avaliação não está vinculada com nenhum <strong>lançamento</strong>.<p>Deseja realmente excluir esta <i>Avaliação</i>?</p>', function() {
            $.ajax({
                type: 'DELETE',
                dataType: 'json',
                url: "{{asset('/gaviao/ajax/gerenciar-avaliacao').(isset($esaAvaliacoes) ? '/'.$esaAvaliacoes->id : null)}}",
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    $('div.alert.alertas-avaliacoes').empty().hide();
                    $('div.alert.alertas-avaliacoes').removeClass('alert-success').empty();
                    $('div.alert.alertas-avaliacoes').removeClass('alert-danger').empty();
                },
                success: function(data) {
                    if (data.status == 'ok') {
                        $('div.alert.alertas-avaliacoes').addClass('alert-success').empty().slideDown();
                        setTimeout(function() {
                            $('div.alert.alertas-avaliacoes').slideUp(200, function() {
                                $('#form-avaliacao .btn.btn-warning').click();

                                //Ativa o gatilho para o carregamento do calendário...
                                carregaContainerCalendario("{{ asset('gaviao/ajax/calendario/index/'.$cursoSelecionado->escolhaQms->anoFormacao->id) }}");
                            });
                        }, 2000);
                    } else {
                        $('div.alert.alertas-avaliacoes').html('<strong>ATENÇÃO:</strong><br />').slideDown();
                    }
                },
                error: function(jqxhr) {
                    $('div.alert.alertas-avaliacoes').addClass('alert-danger').empty();
                    $('div.alert.alertas-avaliacoes').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
                }
            });
        });
    });

    $('form#form-avaliacoes button.btn.btn.btn-success').click(function(evt) {
        evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

        $(this).hide();

        var formData = $('form#form-avaliacoes').serialize();

        $.ajax({
            dataType: 'json',
            url: "{{asset('/gaviao/ajax/gerenciar-avaliacao').(isset($esaAvaliacoes) ? '/'.$esaAvaliacoes->id : null)}}",
            type: (evt.target.textContent == 'Salvar') ? 'POST' : 'PUT',
            data: formData,
            beforeSend: function() {
                $('div.alert.alertas-avaliacoes').empty().hide();
                $('div.alert.alertas-avaliacoes').removeClass('alert-success').empty();
                $('div.alert.alertas-avaliacoes').removeClass('alert-danger').empty();
            },
            success: function(data) {

                if (data.status == 'success') {
                    $('div.alert.alertas-avaliacoes').addClass('alert-success').empty().slideDown();
                    $('div.alert.alertas-avaliacoes').append('<li>' + data.response + '</li>');

                    setTimeout(function() {
                        $('div.alert.alertas-avaliacoes').slideUp(200, function() {
                            $('#form-avaliacao .btn.btn-warning').click();

                            //Ativa o gatilho para o carregamento do calendário...
                            carregaContainerCalendario("{{ asset('gaviao/ajax/calendario/index/'.$cursoSelecionado->escolhaQms->anoFormacao->id) }}");
                        });
                    }, 2000);
                } else {
                    $('div.alert.alertas-avaliacoes').html('<strong>ATENÇÃO:</strong><br />').slideDown();
                    $('div.alert.alertas-avaliacoes').addClass('alert-danger').empty();
                    $.each(data.response, function(key, value) {
                        $('div.alert.alertas-avaliacoes').append('<li>' + value + '</li>');
                    });

                    $('form#form-avaliacoes button.btn.btn btn-success').show();
                }

            },
            error: function(jqxhr) {
                $('div.alert.alertas-avaliacoes').addClass('alert-danger').empty();
                $('div.alert.alertas-avaliacoes').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
            }
        });
    });

    $('#form-avaliacao .btn.btn-warning').click(function(evt) {
        evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
        //Ativa o gatilho para o carregamento do calendário...
        carregaContainerCalendario("{{ session('url_mes_calendario') }}");
    });

    @isset($esaAvaliacoes)
    $('#form-avaliacao .btn.btn-primary').click(function(evt) {
        evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

        $('div#modalDinamica div.modal-content').fadeIn(1000);

        $.ajax({
            type: 'GET',
            url: "{{ asset('/gaviao/ajax/gerenciar-avaliacao/rap/').'/'.$esaAvaliacoes->id }}",
            beforeSend: function() {
                $('div.alert.alertas-avaliacoes').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
                loadingModalDinamica('show', 'lg');
            },
            success: function(data) {
                $('#form-avaliacao').fadeOut();
                $('div#modalDinamica div.modal-content').html(data);

                $('.carousel').carousel({
                    interval: false
                });

                loadingModalDinamica('hide', 'lg');
            }
        });
    });

    $('form#form-avaliacoes div.custom-checkbox input[name=ciente]').click(function(evt) {
        evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

        if (evt.target.checked) {
            $(document).confirmAcao('Registrar o <strong>"Ciente do Aluno"</strong>.<p>Deseja realmente realizar?</p> </p><i>Esse procedimento pode alterar as notas dos alunos</i>?</p>', function() {

                var formData = $('form#form-avaliacoes').serialize();

                $.ajax({
                    dataType: 'json',
                    url: "{{asset('/gaviao/ajax/gerenciador-resultados/dar-ciente-aluno/')}}",
                    type: 'POST',
                    data: formData,
                    beforeSend: function(){
                        $('div.alertas-avaliacoes').empty().hide();

                        $('div.alertas-avaliacoes').removeClass('alert-success').empty();
                        $('div.alertas-avaliacoes').removeClass('alert-danger').empty();
                    },
                    success: function(data) {
                        if(data.success){
                            $('div.alertas-avaliacoes').addClass('alert-success').empty();
                        }else{
                            $('div.alertas-avaliacoes').addClass('alert-danger').empty();
                        }
                        $('div.alertas-avaliacoes').html(data.message).slideDown();
                    },
                    error: function(jqxhr) {
                        $('div.alertas-avaliacoes').addClass('alert-danger').empty();
                        $('div.alertas-avaliacoes').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
                    }
                });
            });
        }
    });
    @endisset
</script>
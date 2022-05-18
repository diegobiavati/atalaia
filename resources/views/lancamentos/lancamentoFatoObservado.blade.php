<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/bootstrap-datepicker.pt-BR.min.js"></script>
<link href="/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css"/>

<form id="lancamentoFatoObservado">

    <input type="hidden" name="rotaTurma" value="{{$rotaTurma}}" />
    <input type="hidden" name="_token" value="{{csrf_token()}}" />

    <div style="width: 40%; margin: 22px auto; text-align: center; border-bottom: 0px solid #ccc;">

        <div style="margin-bottom: 15px;">
            <div>
                <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(121, 161, 212);">
                    <font style="color:rgb(255, 255, 255);">{{ (isset($cursos) ? 'Curso' : 'Uete') }}</font>
                </label>
            </div>
            @if(isset($cursos))
            <select name="qmsID" class="custom-select required_to_show_button" {{ $readOnly }}>
                <option value="0" disabled selected hidden>Selecione um Curso</option>
                @if($ownauthcontroller->PermissaoCheck(1))
                <option value="todas_qmss">TODOS OS CURSOS</option>
                @endif
                @foreach ($cursos as $curso)
                <option value={{$curso->id}} {{ (isset($lancamentoFo->aluno) && $curso->id == $lancamentoFo->aluno->qms_id)  ? 'selected': ''}}>{{ $curso->qms }}</option>
                @endforeach
            </select>
            @else
            <select name="omctID" class="custom-select required_to_show_button" {{ $readOnly }}>
                <option value="0" disabled selected hidden>Selecione uma UETE</option>
                @if($ownauthcontroller->PermissaoCheck(1))
                <option value="todas_omct">TODAS AS UETE</option>
                @endif
                @foreach ($uetes as $uete)
                <option value={{$uete->id}} {{ (isset($lancamentoFo->aluno) && $uete->id == $lancamentoFo->aluno->omcts_id)  ? 'selected': ''}}>{{ $uete->omct }}</option>
                @endforeach
            </select>
            @endif
        </div>
        <div style="margin-bottom: 15px;">
            <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(121, 161, 212);">
                <font style="color:rgb(255, 255, 255);">Observador</font>
                <input class="form-control" style="display:block; text-align:center;" name="observador" value="{{ (isset($lancamentoFo->operador) ? $lancamentoFo->operador->posto->postograd_abrev.' '.$lancamentoFo->operador->nome_guerra : session()->get('nomeOperador'))  }}" autocomplete="off" readonly />
            </label>
        </div>
        <div style="margin-bottom: 15px;">
            <div><label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(121, 161, 212);">
                    <font style="color:rgb(255, 255, 255);">Data da Observação </font>
                    <font style="color:rgb(255, 0, 0);font-size: smaller;">*Se nenhuma data for marcada, será registrada a data atual!</font>
                </label></div>
            <input type="hidden" name="dateObs" value="{{ (isset($lancamentoFo->data_obs) ? str_replace('/', '-', App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($lancamentoFo->data_obs)) : null) }}" />
            <div id="datepicker" style="display: flex;justify-content: center;margin-top: 10px;" data-date-end-date="{{ (isset($lancamentoFo) ? str_replace('/', '-', App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($lancamentoFo->data_obs)) : null) }}" data-date-start-date="{{ (isset($lancamentoFo) ? str_replace('/', '-', App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($lancamentoFo->data_obs)) : null) }}"></div>
        </div>
        <div style="margin-bottom: 15px;">
            <div>
                <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(121, 161, 212);">
                    <font style="color:rgb(255, 255, 255);">Tipo de FO</font>
            </div>

            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" class="custom-control-input" id="radio1" {{ ((isset($lancamentoFo) && $lancamentoFo->tipo == 0) ? 'checked' : '') }} name="radioTipoFO" value="0" {{ ((isset($readOnly)) ? 'disabled="disabled"' : '' ) }}>
                <label class="custom-control-label" for="radio1">Negativo</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" class="custom-control-input" id="radio2" {{ ((isset($lancamentoFo) && $lancamentoFo->tipo == 1) ? 'checked' : '') }} name="radioTipoFO" value="1" {{ ((isset($readOnly)) ? 'disabled="disabled"' : '' ) }}>
                <label class="custom-control-label" for="radio2">Neutro</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" class="custom-control-input" id="radio3" {{ ((isset($lancamentoFo) && $lancamentoFo->tipo == 2) ? 'checked' : '') }} name="radioTipoFO" value="2" {{ ((isset($readOnly)) ? 'disabled="disabled"' : '' ) }}>
                <label class="custom-control-label" for="radio3">Positivo</label>
            </div>
            </label>
        </div>

        <div style="margin-bottom: 15px;">
            <div>
                <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(121, 161, 212);">
                    <font style="color:rgb(255, 255, 255);">Observações sobre o Fato</font>
                    <br>
                    <font style="color:rgb(255, 0, 0);font-size: xx-small;">*O comportamento observado deve ser descrito e sua ligação com o objetivo previsto no PLADIS (quem, o que e como).</font>
                </label>
            </div>
            <div>
                <textarea class="form-control" name="textAreaObservacaoFO" rows="3" style="display: inline;" {{ $readOnly }}>{{ ((isset($lancamentoFo)) ? $lancamentoFo->observacao : null) }}</textarea>
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <div>
                <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(121, 161, 212);">
                    <font style="color:rgb(255, 255, 255);">Conteúdo Atitudinal</font>
                    <br>
                    <font style="color:rgb(255, 0, 0);font-size: xx-small;">*Deve ser citado o conteúdo atitudinal que está relacionado com o comportamento observado.</font>
                </label>
            </div>
            <div>
                @foreach($conteudoAtitudinal as $conteudo)
                <div class="custom-control custom-checkbox custom-control-inline" style="width: 30%;text-align:left;margin-right:0px">
                    <input type="checkbox" name="atitudinal_{{$conteudo->id}}" {{ ((isset($lancamentoFo)) && in_array($conteudo->id, json_decode($lancamentoFo->conteudo_atitudinal)) ? 'checked': '') }} class="custom-control-input" id="atitudinal_{{$conteudo->id}}" {{ ((isset($readOnly)) ? 'disabled="disabled"' : '' ) }}>
                    <label class="custom-control-label" for="atitudinal_{{$conteudo->id}}">{{$conteudo->descricao}}</label>
                </div>
                @endforeach
            </div>
        </div>

        <!--Só libera se for Cmt de Cia-->
        <div id="divProvidencia" style="margin-bottom: 15px; display: {{ ($ownauthcontroller->PerfilCheck([1,2,9001,9999]) || (isset($lancamentoFo) && isset($lancamentoFo->providencia))) ? 'block': 'none'}} ;">
            <div>
                <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(121, 161, 212);">
                    <font style="color:rgb(255, 255, 255);">Providências</font>
                    <br>
                    <font style="color:rgb(255, 0, 0);font-size: xx-small;">*É descrito o tipo de orientação que o aluno recebeu em relação ao seu comportamento ("feedback").</font>
                </label>
            </div>
            <div>
                <textarea class="form-control required_to_show_button" name="textAreaProvidencias" rows="3" style="display: inline;">{{ ((isset($lancamentoFo)) ? $lancamentoFo->providencia : null) }}</textarea>
            </div>

            <div class="btn-group-toggle" data-toggle="buttons" style="margin: 10px;">
                <label class="btn btn-outline-warning {{ ((isset($lancamentoFo) && ($lancamentoFo->frad == 'S')) ? 'active' : '') }}">
                    <input type="radio" name="btnPunir" value="Frad" autocomplete="off" {{ ((isset($lancamentoFo) && ($lancamentoFo->frad == 'S')) ? 'checked' : '') }}>Enviar para FRAD
                </label>
                <label class="btn btn-outline-danger {{ ((isset($lancamentoFo) && ($lancamentoFo->fatd == 'S')) ? 'active' : '') }}">
                    <input type="radio" name="btnPunir" value="Fatd" autocomplete="off" {{ ((isset($lancamentoFo) && ($lancamentoFo->fatd == 'S')) ? 'checked' : '') }}>Apurar com FATD (Enviar para Sargenteante)
                </label>
            </div>
        </div>

        <!--Só libera se for período ESA-->
        <!--<div id="divEnquadramento" style="margin-bottom: 15px;">
            <div>
                <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(121, 161, 212);">
                    <font style="color:rgb(255, 255, 255);">Enquadramento</font>
                </label>
            </div>-->
            @if(isset($cursos))
            
                @include('lancamentos.componenteSelectNASE')

            @endif
        <!--</div>-->

        <div style="margin-bottom: 15px;">
            <div>
                <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(121, 161, 212);">
                    <font style="color:rgb(255, 255, 255);">Selecionar Turma</font>
                </label>
            </div>
            <div>
                <select name="turmaID" class="custom-select" {{ $readOnly }}>
                    @if(isset($lancamentoFo))
                    @include('lancamentos.lancamentoConsultaTurma', ['turma' => $lancamentoFo->aluno->turma])
                    @else
                    <option value="0" disabled selected hidden>Selecione uma Turma</option>
                    @endif
                </select>
            </div>
            <div id="container-turma">
                @if(isset($lancamentoFo))
                @include('lancamentos.lancamentoAlunosFO', ['alunosTurma' => [$lancamentoFo->aluno], 'edit' => true])
                @endif
            </div>
            <div id="container-cancelamento">
                @if((isset($lancamentoFo) 
                && (in_array(4, session()->get('login.perfil')) || in_array(9002, session()->get('login.perfil'))) ) 
                || (isset($lancamentoFo) && $lancamentoFo->cancelado == 'S'))
                @include('lancamentos.lancamentoCancelamentoFO', $lancamentoFo)
                @endif
            </div>
        </div>

        <div class="alert alert-danger errors-lancamento-fo" role="alert"></div>
        <div class="alert alert-success success-lancamento-fo" role="alert"></div>

        <div style="margin-top:24px;">
            <button id="btnRegistraFO" type="button" class="btn btn-primary" style="display: none;">Registrar Fato Observado</button>
            @if(isset($lancamentoFo))
            <button id="btnAtualizaFO" type="button" class="btn btn-success">Atualizar Fato Observado</button>
            @endif
        </div>
    </div>
</form>


<script>
    $(document).ready(function() {
        
        $('div.errors-lancamento-fo').empty().hide();
        $('div.success-lancamento-fo').empty().hide();

        $('div#datepicker').datepicker({
            
            format: "dd/mm/yyyy",
            maxViewMode: 0,
            language: "pt-BR"
        });

        $("div#datepicker").datepicker("setDate", $("#lancamentoFatoObservado input[name=dateObs]").val());

        $(document).on('change', 'select.required_to_show_button', function() {
            $('select.required_to_show_button').each(function(index, element) {
                if ($(element).val() == 0) {
                    $('#btnRegistraFO.btn.btn-primary').slideUp(100);
                    return false;
                }
                $('#btnRegistraFO.btn.btn-primary').slideDown(100);
            });
        });

        $(document).on('change keyup paste', 'textarea.required_to_show_button', function() {
            $('#btnAtualizaFO.btn.btn-success').slideDown(100);
        });

        $('#btnRegistraFO.btn.btn-primary').click(function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

            var formData = $('form#lancamentoFatoObservado').serialize();
            var url = "/ajax/lancamentos";

            if ($("div#datepicker").datepicker("getDate") != null) {
                formData = formData + '&dataFO=' + $("div#datepicker").datepicker("getDate").toDateString();
            }

            $.ajax({
                dataType: 'json',
                url: url,
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    $('div.errors-lancamento-fo').empty().hide();
                    $('div.success-lancamento-fo').empty().hide();
                },
                success: function(data) {

                    if (data.status == 'err') {
                        $('div.errors-lancamento-fo').html(data.response).slideDown();
                    } else {
                        $('div.success-lancamento-fo').html(data.response).slideDown();

                        setTimeout(function() {
                            $('div.success-lancamento-fo').slideUp(200, function() {
                                $(this).removeClass('alert-success').empty();
                                $('div#full-modal').modal('hide');
                            });
                        }, 3000);
                    }

                },
                error: function(jqxhr) {
                    $('div.errors-lancamento-fo').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
                }
            });
        });

        $('#btnAtualizaFO.btn.btn-success').click(function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

            var formData = $('form#lancamentoFatoObservado').serialize();
            var url = "/ajax/lancamentos/{{ (isset($lancamentoFo->id) ? $lancamentoFo->id : null) }}";

            $.ajax({
                dataType: 'json',
                url: url,
                type: 'PUT',
                data: formData,
                beforeSend: function() {
                    $('div.errors-lancamento-fo').empty().hide();
                    $('div.success-lancamento-fo').empty().hide();
                },
                success: function(data) {

                    if (data.status == 'err') {
                        $('div.errors-lancamento-fo').html(data.response).slideDown();
                    } else {
                        $('div.success-lancamento-fo').html(data.response).slideDown();

                        setTimeout(function() {
                            $('div.success-lancamento-fo').slideUp(200, function() {
                                $(this).removeClass('alert-success').empty();
                                $('div#full-modal').modal('hide');
                            });
                        }, 3000);
                    }

                },
                error: function(jqxhr) {
                    $('div.errors-lancamento-fo').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
                }
            });
        });

        $(document).on('change', 'form#lancamentoFatoObservado select.custom-select[name="turmaID"]', function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

            dataPost = '_token=' + $('input[name="_token"]').val() +
                '&turmaID=' + $(this).children("option:selected").val() +
                '&omctID=' + $('select.custom-select[name="omctID"]').children("option:selected").val() +
                '&qmsID=' + $('select.custom-select[name="qmsID"]').children("option:selected").val() +
                '&anoFormacaoID=' + $('.btn.btn-secondary.active input[name="ano_formacao"]').val();

            $.ajax({
                url: $('input[name="rotaTurma"]').val(),
                type: 'POST',
                data: dataPost,
                beforeSend: function() {
                    $('div#container-turma').empty();
                    $('div#container-turma').html('<div id="temp"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
                },
                success: function(data) {
                    $('div#container-turma').empty();
                    $('div#container-turma').html(data);
                },
                error: function(jqxhr) {
                    $('div#container-turma').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
                }
            });
        });

        $(document).on('change', 'form#lancamentoFatoObservado select[name="omctID"]', function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

            $('div#container-turma').empty();
            $('div#datepicker').datepicker('clearDates');

            dataPost = '_token=' + $('input[name="_token"]').val() +
                '&omctID=' + $('select.custom-select[name="omctID"]').children("option:selected").val() +
                '&anoFormacaoID=' + $('.btn.btn-secondary.active input[name="ano_formacao"]').val();

            //Busca as turmas da UETE seleciona
            $.ajax({
                url: 'ajax/consultaTurma',
                type: 'POST',
                data: dataPost,
                beforeSend: function() {
                    $('select.custom-select[name="turmaID"]').empty();
                },
                success: function(data) {
                    $('select.custom-select[name="turmaID"]').html(data);
                },
                error: function(jqxhr) {
                    $('div#container-turma').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
                }
            });
        });

        $(document).on('change', 'form#lancamentoFatoObservado select[name="qmsID"]', function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

            $('div#container-turma').empty();
            $('div#datepicker').datepicker('clearDates');

            dataPost = '_token=' + $('input[name="_token"]').val() +
                '&qmsID=' + $('select.custom-select[name="qmsID"]').children("option:selected").val() +
                '&anoFormacaoID=' + $('.btn.btn-secondary.active input[name="ano_formacao"]').val();

            //Busca as turmas da UETE seleciona
            $.ajax({
                url: '/ajax/consultaTurma',
                type: 'POST',
                data: dataPost,
                beforeSend: function() {
                    $('select.custom-select[name="turmaID"]').empty();
                },
                success: function(data) {
                    $('select.custom-select[name="turmaID"]').html(data);
                },
                error: function(jqxhr) {
                    $('div#container-turma').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
                }
            });
        });

        $('.custom-control.custom-radio input[type="radio"]').click(function() {
            var radioValue = $('.custom-control.custom-radio input[type="radio"]:checked').val();
            if (radioValue > 0) {
                $('#divProvidencia').css('display', 'block');
                $('.btn.btn-outline-danger').css('display', 'none');
                $('.btn.btn-outline-warning').addClass('active');
                $('.btn.btn-outline-warning input[type="radio"]').prop("checked", true);
            } else {
                $('#divProvidencia').css('display', 'none');
                $('.btn.btn-outline-warning').removeClass('active');
                $('.btn.btn-outline-warning input[type="radio"]').prop("checked", false);
            }
        });

    });
</script>
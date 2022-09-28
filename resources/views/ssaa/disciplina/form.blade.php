<style>
    select[readonly] {
        background-color: {{ App\Http\Controllers\Utilitarios\FuncoesController::getQmsColor($cursoSelecionado->qms_matriz_id)->backgroundColor }};
        font-size: 20px;
        font-weight: bold;
        text-align: center;
    }

    .custom-select {
        height: auto;
    }

    .custom-label-curso {
        border-radius: .25rem;
        padding: 5px;
        width: 100%;
        background-color: rgb(121, 161, 212);
        margin-bottom: 0px;
        text-align: center;
    }
</style>

<div id="esa_adiciona_disciplinas">
    <div class="card-header">
        <i class="ion-ios-bookmarks" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i><b>{{ (isset($esaDisciplinas) ? 'Editar Disciplina' : 'Adicionar Disciplina ao Sistema') }} </b>
    </div>

    <div class="card-body">

        <div class="alert alertas-disciplinas" role="alert" style="margin:10px;width:100%;"></div>

        <form id="form_esa_disciplina">
            @csrf
            <div style="margin: 14px auto; width: 70%; max-width: 380px; text-align:center;">
                <div style="float: right; width: 100%; margin-top: 4px; padding: 0 0 10px 6px; ">
                    @include('ajax.componenteSelectUeteCurso')
                </div>
            </div>

            <div style="margin: 14px auto; width: 70%; text-align:center; max-width: 380px;">
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-secondary" style="text-align: center;">
                        <input type="radio" name="ano_formacao" value="{{$cursoSelecionado->escolhaQms->anoFormacao->id}}" checked /> {{ $cursoSelecionado->escolhaQms->anoFormacao->ano_cfs }}
                    </label>
                </div>
                <div class="clear"></div>
            </div>

            <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                <div style="float: left;"> <i class="ion-ios-compose" style="font-size: 24px; color: #696969;"></i> </div>
                <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                    <input class="form-control text-uppercase" style="width: 100%;" name="nome_disciplina" type="text" value="{{$esaDisciplinas->nome_disciplina or old('nome_disciplina')}}" maxlength="100" autocomplete="off" placeholder="Nome completo da disciplina" />
                </div>
                <div class="clear"></div>
            </div>

            <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                <div style="float: left;"> <i class="ion-ios-compose-outline" style="font-size: 24px; color: #696969;"></i> </div>
                <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                    <input class="form-control text-uppercase" style="width: 100%;" name="nome_disciplina_abrev" type="text" value="{{$esaDisciplinas->nome_disciplina_abrev or old('nome_disciplina_abrev')}}" maxlength="50" autocomplete="off" placeholder="Nome abreviado" />
                </div>
                <div class="clear"></div>
            </div>

            <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" data-html="true" title="Informe o prazo em <b>horas</b> da carga horária da disciplina.">
                <div style="float: left;"><i class="ion-clock" style="font-size: 24px; color: #696969;"></i></div>
                <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                    <input class="form-control" style="width: 100%;" name="carga_horaria" type="number" step="1" min="0" max="200" autocomplete="off" value="{{$esaDisciplinas->carga_horaria or old('carga_horaria')}}" placeholder="Carga horária" required data-required-error="Preencha este campo ou o celular."/>
                </div>
                <div class="clear"></div>
            </div>

            <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                <div style="float: left; margin-top: 3px;"><i class="ion-information-circled" style="font-size: 24px; color: #696969;"></i></div>
                <div style="float:right; width: 93%;">
                    <select class="custom-select" name="tipo_disciplina">
                        <option value="">Selecione um tipo de disciplina</option>
                        @foreach($tipo_disciplina as $disciplina)
                        <option value="{{$disciplina->id}}" {{ (isset($esaDisciplinas) && ($esaDisciplinas->tipo_disciplina == $disciplina->id)) ? 'selected' : '' }}>{{$disciplina->descricao}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="clear"></div>
            </div>

            <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                <div class="custom-control custom-checkbox" style="margin-top: 20px;">
                    <input id="customCheck" name="tfm" type="checkbox" value="0" class="custom-control-input" {{ (isset($esaDisciplinas) && ($esaDisciplinas->tfm == 'S')) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="customCheck">TFM (Treinamento Físico Militar)</label>
                </div>
            </div>

        </form>

    </div>

    <div class="card-footer" style="text-align: right;">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary salvar-disciplina">{{ isset($esaDisciplinas) ? 'Modificar' : 'Salvar' }}</button>
    </div>
</div>


<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    });

    $('#form_esa_disciplina .selectpicker').attr("readonly", "readonly");

    $('.btn.btn-primary.salvar-disciplina').click(function(evt) {
        //evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

        $(this).hide();

        var formData = $('form#form_esa_disciplina').serialize();

        $.ajax({
            dataType: 'json',
            url: '/gaviao/ajax/gerenciar-disciplinas{{ (isset($esaDisciplinas) ? "/".$esaDisciplinas->id : null) }}',
            type: (evt.target.textContent == 'Salvar') ? 'POST' : 'PUT',
            data: formData,
            beforeSend: function() {
                $('div.alertas-disciplinas').empty().hide();
                $('div.alertas-disciplinas').removeClass('alert-success').empty();
                $('div.alertas-disciplinas').removeClass('alert-danger').empty();
            },
            success: function(data) {

                if (data.status == 'success') {
                    $('div.alertas-disciplinas').addClass('alert-success').empty().slideDown();
                    $('div.alertas-disciplinas').append('<li>' + data.response + '</li>');

                    setTimeout(function() {
                        $('div.alertas-disciplinas').slideUp(200, function() {
                            $('#esa_adiciona_disciplinas .btn.btn-secondary').click();

                            //Ativa o gatilho da caixa de seleção para carregar o gerenciador de disciplinas...
                            $('div.container_disciplinas select.custom-select[name="qmsID"]').change()
                        });
                    }, 2000);
                } else {
                    $('div.alertas-disciplinas').html('<strong>ATENÇÃO:</strong><br />').slideDown();
                    $('div.alertas-disciplinas').addClass('alert-danger').empty();
                    $.each(data.response, function(key, value) {
                        $('div.alertas-disciplinas').append('<li>' + value + '</li>');
                    });
                }

            },
            error: function(jqxhr) {
                $('div.alertas-disciplinas').addClass('alert-danger').empty();
                $('div.alertas-disciplinas').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
            }
        });

    });
</script>
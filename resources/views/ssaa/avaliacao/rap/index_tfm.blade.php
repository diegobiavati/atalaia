<div id="relatorioAplicacaoProva">
    <div class="card-header">
        <i class="ion-plus" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> RAP/TFM - Relatório de Aplicação de Prova de TFM
    </div>

    <div class="card-body">
        <div class="alert alert-danger errors-rap" role="alert" style="display: none;"></div>

        <div id="carouselPanel" class="carousel slide">
            <div class="carousel-inner">
                <form id="parametrizacao_rap">
                    <div class="carousel-item active">
                        @csrf
                        <input type="hidden" name="id_avaliacao" value="{{ encrypt($esaAvaliacoes->id.'_'.$esaAvaliacoes->id_esa_disciplinas) }}"/>

                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                            <div style="float: left;">
                                <i class="ion-android-clipboard" style="font-size: 24px; color: #696969;"></i>
                            </div>
                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                <label>Disciplina</label>
                                <input type="text" class="form-control" value="{{ $esaAvaliacoes->esadisciplinas->nome_disciplina }}" name="nome_disciplina" autocomplete="off" required readOnly>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                            <div style="float: left;">
                                <i class="ion-android-list" style="font-size: 24px; color: #696969;"></i>
                            </div>
                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                <label>Prova</label>
                                <input type="text" class="form-control" value="{{ $esaAvaliacoes->nome_avaliacao.' - '.$esaAvaliacoes->getDescricao() }}" name="nome_avaliacao" autocomplete="off" required readOnly>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                            <div style="float: left;">
                                <i class="ion-android-bookmark" style="font-size: 24px; color: #696969;"></i>
                            </div>
                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                <label>Curso</label>
                                @include('ajax.componenteSelectUeteCurso')
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                            <div style="float: left;">
                                <i class="ion-android-pin" style="font-size: 24px; color: #696969;"></i>
                            </div>
                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                <label>Local de aplicação</label>
                                <input class="no-style" style="width: 100%;" name="local" type="text" value="" maxlength="100" autocomplete="off" placeholder="Local de Aplicação. Ex: Sala de Aula" />
                            </div>
                            <div class="clear"></div>
                        </div>
                        <fieldset style="border:1px solid #ced4da;padding:0 1.4em 1.4em 1.4em; width: 605px;margin: auto;">
                            <legend style="font-size:0.9rem;">Sequência de aplicação</legend>
                            <div class="row">
                                <div class="col-4" style="text-align: center;">
                                    <label style="font-weight:bold;">Data da aplicação</label>
                                </div>
                                <div class="col-4" style="text-align: center;">
                                    <label style="font-weight:bold;">Hora de início</label>
                                </div>
                                <div class="col-4" style="text-align: center;">
                                    <label style="font-weight:bold;">Hora de término</label>
                                </div>
                            </div>

                            <div class="row" style="margin-bottom: 10px;">
                                <div class="col-4" style="text-align: center;padding: 0 5px 0 5px;">
                                    <div style="float: left;padding-right: 10px;">
                                        <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                    </div>
                                    <div style="float: left;width: 155px;">
                                        <input type="date" class="form-control" name="data_aplicacao[]" autocomplete="off" placeholder="Data de Aplicação" required>
                                    </div>
                                </div>
                                <div class="col-4" style="text-align: center;padding: 0 5px 0 5px;">
                                    <div style="display: table;margin: 0 auto;">
                                        <div style="float: left;padding-right: 10px;">
                                            <i class="ion-clock" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: left;">
                                            <input type="time" class="form-control" name="hora_inicio[]" autocomplete="off" placeholder="Hora de início" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4" style="text-align: center;padding: 0 5px 0 5px;">
                                    <div style="display: table;margin: 0 auto;">
                                        <div style="float: left;padding-right: 10px;">
                                            <i class="ion-clock" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: left;">
                                            <input type="time" class="form-control" name="hora_termino[]" autocomplete="off" placeholder="Hora de término" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-bottom: 10px;">
                                <div class="col-4" style="text-align: center;padding: 0 5px 0 5px;">
                                    <div style="float: left;padding-right: 10px;">
                                        <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                    </div>
                                    <div style="float: left; width: 155px;">
                                        <input type="date" class="form-control" name="data_aplicacao[]" autocomplete="off" placeholder="Data de Aplicação" required>
                                    </div>
                                </div>
                                <div class="col-4" style="text-align: center;padding: 0 5px 0 5px;">
                                    <div style="display: table;margin: 0 auto;">
                                        <div style="float: left;padding-right: 10px;">
                                            <i class="ion-clock" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: left;">
                                            <input type="time" class="form-control" name="hora_inicio[]" autocomplete="off" placeholder="Hora de início" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4" style="text-align: center;padding: 0 5px 0 5px;">
                                    <div style="display: table;margin: 0 auto;">
                                        <div style="float: left;padding-right: 10px;">
                                            <i class="ion-clock" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: left;">
                                            <input type="time" class="form-control" name="hora_termino[]" autocomplete="off" placeholder="Hora de término" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-bottom: 10px;">
                                <div class="col-4" style="text-align: center;padding: 0 5px 0 5px;">
                                    
                                        <div style="float: left;padding-right: 10px;">
                                            <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: left;width: 155px;">
                                            <input type="date" class="form-control" name="data_aplicacao[]" autocomplete="off" placeholder="Data de Aplicação" required>
                                        </div>
                                    
                                </div>
                                <div class="col-4" style="text-align: center;padding: 0 5px 0 5px;">
                                    <div style="display: table;margin: 0 auto;">
                                        <div style="float: left;padding-right: 10px;">
                                            <i class="ion-clock" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: left;">
                                            <input type="time" class="form-control" name="hora_inicio[]" autocomplete="off" placeholder="Hora de início" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4" style="text-align: center;padding: 0 5px 0 5px;">
                                    <div style="display: table;margin: 0 auto;">
                                        <div style="float: left;padding-right: 10px;">
                                            <i class="ion-clock" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: left;">
                                            <input type="time" class="form-control" name="hora_termino[]" autocomplete="off" placeholder="Hora de término" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-bottom: 10px;">
                                <div class="col-4" style="text-align: center;padding: 0 5px 0 5px;">
                                    
                                        <div style="float: left;padding-right: 10px;">
                                            <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: left;width: 155px;">
                                            <input type="date" class="form-control" name="data_aplicacao[]" autocomplete="off" placeholder="Data de Aplicação" required>
                                        </div>
                                    
                                </div>
                                <div class="col-4" style="text-align: center;padding: 0 5px 0 5px;">
                                    <div style="display: table;margin: 0 auto;">
                                        <div style="float: left;padding-right: 10px;">
                                            <i class="ion-clock" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: left;">
                                            <input type="time" class="form-control" name="hora_inicio[]" autocomplete="off" placeholder="Hora de início" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4" style="text-align: center;padding: 0 5px 0 5px;">
                                    <div style="display: table;margin: 0 auto;">
                                        <div style="float: left;padding-right: 10px;">
                                            <i class="ion-clock" style="font-size: 24px; color: #696969;"></i>
                                        </div>
                                        <div style="float: left;">
                                            <input type="time" class="form-control" name="hora_termino[]" autocomplete="off" placeholder="Hora de término" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset> 
                    </div>
                    
                    <div class="carousel-item">
                        <div style="width: 80%; margin: 0 auto;">
                            <h4 style="text-align: center;">Faltas</h4>
                        </div>
                        <div id="container-alunos" style="min-height: 250px;"></div>
                    </div>

                    <div class="carousel-item">
                        <div style="width: 80%; margin: 0 auto;">
                            <h4 style="text-align: center;">Motivos de Faltas</h4>
                        </div>
                        <div id="container-alunos-motivos" style="min-height: 250px;"></div>
                    </div>

                    <div class="carousel-item">
                        <!--
                            Acidentes Ocorridos/Teste Físico
                        -->
                        <div class="row">
                            <div style="margin: 14px auto; width: 95%;">
                                <div style="margin-top: 4px;">
                                    <label>Acidentes Ocorridos/Teste Físico</label>
                                    <textarea class="form-control" id="acidentes" name="acidentes" rows="3" placeholder=""></textarea>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div style="margin: 14px auto; width: 95%;">
                                <div style="margin-top: 4px;">
                                    <label>Fatores que Influenciaram Negativamente ou Positivamente na Aplicação</label>
                                    <textarea class="form-control" id="fatores_neg_pos" name="fatores_neg_pos" rows="3" placeholder=""></textarea>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                        <!--
                            Fim Acidentes Ocorridos/Teste Físico
                        -->

                        <!--
                            Condições Metereológicas Impediram a Realização de Algum Teste
                        -->
                        <fieldset style="border: 1px solid #ced4da;padding: 0 1.4em 1.4em 1.4em;">
                            <legend style="font-size: 0.9rem;">Condições Metereológicas Impediram a Realização de Algum Teste</legend>
                                <div class="row">
                                    <div class="col">
                                        <div style="margin-right: 10px;">
                                            <label style="font-weight: bold;">Não</label>
                                            <textarea class="form-control" name="cond_meter_nao" rows="3" placeholder=""></textarea>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div style="margin-right: 10px;">
                                            <label style="font-weight: bold;">Sim. Qual(is)</label>
                                            <textarea class="form-control" name="cond_meter_sim" rows="3" placeholder=""></textarea>
                                        </div>
                                    </div>
                                </div>
                        </fieldset>
                        <!--
                            Fim Condições do local de aplicação
                        -->

                        <!--
                            Efetivos
                        -->
                        <fieldset style="border: 1px solid #ced4da; padding: 0 1.4em 1.4em 1.4em; margin-top: 10px;">
                            <legend style="font-size: 0.9rem;">Efetivos</legend>
                                <div class="row" style="text-align: center;">
                                    <div class="col" style="padding:0px;">
                                        <label for="efetivo_curso" style="font-weight: bold;padding-right: 5px;">Efetivo do Curso :</label>
                                        <div class="col-sm-4 custom-control-inline" style="padding: 0px;width:75px;">
                                            <input type="number" class="form-control" id="efetivo_curso" name="efetivo_curso" placeholder="0" min="0" readonly>
                                        </div>
                                    </div>
                                    <div class="col" style="padding:0px; flex-grow: 2;">
                                        <label for="efetivo_realizou" style="font-weight: bold;padding-right: 5px;">Efetivo do Curso que Realizou a Prova :</label>
                                        <div class="col-sm-4 custom-control-inline" style="padding: 0px;width:75px;">
                                            <input type="number" class="form-control" id="efetivo_realizou" name="efetivo_realizou" placeholder="0" min="0" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                        </fieldset>
                        <!--
                            Fim Efetivos
                        -->
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="card-footer" style="text-align: right;">
        <button type="button" class="btn btn-success float-left" onclick="javascript:void(0);">Salvar</button>

        <a class="btn btn-info" class="carousel-control-prev" href="#carouselPanel" role="button" data-slide="prev">&laquo; Voltar</a>
        <a class="btn btn-info" class="carousel-control-next" href="#carouselPanel" role="button" data-slide="next">Próximo &raquo;</a>

        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Cancelar
        </button>
    </div>
</div>
<script>
    $('.tempo_mask').mask('00:00');
    $('.data_mask').mask('00/00/0000');
    
    $('#modalDinamica').on('hidden.bs.modal', function () {
        $('#form-avaliacao').fadeIn();
    });

    $('form#parametrizacao_rap select[name="qmsID"]').on('change', function(evt){
        evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

        $.ajax({
            url: '{{$rotaViewListagemCurso}}' + evt.target.value,
            beforeSend:function(){
                $('form#parametrizacao_rap #container-alunos').empty();
                $('form#parametrizacao_rap div#container-alunos-motivos').empty();
                $('div.alert.errors-rap').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
            }
        }).done(function(data){
            $('form#parametrizacao_rap #container-alunos').html('<label style="color:#ff0505">(*) Marque os alunos que faltaram</label>');
            $('form#parametrizacao_rap #container-alunos').append(data);

            setarEfetivoCurso($('form#parametrizacao_rap div#container-alunos input[type=checkbox]').length);
            setarEfetivoRealizouProva($('form#parametrizacao_rap div#container-alunos input[type=checkbox]').length);
            preencheComboAlunos();

            $('form#parametrizacao_rap div#container-alunos').on('change', 'input[type=checkbox]', function(evt) {
                evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
                var checkbox = $(this);
                
                //Caso exista remove para adicionar uma nova.
                $('form#parametrizacao_rap div#container-alunos-motivos div#'+checkbox.attr('id')).remove();
                
                if (checkbox.is(':checked')){

                    $.ajax({
                        url: "{{asset('/gaviao/ajax/gerenciar-avaliacao/motivoFalta')}}" + '/' +checkbox.attr('id'),
                        type: 'GET',
                    }).done(function(data){
                        
                        $('form#parametrizacao_rap div#container-alunos-motivos').append(data);
                    }).fail(function(data){
                        $('div.alert.errors-rap').slideDown().addClass('alert-danger').html('Erro Interno!!');
                    });
                    
                }

                setarEfetivoRealizouProva($('form#parametrizacao_rap div#container-alunos input[type=checkbox]').length - $('form#parametrizacao_rap div#container-alunos input[type=checkbox]:checked').length);
                preencheComboAlunos();
            });
        }).fail(function(data){
            $('div.alert.errors-rap').slideDown().addClass('alert-danger').html('Erro Interno!!');
        });
    });

    $('div#relatorioAplicacaoProva').on('click', 'button.btn.btn-success.float-left',function(evt){
        
        evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
        $(this).hide();

        $.ajax({
            url: '{{$rotaSalvaRap}}',
            dataType: 'json',
            type: 'POST',
            data: $('form#parametrizacao_rap').serialize(),
            beforeSend:function(){
                $('div.alert.errors-rap').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
            }
        }).done(function(data){
            if(data.status == 'success'){
                $('div.alert.errors-rap').slideDown().addClass('alert-success');
                $('div.alert.errors-rap').append('<li>' + data.response + '</li>');

                setTimeout(function() {
                    $('div.alert.errors-rap').slideUp(200, function() {
                        $('#form-avaliacao .btn.btn-warning').click();
                        $('div#relatorioAplicacaoProva .btn.btn-secondary').click();
                        //Ativa o gatilho para o carregamento do calendário...
                        //carregaContainerCalendario("{{ asset('gaviao/ajax/calendario/index/'.$cursoSelecionado->escolhaQms->anoFormacao->id) }}");
                    });
                }, 2000);
            }else{
                $('div.alert.errors-rap').slideDown().addClass('alert-danger');
                $.each(data.response, function(key, value) {
                    $('div.alert.errors-rap').append('<li>' + value + '</li>');
                });

                $('div#relatorioAplicacaoProva button.btn.btn-success.float-left').show();
            }
            
        }).fail(function(data){
            $('div.alert.errors-rap').slideDown().addClass('alert-danger').html('Erro Interno!! ' + data);
        });
    });
    
    function preencheComboAlunos(){
        $('form#parametrizacao_rap select[name$="_discente"]').empty();

        $('form#parametrizacao_rap select[name$="_discente"]').append('<option value="0" disabled selected>Selecione um Aluno</option>');

        /*Preenche as combos com os alunos que não faltaram*/
        $('form#parametrizacao_rap div#container-alunos input[type=checkbox]').each(function(index, element){

            var checkbox = $(this);
                
            if (!checkbox.is(':checked')){
                $('form#parametrizacao_rap select[name$="_discente"]').append($('<option>', { 
                    value: element.value,
                    text: element.labels[0].innerText
                }));
            }
        });
    }

    function setarEfetivoRealizouProva(valor){
        /*Informa o quantitativo que realizou a prova*/
        $('form#parametrizacao_rap input[name="efetivo_realizou"]').val(valor);
        $('form#parametrizacao_rap input[name="efetivo_realizou"]').prop('max', valor);
        $('form#parametrizacao_rap input[name="efetivo_realizou"]').prop('min', valor);
    }

    function setarEfetivoCurso(valor){
        $('form#parametrizacao_rap input[name="efetivo_curso"]').val(valor);
        $('form#parametrizacao_rap input[name="efetivo_curso"]').prop('max', valor);
        $('form#parametrizacao_rap input[name="efetivo_curso"]').prop('min', valor);
    }

    $('form#parametrizacao_rap select[name="qmsID"]').change();
</script>
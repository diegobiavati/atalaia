<div id="relatorioAplicacaoProva">
    <div class="card-header">
        <i class="ion-plus" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> RAP - Relatório de Aplicação de Prova
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

                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                            <div style="float: left;">
                                <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                            </div>
                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                <label>Data da aplicação</label>
                                <input type="date" class="form-control" value="{{ $esaAvaliacoes->realizacao or old('realizacao') }}" name="data_aplicacao" autocomplete="off" placeholder="Data de Aplicação" required readOnly>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                            <div style="float: left;">
                                <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                            </div>
                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                <label>Duração prevista</label>
                                <input type="time" class="form-control" value="{{ $esaAvaliacoes->duracao or old('duracao') }}" name="duracao" autocomplete="off" placeholder="Duração prevista" required>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                            <div style="float: left;">
                                <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                            </div>
                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                <label>Horário de início</label>
                                <input type="time" class="form-control" value="{{ $esaAvaliacoes->hora_inicio or old('hora_inicio') }}" name="hora_inicio" autocomplete="off" placeholder="Horário de início" required>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                            <div style="float: left;">
                                <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
                            </div>
                            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                <label>Horário de término</label>
                                <input type="time" class="form-control" value="{{ $esaAvaliacoes->hora_termino or old('hora_termino') }}" name="hora_termino" autocomplete="off" placeholder="Horário de término" required>
                            </div>
                            <div class="clear"></div>
                        </div>

                        

                    </div>
                    
                    <div class="carousel-item">
                        <div style="width: 80%; margin: 0 auto;">
                            <h4 style="text-align: center;">Faltas</h4>
                        </div>
                        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
                            <div style="float: left;">
                                <i class="ion-android-clipboard" style="font-size: 24px; color: #696969;"></i>
                            </div>
                            <div style="float: left; border-bottom: 1px solid #ccc; width: 60%; margin-top: 4px; padding: 0 0 10px 6px; ">
                                <label>Turma</label>
                                @include('ajax.componentes.componenteTurma')
                            </div>
                            <div class="clear"></div>
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
                            Erros de impressão
                        -->
                        <div class="row">
                            <div style="margin: 14px auto; width: 40%;">
                                <div style="margin-top: 4px;">
                                    <label>Erros de impressão</label>
                                    <textarea class="form-control" id="erros_impressao" name="erros_impressao" rows="3" placeholder="Questão(ões) ou Item(ns)"></textarea>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div style="margin: 14px auto; width: 40%;">
                                <div style="margin-top: 4px;">
                                    <label>Erros de interpretação</label>
                                    <textarea class="form-control" id="erros_interpretacao" name="erros_interpretacao" rows="3" placeholder="Questão(ões) ou Item(ns)"></textarea>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                        <!--
                            Fim Erros de impressão
                        -->

                        <!--
                            Condições do local de aplicação
                        -->
                        <fieldset style="border: 1px solid #ced4da;padding: 0 1.4em 1.4em 1.4em;">
                            <legend style="font-size: 0.9rem;">Condições do local de aplicação</legend>
                                <div class="row">
                                    <div class="col">
                                        <div style="float: left; width: 90px; margin-right: 10px; text-align: end;">
                                            <label style="font-weight: bold;">Adequação :</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="radioAdequacaoMB" name="radioAdequacao" value="MB" class="custom-control-input">
                                            <label class="custom-control-label" for="radioAdequacaoMB">MB</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="radioAdequacaoB" name="radioAdequacao" value="B" class="custom-control-input">
                                            <label class="custom-control-label" for="radioAdequacaoB">B</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="radioAdequacaoR" name="radioAdequacao" value="R" class="custom-control-input">
                                            <label class="custom-control-label" for="radioAdequacaoR">R</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="radioAdequacaoI" name="radioAdequacao" value="I" class="custom-control-input">
                                            <label class="custom-control-label" for="radioAdequacaoI">I</label>
                                        </div>
                                        <div class="clear"></div>
                                        
                                        <div style="float: left; width: 90px; margin-right: 10px; text-align: end;">
                                            <label style="font-weight: bold;">Arrumação :</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="radioArrumacaoMB" name="radioArrumacao" value="MB" class="custom-control-input">
                                            <label class="custom-control-label" for="radioArrumacaoMB">MB</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="radioArrumacaoB" name="radioArrumacao" value="B" class="custom-control-input">
                                            <label class="custom-control-label" for="radioArrumacaoB">B</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="radioArrumacaoR" name="radioArrumacao" value="R" class="custom-control-input">
                                            <label class="custom-control-label" for="radioArrumacaoR">R</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="radioArrumacaoI" name="radioArrumacao" value="I" class="custom-control-input">
                                            <label class="custom-control-label" for="radioArrumacaoI">I</label>
                                        </div>
                                        <div class="clear"></div>
                                        
                                        <div style="float: left; width: 90px; margin-right: 10px; text-align: end;">
                                            <label style="font-weight: bold;">Silêncio :</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="radioSilencioMB" name="radioSilencio" value="MB" class="custom-control-input">
                                            <label class="custom-control-label" for="radioSilencioMB">MB</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="radioSilencioB" name="radioSilencio" value="B" class="custom-control-input">
                                            <label class="custom-control-label" for="radioSilencioB">B</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="radioSilencioR" name="radioSilencio" value="R" class="custom-control-input">
                                            <label class="custom-control-label" for="radioSilencioR">R</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="radioSilencioI" name="radioSilencio" value="I" class="custom-control-input">
                                            <label class="custom-control-label" for="radioSilencioI">I</label>
                                        </div>
                                        <div class="clear"></div>
                                        
                                        <div style="float: left; width: 90px; margin-right: 10px; text-align: end;">
                                            <label style="font-weight: bold;">Iluminação :</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="radioIluminacaoMB" name="radioIluminacao" value="MB" class="custom-control-input">
                                            <label class="custom-control-label" for="radioIluminacaoMB">MB</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="radioIluminacaoB" name="radioIluminacao" value="B" class="custom-control-input">
                                            <label class="custom-control-label" for="radioIluminacaoB">B</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="radioIluminacaoR" name="radioIluminacao" value="R" class="custom-control-input">
                                            <label class="custom-control-label" for="radioIluminacaoR">R</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="radioIluminacaoI" name="radioIluminacao" value="I" class="custom-control-input">
                                            <label class="custom-control-label" for="radioIluminacaoI">I</label>
                                        </div>
                                    </div>

                                    <div class="col" style="margin-top: -15px;">
                                        <label>Fatores que influenciaram a aplicação</label>
                                        <textarea class="form-control" id="fatores_influencia_aplicacao" name="fatores_influencia_aplicacao" rows="3" placeholder=""></textarea>
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
                                        <label for="efetivo_realizou" style="font-weight: bold;padding-right: 5px;">Efetivo que realizou a prova :</label>
                                        <div class="col-sm-4 custom-control-inline" style="padding: 0px;width:75px;">
                                            <input type="number" class="form-control" id="efetivo_realizou" name="efetivo_realizou" placeholder="0" min="0" readonly>
                                        </div>
                                    </div>
                                    <div class="col" style="padding:0px;">
                                        <label for="efetivo_termino" style="font-weight: bold;padding-right: 5px;">Efetivo na sala ao término do tempo :</label>
                                        <div class="col-sm-4 custom-control-inline" style="padding: 0px;width:75px;">
                                            <input type="number" class="form-control" id="efetivo_termino" name="efetivo_termino" placeholder="0" min="0">
                                        </div>
                                    </div>
                                </div>
                                
                        </fieldset>
                        <!--
                            Fim Efetivos
                        -->

                        <!-- 
                            Sequência de entrega
                        -->
                        <fieldset style="border:1px solid #ced4da;padding:0 1.4em 1.4em 1.4em;margin-top: 10px;">
                            <legend style="font-size:0.9rem;">Sequência de entrega</legend>

                                    <div class="row">
                                        <div class="col-4" style="text-align: center;">
                                            <label style="font-weight:bold;"></label>
                                        </div>
                                        <div class="col-3" style="text-align: center;">
                                            <label style="font-weight:bold;">Tempo</label>
                                        </div>
                                        <div class="col-5" style="text-align: center;">
                                            <label style="font-weight:bold;">Discente</label>
                                        </div>
                                    </div>

                                    <div class="row" style="text-align: right;">
                                        <div class="col-7">
                                            <div class="custom-control-inline">
                                                <label for="tempo_primeiro_discente" style="font-weight:bold;padding-right: 5px;">Primeiro discente :</label>
                                            </div> 
                                            <div class="col-sm-4 custom-control-inline" style="padding:0px; width:120px;margin-bottom:5px;">
                                                <input type="time" class="form-control" style="text-align:end;" id="tempo_primeiro_discente" value="" name="tempo_primeiro_discente" autocomplete="off" required>
                                            </div>
                                            <div class="clear"></div>

                                            <div class="custom-control-inline">
                                                <label for="tempo_segundo_discente" style="font-weight:bold;padding-right: 5px;">Segundo discente :</label>
                                            </div>
                                            <div class="col-sm-4 custom-control-inline" style="padding:0px; width:120px; margin-bottom:5px;">
                                                <input type="time" class="form-control" style="text-align:end;" id="tempo_segundo_discente" value="" name="tempo_segundo_discente" autocomplete="off" required>
                                            </div>
                                            <div class="clear"></div>

                                            <div class="custom-control-inline">
                                                <label for="tempo_terceiro_discente" style="font-weight:bold;padding-right: 5px;">Terceiro discente :</label>
                                            </div>
                                            <div class="col-sm-4 custom-control-inline" style="padding:0px; width:120px;margin-bottom:5px;">
                                                <input type="time" class="form-control" style="text-align:end;" id="tempo_terceiro_discente" value="" name="tempo_terceiro_discente" autocomplete="off" required>
                                            </div>
                                            <div class="clear"></div>

                                            <div class="custom-control-inline">
                                                <label for="tempo_maioria_efetivo" style="font-weight:bold;padding-right: 5px;">Maioria (Meta da turma + 1) :</label>
                                            </div>
                                            <div class="col-sm-4 custom-control-inline" style="padding:0px; width:120px;margin-bottom:5px;">
                                                <input type="time" class="form-control" style="text-align:end;" id="tempo_maioria_efetivo" value="" name="tempo_maioria_efetivo" autocomplete="off" required>
                                            </div>
                                            <div class="clear"></div>

                                            <div class="custom-control-inline">
                                                <label for="tempo_todo_efetivo" style="font-weight:bold;padding-right: 5px;">Todo efetivo :</label>
                                            </div>
                                            <div class="col-sm-4 custom-control-inline" style="padding:0px; width:120px;margin-bottom:5px;">
                                                <input type="time" class="form-control" style="text-align:end;" id="tempo_todo_efetivo" value="" name="tempo_todo_efetivo" autocomplete="off" required>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="custom-control-inline" style="width: 100%;margin-bottom:5px;">
                                                <select name="primeiro_discente" class="selectpicker custom-select">
                                                </select>
                                            </div>
                                            <div class="clear"></div>

                                            <div class="custom-control-inline" style="width: 100%;margin-bottom:5px;">
                                                <select name="segundo_discente" class="selectpicker custom-select">
                                                </select>
                                            </div>
                                            <div class="clear"></div>

                                            <div class="custom-control-inline" style="width: 100%;margin-bottom:5px;">
                                                <select name="terceiro_discente" class="selectpicker custom-select">
                                                </select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                        </fieldset>
                        <!-- 
                            Fim Sequência de entrega
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

    $('form#parametrizacao_rap select[name="turmaID"]').on('change', function(evt){
        evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

        $.ajax({
            url: '{{$rotaViewListagemTurma}}' + evt.target.value,
            beforeSend:function(){
                $('form#parametrizacao_rap #container-alunos').empty();
                $('form#parametrizacao_rap div#container-alunos-motivos').empty();
                $('div.alert.errors-rap').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
            }
        }).done(function(data){
            $('form#parametrizacao_rap #container-alunos').html('<label style="color:#ff0505">(*) Marque os alunos que faltaram</label>');
            $('form#parametrizacao_rap #container-alunos').append(data);

            setarEfetivoRealizouProva($('form#parametrizacao_rap div#container-alunos input[type=checkbox]').length);
            preencheComboAlunos();

            $('form#parametrizacao_rap div#container-alunos').on('change', 'input[type=checkbox]', function(evt) {
                evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
                var checkbox = $(this);
                
                if (checkbox.is(':checked')){
                    var label = $("form#parametrizacao_rap label[for='"+checkbox.attr('id')+"']");

                    //Caso exista remove para adicionar uma nova.
                    $('form#parametrizacao_rap div#container-alunos-motivos div#id_'+checkbox.attr('id')).remove();
                    $('form#parametrizacao_rap div#container-alunos-motivos').append('<div id="id_'+checkbox.attr('id')+'" style="margin: 14px auto;"><label>'+label.text()+'</label><input type="text" class="form-control" name="id_'+checkbox.attr('id')+'" placeholder="Informe aqui o motivo da falta."><div class="clear"></div></div>');
                }else{
                    $('form#parametrizacao_rap div#container-alunos-motivos div#id_'+checkbox.attr('id')).remove();
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
</script>
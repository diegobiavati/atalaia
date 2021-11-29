<div class="modal-header" style="background-color: #F2F2F2">
    <i class="ion-ios-compose" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Adicionar Avaliação de Recuperação
</div>

<div class="modal-body">
    <div class="alert alert-danger errors-adicionar-avaliacoes" role="alert"></div>
    <form id="adicionar_avaliacao_recuperacao">
        @csrf
        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
            <div style="float: left; margin-top: 6px;">
                <i class="ion-ios-bookmarks" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <select class="custom-select" name="disciplinas_id" onchange="loadDialogChamadas(this);" {{ (isset($avaliacao) ? 'readonly' : '') }}>
                <option value="0" disabled selected hidden>Selecione uma Disciplina</option>
                @foreach ($disciplinas as $disciplina)
                <option value={{$disciplina->id}} {{ (isset($avaliacao) && $avaliacao->disciplinas_id == $disciplina->id) ? 'selected' : ''}}>{{ $disciplina->nome_disciplina }}</option>
                @endforeach
                </select>
            </div>
            <div class="clear"></div>
        </div>

        <div class="input_abdominal"></div>

        <div style="margin: 14px auto; width: 70%; max-width: 380px; display:none;">
            <div style="float: left;">
                <i class="ion-ios-compose" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <input class="no-style" style="width: 100%;" name="nome_completo" type="text" value="{{ $avaliacao->nome_completo or old('nome_completo') }}" maxlength="100" autocomplete="off" placeholder="Nome completo da avaliação" />
            </div>
            <div class="clear"></div>
        </div>
        <div style="margin: 14px auto; width: 70%; max-width: 380px; display:none;">
            <div style="float: left;">
                <i class="ion-ios-compose-outline" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <input class="no-style" style="width: 100%;" name="nome_abrev" type="text" value="{{ $avaliacao->nome_abrev or old('nome_abrev') }}" maxlength="50" autocomplete="off" placeholder="Nome abreviado. Ex AC1" />
            </div>
            <div class="clear"></div>
        </div>

        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
            <div style="float: left;">
                <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <input class="no-style data_mask" style="width: 100%;" name="data_prova" type="text" value="{{ isset($avaliacao->data) ? \Carbon\Carbon::parse($avaliacao->data)->format('d/m/Y') : null}}" maxlength="10" autocomplete="off" placeholder="Data da prova" />
            </div>
            <div class="clear"></div>
        </div>
        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
            <div style="float: left;">
                <i class="ion-clock" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <input class="no-style minuto_mask" style="width: 100%;" name="hora_prova" type="text" value="{{ $avaliacao->hora or old('hora') }}" maxlength="5" autocomplete="off" placeholder="Hora da prova" />
            </div>
            <div class="clear"></div>
        </div>

        <div class="input_gbm"></div>

        <div style="margin: 14px auto; width: 70%; max-width: 380px;" data-toggle="tooltip" data-placement="right" title="Informe o prazo em dias que a UETE terá para informar o resultado obtido na avaliação">
            <div style="float: left;">
                <i class="ion-compass" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <input class="no-style" style="width: 100%;" name="prazo_nota" type="text" value="{{ $avaliacao->prazo_nota or old('prazo_nota') }}" maxlength="5" autocomplete="off" placeholder="Prazo para envio de resultado (Nº dias)" />
            </div>
            <div class="clear"></div>
        </div>
        <div style="margin: 14px auto; width: 70%; max-width: 380px;">
            <div style="float: left; margin-top: 6px;">
                <i class="ion-chatbubble-working" style="font-size: 24px; color: #696969;"></i>
            </div>
            <div style="float: right; border-bottom: 1px solid #ccc; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; ">
                <textarea class="form-control" id="observacao" name="observacao" rows="3" placeholder="Observações pertinentes a avaliação">{{ $avaliacao->observacao or old('observacao') }}</textarea>
            </div>
            <div class="clear"></div>
        </div>

    </form>
</div>

<div class="modal-footer" style="text-align: right;">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        Cancelar
    </button>
    @if(isset($avaliacao))
    <button type="button" class="btn btn-primary" onclick="EditarAvaliacaoRec({{ $avaliacao->id }});">
        Editar
    </button>
    @else
    <button type="button" class="btn btn-primary" onclick="AdicionarAvaliacaoRec();">
        Salvar
    </button>
    @endif
    <script>
        $('.minuto_mask').mask('00:00');
        $('.data_mask').mask('00/00/0000');
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        $(document).ready(function(){
            
            @isset($avaliacao)
                $('select[name=disciplinas_id]').change();
            @endisset

        });
        /* CARREGA DINAMICAMENTE DIALOGO DAS CHAMADAS (DENTRO DA MODAL ADICIONAR AVALIAÇÃO) */

        function loadDialogChamadas(select_value){
            var disciplina_id = $(select_value).val();

            var url = '/ajax/dialog-chamadas/' + disciplina_id;
            @isset($avaliacao)
                url += '/' + '{{$avaliacao->id}}';
            @endisset

             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: url,
                beforeSend: function(){
                    $('.box-dialog-chamadas').html('<div style="text-align: center;"><img src="/images/loadings/loading_01.svg" style="width: 20px;"> Carregando opções...</div>');
                },
                success: function(data){
                    $('.box-dialog-chamadas').html(data.result);
                    $('div.input_gbm').html(data.gbm);
                    $('div.input_abdominal').html(data.abdominal);
                }
            });                        
        }

        // ADICIONAR AVALIAÇÃO DE RECUPERAÇÃO

        function AdicionarAvaliacaoRec() {
            var dataForm = $('form#adicionar_avaliacao_recuperacao').serialize();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/adicionar-avaliacao-recuperacao',
                beforeSend: function() {
                    $('div.errors-adicionar-avaliacoes ul').remove().parent().hide();
                },
                success: function(data) {
                    if (data.data_prova == 'err') {
                        $('html, body').animate({
                            scrollTop: 10
                        }, 200);
                        $('div.errors-adicionar-avaliacoes').html('<strong>ATENÇÃO: </strong> A data da prova não deve ser menor que o prazo para lançamento do Pronto de Faltas e Grau escolar. Por favor, aumente o prazo para UETE lançar os resultados ou altere a data da avaliação.').slideDown();
                    } else if (data.data_prova == 'err1') {
                        $('html, body').animate({
                            scrollTop: 10
                        }, 200);
                        $('div.errors-adicionar-avaliacoes').html('<strong>ATENÇÃO: </strong> A avaliação deve ser criada mais próxima de sua realização.').slideDown();
                    } else if (data.data_prova == 'err2') {
                        $('html, body').animate({
                            scrollTop: 10
                        }, 200);
                        $('div.errors-adicionar-avaliacoes').html('<strong>ATENÇÃO: </strong> A data/hora informada é inválida.').slideDown();
                    } else {
                        if (data.err_chamada == 'err') {
                            $('html, body').animate({
                                scrollTop: 10
                            }, 200);
                            $('div.errors-adicionar-avaliacoes').html('<strong>ATENÇÃO: </strong> Novas chamadas devem ter uma outra avaliação de referência.').slideDown();
                        } else {
                            if (data.status == 'ok') {
                                $('div#modalDinamica').modal('hide');
                                $('a#avaliacoes').trigger('click');
                                setTimeout(function() {
                                    $('button#disciplina_' + data.disciplinaID).trigger('click');
                                    $('blockquote#disciplina_' + data.disciplinaID).show();
                                    $('blockquote#disciplina_' + data.disciplinaID + ' footer').html('Uma avaliação criada agora mesmo!');
                                    setTimeout(function() {
                                        $('blockquote#disciplina_' + data.disciplinaID).fadeOut();
                                        $('blockquote#disciplina_' + data.disciplinaID + ' footer').empty();
                                    }, 10000);
                                }, 460);
                            } else {
                                $('div#modalDinamica').modal('hide');
                                $('div.errors-adicionar-avaliacoes2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar inserir uma avaliação').slideDown();
                            }
                        }
                    }
                },
                error: function(jqxhr) {
                    if (jqxhr.status == 500) {
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-avaliacoes2').html('<strong>ATENÇÃO: </strong> Houve um erro interno ao tentar inserir uma nova avaliação. Por favor, repita a operação.').slideDown();
                    } else if (jqxhr.status == 422) {
                        $('div.errors-adicionar-avaliacoes').slideDown(100);
                        var errors = $.parseJSON(jqxhr.responseText);
                        $('div.errors-adicionar-avaliacoes').prepend('<ul style="margin: 0 6px;"></ul>');
                        $.each(errors.errors, function(index, value) {
                            $('div.errors-adicionar-avaliacoes ul').append('<li>' + value + '</li>');
                        });
                    }
                }
            });
        }

        //EDITAR AVALIAÇÃO

        function EditarAvaliacaoRec(id){
            var dataForm = $('form#adicionar_avaliacao_recuperacao').serialize();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/editar-avaliacao/' + id,
                beforeSend: function(){
                    $('div.errors-editar-avaliacoes ul').remove().parent().hide();
                },
                success: function(data){
                    //errors-editar-avaliacoes
                    if(data.data_prova=='err'){
                        $('div.errors-editar-avaliacoes').html('<strong>ATENÇÃO: </strong> A data da prova não deve ser menor que o prazo para lançamento do Pronto de Faltas e Grau escolar. Por favor, aumente o prazo para UETE lançar os resultados ou altere a data da avaliação.').slideDown();     
                    } else if(data.data_prova=='err1'){
                        $('div.errors-editar-avaliacoes').html('<strong>ATENÇÃO: </strong> A avaliação deve ser criada mais próxima de sua realização.').slideDown();     
                    } else if(data.data_prova=='err2'){
                        $('div.errors-editar-avaliacoes').html('<strong>ATENÇÃO: </strong> A data/hora informada é inválida.').slideDown();     
                    } else {
                        if(data.status=='ok'){
                            $('div#modalDinamica').modal('hide');
                            $('a#avaliacoes').trigger('click');
                            setTimeout(function(){
                                $('button#disciplina_' + data.disciplinaID).trigger('click');
                                $('blockquote#disciplina_' + data.disciplinaID).show();
                                $('blockquote#disciplina_' + data.disciplinaID + ' footer').html('Uma avaliação atualizada agora mesmo!');
                                setTimeout(function(){
                                    $('blockquote#disciplina_' + data.disciplinaID).fadeOut();
                                    $('blockquote#disciplina_' + data.disciplinaID + ' footer').empty();
                                }, 10000);
                            }, 460);
                        } else {
                            $('div#modalDinamica').modal('hide');
                            $('div.errors-adicionar-avaliacoes2').html('<strong>ATENÇÃO: </strong> Houve um erro ao tentar editar a avaliação').slideDown();    
                        }
                    }
                },
                error: function(jqxhr){
                    if(jqxhr.status==500){
                        $('div#modalDinamica').modal('hide');
                        $('div.errors-adicionar-avaliacoes2').html('<strong>ATENÇÃO: </strong> Houve um erro interno ao tentar inserir uma nova avaliação. Por favor, repita a operação.').slideDown();    
                    } else if(jqxhr.status==422){
                        $('div.errors-editar-avaliacoes').slideDown(100);
                        var errors = $.parseJSON(jqxhr.responseText);
                        $('div.errors-editar-avaliacoes').prepend('<ul style="margin: 0 6px;"></ul>');                            
                        $.each(errors.errors, function (index, value) {
                            $('div.errors-editar-avaliacoes ul').append('<li>' + value + '</li>');
                        });  
                    }
                }                    
            });            
        }
    </script>
</div>
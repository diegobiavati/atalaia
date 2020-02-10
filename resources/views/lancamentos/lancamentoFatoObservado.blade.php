<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/bootstrap-datepicker.pt-BR.min.js"></script>
<link href="/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />

<form id="lancamentoFatoObservado">
    <div class="alert alert-danger errors-lancamento-fo" role="alert"></div>
    <div class="alert alert-success success-lancamento-fo" role="alert"></div>

    <input type="hidden" name="_token" value="{{csrf_token()}}" />
    <input type="hidden" name="rotaTurma" value="{{$rotaTurma}}" />

    {!! App\Http\Controllers\Utilitarios\FuncoesController::retornaBotaoAnoFormacao() !!}

    <div style="width: 40%; margin: 22px auto; text-align: center; border-bottom: 0px solid #ccc;">
        <div style="margin-bottom: 15px;">
            <div>
                <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(250, 235, 215);">Uete</label>
            </div>
            <div>
                <select name="omctID" class="custom-select required_to_show_button">
                    <option value="0" disabled selected hidden>Selecione uma UETE</option>
                    @foreach ($uetes as $uete)
                    <option value={{$uete->id}}>{{ $uete->omct }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div style="margin-bottom: 15px;">
            <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(250, 235, 215);">Observador
                <input class="form-control" style="display:block; text-align:center;" name="observador" value="{{ session()->get('nomeOperador') }}" autocomplete="off" readonly />
            </label>
        </div>
        <div style="margin-bottom: 15px;">
            <div><label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(250, 235, 215);">Data da Observação <font style="color:rgb(255, 0, 0);font-size: smaller;">*Se nenhuma data for marcada, será registrada a data atual!</font></label></div>
            <div id="datepicker" style="display: flex;justify-content: center;margin-top: 10px;"></div>
        </div>
        <div style="margin-bottom: 15px;">
            <div>
                <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(250, 235, 215);">Tipo de FO
            </div>

            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" class="custom-control-input" id="radio1" name="radioTipoFO">
                <label class="custom-control-label" for="radio1">Negativo</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" class="custom-control-input" id="radio2" name="radioTipoFO">
                <label class="custom-control-label" for="radio2">Neutro</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" class="custom-control-input" id="radio3" name="radioTipoFO">
                <label class="custom-control-label" for="radio3">Positivo</label>
            </div>
            </label>
        </div>

        <div style="margin-bottom: 15px;">
            <div>
                <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(250, 235, 215);">Observações sobre o Fato
                    <br>
                    <font style="color:rgb(255, 0, 0);font-size: xx-small;">*O comportamento observado deve ser descrito e sua ligação com o objetivo previsto no PLADIS (quem, o que e como).</font>
                </label>
            </div>
            <div>
                <textarea class="form-control" name="textAreaObservacaoFO" rows="3" style="display: inline;"></textarea>
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <div>
                <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(250, 235, 215);">Conteúdo Atitudinal
                    <br>
                    <font style="color:rgb(255, 0, 0);font-size: xx-small;">*Deve ser citado o conteúdo atitudinal que está relacionado com o comportamento observado.</font>
                </label>
            </div>
            <div>
                @foreach($conteudoAtitudinal as $conteudo)
                <div class="custom-control custom-checkbox custom-control-inline" style="width: 30%;text-align:left;margin-right:0px">
                    <input type="checkbox" class="custom-control-input" id="atitudinal{{$conteudo->id}}">
                    <label class="custom-control-label" for="atitudinal{{$conteudo->id}}">{{$conteudo->descricao}}</label>
                </div>
                @endforeach
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <div>
                <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(250, 235, 215);">Providências
                    <br>
                    <font style="color:rgb(255, 0, 0);font-size: xx-small;">*É descrito o tipo de orientação que o aluno recebeu em relação ao seu comportamento ("feedback").</font>
                </label>
            </div>
            <div>
                <textarea class="form-control" name="textAreaProvidencias" rows="3" style="display: inline;"></textarea>
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <div>
                <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(250, 235, 215);">Selecionar Turma</label>
            </div>
            <div>
                <select name="turmaID" class="custom-select">
                    <option value="0" disabled selected hidden>Selecione uma Turma</option>
                    @foreach ($turmas as $turma)
                    <option value={{$turma->id}}>{{ $turma->turma }}</option>
                    @endforeach
                </select>
            </div>
            <div id="container-turma"></div>
        </div>

        <div style="margin-top:24px;">
            <button type="button" class="btn btn-primary" style="display: none;">Registrar Fato Observado</button>
        </div>
    </div>
    <script>
        $(document).ready(function() {

            $('div.errors-lancamento-fo').empty().hide();
            $('div.success-lancamento-fo').empty().hide();

            $('div#datepicker').datepicker({
                format: "dd/mm/yyyy",
                maxViewMode: 0,
                language: "pt-BR"
            });

            $(document).on('change', 'select.required_to_show_button', function() {
                $('select.required_to_show_button').each(function(index, element) {
                    if ($(element).val() == 0) {
                        $('button.btn.btn-primary').slideUp(100);
                        return false;
                    }
                    $('button.btn.btn-primary').slideDown(100);
                });

            });

            $('button.btn.btn-primary').click(function() {

                var formData = $('form#lancamentoFatoObservado').serialize();
                var url = "ajax/lancamentos";

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

                        $('div.success-lancamento-fo').html(data.response).slideDown();

                        setTimeout(function() {
                            $('div.success-lancamento-fo').slideUp(200, function() {
                                $(this).removeClass('alert-success').empty();
                            });
                        }, 3000);

                    },
                    error: function(jqxhr) {
                        $('div.errors-lancamento-fo').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
                    }
                });
            });

            $(document).on('change', 'select.custom-select[name="turmaID"]', function() {

                dataPost = '_token=' + $('input[name="_token"]').val() +
                    '&turmaID=' + $(this).children("option:selected").val() +
                    '&omctID=' + $('select.custom-select[name="omctID"]').children("option:selected").val() +
                    '&anoFormacaoID=' + $('input[name="ano_formacao"]:checked').val();

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
        });
    </script>
</form>
<form id="parametrosAtalaia">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <div class="alert alert-danger errors-parametros" role="alert"></div>
    <div class="alert alert-success success-parametros" role="alert"></div>

    <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc; padding-bottom:10px;"></div>
    <div style="text-align: center; margin-top: 22px; color:  #696969;">
        <h4>Candidatos Aguardando Aprovação</h4>
    </div>

    <div style="margin: 44px auto; width: 530px; text-align: center; margin-top: 30px;">
        <div>
            <input type="hidden" name="idParametro" value="{{$parametros->id or old('id') }}" />
        </div>
        <div>
            <textarea class="form-control" name="textCandidatosAguardando" rows="3" style="display: inline;">{{$parametros->candidato_aguar_aprov or old('candidato_aguar_aprov')}}</textarea>
        </div>
        <div style="margin-top:24px;">
            <button id="btnSalvar" type="button" class="btn btn-primary">Salvar</button>
        </div>
    </div>
    <script>
        $(document).ready(function() {

            $('div.errors-parametros').empty().hide();
            $('div.success-parametros').empty().hide();

            $('#btnSalvar.btn.btn-primary').click(function(evt) {
                evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

                var formData = $('form#parametrosAtalaia').serialize();
                formData = formData + '&anoFormacao=' + $('.btn.btn-secondary.active input[name="ano_formacao"]').val();

                $.ajax({
                    dataType: 'json',
                    url: "ajax/parametros",
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('div.errors-parametros').empty().hide();
                        $('div.success-parametros').empty().hide();
                    },
                    success: function(data) {

                        if (data.status == 'erro') {
                            $('div.errors-parametros').html(data.response).slideDown();
                        } else {
                            $('div.success-parametros').html(data.response).slideDown();

                            setTimeout(function() {
                                $('div.success-parametros').slideUp(200, function() {
                                    $(this).removeClass('alert-success').empty();
                                    $('div#full-modal').modal('hide');
                                });
                            }, 3000);

                            
                        }
                    },
                    error: function(jqxhr) {
                        $('div.errors-parametros').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
                    }
                });
            });

            carregaOpcaoParametros('parametros', 'rod|'+$('input[name=\"ano_formacao\"]:checked').val());
        });
    </script>
</form>
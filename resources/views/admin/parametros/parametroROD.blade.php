<form id="parametrosROD">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <div class="alert alert-danger errors-parametrosROD" role="alert"></div>
    <div class="alert alert-success success-parametrosROD" role="alert"></div>

    <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc; padding-bottom:10px;"></div>
    <div style="text-align: center; margin-top: 22px; color:  #696969;">
        <h4>(ROD) Conteúdos Atitudinais</h4>
    </div>

    <div style="margin: 44px auto; width: 530px; text-align: center; margin-top: 30px;">
        <div>
            <input type="hidden" name="idParametro" value="{{$parametros->id or old('id') }}" />
        </div>

        <div style="width: 90%; margin: 22px auto; text-align: center;">
            
            <div class="clear"></div>
            @foreach ($conteudoAtitudinal as $item)
            <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                <input type="checkbox" class="custom-control-input" id="conteudo_{{$item->id}}" name="conteudo[]" value="{{$item->id}}" {{ (isset($parametros->conteudo_atitudinal_rod) && in_array($item->id, json_decode($parametros->conteudo_atitudinal_rod)) ? 'checked' : '') }}/>
                <label class="custom-control-label" for="conteudo_{{$item->id}}">{{$item->descricao}}</label>
            </div>
            @endforeach
        </div>

        <div style="margin-top:24px;">
            <button id="btnSalvarROD" type="button" class="btn btn-primary">Salvar</button>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            $('div.errors-parametrosROD').empty().hide();
            $('div.success-parametrosROD').empty().hide();

            $('#btnSalvarROD.btn.btn-primary').click(function(evt) {
                evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

                var formData = $('form#parametrosROD').serialize();
                formData = formData + '&anoFormacao=' + $('.btn.btn-secondary.active input[name="ano_formacao"]').val();
                
                $.ajax({
                    dataType: 'json',
                    url: "ajax/rod-conteudo-atitudinal",
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('div.errors-parametrosROD').empty().hide();
                        $('div.success-parametrosROD').empty().hide();
                    },
                    success: function(data) {

                        if (data.status == 'erro') {
                            $('div.errors-parametrosROD').html(data.response).slideDown();
                        } else {
                            $('div.success-parametrosROD').html(data.response).slideDown();

                            setTimeout(function() {
                                $('div.success-parametrosROD').slideUp(200, function() {
                                    $(this).removeClass('alert-success').empty();
                                    $('div#full-modal').modal('hide');
                                });
                            }, 3000);
                        }
                    },
                    error: function(jqxhr) {
                        $('div.errors-parametrosROD').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
                    }
                });
            });
        });
    </script>
</form>
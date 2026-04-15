<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<style>
    .ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 9999;
        background: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .ui-autocomplete {
    max-height: 200px;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 9999;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    word-wrap: break-word;
    white-space: normal;
}
    .ui-autocomplete .ui-menu-item-wrapper.ui-state-active {
        background-color: rgb(121, 161, 212);
        color: #fff;
        border: none;
    }
</style>
<form id="lancamentoFATD">
    <input type="hidden" name="_token" value="{{csrf_token()}}" />

    <div style="width: 40%; margin: 22px auto; text-align: center; border-bottom: 0px solid #ccc;">

        <div style="margin-bottom: 15px;">
            <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(121, 161, 212);">
                <font style="color:rgb(255, 255, 255);">Sargenteante</font>
                <input class="form-control" style="display:block; text-align:center;" name="sargenteante" value="{{ (isset($fatd->sargenteante) ? $fatd->sargenteante->posto->postograd_abrev.' '.$fatd->sargenteante->nome_guerra : session()->get('nomeOperador'))  }}" autocomplete="off" readonly />
            </label>
        </div>

        <div style="margin-bottom: 15px;">
            <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(121, 161, 212);">
                <font style="color:rgb(255, 255, 255);">Aluno</font>
                <input class="form-control" style="display:block; text-align:center;font-weight:bold;" name="aluno" value="{{$fatd->lancamentoFo->aluno->numero.' - '.$fatd->lancamentoFo->aluno->nome_guerra}}" autocomplete="off" readonly />
            </label>
        </div>

        <div id="btnJustificado" class="btn-group btn-group-toggle" data-toggle="buttons">
            <label class="btn btn-outline-info {{ (isset($fatd->justificado) && $fatd->justificado == 'S')  ? 'active': ''}}">
                <input type="radio" name="justificado" value="S">
                Justificado
            </label>
            <label class="btn btn-outline-info {{ (isset($fatd->justificado) && $fatd->justificado == 'N')  ? 'active': ''}}">
                <input type="radio" name="justificado" value="N">
                Não Justificado
            </label>
        </div>

        <div id="divNJustificado" style="margin-top: 30px;padding: 0 0px 5px 5px;display:none;">
            <div class="btn-group btn-group-toggle" data-toggle="buttons" style="padding-bottom:10px;">
                @foreach ($enquadramentos as $enquadramento)
                <label class="btn btn-outline-info {{ (isset($fatd->enquadramento_id) && $fatd->enquadramento_id == $enquadramento->id)  ? 'active': ''}}" style="font-size: 13px;">
                    <input type="radio" name="enquadramento_id" id="enquadramento_{{$enquadramento->id}}" value="{{$enquadramento->id}}" autocomplete="off">{{$enquadramento->enquadramento_esa}}
                </label>
                @endforeach
            </div>

            <div class="card" style="display:none;">
                <div data-parent="#divNJustificado" style="padding: 10px;background-color:rgb(237,236,228);">
                    <div class="form-group">
                        <label for="enquadramento">Enquadramento NASE</label>
                        <textarea class="form-control" name="enquadramento" id="enquadramento" rows="3">{{$fatd->enquadramento}}</textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="bi_desc">BI</label>
                            <input type="text" class="form-control" name="bi_desc" id="bi_desc" value="{{$fatd->bi_desc}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="dt_bi">Data BI</label>
                            <input id="dt_bi" name="dt_bi" value="{{ isset($fatd->dt_bi) ? \Carbon\Carbon::parse($fatd->dt_bi)->format('d/m/Y') : null }}" class="form-control data_mask" style="text-align:center;" type="text" maxlength="10" autocomplete="off" placeholder="dd/mm/AAAA">
                        </div>
                        <div id="div_nr_dias" class="form-group col-md-3">
                            <label for="nr_dias">Nº Dias</label>
                            <input id="nr_dias" name="nr_dias" value="{{$fatd->nr_dias}}" class="form-control" type="number">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="comportamento_id">Comportamento</label>
                            <select class="custom-select" name="comportamento_id" id="comportamento_id">
                                <option value="0" disabled selected hidden>Comportamento</option>
                                @foreach ($comportamentos as $comportamento)
                                <option {{ (isset($fatd->comportamento_id) && $fatd->comportamento_id == $comportamento->id)  ? 'selected': ''}} value={{$comportamento->id}}>{{$comportamento->comportamento}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-danger errors-lancamento-fatd" role="alert"></div>
        <div class="alert alert-success success-lancamento-fatd" role="alert"></div>

        <div style="margin-top:24px;">
            <button id="btnAtualizaFATD" type="button" class="btn btn-primary">Atualizar FATD</button>
        </div>
    </div>
    <script>
        $(document).ready(function() {

            $('div.errors-lancamento-fatd').empty().hide();
            $('div.success-lancamento-fatd').empty().hide();

            $('.data_mask').mask('00/00/0000');
            var enquadramentosNase = @json($enquadramentosNase);

           $('#enquadramento').autocomplete({
    source: function(request, response) {
        var results = $.ui.autocomplete.filter(enquadramentosNase, request.term);
        response(results.slice(0, 10));
    },
    minLength: 3,
    select: function(event, ui) {
        $('#enquadramento').val(ui.item.value);
    },
    open: function() {
        var width = $('#enquadramento').outerWidth();
        $('.ui-autocomplete').css('width', width + 'px');
    }
});
            $('#btnJustificado .btn').on('click', function(evt) {
                var val = $(this).find('input').val();
                if (val == 'N') {
                    $('div#divNJustificado').show();
                } else {
                    $('div#divNJustificado div.card').find('input').val(null);
                    $('div#divNJustificado div.card').find('textarea').val(null);
                    $('div#divNJustificado div.card').hide();
                    $('div#divNJustificado').hide();
                }
            });

            //pegar valor data-toogle
            $('#divNJustificado div[data-toggle="buttons"] :input').change(function() {
                var val = $(this).val();
                if ($('#enquadramento_' + val).val() == 1 || $('#enquadramento_' + val).val() == 3) {
                    $('#div_nr_dias').hide();
                } else {
                    $('#div_nr_dias').show();
                }
                $('div#divNJustificado div.card').show();
            });

            @if(isset($fatd->justificado) && $fatd->justificado == 'N')
            $('div#divNJustificado').show();
            $('div#divNJustificado div.card').show();
            //Efetua o evento de click para sumir ou não com o botão nº dias
            $('#divNJustificado div[data-toggle="buttons"] :input#enquadramento_{{$fatd->enquadramento_id}}').click()
            @endif

            $('#btnAtualizaFATD.btn.btn-primary').click(function(evt) {
                evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

                var formData = $('form#lancamentoFATD').serialize();

                $.ajax({
                    url: '/ajax/fatdSargenteante/{{$fatd->lancamento_fo_id}}',
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('div.errors-lancamento-fatd').empty().hide();
                        $('div.success-lancamento-fatd').empty().hide();
                    },
                    success: function(data) {

                        if (data.status == 'err') {
                            $('div.errors-lancamento-fatd').html('<strong>ATENÇÃO:</strong><br />').slideDown();
                            $.each(data.response, function(key, value) {
                                $('div.errors-lancamento-fatd').append('<li>' + value + '</li>');
                            });
                        } else {
                            $('div.success-lancamento-fatd').html(data.response).slideDown();

                            setTimeout(function() {
                                $('div.success-lancamento-fatd').slideUp(200, function() {
                                    $(this).removeClass('alert-success').empty();
                                    $('div#full-modal').modal('hide');
                                });
                            }, 3000);
                        }
                    },
                    error: function(jqxhr) {
                        $('div.errors-lancamento-fatd').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
                    }
                });
            });
        });
    </script>
</form>
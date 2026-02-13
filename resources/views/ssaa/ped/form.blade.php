<style>
/*div .row .col-sm .quadro-gbm{
        border: solid #dcfb05 1px;
        margin: auto;
        background-color: #e3e68e;
        margin-top: 20px;
    }*/

/*.font-color-qms{
        color: {{ isset($cursoSelecionado) ? App\Http\Controllers\Utilitarios\FuncoesController::getQmsColor($cursoSelecionado->qms_matriz_id)->backgroundColor : '#000' }};
    }*/

.message_feedback {
    background-color: {
            {
            isset($cursoSelecionado) ? App\Http\Controllers\Utilitarios\FuncoesController: :getQmsColor($cursoSelecionado->qms_matriz_id)->backgroundColor: '#f00'
        }
    }

    ;
    color: white;
    padding: 5px;
}

th.editablegrid-id_esa_avaliacoes,
td.editablegrid-id_esa_avaliacoes {
    display: none;
}

div .toolbar {
    margin-bottom: 10px;
}

.col-sm {
    height: 40px;
}

.container {
    min-width: 16rem;
    max-width: 70rem;
    margin-right: auto;
    margin-left: auto;
    margin-top: 35px;
}

.container-header {
    padding: 20px;
    height: 8rem;
    margin-bottom: 0.5rem;
    background-color: #c2c0c0;
    border-radius: 0.25rem;
}

.container-side {
    float: right;
    border-radius: 0.25rem;
    padding: 10px;

    border: solid 2px {
            {
            isset($cursoSelecionado) ? App\Http\Controllers\Utilitarios\FuncoesController: :getQmsColor($cursoSelecionado->qms_matriz_id)->backgroundColor: '#f00'
        }
    }

    ;
}

.container-body {
    background-color: #ffffff52;
    border-radius: 0.25rem;
}

div#imagem_curso {
    background: url("{{ asset('/images/logo_esa.png') }}") no-repeat center center;
    width: 100px;
    height: 100px;
    background-size: contain;
    margin-top: -5px;
}
</style>
<div class="container-ped">
    <div class="alert alertas-ped" role="alert"></div>

    <div class="container-header">
        <div class="row" style="height:105px;">
            <div class="col-sm" id='imagem_curso'></div>
            <div class="col-sm" style="flex: 5; height:105px;">
                <div class="row" style="padding: 5px;margin-top: 20px;">
                    <div class="col-sm text-right"
                        style="max-width: 65px; line-height: 3;font-weight: bold;padding-right: 0px;">PED:</div>
                    <div class="col-sm" style="max-width: 200px;">
                        @include('ajax.ssaa.componentePed')
                    </div>
                    <div class="col-sm  text-right"
                        style="max-width: 90px; line-height: 3;font-weight: bold;padding-right: 0px;">Exercício:</div>
                    <div class="col-sm" style="max-width: 300px;">
                        @include('ajax.ssaa.componenteDisciplinas')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--<div class="container-side">
        <div class="row">
            <div class="col-sm" style="margin-bottom: 10px;">
                <button type="button" class="btn btn-primary" id="btn-lancamento" onclick="javascript: void(0);">Lançamento</button>
            </div>
        </div>
        <div class="row">
            <div class="col-sm">
                <button type="button" class="btn btn-warning" id="btn-editar-lancamento" onclick="javascript: void(0);">Editar Lançamento</button>
            </div>
        </div>
    </div>-->

    <div class="container-body" style="height:auto;"></div>
</div>
<script>
$(document).ready(function() {

    $('div.alertas-ped').slideUp();

    $('div .container-body').css('display', 'none');
    //$('div .container-side').css('display', 'none');

    $('div.container-ped select.custom-select[name="pedID"]').change(function(evt) {
        evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

        url = '{{$urlPedExercicio}}/' + evt.target.value;

        $('div.container-ped.container-body').empty();
        //Carrega a combo com Uetes ou Cursos
        carregaExercicioPed(url);
    });

    function carregaExercicioPed(url) {
        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() {
                $('div.container-ped.container-body').empty();
            },
            success: function(data) {
                $('div.container-ped.container-body').html(data);
            },
            error: function(jqxhr) {
                $('div.container-ped.container-body').html(
                    '<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
            }
        });
    }

});
</script>
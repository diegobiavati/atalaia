<style>
    select[readonly] {
        background-color: {
                {
                App\Http\Controllers\Utilitarios\FuncoesController: :getQmsColor($cursoSelecionado->qms_matriz_id)->backgroundColor
            }
        }

        ;
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

    div#imagem_curso {
        background: url("{{ isset($cursoSelecionado) ? asset($cursoSelecionado->qmsMatriz->img_ssaa) : asset('/images/logo_esa.png') }}") no-repeat center center;
        height: 50px;
        background-size: contain;
        margin-top: -5px;
    }

    .carousel-control-next-icon,
    .carousel-control-prev-icon {
        filter: invert(29%) sepia(83%) saturate(596%) hue-rotate(180deg) brightness(90%) contrast(85%);
    }
</style>


<div id="esa_container">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-4">
                <i class="ion-ios-bookmarks"
                    style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i><b>Importador de
                    Disciplinas</b>
            </div>
            <div id="imagem_curso" class="col-sm-5"></div>
        </div>
    </div>
    <form id="form_importa_disciplina">
        @csrf
        <div class="card-body">
            <div class="alert alertas-disciplinas" role="alert" style="margin:10px;width:100%;"></div>


            <input type="hidden" name="cursoSelecionado" value="{{ $cursoSelecionado->id }}" />
            <div id="esa_importa_disciplinas" class="carousel slide" data-interval="false">
                <div class="carousel-inner">
                    @foreach($agrupados as $agrupado)
                    <div class="carousel-item {{ $loop->last ? 'active' : '' }}">

                        <div class="container">
                            <h2 class="text-center mb-4">{{ $agrupado->formacao }}</h2>
                            <div class="row justify-content-center">
                                @foreach($query as $linha)
                                @if($agrupado->formacao == $linha->formacao)
                                <div class="col-md-8 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="disciplinas[]"
                                            id="disciplina_{{ $linha->id }}" value="{{ $linha->id }}">
                                        <label class="form-check-label" for="disciplina_{{ $linha->id }}">
                                            {{ $linha->nome_disciplina }} ({{ $linha->carga_horaria }}h)
                                        </label>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>

                <a class="carousel-control-prev" href="#esa_importa_disciplinas" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Anterior</span>
                </a>
                <a class="carousel-control-next" href="#esa_importa_disciplinas" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Próximo</span>
                </a>
            </div>

        </div>

        <div class="card-footer" style="text-align: right;">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary importar-disciplina">Salvar</button>
        </div>
    </form>
</div>

<script>
    $('div.alertas-disciplinas').empty().hide();
    $('div#esa_container button.btn.btn-danger').click(function(evt) {
        $('div.container_disciplinas').fadeIn(1000);
    });

    $('.btn.btn-primary.importar-disciplina').click(function(evt) {
        evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

        $(this).hide();

        var formData = $('form#form_importa_disciplina').serialize();

        $.ajax({
            dataType: 'json',
            url: "{{ route('gaviao.ajax.disciplinas.importar') }}",
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('div.alertas-disciplinas').empty().hide();
                $('div.alertas-disciplinas').removeClass('alert-success').removeClass('alert-danger').empty();
            },
            success: function(data) {

                if (data.status == 'success') {
                    $('div.alertas-disciplinas').addClass('alert-success').empty().slideDown();
                    $('div.alertas-disciplinas').append('<li>' + data.response + '</li>');

                    setTimeout(function() {
                        $('div.alertas-disciplinas').slideUp(200, function() {
                            $('#form_importa_disciplina .btn.btn-danger').click();

                            //Ativa o gatilho da caixa de seleção para carregar o gerenciador de disciplinas...
                            $('div.container_disciplinas select.custom-select[name="qmsID"]')
                                .change()
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
                $('div.alertas-disciplinas').html('<strong>ATENÇÃO: </strong> Houve um erro interno')
                    .slideDown();
            }
        });

    });
</script>
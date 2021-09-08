<style>
    .table td {
        vertical-align: middle;
         !important
    }
</style>

<div style="margin-top: 64px; text-align: center; color: #696969;">
    <h5>{{ (($avaliacao->disciplinas->tfm == 'S') ? 'LANÇAMENTO DE NOTAS DE TFM INDIVIDUAL' : 'LANÇAMENTO DO GRAU ESCOLAR INDIVIDUAL') }}
        <h5>
</div>

<div style="margin: 64px auto; width: 50%;">
    <form id="ponto-de-faltas">
        @csrf
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ALUNO(A)</th>
                    <th style="text-align: center; width: 150px;">{{ (($avaliacao->disciplinas->tfm == 'S') ? 'NOTA' : 'GBO') }}</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_notas_lancadas = 0;
                ?>
                @foreach($alunos as $aluno)
                <tr onclick="$(this).find('input').focus();" style="cursor: pointer;">
                    <td style="text-align: left;">
                        <b>{{ $aluno->numero }} {{ $aluno->nome_guerra }}</b><br />
                        <span style="color: #696969;"><i>{{ $aluno->nome_completo }}</i></span>
                    </td>
                    <td id="col_aluno_{{$aluno->id}}" style="text-align: center; ">
                        @if(isset($nota[$aluno->id]))
                        <div style="width: 100%; margin: 0 auto;" data-toggle="tooltip" data-placement="right" title="Clique aqui para retificação do grau">
                            @if($avaliacao->disciplinas->tfm == 'S')
                            @if($avaliacao->tfm_abdominal == 'S')
                            @include('ajax.avaliacao.notas.view-suficiencia-abdominal')
                            @else
                            @include('ajax.avaliacao.notas.view-nota-tfm')
                            @endif
                            @else
                            <div style="float: left;">
                                <span><span style="color: #0B3B0B;"><b>GBO:</span></b> <span style="font-family: Tahoma;"> {{ (isset($nota[$aluno->id]) ? $nota[$aluno->id]['gbo'] : null) }} </span></span><br />
                                <span><span style="color: #0B3B0B;"><b>NOTA:</span></b> <span style="font-family: Tahoma;"> {{ $avaliacao->getNota($nota[$aluno->id]['gbo']) }}</span></span>
                            </div>
                            <div style="float: right; margin: 8px 10px 0 0;">
                                <a href="javascript: void(0);" class="no-style" onclick="editarRegistroGrauAluno({{$aluno->id}}, {{$request->id}});">
                                    <i class="ion-android-create" style="color: #0B610B; font-size: 18px;"></i>
                                </a>
                            </div>
                            @endif
                            <div class="clear"></div>
                        </div>
                        <?php
                        $total_notas_lancadas++;
                        ?>
                        @else
                        <div style="text-align: center;">
                            @if($avaliacao->disciplinas->tfm == 'S')
                            @if($avaliacao->tfm_abdominal == 'S')

                            @include('ajax.avaliacao.notas.view-lanca-abdominal')

                            @else

                            @include('ajax.avaliacao.notas.view-lanca-tfm')

                            @endif
                            @else
                            <input type="text" name="nota_aluno_id_{{$aluno->id}}" value="" style="width: 38px; border: 1px solid #ccc; padding: 3px 4px; text-align: right; margin-top: -10px;" onkeyup="toogleConfirmGrau(this);" autocomplete="off" maxlength="3" /><br />
                            <a href="javascript: void(0);" class="badge badge-success" onclick="registrarGrauAluno({{$aluno->id}}, {{$request->id}});" style="display: none; margin-top: 8px;">Confirmar</a>
                            <span class="badge badge-secondary" style="margin-top: 8px;">Confirma</span>
                            @endif
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach

                <?php
                $total_notas_lancadas = ($total_notas_lancadas) ?? 0;
                $data['porcento_preenchido'] = (floor(($total_notas_lancadas * 100) / count($alunos)) > 100) ? 100 : floor(($total_notas_lancadas * 100) / count($alunos));
                ?>
            </tbody>
        </table>
    </form>
    <!--<div id="status_top">
        <strong>Porcentagem preenchida:</strong><br />
        <div id="porcentagem-preenchimento" style="padding: 3px; border: 1px solid #ccc; margin-top: 4px;">
            <div style="width: {{$data['porcento_preenchido']}}%; background-color: #0B6121; text-align: center; font-size: 12px; color: #D8F6CE;">{{$data['porcento_preenchido']}}%</div>
        </div>
    </div>-->
</div>
<script>
    function registrarGrauAluno(alunoID, avaliacaoID) {
        var gbo = $('input[name="nota_aluno_id_' + alunoID + '"]').val();
        var contentTr = $('form td#col_aluno_' + alunoID).html();
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: 'ajax/registrar-grau-aluno/' + alunoID + '/' + avaliacaoID + '/' + gbo,
            beforeSend: function() {

                $('form td#col_aluno_' + alunoID).html('<div style="text-align: center;"><img src="/images/loadings/loading_04.svg" style="width: 24px; margin-top: 12px;" /></div>');
            },
            success: function(data) {
                //$('div#status_top').removeClass('visible');
                if (data.status == 'ok') {
                    $('form td#col_aluno_' + alunoID).html(data.response);
                    if (data.porcento_preenchido > 5) {
                        $('div#porcentagem-preenchimento div').css('width', data.porcento_preenchido + '%').html(data.porcento_preenchido + '%');
                        setTimeout(function() {
                            /*                                 if(!$('div#status_top').is(':visible')){
                                                                $('div#status_top').show().animate({
                                                                    'margin-top': '-84px' 
                                                                }, 400);                                
                                                            } */
                        }, 800);
                    }
                    //$('[data-toggle="tooltip"]').tooltip();                        
                } else if (data.status == 'err') {
                    $('form td#col_aluno_' + alunoID).html(contentTr);
                    $('div#modalDinamica_no_style').modal('show');
                    $('div#modalDinamica_no_style div.modal-body').html(data.response);
                }

            },
            error: function(jqxhr) {
                setTimeout(function() {
                    $('form td#col_aluno_' + alunoID).html(contentTr);
                    alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                }, 1000);
            }
        });
    }

    function editarRegistroGrauAluno(alunoID, avaliacaoID) {
        var contentTr = $('form td#col_aluno_' + alunoID).html();
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: '/ajax/editar-registro-grau-aluno/' + alunoID + '/' + avaliacaoID,
            beforeSend: function() {
                $('form td#col_aluno_' + alunoID).html('<div style="text-align: center;"><img src="/images/loadings/loading_04.svg" style="width: 24px; margin-top: 12px;" /></div>');
            },
            success: function(data) {
                //$('div#status_top').removeClass('visible');
                if (data.status == 'ok') {
                    $('form td#col_aluno_' + alunoID).html(data.response);
                    $('[data-toggle="tooltip"]').tooltip('hide');
                    if (data.porcento_preenchido > 5) {
                        $('div#porcentagem-preenchimento div').css('width', data.porcento_preenchido + '%').html(data.porcento_preenchido + '%');
                        setTimeout(function() {
                            /*                                 if(!$('div#status_top').is(':visible')){
                                                                $('div#status_top').show().animate({
                                                                    'margin-top': '-84px' 
                                                                }, 400);                                
                                                            } */
                        }, 800);
                    } else {
                        setTimeout(function() {
                            /*                                 if($('div#status_top').is(':visible')){
                                                                $('div#status_top').animate({
                                                                    'margin-top': '84px' 
                                                                }, 400);                                
                                                            } */
                        }, 800);
                    }
                } else if (data.status == 'err') {
                    $('form td#col_aluno_' + alunoID).html(contentTr);
                    $('div#modalDinamica_no_style').modal('show');
                    $('div#modalDinamica_no_style div.modal-body').html(data.response);
                }

            },
            error: function(jqxhr) {
                setTimeout(function() {
                    $('form td#col_aluno_' + alunoID).html(contentTr);
                    alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                }, 1000);
            }
        });
    }

    function toogleConfirmGrau(inputField) {
        if ($.isNumeric($(inputField).val())) {
            $(inputField).nextAll('a').first().show();
            $(inputField).nextAll('span').first().hide();
        } else {
            $(inputField).nextAll('a').first().hide();
            $(inputField).nextAll('span').first().show();
        }
    }

    function lancarAbdominal(idAvaliacao, idAluno, element) {

        var data_info = {
            "_token": "{{ csrf_token() }}",
            "idAvaliacao": idAvaliacao,
            "idAluno": idAluno,
            "suficiencia": element.value
        };

        $.ajax({
            dataType: 'json',
            type: 'POST',
            data: data_info,
            url: '/ajax/lancar-abdominal-aluno',
            beforeSend: function() {
                $('form td#col_aluno_' + idAluno).html('<div style="text-align: center;"><img src="/images/loadings/loading_04.svg" style="width: 24px; margin-top: 12px;" /></div>');
            },
            success: function(data) {
                if (data.status == 'ok') {
                    $('form td#col_aluno_' + idAluno).html(data.response);
                } else if (data.status == 'err') {
                    $('div#modalDinamica_no_style').modal('show');
                    $('div#modalDinamica_no_style div.modal-body').html(data.response);
                }
            }
        });
    }

    function editarAbdominal(idAvaliacao, idAluno) {
        var contentTr = $('form td#col_aluno_' + idAluno).html();

        var data_info = {
            "_token": "{{ csrf_token() }}",
            "idAvaliacao": idAvaliacao,
            "idAluno": idAluno
        };

        $.ajax({
            dataType: 'json',
            type: 'POST',
            data: data_info,
            url: '/ajax/editar-abdominal-aluno',
            beforeSend: function() {
                $('form td#col_aluno_' + idAluno).html('<div style="text-align: center;"><img src="/images/loadings/loading_04.svg" style="width: 24px; margin-top: 12px;" /></div>');
            },
            success: function(data) {

                if (data.status == 'ok') {
                    $('form td#col_aluno_' + idAluno).html(data.response);
                    $('[data-toggle="tooltip"]').tooltip('hide');
                } else if (data.status == 'err') {
                    $('form td#col_aluno_' + idAluno).html(contentTr);
                    $('div#modalDinamica_no_style').modal('show');
                    $('div#modalDinamica_no_style div.modal-body').html(data.response);
                }

            },
            error: function(jqxhr) {
                setTimeout(function() {
                    $('form td#col_aluno_' + idAluno).html(contentTr);
                    alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                }, 1000);
            }
        });
    }
    
    function lancarTfm(idAvaliacao, idAluno, element) {

        var data_info = {
            "_token": "{{ csrf_token() }}",
            "idAvaliacao": idAvaliacao,
            "idAluno": idAluno,
            "nota": element.value
        };

        $.ajax({
            dataType: 'json',
            type: 'POST',
            data: data_info,
            url: '/ajax/lancar-tfm-aluno',
            beforeSend: function() {
                $('form td#col_aluno_' + idAluno).html('<div style="text-align: center;"><img src="/images/loadings/loading_04.svg" style="width: 24px; margin-top: 12px;" /></div>');
            },
            success: function(data) {
                if (data.status == 'ok') {
                    $('form td#col_aluno_' + idAluno).html(data.response);
                } else if (data.status == 'err') {
                    $('div#modalDinamica_no_style').modal('show');
                    $('div#modalDinamica_no_style div.modal-body').html(data.response);
                }
            }
        });
    }

    function editarTfm(idAvaliacao, idAluno) {
        var contentTr = $('form td#col_aluno_' + idAluno).html();

        var data_info = {
            "_token": "{{ csrf_token() }}",
            "idAvaliacao": idAvaliacao,
            "idAluno": idAluno
        };

        $.ajax({
            dataType: 'json',
            type: 'POST',
            data: data_info,
            url: '/ajax/editar-tfm-aluno',
            beforeSend: function() {
                $('form td#col_aluno_' + idAluno).html('<div style="text-align: center;"><img src="/images/loadings/loading_04.svg" style="width: 24px; margin-top: 12px;" /></div>');
            },
            success: function(data) {

                if (data.status == 'ok') {
                    $('form td#col_aluno_' + idAluno).html(data.response);
                    $('[data-toggle="tooltip"]').tooltip('hide');
                } else if (data.status == 'err') {
                    $('form td#col_aluno_' + idAluno).html(contentTr);
                    $('div#modalDinamica_no_style').modal('show');
                    $('div#modalDinamica_no_style div.modal-body').html(data.response);
                }

            },
            error: function(jqxhr) {
                setTimeout(function() {
                    $('form td#col_aluno_' + idAluno).html(contentTr);
                    alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                }, 1000);
            }
        });
    }
</script>
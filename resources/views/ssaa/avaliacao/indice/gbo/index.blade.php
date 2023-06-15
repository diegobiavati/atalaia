<div id="lancamento-gbo">
    <div class="row">
        <div class="col-sm" id='imagem_curso' style="height: 45px;max-width: 105px;margin-top: 0;"></div>
        <div class="col-sm" style="height: 45px;line-height: 3;font-weight: bold;">
            Processo Nº: <span class="badge badge-info" style="font-size: large;"> {{ $esaAvaliacoes->id }} / {{ $esaAvaliacoes->esadisciplinas->qms->escolhaQms->anoFormacao->ano_cfs }} </span> 
        </div>
        
    </div>
    

    <div class="container-lancamento-gbo">
        <div class="alert alertas-lancamento-gbo" role="alert"></div>
        
        <div class="container-header" style="height: auto;margin:0px 5px 5px;padding:20px 10px 20px 10px;">
            <div class="row" style="height:105px;">
                <div class="col-sm" style="flex: 5; height:105px;">
                    <div class="row" style="padding: 5px;">
                        <div class="col-sm" style="max-width: 65px; line-height: 3;font-weight: bold;text-align: right;">Turma:</div>
                        <div class="col-sm" style="max-width: 120px;">
                            @include('ajax.componentes.componenteTurma')
                        </div>
                        <div class="col-sm" style="max-width: 60px; line-height: 3;font-weight: bold;text-align: right;">Aluno:</div>
                        <div class="col-sm componenteAlunos">
                            @include('ajax.componentes.componenteAlunos')
                        </div>
                    </div>
                    <div class="row" style="padding: 5px;">
                        <div class="col-sm" style="max-width: 65px; line-height: 3;font-weight: bold;">Prova:</div>
                        <div class="col-sm font-color-qms" style="line-height: 2.5;font-weight:bold;text-shadow: black 1px 1px 0px;font-size: large;">
                            {{ $esaAvaliacoes->getNomeProva() }}
                        </div>
                    </div>
                </div>
                <div class="col-sm" style="text-align:center; height:105px;">
                    <div class="quadro-gbm" style="margin-top:-16px;">
                        <div class="col-sm" style="line-height: 3;font-weight: bold;margin-bottom: -16px;">GBM</div>
                        <div class="col-sm" id="gbm" style="font-size: xx-large;">{{$gbm}}</div>
                    </div>
                    <div class="quadro-soma-gbo" style="margin-top:5px;background-color: #dfff00e0;">
                        <div class="col-sm" style="line-height: 3;font-weight: bold;margin-bottom: -16px;">GBO</div>
                        <div class="col-sm" id="gbo" style="font-size: xx-large;">{{$gbo}}</div>
                    </div>
                </div>
            </div>
        </div>
        <!--<div class="container-side">
            <div class="row">
                <div class="col-sm">
                    <button type="button" class="btn btn-warning" >Lateral</button>
                </div>
            </div>
        </div>-->
        <div class="container-body" style="padding: 0px 20px;">
            <div class="row">
                <div class="col-sm" style="height:auto;">
                        <div class="componente-navegacao"></div>
                </div>
            </div>
        </div>
        <div class="container-footer">
            <div class="row">
                <div class="col-sm">
                    <button type="button" class="btn btn-secondary" id="btn-lancamento-cancelamento" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
        
    </div>
</div>
<script>
    $(document).ready(function() {
        $('div#lancamento-gbo .container-lancamento-gbo .alertas-lancamento-gbo').slideUp();

        //Remover Modal com FadeIn
        $(document).on('click', '[data-dismiss="modal"]', function(evt){
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
            $('div.container_indice_dificuldades').fadeIn(1000);
        });

        $('div#lancamento-gbo .container-lancamento-gbo .container-header select[name="turmaID"]').on('change', function(evt){
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

            $.ajax({
                url: '{{$urlComboAlunos}}' + evt.target.value + "{{((isset($edicao)) ? '/true' : null)}}",
                beforeSend:function(){
                    $('div#lancamento-gbo .container-lancamento-gbo .container-header .componenteAlunos').empty()
                    $('div.alert.alertas-lancamento-gbo').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
                }
            }).done(function(data){
                $('div#lancamento-gbo .container-lancamento-gbo .container-header .componenteAlunos').html(data);

                $('div#lancamento-gbo .container-lancamento-gbo .container-header .componenteAlunos select[name="alunoID"]').on('change', function(evt, param1, param2){
                    evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
                    
                    param = param1 != undefined ? '/'+param1+'/'+param2 : '';

                    $.ajax({
                        url: '{{$urlNavegacaoItem}}' + evt.target.value + param + "{{((isset($edicao)) ? '?edicao=true' : null)}}",
                        beforeSend:function(){
                            $('div#lancamento-gbo .container-lancamento-gbo .container-body .componente-navegacao').empty();
                            $('div.alert.alertas-lancamento-gbo').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
                        }
                    }).done(function(data){
                        $('div#lancamento-gbo .container-lancamento-gbo .container-body .componente-navegacao').html(data);
                    }).fail(function(data){
                            $('div.alertas-lancamento-gbo').slideDown().addClass('alert-danger').html('Erro Interno!!'+ data);
                    });

                    
                });
                //Ativa o gatilho para o primeiro item...
                $('div#lancamento-gbo .container-lancamento-gbo .container-header .componenteAlunos select[name="alunoID"]').prop("selectedIndex", 1).change();
            }).fail(function(data){
                    $('div.alertas-lancamento-gbo').slideDown().addClass('alert-danger').html('Erro Interno!!'+ data);
            });
        });
    });
</script>
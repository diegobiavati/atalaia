
<div class="row" style="background-color: #d33e3e;border-radius: 5px 5px 0px 0px;padding: 10px 0px 0px 0px;text-align: right;">
    <div class="col-sm" style="height: auto;">
        <span style="color:white; font-weight: bold;font-size: x-large;text-shadow: black 4px 4px 4px;">Item: {{ $esaAvaliacoesIndices->get($selecionado)->nr_item }}</span>
    </div>
</div>
<div class="row" style="height: 100px; background-color: #d33e3e;">
    @if(isset($esaAvaliacoesIndices->get($selecionado)->score_vermelho))
    <div class="col-sm" style="height:auto; text-align: center;flex: 1;">
        <span style="color:#1fa3d7; font-weight: bold;font-size: x-large; vertical-align: -48px;">Registrado ( {{$esaAvaliacoesIndices->get($selecionado)->score_vermelho}} )</span>
    </div>
    @endif
    <div class="col-sm" style="height:auto; text-align: end;flex: 2;">
        <span style="color:white; font-weight: bold;font-size: xx-large; vertical-align: -48px;text-shadow: black 4px 4px 5px;">Escore Vermelho</span>
    </div>  
    <div class="col-sm" style="height:auto;flex: 0;">
        <input class="form-control" name="score-vermelho" type="number" min="1" max="100"/>
    </div>    
    @php
        $id_indice = encrypt('id_indice_'.$id_indice);
        $id_aluno = encrypt('id_aluno_'.$id_aluno);
    @endphp  
</div>
<div class="row" style="background-color: #bd7551;border-radius: 0px 0px 5px 5px;padding: 10px 10px 0px;">
    <nav aria-label="Navegação de Lançamento de GBO">
        <ul class="pagination">
            @if($selecionado != 0)
            <li class="page-item"><a class="page-link page-ant" href="#">Anterior</a></li>
            @endif
            <li class="page-item"><a class="page-link page-prox" href="#">Próximo</a></li>
        </ul>
    </nav>
</div>

<script>

    $('div#lancamento-gbo .componente-navegacao a.page-ant').click(function(evt) {
        evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

        $('div#lancamento-gbo .container-lancamento-gbo .container-header .componenteAlunos select[name="alunoID"]').trigger('change', ['anterior', '{{$id_indice}}']);
    });

    $('div#lancamento-gbo .componente-navegacao a.page-prox').click(function(evt) {
        evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

        $('div#lancamento-gbo .container-lancamento-gbo .container-header .componenteAlunos select[name="alunoID"]').trigger('change', ['proximo', '{{$id_indice}}']);
    });

    $('div#lancamento-gbo .componente-navegacao input[name=score-vermelho].form-control').keyup(function(evt) {
        evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

        if (event.which === 13) {

            var data = '_token={{csrf_token()}}&id_aluno={{$id_aluno}}&score_vermelho=' + evt.target.value;

            $.ajax({
                url: '{{$urlLancamentoGBO}}' + '/{{$id_indice}}',
                dataType: 'json',
                method: 'PUT',
                data: data,
                beforeSend:function(){
                    $('div.alert.alertas-lancamento-gbo').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');
                }
            }).done(function(data){

                if(data.status == 'success'){
                    $('div#lancamento-gbo .container-lancamento-gbo .container-header .componenteAlunos select[name="alunoID"]').change();
                }else{
                    $('div.alertas-lancamento-gbo').slideDown().addClass('alert-danger').html(data.response);
                }
            }).fail(function(data){
                    $('div.alertas-lancamento-gbo').slideDown().addClass('alert-danger').html('Erro Interno!!'+ data);
            });
        }
    });

    $('div#lancamento-gbo .componente-navegacao input[name=score-vermelho].form-control').trigger('focus');
    $('.container-lancamento-gbo .quadro-soma-gbo #gbo').text('{{ $gbo }}');
    
</script>
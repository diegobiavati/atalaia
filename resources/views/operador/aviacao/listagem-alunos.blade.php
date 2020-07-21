@if(is_null($alunos))
<div style="text-align: center; width: 70%; max-width: 800px; margin: 32px auto;">
    <div style="border-radius: 50%; border: 1px solid #B40404; width: 40px; height: 40px; margin: 24px auto;"></div>
    <h6 style="color: #B40404;"><b>LANÇAMENTO INSDISPONÍVEL</b><br /><small>Não Foi Liberado o Período de Lançamento</small></h6>
</div>
@else
<form id="lista-alunos">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div id="tableAlunos" style="width: 60%; margin: 22px auto; text-align: center;">
        <table class="table table-striped" style="margin: 60px 0 90px 0;">
            <thead>                    
                <tr>
                    <th>
                        <div class="custom-control custom-checkbox" style="width: 36%; margin: 4px auto;">
                            <input type="checkbox" class="custom-control-input" id="selectAll" name="selectAll"/>
                            <label class="custom-control-label" for="selectAll"></label>
                        </div>
                    </th>
                    <th style="width: 10%;">Numero</th>
                    <th style="width: 80%;text-align: left;">Nome Guerra</th>
                </tr>
            </thead>
            <tbody>

                @foreach($alunos as $aluno)
                <tr onclick="if($(this).find('input').is(':checked')) { $(this).find('input').prop('checked', false); } else { $(this).find('input').prop('checked', true); }" style="cursor: pointer;">
                    <td>
                        <div class="custom-control custom-checkbox" style="width: 36%; margin: 4px auto;">
                            @if(isset($exameAviacao))
                                <input type="checkbox" class="custom-control-input" id="{{'alunoID_'.$aluno->id}}" name="alunos_ids[]" value="{{$aluno->id}}" {{ ($aluno->aluno_voluntario_aviacao->selecionado_exame == 'S') ? 'checked' : ''}}/>
                            @else
                                <input type="checkbox" class="custom-control-input" id="{{'alunoID_'.$aluno->id}}" name="alunos_ids[]" value="{{$aluno->id}}" {{ (($aluno->aluno_voluntario_aviacao )) ? 'checked' : '' }}/>
                            @endif
                            <label class="custom-control-label" for="{{'alunoID_'.$aluno->id}}"></label>
                        </div>
                    </td>
                    <td>{{$aluno->numero}}</td>
                    <td style="text-align: left;"><b>{{ $aluno->numero . ' ' . $aluno->nome_guerra }}</b><br />
                        <span style="color: #696969;"><i>{{ $aluno->nome_completo }}</i></span><br />
                        {{ $aluno->omct->sigla_omct }}
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
    <div style="margin: 32px auto; text-align: center; ">
    @if(isset($exameAviacao))
        <button type="button" class="btn btn-success" onclick="enviarVoluntariosExameAviacao(this);">Gravar Voluntários para o Exame (Aviação)</button>
    @else
        <button type="button" class="btn btn-success" onclick="enviarVoluntariosAviacao(this);">Gravar Voluntários para Aviação</button>
    @endif
    </div>
    
</form>
<script>
    $("#selectAll").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    function enviarVoluntariosAviacao(dataButton){
        
        $('div.alert').removeClass('alert-danger').removeClass('alert-success').hide();
        
        var dataForm = $('form#lista-alunos').serialize();
        var dataButtonContent = $(dataButton).html();
        $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/selecao-voluntarios-aviacao',
                beforeSend: function(){
                    $(dataButton).html('<img src="/images/loadings/loading_03.svg" style="margin-right: 3px; width: 32px;" />Registrando...').addClass('disabled');
                },
                success: function(data){
                    $(dataButton).html('Gravar Voluntários para Aviação').removeClass('disabled');
                    $('html, body').animate({
                            scrollTop: 80
                    }, 1000);                    
                        if(data.status=='ok'){
                            $('div.alert').addClass('alert-success').html(data.response).slideDown();
                        } else if(data.status=='err'){
                            $('div.alert').addClass('alert-danger').html(data.response).slideDown();
                        }
                },
                error: function(jqxhr){
                       $(dataButton).html(dataButtonContent).removeClass('disabled');
                    setTimeout(function(){
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }  
            });
    }

    function enviarVoluntariosExameAviacao(dataButton){
        $('div.alert').removeClass('alert-danger').removeClass('alert-success').hide();
        
        var dataForm = $('form#lista-alunos').serialize();
        var dataButtonContent = $(dataButton).html();
        $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/selecao-voluntarios-exame-aviacao',
                beforeSend: function(){
                    $(dataButton).html('<img src="/images/loadings/loading_03.svg" style="margin-right: 3px; width: 32px;" />Registrando...').addClass('disabled');
                },
                success: function(data){
                    $(dataButton).html('Gravar Voluntários para o Exame (Aviação)').removeClass('disabled');
                    $('html, body').animate({
                            scrollTop: 80
                    }, 1000);                    
                        if(data.status=='ok'){
                            $('div.alert').addClass('alert-success').html(data.response).slideDown();
                        } else if(data.status=='err'){
                            $('div.alert').addClass('alert-danger').html(data.response).slideDown();
                        }
                },
                error: function(jqxhr){
                       $(dataButton).html(dataButtonContent).removeClass('disabled');
                    setTimeout(function(){
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }  
            });
    }
      
</script>
@endif
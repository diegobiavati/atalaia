@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Escolha de QMS')

@section('titulo-relatorio', 'ESCOLHA DE QMS - CFS - FORMAÇÃO '. $ano_selecionado)

@section('content')

<script src="/js/jquery/jquery.tabletojson.min.js"></script>

@if(!key_exists('aluno', $data))
<div style="text-align: center; margin-top: 240px;">
    <h4>
        NÃO AUTORIZADO!<br />SEM LANÇAMENTOS
    </h4>
</div>
@else
<div style="width: 100%; margin: 4px auto; page-break-after: always; text-align: center;">

    <!-- Alunos da Aviação -->
    @if($_GET['tipo_relatorio']==13)
        <h4 style="margin-top: -18px;">AVIAÇÃO - Segmento Masculino</h4>
    @endif

    @if($_GET['tipo_relatorio']==14)
        <h4 style="margin-top: -18px;">AVIAÇÃO - Segmento Feminino</h4>
    @endif

    @if(key_exists('alunos_aviacao', $data) && $data['alunos_aviacao'])
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 80%;">
            <tr style="background-color: #E6E6E6;">
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Ordem</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Al Nr</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Nome</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>UETE</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>N1</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Classificação<br/>Geral</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Classificação<br />Por area e Segmento</b></td>
            </tr>
            @foreach ($data['alunos_aviacao'] as $aluno)
                <tr>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$loop->index + 1}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->numero}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nome_guerra}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->omct->sigla_omct}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->classificacao->nota_final_arredondada}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->classificacao->classificacao}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->classificacao->classificacao_por_area}}</td>
                </tr>    
            @endforeach
        </table>
    @else
        <div style="text-align: center;border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 80%;">DISTRIBUIÇÃO PARA QMS AVIAÇÃO INDISPONÍVEL</div>
    @endif
    
    <div style="margin-bottom: 40px;"></div>
    <!-- Fim Alunos da Aviação -->

    @if($_GET['tipo_relatorio']==13 || $_GET['tipo_relatorio']==15)
    <h4>COMBATENTE/LOGISTICA-TÉCNICA - SEGMENTO MASCULINO</h4>
    @endif

    @if($_GET['tipo_relatorio']==14 || $_GET['tipo_relatorio']==16)
    <h4>COMBATENTE/LOGISTICA-TÉCNICA - SEGMENTO FEMININO</h4>
    @endif

    @if($data['aluno'])

    <table id="esquolha-qms-tabela" style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 96%; font-size: 12px;">
        <tr style="background-color: #E6E6E6;">
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Ordem</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Al Nr</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Nome</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>UETE</b></td>
            @for($i=1;$i<=$total_opcoes;$i++) <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>{{$i}}ª</b></td>
                @endfor
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>N1</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Classificação<br />Geral</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Classificação<br />Por area e Segmento</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Opção atendida</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>QMS<br />Designada</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Classificação<br />Por QMS Designada</b></td>
        </tr>
        @foreach ($data['aluno'] as $aluno)
        <tr>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$loop->index + 1}}</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno['numero']}}</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno['nome_guerra']}}</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno['omct']}}</td>
            @foreach($aluno['opcoes'] as $opcao)
            @if($aluno['qmsdesignda']==$opcao)
            <td style="border: 1px solid #000; padding: 6px; text-align: center; background-color: #ccc;">
                @else
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                @endif
                {{$qms_id_nome[$opcao]}}<br />
                {{$aluno['qms_vagas_restantes'][$opcao]}}/{{$data['qms_vagas_fixas'][$opcao]}}
            </td>
            @endforeach
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{number_format($aluno['NPB'], 3, ',', '')}}</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno['classificacao']}}</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno['classificacao_por_area']}}</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno['opcao_atendido']}}ª</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno['qmsdesignda_nome']}}</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno['classificacao_qmsdesignda']}}</td>
        </tr>
        @endforeach
    </table>


    @if(is_null($data['bi_bloqueio']))
        @if(isset($ownauthcontroller) && $ownauthcontroller->PermissaoCheck(1) && session()->get('login')['omctID'] == 1)
        <!--Precisa ter perfil super administrador e Uete ESA-->
        
        <div class="alert info-aplicar-qms" role="alert" style="margin: 32px auto; width: 96%;"></div>
        <button id="aplicar_qms" type="button" class="btn {{ ($data['recuperado'] == true) ? 'btn-danger' : 'btn-success' }} btn-lg btn-block" style="margin: 32px auto; width: 96%;">{{ ($data['recuperado'] == true) ? 'Informações Já Registradas, Deseja Remover Para Registrar Novamente?': 'Registrar Alunos na QMS'}}</button>

            @if($data['recuperado'] == true)
                <div class="form-group" style="margin: 32px auto; width: 96%;">
                    <input type="text" name="nrbi" class="form-control" id="NrBICmd" placeholder="Número do BI Assinado Pelo CMT da ESA"/>
                    <small class="form-text text-muted">Após Informar o Número do BI Assinado Pelo CMT da ESA Não Será Possível Alterar As Informações.</small>

                    <button type="button" id="aplicar_bi" class="btn btn-warning btn-lg btn-block" style="margin-top: 10px;">Informar BI Assinado Pelo CMT da ESA</button>
                </div>
            @endif
        @endif
    @endif

    <h4 style="margin-top: 52px;">QUANTITATIVO ATENDIDO POR OPÇÃO</h4>
    <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 96%; font-size: 12px;">
        <tr>
            <td style="border: 1px solid #000; padding: 6px; text-align: center; background-color: #E6E6E6;"><b>OPÇÕES</b></td>
            @for($i=1;$i<=$total_opcoes;$i++) <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>{{$i}}ª OPÇÃO</b></td>
                @endfor
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 6px; text-align: center; background-color: #E6E6E6;"><b>QUANTITATIVO ATENDIDO</b></td>
            @for($i=1;$i<=$total_opcoes;$i++) <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$data['estatistica']['opcao_atendido'][$i] -1}}</td>
                @endfor
        </tr>
    </table>

    <h4 style="margin-top: 52px;">QUANTITATIVO DE PRIORIDADE DE QMS DENTRO DAS OPÇÕES</h4>
    <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 96%; font-size: 12px;">
        <tr>
            <td style="border: 1px solid #000; padding: 6px; text-align: center; background-color: #E6E6E6;"><b>QMS/OPÇÕES</b></td>
            @for($i=1;$i<=$total_opcoes;$i++) <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>{{$i}}ª OPÇÃO</b></td>
                @endfor
        </tr>
        @foreach($data['qmEsses'] as $qms)
        <tr>
            <td style="border: 1px solid #000; padding: 6px; text-align: center; background-color: #E6E6E6;">{{$qms->qms}}</td>
            @for($i=1;$i<=$total_opcoes;$i++) <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$data['estatistica']['opcao'][$i][$qms->id]-1}}</td>
                @endfor
        </tr>
        @endforeach
    </table>

    @else

    <div style="text-align: center;">DISTRIBUIÇÃO COMBATENTE/LOGISTICA-TÉCNICA INDISPONÍVEL</div>

    @endif
</div>
@endif

<script>
    $(document).ready(function() {
        $("body").removeAttr("style"); //remove o display: table; para centralizar tudo

        var json = {'_token' : '{{ csrf_token() }}'}

        $('#aplicar_qms').click(function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

            Aplica('{{$rota}}', json);
        });

        $('#aplicar_bi').click(function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
            
            json.ano_formacao = "{{$data['ano_formacao']->id}}";
            json.segmento = "{{$data['segmento']}}";
            json.nota_bi = $('.form-group input[name="nrbi"]').val();
            Aplica('{{$rota_bi}}', json);
        });
    });

    function Aplica(rota, json){
        $.ajax({
            dataType: 'json',
            url: rota,
            type: 'POST',
            data: json,
            beforeSend: function(){
                $('div.info-aplicar-qms').empty().hide();
                $('div.info-aplicar-qms').removeClass('alert-success');
                $('div.info-aplicar-qms').removeClass('alert-danger');
                $('div.info-aplicar-qms').html('<div id="temp" style="text-align: center; margin: 24px; padding: 24px;"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
            },
            success: function(data){

                if(data.status=='success'){
                    $('div.info-aplicar-qms').addClass('alert-success');
                    $('div.info-aplicar-qms').html(data.response).slideDown();

                    setTimeout(function(){
                        $('div.info-aplicar-qms').slideUp(200, function(){
                            $(this).removeClass('alert-success').empty();
                        });
                        location.reload();
                    }, 5000);
                } else {
                    $('div.info-aplicar-qms').addClass('alert-danger');
                    $('div.info-aplicar-qms').html('<strong>ATENÇÃO:</strong><br />').slideDown();
                    $.each(data.response, function(key, value){
                        $('div.info-aplicar-qms').append('<li>' + value + '</li>');
                    });
                }
            },
            error: function(jqxhr){
                $('div.info-aplicar-qms').addClass('alert-warning');
                $('div.info-aplicar-qms').html('<strong>ATENÇÃO: </strong> Ocorreu um Erro!!!').slideDown(); 
            }
        });
    }
</script>
@stop
@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Escolha de QMS')

@section('titulo-relatorio', 'ESCOLHA DE QMS - CFS - FORMAÇÃO '. $ano_selecionado)

@section('content')

@if(!key_exists('aluno', $data))
    <div style="text-align: center; margin-top: 240px;">
                            <h4>
                                NÃO AUTORIZADO!<br />SEM LANÇAMENTOS
                            </h4>
                        </div>
@else
<div style="width: 100%; margin: 4px auto; page-break-after: always; text-align: center;">

    @if($_GET['tipo_relatorio']==13 || $_GET['tipo_relatorio']==15)
    <h4 style="margin-top: -18px;">COMBATENTE/LOGISTICA-TÉCNICA - SEGMENTO MASCULINO</h4>
    @endif

    @if($_GET['tipo_relatorio']==14 || $_GET['tipo_relatorio']==16)
    <h4 style="margin-top: -18px;">COMBATENTE/LOGISTICA-TÉCNICA - SEGMENTO FEMININO</h4>
    @endif

    @if($data['aluno'])

    <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 96%; font-size: 12px;">
        <tr style="background-color: #E6E6E6;">
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ORDEM</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>UETE</b></td>
            @for($i=1;$i<=$total_opcoes;$i++) <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>{{$i}}ª</b></td>
                @endfor
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>N1</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Classificação<br />Perd Bas</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Classificação<br />por area</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Opção atendida</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>QMS<br />Designada</b></td>
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
        </tr>
        @endforeach
    </table>

    @if($ownauthcontroller->PermissaoCheck(1) && session()->get('login')['omctID'] == 1)
    <!--Precisa ter perfil super administrador e Uete ESA-->
    <button id="aplicar_qms" type="button" class="btn btn-danger btn-lg btn-block">Aplicar QMS</button>
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

        $('#aplicar_qms').click(function() {
            //Aplicar Qms

        });

    });
</script>
@stop
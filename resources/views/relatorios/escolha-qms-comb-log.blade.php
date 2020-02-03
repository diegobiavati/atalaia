@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Escolha de QMS')

@section('titulo-relatorio', 'ESCOLHA DE QMS - CFS - FORMAÇÃO '. $ano_selecionado)

@section('content')
@php

@endphp

<div style="width: 1600px; margin: 4px auto; page-break-after: always; text-align: center;">
    
    @if($_GET['tipo_relatorio']==1 || $_GET['tipo_relatorio']==7)
        <h4 style="margin-top: -18px;">COMBATENTE/LOGISTICA-TÉCNICA - SEGMENTO MASCULINO</h4>
    @endif

    @if($_GET['tipo_relatorio']==2 || $_GET['tipo_relatorio']==8)
        <h4 style="margin-top: -18px;">COMBATENTE/LOGISTICA-TÉCNICA - SEGMENTO FEMININO</h4>
    @endif

    @if($alunos)
    
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 80%;">
            <tr style="background-color: #E6E6E6;">
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ORDEM</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>UETE</b></td>
                @for($i=1;$i<=$total_opcoes;$i++)
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>{{$i}}ª</b></td>
                @endfor
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NPB</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Opção atendida</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>QMS<br />Designada</b></td>
            </tr>
            @foreach ($alunos as $aluno)
                <tr>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$loop->index + 1}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno['numero']}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno['nome_guerra']}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno['omct']}}</td>
                    @foreach($aluno['opcoes'] as $opcao)
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$qms_id_nome[$opcao]}}</td>
                    @endforeach
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{number_format($aluno['NPB'], 3, ',', '')}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno['opcao_atendido']}}ª</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno['qmsdesignda_nome']}}</td>
                </tr>    
            @endforeach
        </table>

    @else

        <div style="text-align: center;">DISTRIBUIÇÃO COMBATENTE/LOGISTICA-TÉCNICA INDISPONÍVEL</div>

    @endif

    </div>


@stop
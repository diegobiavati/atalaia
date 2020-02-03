@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Escolha de QMS')

@section('titulo-relatorio', 'ESCOLHA DE QMS - CFS - FORMAÇÃO '. $ano_selecionado)

@section('content')
@php

@endphp

@if(count($alunos)>0)

<div style="width: 1600px; margin: 4px auto; page-break-after: always; text-align: center;">
    
    @if($_GET['tipo_relatorio']==3 || $_GET['tipo_relatorio']==11)
        <h4 style="margin-top: -18px;">AVIAÇÃO - Segmento Masculino</h4>
    @endif

    @if($_GET['tipo_relatorio']==4 || $_GET['tipo_relatorio']==12)
        <h4 style="margin-top: -18px;">AVIAÇÃO - Segmento Feminino</h4>
    @endif
    
    <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 80%;">
        <tr style="background-color: #E6E6E6;">
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Class Arma</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Al Nr</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Nome</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>UETE</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NPB</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Classificação<br />Perd Bas</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Classificação<br />Por area</b></td>
        </tr>
        @foreach ($alunos as $aluno)
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$loop->index + 1}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->aluno->numero}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->aluno->nome_guerra}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->aluno->omct->sigla_omct}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nota_final_arredondada}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->classificacao}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->classificacao_por_area}}</td>
            </tr>    
        @endforeach


    </table>

        
        
    </div>

@else

    <div style="text-align: center;">DISTRIBUIÇÃO PARA QMS AVIAÇÃO INDISPONÍVEL</div>

@endif

@stop
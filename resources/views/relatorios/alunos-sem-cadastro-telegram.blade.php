@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Alunos sem cadastro no TELEGRAM')

@section('titulo-relatorio', 'RELAÇÃO DE ALUNOS NÃO REGISTRADOS NO APLICATIVO TELEGRAM - ANO FORMAÇÃO '.$ano_formacao->formacao)

@section('content')


@if(count($alunos)>0)



<div style="width: 940px; margin: 4px auto; page-break-after: always;">
    
    <h3 style="text-align: center;">
        {{$omct->omct}}
    </h3>
        
    <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 100%;">
        <tr>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ORDEM</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME COMPLETO</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME DE GUERRA</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>TURMA</b></td>
        </tr>
        @foreach($alunos as $aluno)                
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$loop->index + 1}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->numero}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nome_completo}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nome_guerra}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->turma->turma or 'Não informada'}}</td>
            </tr>
        @endforeach
    </table>    
</div>    

@else

    <div style="text-align: center;">NÃO HÁ REGISTROS DE ALUNOS NESSE UNIVERSO SEM REGISTRO NO APP TELEGRAM</div>

@endif

@stop
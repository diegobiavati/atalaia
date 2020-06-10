@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Pronto do lançamento do TFM')

@section('titulo-relatorio', 'PRONTO DO LANÇAMENTO DE NOTAS DO TFM')

@section('content')

<div style="width: 940px; margin: 4px auto; page-break-after: always;">
    <div style="text-align: center;">
        <h3>
            ALUNOS SEM NOTA
        </h3>
    </div>
    
    @if(count($alunos)>0)
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 100%;">
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME DE GUERRA</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>TURMA</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>UETE</b></td>
            </tr>
        @foreach($alunos as $aluno)
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->numero}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nome_guerra}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->turma->turma or 'Não informada'}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->omct->sigla_omct}}</td>
            </tr>
        @endforeach
        </table>   
    @else
        <div style="text-align: center; color: #0B6121;">SEM LANÇAMENTO PENDENTES</div>
    @endif

</div>

@stop
@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Relação de alunos em recuperação')

@section('content')

    <h3 style="text-align: center; margin-top: -8px; font-size: 18px;">
        CURSO DE FORMAÇÃO DE SARGENTOS - ANO DE FORMAÇÃO {{$ano_selecionado->formacao}}<br />
        <br />RELAÇÃO DE ALUNOS DE RECUPERAÇÃO EM {{$disciplina->nome_disciplina}} 
    </h3>
    <div style="margin: 14px auto; width: 100%;">
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 90%;">
            <tr style="background-color: #E6E6E6;">
                <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 1cm;"><b>ORDEM</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 1.5cm;"><b>NÚMERO</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME DE GUERRA</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME COMPLETO</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>UETE</b></td>
                @if($ownauthcontroller->PermissaoCheck(1) || !$ownauthcontroller->PermissaoCheck(20))
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ND</b></td>
                @endif
            </tr>

            @forelse($alunos as $aluno)
                <tr>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$loop->index +1}}</td>                   
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->numero}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nome_guerra}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nome_completo}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->omct->sigla_omct}}</td> 
                    @if($ownauthcontroller->PermissaoCheck(1) || !$ownauthcontroller->PermissaoCheck(20))
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$nd_aluno[$aluno->id]}}</td>
                    @endif                                       
                </tr>
            @empty
                <tr><td colspan="100" style="text-align: center; padding: 12px;">SEM ALUNOS EM RECUPERAÇÃO NESTA DISCIPLINA</td></tr>
            @endforelse
        </table>
    </div>  


@stop
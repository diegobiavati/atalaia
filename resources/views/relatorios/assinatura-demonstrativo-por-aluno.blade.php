@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Recebido do Demonstrativo de Notas')

@section('titulo-relatorio', '')

@section('content')

@if(count($alunos)>0)

      

    <div style="width: 940px; margin: 6px auto 32px auto; page-break-after: always;">
        <h3 style="text-align: center;">
            <b>
                CURSO DE FORMAÇÃO DE SARGENTOS - FORMAÇÃO {{$ano_formacao->formacao}} - PERÍODO BÁSICO<br />
                DISCIPLINA {{$avaliacao->disciplinas->nome_disciplina}}<br />
                {{$avaliacao->nome_completo}}<br />
                RECIBO DE ENTREGA DE GRAU DO ALUNO
            </b>
        </h3>        
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 100%;">
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME DE GUERRA</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>UETE</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>TURMA</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 180px;"><b>DATA DE RECEBIMENTO</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 300px;"><b>ASSINATURA POR EXTENSO</b></td>
            </tr>
            @foreach($alunos as $aluno)                
                <tr>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->numero}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nome_guerra}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->omct->sigla_omct or 'Não informada'}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->turma->turma or 'Não informada'}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">&nbsp;</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 300px;">&nbsp;</td>
                </tr>
            @endforeach
        </table>    
    </div>
    

@else

    <div style="text-align: center;">NÃO HÁ REGISTROS DE NOTAS PARA ESTA UETE NESTA AVALIAÇÃO</div>

@endif

@stop
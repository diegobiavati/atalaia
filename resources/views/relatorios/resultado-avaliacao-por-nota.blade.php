@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório:: Relação de alunos e médias')

@section('content')


    <div style="width: 1080px; margin: 4px auto;"> 
        <div style="text-align: center; margin-top: 32px;">
            <h4>
                {{$omct->omct}}<br />
                CURSO DE FORMAÇÃO DE SARGENTOS - ANO DE FORMAÇÃO {{$ano_formacao->formacao}}<br />
                @if($_GET['notas_minimas']==1)
                    RELAÇÃO DE ALUNOS COM MÉDIA ACIMA DE 5 NA NA {{$avaliacao->chamada}}ª CHAMADA DA {{$avaliacao->nome_completo}} DE {{$avaliacao->disciplinas->nome_disciplina}}
                @elseif($_GET['notas_minimas']==2)
                    RELAÇÃO DE ALUNOS COM MÉDIA ABAIXO DE 5 NA {{$avaliacao->chamada}}ª CHAMADA DA {{$avaliacao->nome_completo}} DE {{$avaliacao->disciplinas->nome_disciplina}}
                @endif
            </h4>
        </div>


        @if(count($alunos)>0)
            <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 100%;">
                <tr>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ORDEM</b></td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME COMPLETO</b></td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME DE GUERRA</b></td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>TURMA</b></td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOTA</b></td>
                </tr>        
        @endif

        @forelse($alunos as $aluno)
            <tr>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$loop->index + 1}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->numero}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nome_completo}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nome_guerra}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->turma->turma or 'Não informada'}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$alunoNota[$aluno->id]}}</td>
                </tr>                
        @empty
            <div style="text-align: center; color: #0B6121;">SEM ALUNOS NESSE UNIVERSO</div>
        @endforelse

        @if(count($alunos)>0)
            </table>
        @endif
 
    </div>


@stop
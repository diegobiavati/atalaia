@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Alunos em recuperação')

@section('titulo-relatorio', 'RELAÇÃO DE ALUNOS EM RECUPERAÇÃO CFS - FORMAÇÃO '.$ano_selecionado->formacao)

@section('content')


@if(count($alunos_classificacao)>0)



<div style="width: 940px; margin: 4px auto; page-break-after: always;">
    <div style="text-align: center">TOTAL {{count($alunos_classificacao)}}  
    <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 100%;">
        <tr>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME DE GUERRA</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>TURMA</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>UETE</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>DISCIPLINA(S)</b></td>
        </tr>

            @foreach ($omcts as $omct)
                @foreach($alunos_classificacao as $aluno)                
                    @if($aluno->aluno->omcts_id==$omct->id)
                        <tr>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->aluno->numero}}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->aluno->nome_guerra}}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->aluno->turma->turma or 'Não informada'}}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->aluno->omct->sigla_omct}}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                                @foreach(explode(',', $aluno->disciplinas_reprovado) as $disciplina)
                                    @if(isset($disciplinas[$disciplina]))
                                        <p>{{$disciplinas[$disciplina]}}</p>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                    @endif
                @endforeach
            @endforeach

        </table>    
    </div>

</div>

@else

    <div style="text-align: center;">NÃO HÁ REGISTROS DE ALUNOS EM RECUPERAÇÃO</div>

@endif
<script>
    $(document).ready(function() {
        $("body").removeAttr("style"); //remove o display: table; para centralizar tudo
    });
</script>
@stop
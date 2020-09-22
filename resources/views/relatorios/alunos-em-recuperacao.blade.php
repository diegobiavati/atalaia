@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Alunos em recuperação')

@section('titulo-relatorio', 'RELAÇÃO DE ALUNOS EM RECUPERAÇÃO CFS - FORMAÇÃO '.$ano_selecionado->formacao)

@section('content')


@if(count($alunos_recuperacao)>0)

<div style="width: 940px; margin: 4px auto; page-break-after: always;">
    <div style="text-align: center">TOTAL {{count($alunos_recuperacao)}}  
    <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 100%;">
        <tr>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME DE GUERRA</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>TURMA</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>UETE</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>DISCIPLINA(S)</b></td>
        </tr>

            
                @foreach($alunos_recuperacao as $disciplinas)       
                        <tr>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$disciplinas['aluno']->numero}}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$disciplinas['aluno']->nome_guerra}}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$disciplinas['aluno']->turma->turma or 'Não informada'}}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$disciplinas['aluno']->omct->sigla_omct}}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                                @foreach($disciplinas as $key => $disciplina)
                                    @if($key != 'aluno')
                                       <p>{{ $disciplina['disciplina_nome'] }}</p>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
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
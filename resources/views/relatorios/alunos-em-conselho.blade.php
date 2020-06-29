@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Alunos aprovados em Conselho')

@section('content')

<h3 style="text-align: center; margin: -10px 0 56px 0;">
    CURSO DE FORMAÇÃO DE SARGENTOS - PERÍODO BÁSICO - FORMAÇÃO {{$ano_selecionado->formacao}}<br />
    RELAÇÃO DE ALUNOS APROVADOS EM CONSELHO DE ENSINO
</h3>

@if(count($alunos)>0)

<div style="width: 940px; margin: 4px auto; page-break-after: always;">
    @foreach($omcts as $omct)
        <h4 style="text-align: center;">{{$omct->omct}}</h4>
        @foreach ($alunos as $aluno)
            @if($aluno->aluno->omcts_id==$omct->id)
                @php
                    $aluno_conselho = true;
                @endphp
            @endif
        @endforeach
        @if(!isset($aluno_conselho))
            <div style="text-align: center; color: #0B6121;">Sem alunos em conselho de ensino nesta UETE</div>
        @else
            @unset($aluno_conselho)

            <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 100%;">
                <tr>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME DE GUERRA</b></td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>DISCIPLINA</b></td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ACRÉSCIMO</b></td>
                </tr>
                @foreach ($alunos as $aluno)
                    @if($aluno->aluno->omcts_id==$omct->id)
                        <tr>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->aluno->numero}}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->aluno->nome_guerra}}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$disciplina[$aluno->disciplina_id]['nome_disciplina_abrev']}}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"> + {{$aluno->acrescimo}}</td>
                        </tr>
                    @endif                    
                @endforeach

            </table>            
        @endif
    @endforeach 

</div>

</div>

@else

    <div style="text-align: center;">NÃO HÁ REGISTROS DE ALUNOS APROVADOS EM CONSELHO DE ENSINO</div>

@endif

<script>
    $(document).ready(function() {
        $("body").removeAttr("style"); //remove o display: table; para centralizar tudo
    });
</script>
@stop
@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório:: Relação de alunos e médias')

@section('content')

<h3 style="text-align: center; margin-top: -8px; font-size: 18px;">
    CURSO DE FORMAÇÃO DE SARGENTOS (Disciplinas para o Diploma) - ANO DE FORMAÇÃO {{$anoFormacao->formacao}}<br />
    <br />
</h3>


<div style="margin: 14px auto; width: 60%;">
    <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 90%;">
        <tr>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Codigo</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Nome</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Creditos</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>CargaHoraria</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>TipoAvaliacao</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>TipoDisciplina</b></td>
        </tr>

        @forelse($disciplinas as $disciplina)
        <tr>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$disciplina->id}}</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$disciplina->nome_disciplina}}</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">0</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$disciplina->carga_horaria}}</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                @php
                    switch($disciplina->tipo_avaliacao){
                        case 'N':
                            echo 'Nota';break;
                        case 'C':
                            echo 'Conceito';break;
                        case 'S':
                            echo 'Situação';break;
                    }
                @endphp
            </td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                @php
                    switch($disciplina->tipo_disciplina){
                        case 'OB':
                            echo 'Obrigatória';break;
                        case 'EL':
                            echo 'Eletiva';break;
                        case 'OP':
                            echo 'Optativa';break;
                    }
                @endphp
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="100" style="text-align: center; padding: 12px;">SEM OCORRÊNCIAS</td>
        </tr>
        @endforelse
    </table>
</div>

<script>
    $(document).ready(function() {
        $("body").removeAttr("style"); //remove o display: table; para centralizar tudo
    });
</script>


@stop
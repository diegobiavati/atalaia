@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Mapa de Controle de Efetivo')

@section('content')
<h3 style="text-align: center; margin-top: -8px; font-size: 18px; font-family: Arial;">
    <u>RELAÇÃO DE ALUNO(A)S SEM FOTO / CFGS {{$anoFormacao->ano_per_basico.'-'.$anoFormacao->ano_per_qualificacao}}</u><br />
</h3>

<div style="margin: 14px auto;width: 50%; text-align: center;">
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 32px auto; width: 99%;font-size: larger; font-family: Arial;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col">Número</th>
                <th scope="col">Nome de Guerra</th>
                <th scope="col">Curso</th>
            </tr>
            @foreach($alunos as $aluno)
            <tr>
                <td>{{$aluno->numero}}</td>
                <td>{{$aluno->nome_guerra}}</td>
                <td>{{$aluno->qms->qms}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $("body").removeAttr("style"); //remove o display: table; para centralizar tudo
    });
</script>
@stop
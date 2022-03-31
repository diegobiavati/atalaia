@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Análise Parcial de Notas Por Disciplinas')

@section('content')

<h3 style="text-align: center; margin-top: -8px; font-size: 18px; font-family: Arial;">
    <u>Análise Parcial de Notas Por Disciplinas / CFGS {{$anoFormacao->ano_per_basico.'-'.$anoFormacao->ano_per_qualificacao}}</u><br />
    Situação Geral em {{ date('d/m/Y H:i:s') }}<br /><br />
    {{$uete->omct}} </br>
</h3>
<div style="margin: 14px auto; width: 100%; text-align: center;">
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 32px auto; width: 99%;font-size: x-small; font-family: Arial;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col" style="width:5px;">Número</th>
                <th scope="col" style="width:50px;">UETE</th>
                <th scope="col" style="width:100px;">Nome Guerra</th>
                <th scope="col" style="width:150px;">Disciplina</th>
                <th scope="col" style="width:100px;">ND</th>
            </tr>

            @foreach($retorno_excel as $retorno)
            
            <tr>
                <td>{{$retorno['aluno']->numero}}</td>
                <td>{{$retorno['aluno']->omct->sigla_omct}}</td>
                <td>{{$retorno['aluno']->nome_guerra}}</td>
                <td>{{$retorno['disciplinaNome']}}</td>
                <td>{{ number_format($retorno['ND'], 3, ',', '') }}</td>
            </tr>
            
            @endforeach
        </tbody>
    </table>
</div>

@if($relacao == 'excel')

{{ $fileName = bcrypt(date('Y-m-d H:i:s')).'.ods' }}

{{header("Content-Description: PHP Generated Data")}}
{{header("Content-Type: application/x-msexcel")}}
{{header("Content-Disposition: attachment; filename=\"{$fileName}\"")}}
{{header("Expires: 0")}}
{{header("Cache-Control: must-revalidate, post-check=0, pre-check=0")}}
{{header("Pragma: no-cache")}}

@endif
@stop
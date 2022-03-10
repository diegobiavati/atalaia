@extends('relatorios.template-relatorios-sem-brasao')

@section('title', 'Atalaia :: Modelo::Notas Capitani')

@section('titulo-relatorio', '')

@section('content')
<style>
    table, tr, td {
        border: 1px solid #000;
    }
</style>
<table style="border-collapse: collapse; margin: 0 auto; width: 98%;">

    <tr>
    @foreach ($lista_colunas as $class)
        <td>{{$class}}</td>    
    @endforeach
    </tr>

    @foreach ($lista_retorno as $class)
        <tr>
            <td>{{$class->ano_cad}}</td>
            <td>{{$class->numero}}</td>
            <td>{{$class->nd_tfm}}</td>
            <td>{{$class->nd_armt}}</td>
            <td>{{$class->nd_lidmil}}</td>
            <td>{{$class->nd_etica}}</td>
            <td>{{$class->nd_tec_mil_1}}</td>
            <td>{{$class->nd_tec_mil_2}}</td>
            <td>{{$class->nd_tec_mil_3}}</td>
            <td>{{$class->nd_hist}}</td>
            <td>{{$class->nd_ingles_1}}</td>
            <td>{{$class->n1}}</td>
            <td>{{$class->class}}</td>
            <td>{{$class->men}}</td>
            <td>{{$class->qr_1}}</td>
        </tr>
    @endforeach
</table>
<script>
    $(document).ready(function() {
        $("body").removeAttr("style"); //remove o display: table; para centralizar tudo
    });
</script>
@stop
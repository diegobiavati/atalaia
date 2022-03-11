@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Mapa de Controle de Efetivo')

@section('content')

<h3 style="text-align: center; margin-top: -8px; font-size: 18px; font-family: Arial;">
    <u>MAPA DE CONTROLE DE EFETIVO / CFGS {{$anoFormacao->ano_per_basico.'-'.$anoFormacao->ano_per_qualificacao}}</u><br />
    Evasão Escolar em {{ date('d/m/Y H:i:s') }}<br />
</h3>
<div style="margin: 14px auto;width: 50%; text-align: center;">
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 32px auto; width: 99%;font-size: x-small; font-family: Arial;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col">Efetivo Matriculado</th>
                <th scope="col">Efetivo Desligado</th>
                <th scope="col">Taxa de Evasão</th>
            </tr>

            <tr>
                <td>{{$mapaEfetivo->last()->totEfeMtr}}</td>
                <td>{{$mapaEfetivo->last()->totEfetDesl}}</td>
                <td>{{ number_format(($mapaEfetivo->last()->totEfetDesl / $mapaEfetivo->last()->totEfeMtr) * 100, 2, ',', '') }} %</td>
            </tr>
        </tbody>
    </table>
</div>

<div style="margin: 14px auto;width:50%; text-align: center;">
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 32px auto; width: 99%;font-size: x-small; font-family: Arial;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col" style="width:300px;">UETE</th>
                <th scope="col" style="width:80px;">DESLIGAMENTO A pedido</th>
                <th scope="col" style="width:80px;">DESLIGAMENTO Ex-Oficio</th>
                <th scope="col" style="width:80px;">TRANCAMENTO DE MATRÍCULA A pedido</th>
                <th scope="col" style="width:80px;">TRANCAMENTO DE MATRÍCULA Ex-Oficio</th>
                <th scope="col" style="width:80px;">Total</th>
            </tr>

            @foreach($resumoUetes as $uete)
            <tr>
                <td>{{ $uete->sigla_uete }}</td>
                <td>{{ $uete->desligamentoApedido }}</td>
                <td>{{ $uete->desligamentoExOficio }}</td>
                <td>{{ $uete->trancamentoApedido }}</td>
                <td>{{ $uete->trancamentoExOficio }}</td>
                <td>{{ $uete->total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div style="margin: 14px auto;width: 50%; text-align: center;">
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 32px auto; width: 99%;font-size: x-small; font-family: Arial;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col" style="width:300px;">UETE</th>
                @foreach($evasaoUete as $evasao)
                <th scope="col" style="width:90px;">{{$evasao->sigla_omct}}</th>
                @endforeach
            </tr>

            <tr>
                <td>Evs Es</td>
                @foreach($evasaoUete as $evasao)
                <td scope="col" style="width:90px;">{{$evasao->quantidadeEvasao}}</td>
                @endforeach
            </tr>
            <tr>
                <td>Evs Es %</td>
                @foreach($evasaoUete as $evasao)
                <td scope="col" style="width:90px;">{{$evasao->quantidadeEvasaoPorCent}} %</td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>

<div id="graph" style="margin: 14px auto;width: 50%; text-align: center;"></div>
{!! $lava->render('ColumnChart', 'Evasao Escolar', 'graph') !!}

<!--<div style="margin: 14px auto; width: 20%; text-align: center;">
    <table width="100%">
        <tbody>
            <tr>
                <td style="font-family: 'Times New Roman', Times, serif;
                                            font-size: 12pt">_____________________________________</td>
            </tr>
            <tr>
                <td style="font-family: 'Times New Roman', Times, serif;
                                            font-size: 12pt">ALESSANDRO PEREIRA LOPES - TC</td>
            </tr>
            <tr>
                <td style="font-family: 'Times New Roman', Times, serif;
                                            font-size: 12pt">Ch Seç Coor Pdg P Bas</td>
            </tr>
        </tbody>
    </table>
</div>-->

<script>
    $(document).ready(function() {
        $("body").removeAttr("style"); //remove o display: table; para centralizar tudo
    });
</script>

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
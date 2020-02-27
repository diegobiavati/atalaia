@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Mapa de Controle de Efetivo')

@section('content')

<h3 style="text-align: center; margin-top: -8px; font-size: 18px; font-family: Arial;">
    <u>MAPA DE CONTROLE DE EFETIVO / CFS {{$anoFormacao->ano_per_basico.'-'.$anoFormacao->ano_per_qualificacao}}</u><br />
    Efetivo Desligado em {{ date('d/m/Y H:i:s') }}<br />
</h3>
<div style="margin: 14px auto;width: 50%; text-align: center;">
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
            @if(isset($uete->sigla_uete))
            <tr>
                <td>{{ $uete->sigla_uete }}</td>
                <td>{{ $uete->desligamentoApedido }}</td>
                <td>{{ $uete->desligamentoExOficio }}</td>
                <td>{{ $uete->trancamentoApedido }}</td>
                <td>{{ $uete->trancamentoExOficio }}</td>
                <td>{{ $uete->total }}</td>
            </tr>
            @else
            <tr style="font-weight: bold;font-size:12px">
                <td>{{$uete->total}}</td>
                <td>{{$uete->totDesligamentoApedido}}</td>
                <td>{{$uete->totDesligamentoExOficio}}</td>
                <td>{{$uete->totTrancamentoApedido}}</td>
                <td>{{$uete->totTrancamentoExOficio}}</td>
                <td>{{$uete->totTotal}}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
</div>
<div style="margin: 14px auto;width: 80%; text-align: center;">
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 32px auto; width: 99%;font-size: x-small; font-family: Arial;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col" style="width:3.8%;">UETE</th>
                <th scope="col" style="width:1.8%;">Nº Aluno</th>
                <th scope="col" style="width:20.8%;">Nome Completo</th>
                <th scope="col" style="width:0.8%;">BI</th>
                <th scope="col" style="width:1.8%;">Data</th>
                <th scope="col" style="width:0.8%;">Requerimento</th>
                <th scope="col" style="width:3.8%;">Situação Atual</th>
                <th scope="col" style="width:2.8%;">Motivo</th>
                <th scope="col" style="width:29.8%;">Amparo</th>
            </tr>

            @foreach($alunosSituacoesDiversas as $alunosSituacoesDiversa)
            <tr>
                <td>{{ $alunosSituacoesDiversa->omct->sigla_omct }}</td>
                <td>{{ $alunosSituacoesDiversa->numero }}</td>
                <td>{{ $alunosSituacoesDiversa->nome_completo }}</td>
                <td>{{ $alunosSituacoesDiversa->numero_bi }}</td>
                <td>{{ (isset($alunosSituacoesDiversa->data_bi)) ? \Carbon\Carbon::parse($alunosSituacoesDiversa->data_bi)->format('d/m/Y') : null }}</td>
                <td>{{ ($alunosSituacoesDiversa->solicitacao_situacao == 'EO') ? 'Ex-Oficio':'A Pedido' }}</td>
                <td>{{ $alunosSituacoesDiversa->situacao->descricao }}</td>
                <td>{{ (isset($alunosSituacoesDiversa->motivos)) ? $alunosSituacoesDiversa->motivos->motivo : null }}</td>
                <td>{{ $alunosSituacoesDiversa->amparo }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

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
@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Ficha Disciplinar Individual')

@section('content')

<h3 style="text-align: center; margin-top: -8px; font-size: 18px; font-family: Arial;">
    <u>FICHA DISCIPLINAR INDIVIDUAL / CFGS {{$anoFormacao->ano_per_basico.'-'.$anoFormacao->ano_per_qualificacao}}</u><br />
</h3>

@foreach($alunos as $aluno)
<div style="margin: 40px 100px auto;width: 25%; text-align: center;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>1. IDENTIFICAÇÃO DO MILITAR</b></h6>
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 12px auto; width: 99%;font-size:13px; font-family: Arial;text-align: left;">
        <tr>
            <td style="font-weight:bold;">Número do Aluno</td>
            <td>{{ $aluno->numero }}</td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Nome Completo</td>
            <td>{{ $aluno->nome_completo }}</td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Nome do Pai</td>
            <td>{{ $aluno->nome_pai }}</td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Nome da Mãe</td>
            <td>{{ $aluno->nome_mae }}</td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Nº Identidade Militar</td>
            <td>{{ $aluno->doc_idt_militar }}</td>
        </tr>
    </table>
</div>

<div style="margin: 40px auto;width: 90%; text-align: center;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>2. PUNIÇÕES DISCIPLINARES</b></h6>
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 12px auto; width: 99%;font-size:13px; font-family: Arial;text-align: left;">
        <tr>
            <td style="text-align: center;font-weight:bold;">Nr Punição</td>
            <td style="text-align: center;font-weight:bold;">Data</td>
            <td style="text-align: center;font-weight:bold;">Punição (art.24 do RDE)</td>
            <td style="text-align: center;font-weight:bold;">Nr Dias</td>
            <td style="text-align: center;font-weight:bold;">Enquadramento (Anexo I do RDE)</td>
            <td style="text-align: center;font-weight:bold;">BI e OM</td>
            <td style="text-align: center;font-weight:bold;">Comportamento</td>
        </tr>

        @foreach($aluno->fatds as $fatd)
        <tr>
            <td style="text-align: center;">{{ $fatd->nr_processo }}</td>
            <td style="text-align: center;">{{ App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($fatd->lancamentoFO->data_obs) }}</td>
            <td style="text-align: center;">{{ (isset($fatd->tipo_enquadramento) ? $fatd->tipo_enquadramento->enquadramento : null) }}</td>
            <td style="text-align: center;">{{ (isset($fatd->nr_dias) ? $fatd->nr_dias : 0) }}</td>
            <td>{{ $fatd->enquadramento }}</td>
            <td>{{ $fatd->bi_desc . ' do ' . $aluno->omct->sigla_omct }}</td>
            <td style="text-align: center;">{{ (isset($fatd->comportamento) ? $fatd->comportamento->comportamento : null) }}</td>
        </tr>    
        @endforeach
    </table>
</div>

<div style="margin: 40px auto;width: 90%; text-align: center;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>3. ANULAÇÃO OU CANCELAMENTO DE PUNIÇÕES DISCIPLINARES</b></h6>
    
    <div style="border: 1px solid #000;">
        {{ (isset($aluno->anulacaoCancelamento) ? $aluno->anulacaoCancelamento : 'Sem alterações ou cancelamentos de punições disciplinares') }}
    </div>
</div>

<div style="margin: 40px auto;width: 90%; text-align: center;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>4. RECURSOS DISCIPLINARES</b></h6>
    
    <div style="border: 1px solid #000;">
        {{ (isset($aluno->recursosDisciplinares) ? $aluno->recursosDisciplinares : 'Sem recursos disciplinares') }}
    </div>
</div>

<div style="margin: 40px auto 100px auto;width: 90%; text-align: center;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>5. RECOMPENSAS</b></h6>
    
    <div style="border: 1px solid #000;">
        {{ (isset($aluno->recompensas) ? $aluno->recompensas : 'Sem recompensas') }}
    </div>
</div>
@endforeach


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
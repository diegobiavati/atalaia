@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::FICHA REGISTRO PARA ACOMPANHAMENTO DO DISCENTE')

@section('content')

<h3 style="text-align: center; margin-top: -8px; font-size: 18px; font-family: Arial;">
    <u>FICHA REGISTRO PARA ACOMPANHAMENTO DO DISCENTE / CFGS {{$anoFormacao->ano_per_basico.'-'.$anoFormacao->ano_per_qualificacao}}</u><br />
</h3>

@foreach($alunos as $aluno)
<div style="margin: 40px 100px auto;width: 25%; text-align: center;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>DADOS PESSOAIS DO DISCENTE</b></h6>
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
            <td style="font-weight:bold;">Curso</td>
            <td>{{ $aluno->qms->qms }}</td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Pel/Turma:</td>
            <td>{{ ( session()->has('login.qmsID') ? $aluno->turmaEsa->turma : $aluno->turma->turma )  }}</td>
        </tr>
    </table>
</div>

<div style="margin: 10px auto 100px auto;width: 90%; text-align: center;">
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 12px auto; width: 99%;font-size:13px; font-family: Arial;text-align: left;">
        <tr>
            <td style="text-align: center;font-weight:bold;">Data</td>
            <td style="text-align: center;font-weight:bold;">Observações</td>
            <td style="text-align: center;font-weight:bold;">Conteúdo Atitudinal</td>
            <td style="text-align: center;font-weight:bold;">Providências e Orientações</td>
            <td style="text-align: center;font-weight:bold;">FO</td>
            <td style="text-align: center;font-weight:bold;">Observador/Orientador</td>
        </tr>
        
        @foreach ($aluno->lancamento_fo as $frad)
        @php
        
            $fo = (($frad->tipo == 0) ? 'Negativo' : (($frad->tipo == 1) ? 'Neutro' : 'Positivo'));

            $conteudo_atitudinal = '';
            foreach (json_decode($frad->conteudo_atitudinal) as $atitudinal) {
                foreach ($conteudoAtitudinal as $conteudo) {
                    if ($atitudinal == $conteudo->id) {
                        $conteudo_atitudinal .= $conteudo->descricao . ', ';
                    }
                }
            }

            $conteudo_atitudinal = substr($conteudo_atitudinal, 0, (strlen($conteudo_atitudinal) - 2));
            
        @endphp
            <tr>
                <td>{{ App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($frad->data_obs) }}</td>
                <td>{{ (($frad->cancelado == 'S') ? $frad->cancelado_motivo : $frad->observacao ) }}</td>
                <td>{{ (($frad->cancelado == 'S') ? 'Cancelado' : $conteudo_atitudinal) }}</td>
                <td>{{ (($frad->cancelado == 'S') ? 'Cancelado' : $frad->providencia) }}</td>
                <td>{{ (($frad->cancelado == 'S') ? 'Cancelado' : $fo) }}</td>
                <td>{{ (($frad->cancelado == 'S') ? $frad->operadorCancelado->postograd->postograd_abrev . ' ' . $frad->operadorCancelado->nome_guerra : $frad->operador->postograd->postograd_abrev . ' ' . $frad->operador->nome_guerra)  }}</td>
            </tr>
        @endforeach
    </table>
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
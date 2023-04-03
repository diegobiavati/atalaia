@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Relação de Alunos Prontos')

@section('content')

@if(isset($qmss))
    @foreach($qmss as $qms)
    <h3 style="text-align: center; margin-top: -8px; font-size: 18px;">
        CURSO DE FORMAÇÃO DE SARGENTOS - ANO DE FORMAÇÃO {{$ano_formacao->formacao}}<br />
        {{ $qms->qms }}<br />EFETIVO PRONTO
    </h3>
    <div style="margin: 14px auto; width: 100%;">
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 90%;">
            <tr>
                @if(isset($_GET['ordem']))
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ORDEM</b></td>
                @endif

                @foreach($arrayColunas as $coluna)
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>{{$coluna->description}}</b></td>
                @endforeach

                @if(isset($_GET['alteracao']))
                <td style="border: 1px solid #000; padding: 6px; text-align: center; 5.80cm;"><b>ALTERAÇÃO</b></td>
                @endif
                @if(isset($_GET['ciente']))
                <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 5.80cm;"><b>CIENTE</b></td>
                @endif
            </tr>
            @forelse($alunos as $key => $listaAlunos)

            @if($key == $qms->id)
            @foreach($listaAlunos as $aluno)
            <tr>
                @if(isset($_GET['ordem']))
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$loop->index +1}}</td>
                @endif

                @foreach($arrayColunas as $coluna)
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    @if(App\Http\Controllers\Utilitarios\FuncoesController::validDate($aluno->{$coluna->field}))
                    {{ App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($aluno->{$coluna->field}) }}
                    @elseif($coluna->field == 'qms_id')
                        @if($aluno->qms_id)
                            {{$aluno->qms->qms}}
                        @endif
                    @elseif($coluna->field == 'omcts_id')
                        @if($aluno->omcts_id)
                            {{$aluno->omct->sigla_omct}}
                        @endif    
                    @elseif($coluna->field == 'data_matricula')
                    {{ (isset($aluno->ano_formacao_reintegr_id) ? $aluno->ano_formacao_rematr->formacao : $aluno->ano_formacao->formacao) }}
                    @elseif($coluna->field == 'primeira_data_praca')
                    {{ $aluno->PrimeiraDataPraca() }}
                    @elseif($coluna->field == 'turma_id')
                    {{ $aluno->turma->turma or 'Não informada' }}
                    @elseif($coluna->field == 'area_id')
                    {{ $aluno->area->area or 'Não informada' }}
                    @elseif($coluna->field == 'instrumento_id')
                    {{ $aluno->instrumento['instrumento'] }}
                    @elseif($coluna->field == 'atleta_marexaer')
                    {{ ($aluno->atleta_marexaer == 'S') ? 'Sim' : 'Não' }}
                    @elseif($coluna->field == 'sexo')
                    {{ ($aluno->sexo == 'M') ? 'Masculino' : 'Feminino' }}
                    @elseif($coluna->field == 'nasc_id_uf')
                    {{ $aluno->uf_nascimento['uf_sigla'] }}
                    @elseif($coluna->field == 'id_situacao_anterior')
                    {{ $aluno->situacao_anterior['situacao_anterior'] }}
                    @elseif($coluna->field == 'id_situacao_matricula')
                    {{ $aluno->situacao_matricula['situacao_matricula'] }}
                    @elseif($coluna->field == 'id_situacao_atual')
                    {{ $aluno->situacao_atual['situacao'] }}
                    @elseif($coluna->field == 'id_uf')
                    {{ $aluno->uf['uf_sigla'] }}
                    @elseif($coluna->field == 'id_profissao_mae')
                    {{ $aluno->profissao_mae['profissao'].' '.$aluno->profissao_mae['militar_postograd'] }}
                    @elseif($coluna->field == 'id_profissao_pai')
                    {{ $aluno->profissao_pai['profissao'].' '.$aluno->profissao_pai['militar_postograd'] }}
                    @elseif($coluna->field == 'tatuagem')
                    {{ ($aluno->tatuagem == 'S') ? 'Sim' : 'Não' }}
                    @elseif($coluna->field == 'id_raca')
                    {{ $aluno->raca['descricao'] }}
                    @elseif($coluna->field == 'id_religiao')
                    {{ $aluno->religiao['religiao'] }}
                    @elseif($coluna->field == 'id_escolaridade')
                    {{ $aluno->escolaridade['escolaridade'] }}
                    @elseif($coluna->field == 'id_renda')
                    {{ $aluno->renda['descricao_renda'] }}
                    @elseif($coluna->field == 'id_estado_civil')
                    {{ $aluno->estado_civil['estado_civil'] }}
                    @elseif($coluna->field == 'turma_esa_id')
                    {{ $aluno->turmaEsa->turma }}
                    @else
                    {{ $aluno->{$coluna->field} }}
                    @endif
                </td>
                @endforeach

                @if(isset($_GET['alteracao']))
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"></td>
                @endif
                @if(isset($_GET['ciente']))
                <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 5.80cm;"></td>
                @endif
            </tr>
            @endforeach
            @endif
            @empty
            <tr>
                <td colspan="100" style="text-align: center; padding: 12px;">SEM OCORRÊNCIAS</td>
            </tr>
            @endforelse
        </table>
    </div>
    @endforeach
@else

    @foreach($omct as $uete)
    <h3 style="text-align: center; margin-top: -8px; font-size: 18px;">
        CURSO DE FORMAÇÃO DE SARGENTOS - ANO DE FORMAÇÃO {{$ano_formacao->formacao}}<br />
        {{ $uete->omct }}<br />EFETIVO PRONTO
    </h3>
    <div style="margin: 14px auto; width: 100%;">
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 90%;">
            <tr>
                @if(isset($_GET['ordem']))
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ORDEM</b></td>
                @endif

                @foreach($arrayColunas as $coluna)
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>{{$coluna->description}}</b></td>
                @endforeach

                @if(isset($_GET['alteracao']))
                <td style="border: 1px solid #000; padding: 6px; text-align: center; 5.80cm;"><b>ALTERAÇÃO</b></td>
                @endif
                @if(isset($_GET['ciente']))
                <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 5.80cm;"><b>CIENTE</b></td>
                @endif
            </tr>
            @forelse($alunos as $key => $listaAlunos)

            @if($key == $uete->id)
            @foreach($listaAlunos as $aluno)
            <tr>
                @if(isset($_GET['ordem']))
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$loop->index +1}}</td>
                @endif

                @foreach($arrayColunas as $coluna)
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">

                    @if(App\Http\Controllers\Utilitarios\FuncoesController::validDate($aluno->{$coluna->field}))
                    {{ App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($aluno->{$coluna->field}) }}
                    @elseif($coluna->field == 'qms_id')
                        @if($aluno->qms_id)
                            {{$aluno->qms->qms}}
                        @else
                            <font style="color: red">{{ $aluno->qms->qms }}</font>
                        @endif
                    @elseif($coluna->field == 'data_matricula')
                    {{ $aluno->ano_formacao->formacao }}
                    @elseif($coluna->field == 'primeira_data_praca')
                    {{ $aluno->PrimeiraDataPraca() }}
                    @elseif($coluna->field == 'turma_id')
                    {{ $aluno->turma->turma or 'Não informada' }}
                    @elseif($coluna->field == 'area_id')
                    {{ $aluno->area->area or 'Não informada' }}
                    @elseif($coluna->field == 'instrumento_id')
                    {{ $aluno->instrumento['instrumento'] }}
                    @elseif($coluna->field == 'atleta_marexaer')
                    {{ ($aluno->atleta_marexaer == 'S') ? 'Sim' : 'Não' }}
                    @elseif($coluna->field == 'sexo')
                    {{ ($aluno->sexo == 'M') ? 'Masculino' : 'Feminino' }}
                    @elseif($coluna->field == 'nasc_id_uf')
                    {{ $aluno->uf_nascimento['uf_sigla'] }}
                    @elseif($coluna->field == 'id_situacao_anterior')
                    {{ $aluno->situacao_anterior['situacao_anterior'] }}
                    @elseif($coluna->field == 'id_situacao_matricula')
                    {{ $aluno->situacao_matricula['situacao_matricula'] }}
                    @elseif($coluna->field == 'id_situacao_atual')
                    {{ $aluno->situacao_atual['situacao'] }}
                    @elseif($coluna->field == 'id_uf')
                    {{ $aluno->uf['uf_sigla'] }}
                    @elseif($coluna->field == 'id_profissao_mae')
                    {{ $aluno->profissao_mae['profissao'].' '.$aluno->profissao_mae['militar_postograd'] }}
                    @elseif($coluna->field == 'id_profissao_pai')
                    {{ $aluno->profissao_pai['profissao'].' '.$aluno->profissao_pai['militar_postograd'] }}
                    @elseif($coluna->field == 'tatuagem')
                    {{ ($aluno->tatuagem == 'S') ? 'Sim' : 'Não' }}
                    @elseif($coluna->field == 'id_raca')
                    {{ $aluno->raca['descricao'] }}
                    @elseif($coluna->field == 'id_religiao')
                    {{ $aluno->religiao['religiao'] }}
                    @elseif($coluna->field == 'id_escolaridade')
                    {{ $aluno->escolaridade['escolaridade'] }}
                    @elseif($coluna->field == 'id_renda')
                    {{ $aluno->renda['descricao_renda'] }}
                    @elseif($coluna->field == 'id_estado_civil')
                    {{ $aluno->estado_civil['estado_civil'] }}
                    @else
                    {{ $aluno->{$coluna->field} }}
                    @endif
                </td>
                @endforeach

                @if(isset($_GET['alteracao']))
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"></td>
                @endif
                @if(isset($_GET['ciente']))
                <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 5.80cm;"></td>
                @endif
            </tr>
            @endforeach
            @endif
            @empty
            <tr>
                <td colspan="100" style="text-align: center; padding: 12px;">SEM OCORRÊNCIAS</td>
            </tr>
            @endforelse
        </table>
    </div>
    
    @endforeach
@endif

@if($relacao == 'excel')

{{ $fileName = bcrypt(date('Y-m-d H:i:s')).'.ods' }}

{{header("Content-Description: PHP Generated Data")}}
{{header("Content-Type: application/x-msexcel")}}
{{header("Content-Disposition: attachment; filename=\"{$fileName}\"")}}
{{header("Expires: 0")}}
{{header("Cache-Control: must-revalidate, post-check=0, pre-check=0")}}
{{header("Pragma: no-cache")}}

@endif

<div style="display: flex; justify-content: right; align-items: right;">
<h6 style="margin-top: 20px;width: 30%;"> Consulta Realizada em {{ date('d/m/Y H:i:s') }} </h6>
</div>
<script>
    $(document).ready(function() {
        $("body").removeAttr("style"); //remove o display: table; para centralizar tudo
    });
</script>
@stop
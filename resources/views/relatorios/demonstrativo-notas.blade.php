@extends('relatorios.template-relatorios-sem-brasao')

@section('title', 'Atalaia :: Relatório::Demonstrativo de notas')

@section('titulo-relatorio', '')

@section('content')

@foreach ($alunos_classif as $class)
@php
$data_array = unserialize($class->data_demonstrativo);
//dd($data_array);
@endphp
<div style="width: 940px; margin: 12px auto 56px auto; page-break-after: always;">
    <div style="border: 1px solid #000000; padding: 6px;">
        <b>ALUNO(A):</b> {{$class->aluno->numero}} <b>{{$class->aluno->nome_guerra}}</b> ({{$class->aluno->nome_completo}})<br />
        <b>UETE:</b> {{$class->aluno->omct->sigla_omct}}<br />
        <b>TURMA:</b> {{$class->aluno->turma->turma or 'NÃO INFORMADA'}}
        <div style="float: right; margin-top: -38px;">
            <b>AREA:</b> {{$class->aluno->area->area}}
        </div>
        <div style="clear: both;"></div>
    </div>

    @foreach($data_array as $data)
    @if(isset($data['disciplina_id']))

    @if($data['tfm'] <> 'S')
        <div style="margin: 8px;">
            <b>{{$loop->index +1}}. {{ $data['disciplina_nome'] }}</b>
        </div>
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 0 auto; width: 100%;">
            @if(isset($data['avaliacoes']))

            <tr>
                @foreach($data['avaliacoes'] as $key => $avaliacoes)
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>{{$key}}</b></td>
                @endforeach
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ND</b></td>
            </tr>
            <tr>
                @foreach($data['avaliacoes'] as $key => $avaliacoes)
                @if($key=='FLEXÃO NA BARRA' && $class->aluno->area_id==3)
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">-</td>
                @else
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    @if(isset($avaliacoes->nota))
                    @if(is_numeric($avaliacoes->nota))
                    {{ number_format($avaliacoes->nota, '3', ',', '') }}
                    @else
                    {{ $avaliacoes->nota }}
                    @endif
                    @else
                    
                    @if($key == 'ACR')
                        {{ $data['avaliacoes'][$key] }}
                    @elseif($key == 'CE')
                        {{ $data['avaliacoes'][$key] }}
                    @else
                    Sem Nota
                    @endif
                    
                    
                    @endif
                    
                </td>
                @endif
                @endforeach

                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    @if(isset($data['media_sem_peso']))
                    {{ number_format($data['media_sem_peso'], '3', ',', '') }}
                    @else
                    {{(isset($data['media_anterior']) ? number_format($data['media_anterior'], '3', ',', '') : (is_numeric($data['media']) ? number_format($data['media'], '3', ',', '') : $data['media']) ) }}
                    @endif
                </td>
            </tr>
            @else
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">NÃO HÁ AVALIAÇÕES LANÇADAS</td>
            </tr>
            @endif
        </table>
    @endif

    @endif

    {{-- {{var_dump($data)}} --}}
    @endforeach

        @php
        $valida_col_tfm = false;
        @endphp
        @isset($data_array['avaliacoes_tfm'])
        <div style="margin: 8px;">
            <b>TREINAMENTO FÍSICO MILITAR</b>
        </div>
        
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 0 auto; width: 100%;">
            @foreach($data_array['avaliacoes_tfm'] as $key_aval => $data)
            @if(is_numeric($key_aval))
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;background-color: #dfdfdf;" colspan="{{$data_array['avaliacoes_tfm']['colspan_demonstrativo']}}"><b>{{ $data['disciplina_nome'] }}</b></td>
                @if(!$valida_col_tfm)
                <td style="border: 1px solid #000; padding: 6px; text-align: center;background-color: #dfdfdf;"><b>ND TFM</b></td>

                @endif
            </tr>
            @if(isset($data['avaliacoes']))
            <tr>
                @php
                $valida_colspan = false;
                @endphp

                @foreach($data['avaliacoes'] as $key => $avaliacoes)

                @if($valida_colspan)
                <td style="border: 1px solid #000; padding: 6px; text-align: center; background-color: #eee;"><b>{{$key}}</b></td>
                @else
                <td style="border: 1px solid #000; padding: 6px; text-align: center; background-color: #eee;" colspan='{{ ($data_array['avaliacoes_tfm']['colspan_demonstrativo'] - count($data['avaliacoes'])) }}'><b>{{$key}}</b></td>
                @endif

                @php
                $valida_colspan = true;
                @endphp
                @endforeach
                <td style="border: 1px solid #000; padding: 6px; text-align: center;background-color: #eee;"><b>NA</b></td>

                @if(!$valida_col_tfm)
                <td style="text-align: center;" rowspan="{{ (($loop->count * 2) - 1) }}">{{$data_array['avaliacoes_tfm']['media_tfm']}}</td>
                @php
                $valida_col_tfm = true;
                @endphp
                @endif
            </tr>
            <tr>
                @php
                $valida_colspan = false;
                @endphp
                
                @foreach($data['avaliacoes'] as $key => $avaliacoes)

                <td style="border: 1px solid #000; padding: 6px; text-align: center;" {{ (($valida_colspan) ? '' : 'colspan='.($data_array['avaliacoes_tfm']['colspan_demonstrativo'] - count($data['avaliacoes'])).'') }}>
                    @if((isset($avaliacoes->nota) && is_numeric($avaliacoes->nota)))
                    {{ number_format($avaliacoes->nota, '3', ',', '') }}
                    @elseif($key == 'CE')
                    {{ $avaliacoes }}
                    @elseif($key == 'ACR')
                    @if($data['tfm'] == 'S' && $data['tfm_abdominal'] == 'S')
                    @php
                    $abdominal = ($avaliacoes == 'S') ? 'SUFICIENTE': 'INSUFICIENTE' ;
                    @endphp
                    {{ $abdominal }}
                    @else
                        {{ $avaliacoes}}
                    @endif
                    @else
                    @php
                    $abdominal = (isset($avaliacoes->nota) && $avaliacoes->nota == 'S') ? 'SUFICIENTE': 'INSUFICIENTE' ;
                    @endphp
                    {{ $abdominal }}
                    @endif
                </td>

                @php
                $valida_colspan = true;
                @endphp

                @endforeach

                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                
                    @if(isset($data['media_sem_peso']))
                    
                    @if(isset($data['AR']))
                    {{ (!is_numeric($data['AR'])) ? ($data['AR'] == 'S' ? 'SUFICIENTE':'INSUFICIENTE') : number_format($data['AR'], '3', ',', '') }}
                    @else
                    {{ ($data['tfm_abdominal'] == 'S') ? ($data_array['avaliacoes_tfm']['media_tfm_abdominal'] == 'S' ? 'SUFICIENTE':'INSUFICIENTE') : number_format($data['media_sem_peso'], '3', ',', '') }}
                    @endif
                    
                    @else
                    @if(isset($data['tfm_abdominal']) && $data['tfm_abdominal'] == 'S')
                    {{ $abdominal }}
                    @else
                    {{(isset($data['media_anterior']) ? number_format($data['media_anterior'], '3', ',', '') : (is_numeric($data['media']) ? number_format($data['media'], '3', ',', '') : $data['media']) ) }}
                    @endif
                    
                    @endif
                </td>
            </tr>
            @else
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">NÃO HÁ AVALIAÇÕES LANÇADAS</td>
            </tr>
            @endif

            @endif
            @endforeach
        </table>
        @endisset

        <div style="margin: 8px;">
            <b>SITUAÇÃO GERAL</b>
        </div>
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 0 auto; width: 100%;">
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>N1</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>MENÇÃO</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>CLASSIFICAÇÃO POR ÁREA E SEGMENTO</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>CLASSIFICAÇÃO GERAL</b></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{number_format($class->nota_final, '3', '.', '')}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    @php

                    foreach($mencoes as $mencao){
                    if($class->nota_final>=$mencao->inicio && $class->nota_final<=$mencao->fim){
                        $mencao_aluno = $mencao->mencao;
                        break;
                        }
                        }

                        @endphp
                        {{$mencao_aluno or 'Não calculada'}}
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    {{ $class->classificacao_por_area }}
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    {{ $class->classificacao }}
                </td>
            </tr>
        </table>

        <div style="margin-top: 10px;">
            <b><u>Observações :</u></b>
            <div class="clean"></div>
            - Classificação por Área e Segmento: classificação válida para o Processo de Escolha de QMS dentro de cada segmento (Masc e Fem) na Área Cmb/Log-Tec/Aviação.
            <div class="clean" style="margin-top: 10px;"></div>
            - Classificação Geral: Classificação na Área contendo os segmentos masculino e feminino.
            <div class="clean" style="margin-top: 10px;"></div>
            - De acordo com os Art 59 e 67 das NIAA, o grau dos alunos aprovados por decisão do Diretor de Ensino, após o julgamento do Conselho de Ensino,
            não será computado para o cálculo da Nota do 1º Ano (N1) e da Nota Final de Curso (NFC), sendo utilizados os graus obtidos na disciplina antes da recuperação.
            <div class="clean" style="margin-top: 10px;"></div>
            <b><u>Cálculo Notas :</u></b>
            <div class="clean"></div>
            Avaliação de Acompanhamento (AA): peso 1
            <div class="clean"></div>
            Avaliação de Controle (AC): peso 2
            <div class="clean" style="margin-top: 10px;"></div>
            <table>
                <tr>
                    <td style="padding-right: 6px;">Nota da Disciplina (ND) :</td>
                    <td style="border: 1px solid #000; padding-right: 6px;" rowspan="1"> ND = <u>AA + (2 x AC)</u> <br>
                        <center style="padding-left: 30px;">3</center>
                    </td>
                    <td style="padding-right: 6px;"></td>
                    <td style="border: 1px solid #000; padding-right: 6px;" rowspan="1"> ND = <u>AA1 + AA2 + (2 x AC)</u> <br>
                        <center style="padding-left: 30px;">4</center>
                    </td>
                </tr>
            </table>
            <div class="clean" style="margin-top: 10px;"></div>
            <table>
                <tr>
                    <td style="padding-right: 6px;">Nota do 1º ano CFGS (N1) :</td>
                    <td style="border: 1px solid #000; padding-right: 6px;" rowspan="1"> N1 = <u>ND1 + ND2 + ... + NDm</u>, onde m é a quantidade de disciplinas <br>
                        <center style="padding-right: 230px;">m</center>
                    </td>
                </tr>
            </table>
            <div class="clean" style="margin-top: 10px;"></div>
            <b><u>Menções :</u></b>
            <div class="clean" style="margin-top: 10px;"></div>
            <table>
                <tr>
                    <td style="border: 1px solid #000; text-align:center; width:80px;">Menção</td>
                    <td style="border: 1px solid #000; text-align:center; width:120px;">Faixa de Notas</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; text-align:center;">I</td>
                    <td style="border: 1px solid #000; text-align:center;">0,000 - 4,999</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; text-align:center;">R</td>
                    <td style="border: 1px solid #000; text-align:center;">5,000 - 6,999</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; text-align:center;">B</td>
                    <td style="border: 1px solid #000; text-align:center;">7,000 - 7,9999</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; text-align:center;">MB</td>
                    <td style="border: 1px solid #000; text-align:center;">8,000 - 9,499</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; text-align:center;">E</td>
                    <td style="border: 1px solid #000; text-align:center;">9,500 - 10,0000</td>
                </tr>
            </table>
        </div>

</div>

{{-- {{var_dump($data_array)}} --}}

@endforeach
<script>
    $(document).ready(function() {
        $("body").removeAttr("style"); //remove o display: table; para centralizar tudo
    });
</script>
@stop
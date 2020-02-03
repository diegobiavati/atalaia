@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Demonstrativo de notas')

@section('titulo-relatorio', '')

@section('content')

@foreach ($alunos_classif as $class)
    @php
        $data_array = unserialize($class->data_demonstrativo);
    @endphp
    <div style="width: 940px; margin: 12px auto 56px auto; page-break-after: always;">
        <div style="border: 1px solid #000000; padding: 6px;">
            <b>ALUNO(A):</b> {{$class->aluno->numero}} {{$class->aluno->nome_guerra}} ({{$class->aluno->nome_completo}})<br />
            <b>UETE:</b> {{$class->aluno->omct->sigla_omct}}<br />
            <b>TURMA:</b> {{$class->aluno->turma->turma or 'NÃO INFORMADA'}}
            <div style="float: right; margin-top: -38px;">
                <b>AREA:</b> {{$class->aluno->area->area}}
            </div>
            <div style="clear: both;"></div>
        </div>

        @foreach($data_array as $data)
            @if(isset($data['disciplina_id']))
                <div style="margin: 8px;">
                    <b>{{$loop->index +1}}. {{ $data['disciplina_nome']}}</b>
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
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$avaliacoes}}</td>
                            @endif
                            @endforeach
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{number_format($data['media'], '3', '.', '')}}</td>
                        </tr>  
                    @else
                        <tr>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">NÃO HÁ AVALIAÇÕES LANÇADAS</td>
                        </td>
                    @endif                        
                </table>                         
            @endif
          {{-- {{var_dump($data)}} --}} 
        @endforeach

        <div style="margin: 8px;">
            <b>SITUAÇÃO GERAL</b>
        </div>
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 0 auto; width: 100%;">
                <tr>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NPB</b></td>
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
                        @php
                            $key = array_search($class->aluno_id, $class_por_area_seg[$class->aluno->sexo][$class->aluno->area_id]);
                        @endphp
                        {{$key + 1}}
                    </td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                        @php
                            $key = array_search($class->aluno_id, $class_geral);
                        @endphp
                        {{$key + 1}}
                    </td>
                </tr>
        </table> 
    </div>   
    
    {{-- {{var_dump($data_array)}} --}} 

@endforeach

@stop
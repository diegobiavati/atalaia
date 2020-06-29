@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Alunos Reprovados(Conselho e Recuperação)')

@section('titulo-relatorio', 'Alunos Reprovados (Conselho e Recuperação) - FORMAÇÃO '. $anoFormacao->formacao)

@section('content')
@php

@endphp

@if(count($alunosClassificacao)>0)

<div style="width: 1600px; margin: 4px auto; page-break-after: always; text-align: center;">
    @foreach($areas as $area)

    <h4 style="text-align: center; margin-top: 18px;">CLASSIFICAÇÃO POR ÁREA</h4>
    <h4 style="text-align: center;">{{$area->area}}</h4>

    <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 90%; text-align: center;">
        <tr style="background-color: #E6E6E6;">
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME COMPLETO</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>SEGMENTO</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>OMCT</b></td>
            @foreach ($disciplinas as $disciplina)
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ND<br />{{$disciplina->nome_disciplina_abrev}}</b></td>
            @endforeach
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ND<br />TFM</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>N1</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>MENÇÃO</b></td>
        </tr>

        @foreach($alunosClassificacao as $aluno)
            @if($aluno->aluno->area_id==$area->id)
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->numero }}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->nome_completo }}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->sexo }}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->omct->sigla_omct }}</td>
                @foreach ($disciplinas as $disciplina)
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">                    
                        @if(isset($notas_data_array[$aluno->aluno_id]['data_demonstrativo']))
                        
                            @foreach ($notas_data_array[$aluno->aluno_id]['data_demonstrativo'] as $item)
                                @if(isset($item['disciplina_id']) && $item['disciplina_id']==$disciplina->id)                                           
                                    {{ number_format($item['media'], '3', ',', '') }}
                                @endif
                                @if(isset($item['disciplina_id']) && $item['disciplina_id']==99999)
                                    @php    
                                        $media_tfm=number_format($item['media'], '3', ',', '');
                                    @endphp
                                @endif
                            @endforeach
                        @endif
                    </td>                            
                @endforeach
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{!!$media_tfm or '--'!!}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ number_format($aluno->nota_final_arredondada, '3', ',', '') }}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    @php

                    foreach($mencoes as $mencao){
                        if($aluno->nota_final_arredondada>=$mencao->inicio && $aluno->nota_final_arredondada<=$mencao->fim){
                            $mencao_aluno = $mencao->mencao;
                            break;
                        }
                    }

                    @endphp
                    {{$mencao_aluno or 'Não calculada'}}                            
                </td>
            </tr>
            @endif
        @endforeach
    </table>
    @endforeach
</div>

@else

<div style="text-align: center;">CLASSIFICAÇÃO INDISPONÍVEL PARA ESSE UNIVERSO</div>

@endif
<script>
    $(document).ready(function() {
        $("body").removeAttr("style"); //remove o display: table; para centralizar tudo
    });
</script>
@stop
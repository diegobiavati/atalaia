@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Classificação Geral de Alunos')

@section('titulo-relatorio', 'CLASSIFICAÇÃO CFGS - FORMAÇÃO '. $ano_selecionado)

@section('content')

@if(count($listaAlunoQms)>0)

<div style="width: 1600px; margin: 4px auto; page-break-after: always; text-align: center;">
   
        @if($_GET['options_class_geral']==10)

            @foreach($qmss as $qms)
                <h4 style="text-align: center; margin-top: 18px;">CLASSIFICAÇÃO POR QMS</h4>
                    <h4 style="text-align: center;">{{$qms->qms}}</h4>
                    <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 90%; text-align: center;">
                        <tr style="background-color: #E6E6E6;">
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME COMPLETO</b></td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>SEGMENTO</b></td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>UETE</b></td>
                            @foreach ($disciplinas as $disciplina)
                            
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ND<br />{{$disciplina->nome_disciplina_abrev}}</b></td>                            
                            @endforeach
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ND<br />TFM</b></td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>N1</b></td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>MENÇÃO</b></td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Class QMS</b></td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Class Geral</b></td>
                        </tr>

                        <?php
                            $i=1;
                        ?>
                        @foreach($listaAlunoQms[$qms->qms_sigla] as $aluno)
                                @unset($media_tfm)
                                <tr> 
                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->numero }}</td>
                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->nome_completo }}</td>
                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->sexo }}</td>
                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->omct->sigla_omct }}</td>

                                    @foreach ($disciplinas as $disciplina)
                                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">                                
                                        @foreach($aluno->data_demonstrativo as $key => $data_demonstrativo)
                                            @if(is_array($data_demonstrativo)) 
                                                @if($disciplina->id == $data_demonstrativo['disciplina_id'])

                                                    @if(isset($data_demonstrativo['media_sem_peso']))
                                                        {{ number_format($data_demonstrativo['media_sem_peso'], 3, ',', '') }}
                                                    @else
                                                        {{ (is_numeric($data_demonstrativo['media']) ? number_format($data_demonstrativo['media'], '3', ',', '') : $data_demonstrativo['media']) }}
                                                    @endif
                                                    
                                                    @if(isset($media_tfm))
                                                        @break
                                                    @endif
                                                @endif

                                                @if($data_demonstrativo['disciplina_id'] == 99999)
                                                    @php    
                                                        $media_tfm = number_format($data_demonstrativo['media'], '3', ',', '');
                                                    @endphp
                                                @endif
                                            @endif
                                        @endforeach
                                        </td>
                                    @endforeach
                                    
                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $media_tfm or '--'}}</td>
                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ number_format($aluno->classificacao->nota_final_arredondada, '3', ',', '') }}</td>

                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->mencao->mencao }}</td>
                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $i }}</td>
                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->classificacao->classificacao }}</td>
                                </tr>
                                <?php
                                    $i++;
                                ?>
                        @endforeach
                    </table>
            @endforeach

        
        @endif 
        
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
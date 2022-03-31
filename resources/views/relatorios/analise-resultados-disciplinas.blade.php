@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório:: Análise Parcial de Resultados')

{{-- @section('titulo-relatorio', 'ESCOLHA DE QMS - CFS - FORMAÇÃO '. $ano_selecionado) --}}

@section('content')

<div style="width: 960px; margin: 4px auto; page-break-after: always; text-align: center;">
    
    <h3 style="text-align: center; margin-top: -28px">ANÁLISE PARCIAL DO RESULTADO DA NOTA DA DISCIPLINA (ND)</h3>
    <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 100%;">
        <tr style="background-color: #E6E6E6;">
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>UETE</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Período</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Disciplina</b></td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Efetivo</b></td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$omct}}</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$disciplina_data['periodo']}}</td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                {{$disciplina_data['nome']}}<br>
                @foreach ($avaliacoes as $avaliacao)
                    <span style="font-size: 10px">
                        {{ $avaliacao->nome_completo }}
                        @if(!$loop->last)
                        ,
                        @endif
                    </span>
                @endforeach
            </td>
            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$disciplina_data['efetivo']}}</td>
        </tr>
    </table>

    <div style="text-align: left">
        <p>
            <b>1. Média aritmética:</b> {{$disciplina_data['media_aritmetica']}}
        </p>    
        <p>
            <b>2. Limite da amplitude:</b>
            <ul>
                <li>Maior nota: {{ number_format($disciplina_data['maior'], 3, ',', '') }}</li>
                <li>Menor nota: {{ number_format($disciplina_data['menor'], 3, ',', '') }}</li>
                <li>Amplitude: {{$disciplina_data['amplitude']}}</li>
            </ul>
        </p>
        
        <p>
            <b>3. Distribuição de notas em relação a média:</b>
        </p>          
        <table style="border: 1px solid #000; border-collapse: collapse; margin-top: 26px; width: 45%;">
            <tr style="background-color: #E6E6E6;">
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Situação</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Quantidade</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>%</b></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">x>5</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$disciplina_data['com_media']}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{number_format(($disciplina_data['com_media']*100)/$disciplina_data['efetivo'], 1, ',', '')}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">x<5</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$disciplina_data['sem_media']}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{number_format(($disciplina_data['sem_media']*100)/$disciplina_data['efetivo'], 1, ',', '')}}</td>
            </tr>
        </table>

        <p>
            <b>4. Distribuição de notas pelas faixas de menção:</b>        
        </p>
        <table style="width: 100%;">
            <tr>
                <td style="width: 45%">
                    <table style="border: 1px solid #000; border-collapse: collapse; margin-top: 8px; width: 100%;">
                        <tr style="background-color: #E6E6E6;">
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Menção</b></td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Frequência</b></td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>%</b></td>
                        </tr>
                        @foreach($disciplina_data['mencoes'] as $key => $item)
                            <tr>
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$key}}</td>   
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$item}}</td>   
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{number_format(($item*100)/$disciplina_data['efetivo'], 1, ',', '')}}</td>   
                            </tr>
                        @endforeach
                    </table>                    
                </td>
                <td style="width: 55%">
                        <div id="chart-div"></div>
                        <?= Lava::render('PieChart', 'IMDB', 'chart-div') ?>
                </td>
                </tr>
            <tr>
        </table> 
        
        <p>
            <b>5. Notas obtidas:</b>        
        </p>
        <table style="width: 100%;">
            <tr>
                <td style="width: 45%">
                    <table style="border: 1px solid #000; border-collapse: collapse; margin-top: 8px; width: 100%;">
                        <tr style="background-color: #E6E6E6;">
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Notas</b></td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Frequência</b></td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>%</b></td>
                        </tr>                        
                        @foreach($intervalo as $key => $item)

                            @if(isset($notas_obtidas[$key]))
                                <tr>
                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$item}}</td>
                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{array_sum($notas_obtidas[$key])}}</td>  
                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{number_format((array_sum($notas_obtidas[$key])*100)/$disciplina_data['efetivo'], 1, ',', '')}}</td>  
                                </tr> 
                            @endif

                        @endforeach
                    </table>                    
                </td>
                <td style="width: 55%">
                    <div id="perf_div" style="margin-left: 18px;"></div>
                    <?= Lava::render('ColumnChart', 'faixas', 'perf_div') ?>
                </td>
            </tr>
        </table>
        <p>
            <b>6. Alunos com nota abaixo de 5: </b> {{$disciplina_data['sem_media']}}  aluno(s)     
        </p>                          

    </div>

</div>

<script>
    $(document).ready(function() {
        $("body").removeAttr("style"); //remove o display: table; para centralizar tudo
    });
</script>
@stop
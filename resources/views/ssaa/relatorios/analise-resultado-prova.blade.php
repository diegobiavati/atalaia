<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{asset('/css/app.css')}}" rel="stylesheet" type="text/css" />
    <title>Análise de Resultado de Prova</title>
    <style>

        * {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
            background-color: #fff!important;
        }

        .break { 
            page-break-before: always; 
        }

        /*.footer {
            position: fixed;
            bottom: 0px;
            left: 10;
            width: 100%;
            padding: 5px 10px 10px 10px;
            text-align: right;
            background: #231442;
            color: #d90c59;
            right: 10%;
        }*/

        /*.footer .page:after {
            content: 'Página ' counter(page);
        }*/

        div .col span {
            font-weight: bold;
        }

        div table tr, th, td {
            border: 1px solid #828282;
        }

        .columnTable {
            float: left;
            text-align: center;
        }

        .table td, .table th {
            padding: 0px;
            vertical-align: inherit;
            border-top: none;
        }

        div .brasao img {
            width: 100px!important;
            height: 100px;
        }
    </style>
</head>

<body>
    <div class="container">
        
        <div class="header" style="margin-top: 100px;">
            @include('relatorios.template-brasao-esa')
        </div>

        <div style="text-align: center;">
            <h5 style="margin-top:10px;text-decoration:underline;font-weight:bold;">ANÁLISE DE RESULTADO DE PROVA</h5>
        </div>

        <div class="table-responsive" style="margin-top:10px;">
            <table class="table columnTable" style="width:30%;padding: 0px 5px 0px 0px;">
                <tr>
                    <th><span>Período</span></th>
                    <th rowspan="2"><span>{{ $esaAvaliacoes->nome_avaliacao }}</span></th>
                </tr>
                <tr>
                    <td>{{ App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($esaAvaliacoes->realizacao) }}</td>
                </tr>
            </table>
        
            <table class="table columnTable" style="width:68%;padding: 0px 0px 0px 5px;margin-left:18px;">
                <tr>
                    <th><span>Disciplina</span></th>
                    <th><span>2º Ano CFGS</span></th>
                    <th><span>Efetivo</span></th>
                </tr>
                <tr>
                    <td><span>{{ $esaAvaliacoes->esadisciplinas->nome_disciplina_abrev }}</span></td>
                    <td><span>{{ $esaAvaliacoes->esadisciplinas->qms->qms }}</span></td>
                    <td><span>{{ $efetivo }}</span></td>
                </tr>
            </table>
        </div>
        
        <div class="row" style="margin-top: 10px;">
            <div class="col">
                <div>1. Realizaram a prova: <span>{{ $realizaram }}</span></div>
                <div>2. Média Aritimética: <span>{{ number_format($mediaAritmetica, 3, ',', '.') }}</span></div>
                <div>3. Limites da Amplititude: <span style="float:right;margin-right: 40%;font-weight:normal;">Média - Rendimento <span style="text-transform: uppercase;">{{ App\Models\Mencoes::getMencaoV2($mediaAritmetica)->mencao_completa }}</span></span>
                    <div style="margin-left: 15px;">a. Maior nota: <span>{{ number_format($maiorNota, 3, ',', '.') }}</span></div>
                    <div style="margin-left: 15px;">b. Menor nota: <span>{{ number_format($menorNota, 3, ',', '.') }}</span></div>
                    <div style="margin-left: 15px;">c. Amplititude: <span>{{ number_format($maiorNota - $menorNota, 3, ',', '.') }}</span></div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 10px;">
            <div class="col" >
                <div class="row">
                    <div class="col" style="border:none;max-width:350px;">
                        <div>4. Distribuição das notas em relação à média:</div>


                            <div class="table-responsive" style="text-align: center;">
                                <table class="table table-striped">
                                    <tr>
                                        <th><span>Situação</span></th>
                                        <th><span>Quant</span></th>
                                        <th><span>%</span></th>
                                    </tr>
                                    <tr>
                                        <td>Acima da média</td>
                                        <td>{{$acima_media}}</td>
                                        <td>{{number_format((($acima_media / $realizaram) * 100), 2, ',', '.')}}%</td>
                                    </tr>
                                    <tr>
                                        <td>Abaixo da média</td>
                                        <td>{{$abaixo_media}}</td>
                                        <td>{{number_format((($abaixo_media / $realizaram) * 100), 2, ',', '.')}}%</td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                    </div>
                <div class="row">
                    <div class="col" style="border:none;">
                        <div>5. Distribuição das notas pelas faixas de menção:</div>

                        <div class="table-responsive" style="text-align: center;">
                            <table class="table table-striped">
                                <tr>
                                    <th><span>Menção</span></th>
                                    <th><span>Quant</span></th>
                                    <th><span>%</span></th>
                                </tr>
                                @foreach($mencoes as $mencao)
                                    <tr>
                                        <td>{{$mencao->mencao}}</td>
                                        <td>{{$mencao->quantidade}}</td>
                                        <td>{{ number_format($mencao->porcentagem, 2, ',', '.') }}%</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col" style="border:none;">
                <div id="chart-div" style="width: 550px;height:280px;margin-top:5px;">
                    {!! $lava->render('PieChart', 'Graph', 'chart-div') !!}
                </div>
            </div>
        </div>

        <div class="row" style="margin-top:10px;">
            <div class="col" style="border:none;">
                <div>6. Notas Obtidas:</div>

                <div class="table-responsive" style="text-align: center;">
                    <table class="table">
                        <tr>
                            <th><span>Menção</span></th>
                            <th><span>Notas</span></th>
                            <th><span>Freq</span></th>
                            <th><span>%</span></th>
                        </tr>
                        
                        @foreach($mencoes as $mencao)
                            <tr>
                                <td rowspan={{ (count($mencao->getFrequencia()[0]) + 1) }}> {{$mencao->mencao}} </td>
                                
                                @foreach($mencao->getFrequencia()[0] as $item => $frequencia)
                                <tr>
                                    <td>{{ $item }}</td>
                                    <td>{{ $frequencia['freq'] }}</td>
                                    <td>{{ number_format($frequencia['%'], 2, ',', '.') }}%</td>
                                </tr>
                                @endforeach
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="col">
                <div id="histo-div" style="width: 550px;height:280px;margin-top:30px;">
                    {!! $lava->render('ColumnChart', 'Histograma', 'histo-div') !!}
                </div>
            </div>
        </div>

        <div class="row" style="margin-top:10px;">
            <div class="col" style="border:none;">
                <div>7. Alunos com nota(s) abaixo de 5 (CINCO): <span>{{ $abaixo_5  }}</span></div>
            </div>
        </div>

        <div class="row" style="margin-top:10px;">
            <div class="col" style="border:none;">
                <div>8. Aluno(s) que faltou(aram) a prova : <span>{{ ( $efetivo - $realizaram )  }}</span></div>
            </div>
        </div>

        <div class="row" style="margin-top:100px;">
            <div class="col" style="border:none;text-align:center;">
                <div><span>Três Corações, MG, {{date('d')}} de {{ App\Http\Controllers\SSAA\Calendario\ControllerCalendario::portuguese_month(date('M')) }} de {{date('Y')}} - {{date('H:i:s')}}</span></div>
            </div>
        </div>

        <div style="text-align: center;">
            <img src="{{ route('gaviao.ajax.relatorios.assinatura.mostrar', $assinatura->id) }}" alt="Image" width="auto" height="100"/>
        </div>

        <div class="break"></div>

        <div class="footer">
            <span class="page"></span>
        </div>
    </div>

    @include('ssaa.relatorios.ficha-controle-resultado-avaliacao')

    <div class="break"></div>

    @include('ssaa.relatorios.demonstrativo-individual-reservado')
</body>

</html>
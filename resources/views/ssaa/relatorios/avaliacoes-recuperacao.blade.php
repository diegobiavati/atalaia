<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{asset('/css/app.css')}}" rel="stylesheet" type="text/css" />

    <title>Relação de Aluno(s) em Recuperação</title>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
            background-color: #fff !important;
        }

        .curso strong {
            font-family: "Times New Roman", Times, serif;
            font-size: 20px;
            background-color: #fff !important;
        }

        .break {
            page-break-before: always;
        }

        div .col span {
            font-weight: bold;
        }

        .columnTable {
            float: left;
            text-align: center;
        }

        .table td,
        .table th {
            padding: 0px;
            vertical-align: inherit;
        }

        div .brasao img {
            width: 100px !important;
            height: 100px;
        }

        .border {
            border-width: 3px !important;
            margin-top: 1rem;
        }
    </style>
</head>

<body class="container">

    <!-- Cabeçalho -->
    <div class="header" style="margin-top: 30px;">
        @include('relatorios.template-brasao-esa')
    </div>

    <!-- Informações do Curso -->
    <div class="border rounded p-3">

        <div class="row align-items-center curso">
            <div class="col-auto">
                <img src="{{ asset($esaDisciplinas->qms->qmsMatriz->img_ssaa) }}" class="img-fluid" style="width: 50px;" />
            </div>
            <div class="col">
                <h6 class="mb-0"><strong>Curso de {{$esaDisciplinas->qms->qms}}</strong></h6>
                <h6 class="mb-0"><strong>SSAA</strong></h6>
            </div>
            <div class="col-auto">
                <div class="row" style="margin-right: 0px;">
                    <h6 class="mb-0"><strong>CFGS: {{ $esaDisciplinas->qms->escolhaQms->anoFormacao->ano_cfs }}</strong></h6>
                </div>
                <div class="row" style="margin-right: 0px;">
                    <h6 class="mb-0"><strong>Período: 2º Ano</strong></h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Título -->
    <div style="text-align: center;margin-top: 1rem;">
        <h5 style="text-decoration:underline;font-weight:bold;">Relação de Aluno(s) em Recuperação</h5>
    </div>

    <!-- Disciplina -->
    <p>DISCIPLINA: <strong>{{ $esaDisciplinas->nome_disciplina_abrev }}</strong></p>

    <!-- Tabela de Alunos -->
    <table class="table text-center">
        <thead>
            <tr>
                <th>Ord</th>
                <th>Nº</th>
                <th>Nome de Guerra</th>
                <th>Turma</th>
                <th>ND</th>
                <th>Bônus Atleta</th>
                <th>Ciente do Aluno</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resultados as $resultado)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $resultado->aluno->numero }}</td>
                <td>{{ $resultado->aluno->nome_guerra }}</td>
                <td>{{ $resultado->aluno->turmaESA->turma }}</td>
                <td>{!! number_format(json_decode($resultado->avaliacoes_resultados, true)['ND'], 3, ',', '.') !!}</td>
                <td>-</td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="row" style="margin-top:100px;">
        <div class="col" style="border:none;text-align:center;">
            <div><span>Três Corações, MG, {{date('d')}} de
                    {{ App\Http\Controllers\SSAA\Calendario\ControllerCalendario::portuguese_month(date('M')) }} de
                    {{date('Y')}} - {{date('H:i:s')}}</span></div>
        </div>
    </div>

    <!-- Assinatura -->
    <div style="text-align: center;">
        <img src="{{ route('gaviao.ajax.relatorios.assinatura.mostrar', $assinatura->id) }}" alt="Image" width="auto"
            height="100" />
    </div>

    <div class="break"></div>

    <div class="footer">
        <span class="page"></span>
    </div>
</body>

</html>
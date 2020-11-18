<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="/css/app.css" rel="stylesheet" type="text/css" />
    <script src="/js/jquery/jquery-3.3.1.js"></script>
    <title>@yield('title')</title>
    <style>
        div.imagem_aluno {
            background-size: contain;
            width: 140px;
            height: 155px;
            border: 1px solid #000;
        }

        body {
            font-size:0.7rem;
        }
    </style>
</head>

<body style="display: table;">

    <div style="margin-bottom: 20px;">
        <div style="margin: 64px 0 54px 0;">
            <h3 style="text-align: center;">
                @yield('titulo-relatorio')
            </h3>
        </div>
        <div>
            @yield('content')
        </div>
    </div>

</body>

</html>
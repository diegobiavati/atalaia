<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bem-vindo</title>
</head>
<body>
    <h4>Bem-vindo ao Sistema Atalaia</h4>
    <p>
        Esta é uma mensagem automática, por favor não responda.<br />
    </p>
        @foreach($militar as $info)
			<p>{{$info->arquivo_nome}}</p>
		@endforeach
</body>
</html>
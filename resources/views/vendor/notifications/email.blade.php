<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
    <h4>Olá</h4>
    <p>
        Esta é uma mensagem automática gerada pelo sistema para definir/redefinir sua senha de acesso.<br />
        Certifique-se de estar conectado a EBNet para ter acesso ao link de restauração.<br />
        <a href="{{$data['link']}}" target="_blank">Clique aqui para acessar o página</a>
    </p>
    <p>
        Se você estiver tendo problemas acessar o link, copie e cole o endereço na barra do navegador: <a href="{{$data['link']}}" target="_blank">{{$data['link']}}</a>
    </p>
    <p>{{$data['contato']}}</p>
</body>
</html>

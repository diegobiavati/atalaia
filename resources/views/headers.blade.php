<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--<script src="/js/jquery/jquery-3.3.1.js"></script>-->

    <!--<script src="/js/bootstrap-5.1.3.min.js"></script>-->
    <script src="/js/app.js"></script>
    <!--<link href="/css/bootstrap-5.1.3.edit.css" rel="stylesheet" type="text/css" />-->
    <link href="/css/app.css" rel="stylesheet" type="text/css" />
    @yield('js-includes')
    @yield('css-includes')
    <link rel="icon" href="/images/favicon.png" type="image/x-icon" />
    <link rel="shortcut icon" href="/images/favicon.png" type="image/x-icon" /> 
    <title>@yield('title')</title>
    @yield('css-styles-includes')

    <style type="text/css">
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            z-index: 10;
            background-color: #E6E6E6;
        }

        .footer-copyright {
            display: flex;
            justify-content: space-between; /* Mantém o espaçamento entre os elementos */
            align-items: center; /* Centraliza verticalmente */
            text-align: center;
        }

        .footer-center {
            flex: 1; /* Ocupa todo o espaço disponível */
            text-align: center; /* Centraliza o texto */
        }
    </style>
</head>

<body>

@yield('content')

<div id="back_to_top">
    <i class="ion-android-arrow-up"></i>
</div>

<script>
    /* PRELOADING LOADINGS */
    
    var image1 = new Image();
    var image2 = new Image();
    var image3 = new Image();
    var image4 = new Image();
    image1.src = '/images/loadings/loading_01.svg';
    image2.src = '/images/loadings/loading_02.svg';
    image3.src = '/images/loadings/loading_03.svg';
    image4.src = '/images/loadings/loading_04.svg';

    $(document).scroll(function(){
        if($('html').scrollTop() > 400){
            $('div#back_to_top').fadeIn();
        } else {
            $('div#back_to_top').fadeOut();
        }
    });

    $(document).on('click', 'div#back_to_top', function(){
    $('body,html').animate({
        scrollTop: 0
    }, 1000);
    });

    /*Auto Logout*/
    var w = new Worker("/js/worker.js");
    w.onmessage = function(event){
        if(event.data === 'logout') {
            w.terminate()
            console.log("Proceed to logout")
            $('#menu-lateral #lateral-profile-top div a')[0].click();
        }
    }
    
    window.onblur = function(){ w.postMessage('enableTimeout') }
    window.onfocus = function(){ w.postMessage('disableTimeout') }
    window.onclick = function(){ w.postMessage('disableTimeout') }

</script>

<!-- Footer -->
<footer class="page-footer">
    <div class="footer-copyright py-3">
        
        <!-- Espaço reservado à esquerda (vazio para alinhamento) -->
        <div style="width: 100px;"></div>

        <!-- Elementos centralizados -->
        <div class="footer-center">
            <span class="badge badge-secondary">© ESA / DE</span>
            <span class="badge badge-dark"> 2020 / {{ date('Y') }} </span>
        </div>

        <!-- Elemento alinhado à direita -->
        <span class="badge badge-info">Desenvolvido pelo 1º Ten João Victor</span>

    </div>
</footer>
<!-- Footer -->
</body>
</html>
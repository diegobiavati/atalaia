<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script-->
    <script src="/js/jquery/jquery-3.3.1.js"></script>
    <script src="/js/app.js"></script>
    <link href="/css/app.css" rel="stylesheet" type="text/css" />
    <!--link href="/css/ionicons.css" rel="stylesheet" type="text/css" /-->
    <!--script src="/js/jquery.mask.min.js"></script-->
    @yield('js-includes')
    @yield('css-includes')
    <link rel="icon" href="/images/favicon.png" type="image/x-icon" />
    <link rel="shortcut icon" href="/images/favicon.png" type="image/x-icon" /> 
    <title>@yield('title')</title>
    @yield('css-styles-includes')
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


</script>

</body>
</html>
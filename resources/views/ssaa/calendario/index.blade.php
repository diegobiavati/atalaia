<?php
    $backgroundColor = session()->get('backgroundColor');
?>
  <link href="/css/calendar_all.css" rel="stylesheet" type="text/css" />
  <style>
    .header-col {
      background: #E3E9E5;
      color: #536170;
      text-align: center;
      font-size: 20px;
      font-weight: bold;
    }

    .header-calendar {
      background: {{$backgroundColor}};
      color: white;
    }

    .box-day {
      border: 1px solid #E3E9E5;
      height: 150px;
      overflow: auto;
    }

    .box-dayoff {
      border: 1px solid #E3E9E5;
      height: 150px;
      background-color: #ccd1ce;
    }

    .calendario {
      margin: auto;
      width: 90%;
      padding: 10px;
    }
  </style>
<div class="body-calendar">
  <div id="calendario_ssaa">
      {!! App\Http\Controllers\Utilitarios\FuncoesController::retornaBotaoAnoFormacao((isset($ano_formacao) ? $ano_formacao : null)) !!}

      <div class="container_calendario"></div> <!-- /container -->
  </div>
  
  @include('layouts.footer_ssaa')

</div>
<script>
    

    function carregaContainerCalendario(url) {
        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() {
                $('div.container_calendario').empty();
            },
            success: function(data) {
                $('div.container_calendario').html(data);
            },
            error: function(jqxhr) {
                $('div.container_calendario').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
            }
        });
    }

    $(document).ready(function() {
        $('div#calendario_ssaa .btn.btn-secondary input[name="ano_formacao"]').change(function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

            url = '/gaviao/ajax/calendario/index/' + evt.target.value;
            
            $('div.container_calendario').empty();

            //Carrega a combo com Uetes ou Cursos
            carregaContainerCalendario(url);
        });

        //carrega o container com data selecionada padrão
        carregaContainerCalendario('/gaviao/ajax/calendario/index/' + $('div#calendario_ssaa .btn.btn-secondary.active input[name="ano_formacao"]').val());
    });
</script>
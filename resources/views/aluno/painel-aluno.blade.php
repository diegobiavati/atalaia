@extends('headers')
@section('js-includes')
@stop
@section('css-includes')
<link href="/css/ionicons.css" rel="stylesheet" type="text/css" />
@stop
@section('title', 'Atalaia :: Painel do Aluno')
@section('css-styles-includes')
<style>

body {

  background: #f5f5f5 url('/images/diplomacao.jpg') no-repeat;
  background-size: cover; 

}

.wrapper {
    color: #ffffff;
    position: absolute;
    top: 50%;
    left: 50%;
    margin-top: -280px;
    margin-left: -450px;
    width: 900px;
    height: 620px;
    background-color: rgba(0,0,0,0.825);
    border-radius: 18px;
    box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
}


a.no-style{
    text-decoration: none;
    color: #F2F2F2;
}

a.no-style:hover{
    text-decoration: none;
    color: #ffffff;
}

a.no-style2{
    text-decoration: none;
    color: #696969;
}

a.no-style2:hover{
    text-decoration: none;
    color: #363636;
}

</style>
@stop
@section('content')
<div class="wrapper">
  <div class="inside-wrapper" style="padding: 18px 12px; height: 570px;">
    <h2 style="text-align: center; margin-top: 22px;">Olá Al {{$aluno->nome_guerra}}</h2>
    <h3 style="text-align: center; margin-top: 56px;">
      Seja 
      @if($aluno->sexo=='M')
        bem-vindo
      @else
        bem-vinda
      @endif
      a área do aluno no Sistema Atalaia     
    </h3>
    <div style="text-align: center; margin-top: 36px;">
      <button type="button" class="btn btn-primary" onclick="comecarAssistenteInstalacaoApp({{auth()->id()}});">Assistente de instalação do Telegram</button>
    </div>
    <div style="text-align: center; margin-top: 36px;">
      <button type="button" class="btn btn-success" onclick="showHidePassport();">
        <span class="show-passaport">Visualizar passaporte</span>
        <span class="hide-passaport" style="display: none;">Esconder passaporte</span>
        <span class="passaport" style="display: none;"><br />{{(isset($aluno->passaporte) ? $aluno->passaporte->passaport : null)}}</span>
      </button>
    </div>
    <h3 style="text-align: center; margin-top: 44px;">Para fazer a escolha de QMS, clique no botão abaixo</h3>
    <div style="text-align: center; margin-top: 36px;">
      <button type="button" class="btn btn-warning" onclick="DialogescolhaDeQMS({{$aluno->id}}, {{auth()->id()}});">
        Iniciar escolha de QMS
      </button>
    </div>
  </div>
  <div style="text-align: center;">
    <span id="salvar-opcoes" style="display: none;"><a href="javascript: void(0);" onclick="gravarOpcoesAluno();" class="no-style">Salvar opções</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</span><span id="link-back" style="display: none;"><a href="/aluno" class="no-style">Início</a>&nbsp;&nbsp;&nbsp;| </span>&nbsp;&nbsp;&nbsp;<a href="/sair" class="no-style">Sair</a>
  </div>    
</div>

<!-- MODALS CONTENTE DINAMICA NO STYLE -->
    
<div class="modal fade" id="modalDinamica_no_style" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <!--div class="modal-header" style="background-color: #F2F2F2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div-->
        <div class="modal-body" style="padding: 0"></div>
        <!--div class="modal-footer" style="text-align: right;">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div-->
      </div>
    </div>
</div>  

<!-- FINAL MODAL CONTENTE DINAMICA NO STYLE-->

<!-- MODALS confirmAcao DINAMICA-->

<div class="modal fade bd-example-modal-sm" id="modalConfirmAcao" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content" style="border-radius: 0; padding: 10px;">
            <div style="border-bottom: 1px solid #ccc;"><i class="ion-android-hand" style="font-size: 22px; vertical-align: middle;"></i><span style="margin-left: 10px;"><b>ATENÇÃO!</b></span></div>
            <div class="content-confirmAcao" style="margin: 6px 0;"></div>
            <div style="text-align: right;">
                <div style="display: inline-block;">
                    <a id="confirm-buttom" class="no-style" href="javascript: void(0);"><span style="color: #363636;"><strong>SIM</strong></span></a>
                </div>
                <div style="display: inline-block; margin-left: 8px;">
                    <a class="no-style" href="javascript: void(0);" data-dismiss="modal"><span style="color: #2E64FE;"><strong>NÃO</strong></span></a>
                </div>
            </div>              
      </div>
    </div>
</div>

<!-- FINAL MODAL confirmAcao DINAMICA-->
<script>

    function comecarAssistenteInstalacaoApp(id){

      $.ajax({
        type:'GET',
        url: '/ajax/assistente-instalacao-app/' + id,
        beforeSend: function(){
          $('div.inside-wrapper').html('<div id="temp" style="text-align: center; margin: 210px; padding: 24px;"><img src="/images/loadings/loading_02.svg" style="width: 44px; margin-right: 8px;" /><br />Por favor aguarde...</div>');
        },
        success: function(data){
          $('div#temp').fadeOut(300, function(){
            $(this).remove();
            $('div.inside-wrapper').html(data);
            $('.carousel').carousel({
              interval: false
            });            
            $('span#link-back').fadeIn();
          });
        },
        error: function(jqxhr){
          setTimeout(function(){
            alert('ERRO INTERNO. Por favor tente mais tarde!');
          }, 1000);
        }                    
      });
  }

  function DialogescolhaDeQMS(alunoID, userID){

      $.ajax({
        type:'GET',
        url: '/ajax/escolha-de-qms-aluno/' + alunoID + '/' + userID,
        beforeSend: function(){
          $('div.inside-wrapper').html('<div id="temp" style="text-align: center; margin: 210px; padding: 24px;"><img src="/images/loadings/loading_02.svg" style="width: 44px; margin-right: 8px;" /><br />Por favor aguarde...</div>');
        },
        success: function(data){
          $('div#temp').fadeOut(300, function(){
            $(this).remove();
            $('div.inside-wrapper').html(data);
            $('span#link-back').fadeIn();
          });
        },
        error: function(jqxhr){
          setTimeout(function(){
            alert('ERRO INTERNO. Por favor tente mais tarde!');
          }, 1000);
        }                    
      });
  }

  function showHidePassport(){
    if($('span.show-passaport').is(':visible')){
      $('span.show-passaport').hide();  
      $('span.passaport, span.hide-passaport').show();  
    } else {
      $('span.show-passaport').show();  
      $('span.passaport, span.hide-passaport').hide();
    }
  }

  function selecionarOpcaoAluno(aElement, qmsID){
    $('div.qms_id_' + qmsID).hide();
    var prioridadeID = $(aElement).parents('div.dropdown-menu').prev().attr('name');
    $('input[name="'+ prioridadeID +'"]').val(qmsID);
    $('div#' + prioridadeID + ' button').slideUp(200, function(){
      $('a#' + prioridadeID + '_preenchida').append($(aElement).html()).slideDown();   
    });
    if(!$('div.qms_option_class').is(':visible')){
      $('div#confirm_checkbox_escolha_qms').show();
    }
  }

  function desfazerOpcaoAluno(aElement, prioridadeID, prioridade){
    var qmsID = $('input[name="'+ prioridadeID +'"]').val();
    $('div.qms_id_' + qmsID).show();
    $(aElement).html(prioridade+'ª prioridade - ').slideUp(200, function(){
      $('div#' + prioridadeID + ' button').show();
    });
    $('div#confirm_checkbox_escolha_qms').hide();
    $('span#salvar-opcoes').slideUp();
    $('input.form-check-input').prop('checked', false);
  }

  function gravarOpcoesAluno(){
    var dataForm = $('form[name="escolha_qms_opcoes"]').serialize();
    $.ajax({
      type: 'POST',
      dataType: 'json',
      data: dataForm,
      url: '/ajax/gravar-opcoes-aluno',
      beforeSend: function(){
        $('div.inside-wrapper').html('<div id="temp" style="text-align: center; margin: 210px; padding: 24px;"><img src="/images/loadings/loading_02.svg" style="width: 44px; margin-right: 8px;" /><br />Por favor aguarde...</div>');
      },
      success: function(data){
          $('div#temp').fadeOut(300, function(){
            $(this).remove();
            //$('div.inside-wrapper').html(data);
            if(data.status=='ok'){
              DialogescolhaDeQMS(data.alunoID, data.userID);
              $('div#modalDinamica_no_style').modal('show');
              $('div#modalDinamica_no_style div.modal-body').html(data.content)
            } else {
              alert('Erro interno!');
            }
          });
        $('span#salvar-opcoes').slideUp();
        $('input.form-check-input').prop('checked', false);

      },
      error: function(jqxhr){
          setTimeout(function(){
            alert('ERRO INTERNO. Por favor tente mais tarde!');
          }, 1000);
        }                
    });
  }

  function limparOpcoes(escolhaID, alunoID){
      $(document).confirmAcao('Deseja realmente remover e refazer todas as opções?', function(){
      $.ajax({
        type: 'GET',
        dataType: 'json',
        url: '/ajax/limpar-opcoes-aluno/' + escolhaID + '/' + alunoID,
        beforeSend: function(){
          $('div.inside-wrapper').html('<div id="temp" style="text-align: center; margin: 210px; padding: 24px;"><img src="/images/loadings/loading_02.svg" style="width: 44px; margin-right: 8px;" /><br />Por favor aguarde...</div>');
        },
        success: function(data){
            $('div#temp').fadeOut(300, function(){
              $(this).remove();
              //$('div.inside-wrapper').html(data);
              if(data.status=='ok'){
                DialogescolhaDeQMS(data.alunoID, data.userID);
              } else {
                alert('Erro interno!');
              }
            });
        },
        error: function(jqxhr){
            setTimeout(function(){
              alert('ERRO INTERNO. Por favor tente mais tarde!');
            }, 1000);
        }                
      });
    });
  }

  (function($){
    $.fn.confirmAcao = function(content, callback) {
      return this.each(function() {
        $('div.content-confirmAcao').html(content);
        $('#modalConfirmAcao').modal('show');
        $('#confirm-buttom').click(function(){
          callback();
          $('#modalConfirmAcao').modal('hide');
                                        
          // DESASSOCIA EVENTOS DE CLIQUE ANTERIORES

          $(this).off(); 
        })
      });
    };
  })(jQuery);  
    
</script>

@stop
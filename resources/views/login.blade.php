@extends('headers')
@section('js-includes')
@stop
@section('css-includes')
@stop
@section('title', 'Atalaia :: Login no Sistema')
@section('css-styles-includes')
<style>

body {

  background: #f5f5f5 url('/images/bg_tela_login01.jpg') no-repeat;
  background-size: cover;

}

#atalia_top{
    padding: 38px;
    background-color: #0B6138;
    height: 156px;
    text-align: center;
    color: #fff;
    border-radius: 3px 3px 0 0;
    
}

.form-wrapper {
    position: absolute;
    top: 50%;
    left: 50%;
    margin-top: -260px;
    margin-left: -220px;
    width: 440px;
    height: 496px;
    background-color: #ffffff;
    border-radius: 5px;
    box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
}

.form-group {
  position: relative;
}
.form-group + .form-group {
  margin-top: 30px;
}

.form-label {
  position: absolute;
  left: 12px;
  top: 10px;
  color: #999;
  background-color: #fff;
  z-index: 10;
  transition: font-size 150ms ease-out, -webkit-transform 150ms ease-out;
  transition: transform 150ms ease-out, font-size 150ms ease-out;
  transition: transform 150ms ease-out, font-size 150ms ease-out, -webkit-transform 150ms ease-out;
}

.focused .form-label {
  -webkit-transform: translateY(-125%);
          transform: translateY(-125%);
  font-size: .75em;
}

.form-input {
  position: relative;
  padding: 12px 0px 5px 5px;
  width: 100%;
  outline: 0;
  border: 0;
  box-shadow: 0 1px 0 0 #e5e5e5;
  transition: box-shadow 150ms ease-out;
}
.form-input:focus {
  box-shadow: 0 1px 0 0 #0B6138;
}

.form-input.filled {
  box-shadow: 0 1px 0 0 #696969;
}

.form-label:hover{
  cursor: text;
}

a.no-style{
    text-decoration: none;
    color: #696969;
}

a.no-style:hover{
    text-decoration: none;
    color: #363636;
}

</style>
@stop
@section('content')
<div class="form-wrapper">
    <div id="atalia_top">
        <h4>SISTEMA ATALAIA</h4>
        PERÍODO BÁSICO
        <img src="/images/logo_atalaia_pb.png" style="position: absolute; width: 96px; margin: 36px 0 0 -104px; z-index: 10" />
    </div>
    <div style="margin-top: 28px; padding: 12px 56px;">
      @if(!isset($token))
        <div id="carouselDialogLogin" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
              <div class="carousel-item active">
                <form id="submitLogin" class="form">
                  <div>
                    @csrf
                    <div class="form-group" style="margin-top: 24px;">
                      <label class="form-label" for="first">E-mail</label>
                      <input id="first" name="login" class="form-input" type="email" autocomplete="off" />
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="last">Senha</label>
                      <input id="last" name="senha" class="form-input" type="password" />
                    </div>
                    <div style="text-align: center; margin-top: 38px">
                          <button type="button" id="buttonSubmitLogin" class="btn btn-success" style="background-color: #0B6138; border: none; padding: 8px 22px;">
                            Entrar
                          </button>
                    </div>
                    <div style="text-align: center; margin-top: 28px">
                        <a class="no-style" href="#carouselDialogLogin" role="button" data-slide="next">Primeiro acesso | Esqueci a senha</a>
                    </div>
                  </div>
                </form>
              </div>
              <div class="carousel-item">
                <form id="PasswordRecovery" class="form">
                  <div>
                    @csrf
                    <div class="form-group" style="margin-top: 48px;">
                      <label class="form-label" for="third">Email</label>
                      <input name="email" id="third" class="form-input" type="email" autocomplete="off" />
                    </div>
                    <div style="text-align: center; margin-top: 38px">
                      <button type="button" id="buttonPasswordRecovery" class="btn btn-success" style="background-color: #0B6138; border: none; padding: 8px 22px;">Enviar link por e-mail</button>
                    </div>
                    <div style="text-align: center; margin-top: 28px">
                        <a class="no-style" href="#carouselDialogLogin" role="button" data-slide="prev"><img src="/images/arrow_back.png" style="width: 22px;" /> Voltar ao login</a>
                    </div>
                  </div>
                </form>
              </div>
          </div>
        </div>
      @else
        <form id="NewPasswordRecovery" class="form">
          <div>
            @csrf
            <input name="token" type="hidden" value="{{$token}}" />
            <input name="email" type="hidden" value="{{base64_decode($email)}}" />
            <div class="form-group" style="margin-top: 24px;">
              <label class="form-label" for="first">Nova senha</label>
              <input name="password" id="first" class="form-input" type="password" />
            </div>
            <div class="form-group">
              <label class="form-label" for="last">Confirme a nova senha</label>
              <input name="password_confirmation" id="last" class="form-input" type="password" />
            </div>
            <div id="box_erro_senha" style="color: #B40404; text-align:center; margin-top: 24px; display: none;"></div>                         
            <div style="text-align: center; margin-top: 56px">
              <button type="button" id="buttonCreatePassword" class="btn btn-success" style="background-color: #0B6138; border: none; padding: 8px 22px;">Cadastrar nova senha</button>
            </div>
          </div>
        </form>        
      @endif
    </div>
</div>
<script>

    $(document).ready(function(){
      setTimeout(function(){
        $('input').eq(1).focus();
      }, 600);
    });

    $('.carousel').carousel({
      interval: false
    });

    $('input').focus(function(){
      $(this).parents('.form-group').addClass('focused');
    });
    
    $('input').blur(function(){
      var inputValue = $(this).val();
      if ( inputValue == "" ) {
        $(this).removeClass('filled');
        $(this).parents('.form-group').removeClass('focused');  
      } else {
        $(this).addClass('filled');
      }
    });

    $('input[name="senha"]').keypress(function(e) {
      if (e.which==13) {
        $('button#buttonSubmitLogin').trigger('click');
      }
    });    

    $('input#third').keypress(function(e) {
      if (e.which==13) {
        $('button#buttonPasswordRecovery').trigger('click');
      }
    });    

    $(document).on('click', '#buttonSubmitLogin', function(){
      if($('input[name="login"]').val().length<5){
        $('input[name="login"]').select();  
      } else if($('input[name="senha"]').val().length<5){
        $('input[name="senha"]').select();  
      }else{
        var elementForm = $('form#submitLogin');
        var dataForm = elementForm.serialize();
        $.ajax({
          type: 'POST',
          dataType: 'json',
          data: dataForm,
          url: '{{route('auth')}}',
          beforeSend: function(){
            $('form#submitLogin div').hide();
            $('form#submitLogin').prepend('<div id="temp" style="text-align: center; margin-top: 72px;"><img src="/images/loadings/loading_02.svg" style="width: 44px;" /><br />Autenticando...</div>');  
          },
          success: function(data){
            if(data.status=='ok'){
              $('div#temp').remove();
              $('form#submitLogin').prepend('<div style="text-align: center; margin-top: 108px; color: #0B6138;"><b>' + data.msgOK + '</b></div>');
              setTimeout(function(){
                location.href='{{route('atalaia')}}';
              }, 800);
            } else if(data.status=='err'){
              $('div#temp').remove();
              $('form#submitLogin').prepend('<div id="temp" style="text-align: center; margin-top: 108px; color: #DF0101;"><b>' + data.msgErr + '</b></div>');
              setTimeout(function(){
                $('div#temp').remove();
                $('input[name="senha"]').val('');                
                $('form#submitLogin div').fadeIn(300, function(){
                  $('input[name="senha"]').focus();
                });
              }, 1000);
            } else {
              $('div#temp').remove();
              $('form#submitLogin div').fadeIn(100);
            }
          },
          error: function(jqxhr){
            $('div#temp').remove();
            $('form#submitLogin').prepend('<div id="temp" style="text-align: center; margin-top: 44px; color: #DF0101;"><b>ERRO INTERNO</b></div>');
            setTimeout(function(){
              $('div#temp').remove();
              $('form#submitLogin div').fadeIn(100);
            }, 1000);            
          }                
        });        
      }
    });

    $(document).on('click', '#buttonPasswordRecovery', function(){
      if($('input[name="email"]').val().length<5){
        $('input[name="email"]').select();  
      }else{
        var elementForm = $('form#PasswordRecovery');
        var dataForm = elementForm.serialize();
        $.ajax({
          type: 'POST',
          dataType: 'json',
          data: dataForm,
          url: '{{route('send_recovery_password')}}',
          beforeSend: function(){
            $('form#PasswordRecovery div').hide();
            $('form#PasswordRecovery').prepend('<div id="temp" style="text-align: center; margin-top: 72px;"><img src="/images/loadings/loading_02.svg" style="width: 44px;" /><br />Enviando...</div>');  
          },
          success: function(data){
            $('div#temp').remove();
            if(data.status=='ok'){
              $('form#PasswordRecovery').prepend('<div id="temp" style="text-align: center; margin-top: 108px;  color: #0B6138;"><b>' + data.msg + '</b></div>');
              setTimeout(function(){
                $('div#temp').remove();
                $('input[name="email"]').val('');
                $('form#PasswordRecovery div').fadeIn(200, function(){
                  $('a[href="#carouselDialogLogin"]').trigger('click');
                });
              }, 3000);                
            } else {
              $('form#PasswordRecovery').prepend('<div id="temp" style="text-align: center; margin-top: 108px; color: #DF0101;"><b>' + data.msg + '</b></div>');  
              setTimeout(function(){
                $('div#temp').remove();
                $('form#PasswordRecovery div').fadeIn(100);
              }, 3000);              
            }
          },
          error: function(jqxhr){
            $('div#temp').remove();
            $('form#submitLogin').prepend('<div id="temp" style="text-align: center; margin-top: 44px; color: #DF0101;"><b>ERRO INTERNO</b></div>');
            setTimeout(function(){
              $('div#temp').remove();
              $('form#PasswordRecovery div').fadeIn(100);
            }, 1200);            
          }                
        });        
      }
    });

    $(document).on('focus', 'input[name="senha"], input[name="confirma_senha"]', function(){
      $('div#box_erro_senha').slideUp(200, function(){
        $(this).empty();
      });  
    });

    $(document).on('click', '#buttonCreatePassword', function(){
      if($('input[name="password"]').val().length<6){
        $('input[name="password"], input[name="password_confirmation"]').val('');
        $('div#box_erro_senha').html('A senha deve possuir no mínimo 6 caracteres').slideDown();  
      }else if($('input[name="password"]').val()!=$('input[name="password_confirmation"]').val()){
        $('div#box_erro_senha').html('A senha e sua confirmação não conferem').slideDown();
        $('input[name="password"], input[name="password_confirmation"]').val('');  
      } else {
        var dataForm = $('form#NewPasswordRecovery').serialize();
        $.ajax({
          type: 'POST',
          dataType: 'json',
          data: dataForm,
          url: '{{route('submit_new_password')}}',
          beforeSend: function(){
            $('form#NewPasswordRecovery div').hide();
            $('form#NewPasswordRecovery').prepend('<div id="temp" style="text-align: center; margin-top: 72px;"><img src="/images/loadings/loading_02.svg" style="width: 44px;" /><br />Validando senha..</div>');  
          },
          success: function(data){
            $('div#temp').remove();
            if(data.status=='ok'){
              $('form#NewPasswordRecovery').prepend('<div id="temp" style="text-align: center; margin-top: 108px;  color: #0B6138;"><b>' + data.msg + '</b></div>');
              setTimeout(function(){
                location.href='/';
              }, 3000);                
            } else if(data.status=='err') {
              $('form#NewPasswordRecovery').prepend('<div id="temp" style="text-align: center; margin-top: 108px; color: #DF0101;"><b>' + data.msg + '</b></div>');  
              setTimeout(function(){
                $('div#temp').remove();
                $('input[name="password"], input[name="password_confirmation"]').val('');
                $('form#NewPasswordRecovery div').fadeIn(100);
              }, 3000);              
            } else if(data.status=='passwords.token'){
              $('form#NewPasswordRecovery').prepend('<div id="temp" style="text-align: center; margin-top: 108px; color: #DF0101;"><b>' + data.msg + '</b></div>');  
              setTimeout(function(){
                location.href='/';  
              }, 4500);                 
            }
          },
          error: function(jqxhr){
            $('div#temp').remove();
            $('form#NewPasswordRecovery').prepend('<div id="temp" style="text-align: center; margin-top: 44px; color: #DF0101;"><b>ERRO INTERNO</b></div>');
            setTimeout(function(){
              $('div#temp').remove();
              $('form#NewPasswordRecovery div').fadeIn(100);
            }, 1200);            
          }                
        });        
      }
    });    
    
</script>

@stop
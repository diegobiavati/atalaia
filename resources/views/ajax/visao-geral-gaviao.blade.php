<?php
    $backgroundVisaoGeral = session()->get('backgroundVisaoGeral').';';
?>
<style>
    div.link_rapido_menu{
        display: none;
    }
    
    p.card-text{
        color: #FF4000;
    }   
</style>
<div style="text-align: center; margin-bottom: 56px; color: #696969;">
    <h4>VISÃO GERAL DO SISTEMA</h4>
</div>
<div class="card-deck">
    <div class="card" style="height: fit-content;">
      <div style="background-color: {{$backgroundVisaoGeral}} border-radius: 3px 3px 0 0;">
        <div style="text-align: center; color: #fff; padding: 10px;">
            <i class="ion-ios-calendar-outline" style="font-size: 44px;"></i><br />
            <h5>Ano de formação</h5>
          </div>          
        </div>
        <div class="card-body">
            @if($ownauthcontroller->PermissaoCheck(28))
                <div class="link_rapido_menu" style="position: absolute; width: 56px; height: 56px; right: 5%; margin-top: -48px; border-radius: 50%; background-color: #fff; box-shadow: 1px 1px 8px #696969; text-align: center; padding-top: 10px;">
                    <a href="javascript: void:(0);" class="no-style" style="font-size: 24px;" onclick="$('a#anos-de-formacao').click();"><i class="ion-edit"></i></a>
                </div>          
            @endif
        
        @if(isset($ano_corrente->formacao))<br>
        <p class="card-text" style="text-align: center; font-size: 44px; line-height: 22px;">
            {{$ano_corrente->formacao}}<br /> <span style="font-size: 16px;">data de matrícula em: 
                <br /><b>{{strftime('%d-%m-%Y', strtotime($ano_corrente->data_matricula))}}</b>
            </span>
        </p>
        @else
            <p class="card-text">
                Não há ano de formação cadastrado
            </p>
        @endif
      </div>
    </div>
    @if($ownauthcontroller->PermissaoCheck(9999))
    <div class="card" style="height: fit-content;">
        <div style="background-color: {{$backgroundVisaoGeral}} border-radius: 3px 3px 0 0;">
            <div style="text-align: center; color: #fff; padding: 10px;">
              <i class="ion-ios-people" style="font-size: 44px;"></i><br />
              <h5>Operadores</h5>
            </div>
        </div>
        <div class="card-body">
          <div class="link_rapido_menu" style="position: absolute; width: 56px; height: 56px; right: 5%; margin-top: -48px; border-radius: 50%; background-color: #fff; box-shadow: 1px 1px 8px #696969; text-align: center; padding-top: 10px;">
              <a href="javascript: void:(0);" class="no-style" style="font-size: 24px;" onclick="$('a#gerenciar-operadores-gaviao').click();"><i class="ion-edit"></i></a>
          </div>
          <h5 class="card-title"><b>Total Cadastrado</b></h5>
          <p class="card-text" style="text-align: center; font-size: 44px;">
              {{$total_operadores}}
          </p>
        </div>
    </div>
    @endif
    <div class="card" style="height: fit-content;">
        <div style="background-color: {{$backgroundVisaoGeral}} border-radius: 3px 3px 0 0;">
          <div style="text-align: center; color: #fff; padding: 10px;">
              <i class="ion-android-contacts" style="font-size: 44px;"></i><br />
              <h5>Alunos</h5>
            </div>          
        </div>
        <div class="card-body">
            <div class="link_rapido_menu" style="position: absolute; width: 56px; height: 56px; right: 5%; margin-top: -48px; border-radius: 50%; background-color: #fff; box-shadow: 1px 1px 8px #696969; text-align: center; padding-top: 10px;">
                <a href="javascript: void:(0);" class="no-style" style="font-size: 24px;" onclick="$('a#alunos-gaviao').click();"><i class="ion-edit"></i></a>
            </div>               
            <h5 class="card-title"><b>Efetivo pronto</b></h5>
            <div class="card-text" style="width: 80%; margin: 0 auto;">
                Segmento masculino <i>({{$alunos['total_curso']}})</i><br />
                <div id="bar_porcentagem_alunos" style="background-color: #088A29; width: 0; height: 22px; float: left; margin-right: 6px; margin-top: 2px;"></div>
                <span id="porcentagem_seg_masc" style="display: none;">{{$alunos['porcentagem_alunos']}}%</span>
            </div>
        </div>
    </div>

    @if($ownauthcontroller->PerfilCheck(9001))
    <!--Só libera se for Cmt de Cia-->
    <div class="card" style="height: fit-content;">
        <div style="background-color: {{$backgroundVisaoGeral}} border-radius: 3px 3px 0 0;">
            <div style="text-align: center; color: #fff; padding: 10px;">
                <i class="ion-android-warning" style="font-size: 44px;"></i><br />
                <h5>Fatos observados</h5>
            </div>
        </div>
        <div class="card-body">

            <h5 class="card-title"><b>Resolvidos</b></h5>
            <div class="card-text" style="width: 80%; margin: 40px auto;">
                <p class="card-text" style="text-align: center; font-size: 44px; line-height: 22px;">
                    {{ $lancamentoFo->where('providencia', '<>', null)->count() .'/'. $lancamentoFo->count() }}
                </p>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    @endif

    @if($ownauthcontroller->PerfilCheck(9002))
    <!--Só libera se for Sargenteante da UETE-->
    <div class="card" style="height: fit-content;">
        <div style="background-color: {{$backgroundVisaoGeral}} border-radius: 3px 3px 0 0;">
            <div style="text-align: center; color: #fff; padding: 10px;">
                <i class="ion-android-warning" style="font-size: 44px;"></i><br />
                <h5>FATD</h5>
            </div>
        </div>
        <div class="card-body">

            <h5 class="card-title"><b>Pendentes</b></h5>
            <div class="card-text" style="width: 80%; margin: 40px auto;">
                <p class="card-text" style="text-align: center; font-size: 44px; line-height: 22px;">
                    {{ $fatd->count() }}
                </p>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    @endif   
    
    @if($ownauthcontroller->PerfilCheck([9005, 9006]))
    <!--Só libera se for Operador da SSAA e Coordenador de Provas SSAA-->
    <div class="card">
        <div style="background-color: {{$backgroundVisaoGeral}} border-radius: 3px 3px 0 0;">
            <div style="text-align: center; color: #fff; padding: 10px;">
                <i class="ion-ios-pulse-strong" style="font-size: 44px;"></i><br />
                <h5>Devolução de Provas</h5>
            </div>
        </div>
        <div class="card-body">
            <h5 class="card-title"><b>Próximas Devoluções <h6>limitação de 30 dias</h6></b></h5>
            <p class="card-text">
                @if(count($rapPendentes)>0)
                @foreach($rapPendentes as $avaliacao)
                <hr>
                <li style="color: {{App\Http\Controllers\Utilitarios\FuncoesController::getQmsColor($avaliacao->esadisciplinas->qms->qms_matriz_id)->backgroundColor}}"><b>Avaliação de {{$avaliacao->getDescricao()}} </b><span style="color: {{App\Http\Controllers\Utilitarios\FuncoesController::getQmsColor($avaliacao->esadisciplinas->qms->qms_matriz_id)->backgroundColor}}"> de <i>{{$avaliacao->esadisciplinas->nome_disciplina_abrev}}</i>
                    <br></span> ({{strftime('%A, %d de %B de %Y', strtotime($avaliacao->realizacao))}})</li>
                        <ul>
                            <li style="color: #0094D3;">{{$avaliacao->chamada}}ª chamada</li>
                            Pendência de RAP ({{ $avaliacao->rapLancadas }} / {{ $avaliacao->rapTotal }})
                            <li style="color: #FF4000;">Prazo para a devolução ({{strftime('%A, %d de %B de %Y', strtotime($avaliacao->devolucao))}})</li>
                        </ul>
                @endforeach
                @else
                Sem avaliações nos próximos dias
                @endif
            </p>
        </div>
    </div>
    @endif
  </div>

    <script>

        $(document).ready(function(){
            $('div#bar_porcentagem_alunos').animate({
                width: "{{$alunos['porcentagem_alunos'] +1}}%",
            }, 600, function(){
                $('span#porcentagem_seg_masc').fadeIn();
            });
        });
        
        $(document).on('mouseover', 'div.card', function(){
             $(this).find('div.link_rapido_menu').fadeIn(200);
        }).on('mouseleave', 'div.card', function(){
            $(this).find('div.link_rapido_menu').fadeOut(200);   
        });

    </script>
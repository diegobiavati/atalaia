<?php
foreach($uetes as $uete){
  $combo_uete[] = '<option value='.$uete->id.' '.((isset($aluno) && $uete->id == $aluno->uetes_id) ? 'selected' : '').'>'. $uete->omct .'</option>';
}
?>

<div class="card bg-light mb-3">
    <div class="card-header">
        <i class="ion-ios-compose"></i><strong>Avaliações</strong>
        <div style="float: right;">
            <!--div class="box-pesquisar-in-card-title" style="display: inline-block; padding:0; height: 36px;">
                <input class="pesquisar-in-card-title busca-operador" type="text" placeholder="Busca" />
                <a class="no-style" href="javascript: void(0);">
                        <i class="ion-android-search" style="color: #696969;"></i>
                </a>
            </div-->
            @if(count($disciplinas)>0 && $ownauthcontroller->PermissaoCheck(1))
              <a href="javascript: void(0);" data-toggle="popover" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
                  <i class="ion-android-more-vertical" style="color: #696969;"></i>
              </a>
              <div id="popover-content" style="display: none;">
                  <div class="menu_inside_popover">
                      <i class="ion-ios-compose"></i><a href="javascript: void(0);" onclick="dialogAdicionarAvaliacao();">Adicionar avaliação</a><br />                 
                  </div>                                          
                  <div class="menu_inside_popover">
                      <i class="ion-loop"></i><a href="javascript: void(0);" onclick="dialogAdicionarAvaliacaoRec();">Adicionar avaliação de recuperação</a><br />                 
                  </div>                                          
              </div>
            @endif  
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-danger errors-adicionar-avaliacoes2" role="alert"></div>
        <h4 style="text-align: center; margin: 18px 0 44px 0; color: #696969;">
          AVALIAÇÕES NAS RESPECTIVAS DISCIPLINAS
        </h4>

        <p class="card-text">
            @if(count($disciplinas)>0)
              <div id="accordion">
                @foreach($disciplinas as $disciplina)
                <?php $qtde_avaliacoes = 0; ?>
                  @foreach($avaliacoes as $avaliacao)
                    @if($avaliacao->disciplinas_id==$disciplina->id)
                      <?php
                        if($avaliacao->chamada==0 && $avaliacao->avaliacao_recuperacao==1){
                          $style_color_chamada = 'bg-secondary';
                          $style_color_footer = 'border-secondary';
                          $avaliacao_referencia = 'Única chamada';
                        } else if($avaliacao->chamada==1){
                          $style_color_chamada = 'bg-success';
                          $style_color_footer = 'border-success';
                          $avaliacao_referencia = $avaliacao->chamada.'ª chamada';
                        } else {
                          $style_color_chamada = 'bg-warning';                          
                          $style_color_footer = 'border-warning';
                          $avaliacao_referencia = $avaliacao->chamada.'ª chamada da '.$avaliacao_nome_por_indice[$avaliacao->chamada_refer_id];                          
                        }
                        
                        $qtde_avaliacoes++;

                        /* OPÇÕES DA AVALAIAÇÃO */

                        if($ownauthcontroller->PermissaoCheck(1)) {
                          $opcoes_avaliacoes =    '<a href="javascript: void(0);" class="no-style" onclick="dialogEditarAvaliacao('.$avaliacao->id.');" title="Editar configurações da avaliação">
                                                      <i class="ion-android-create" style="color: #ffffff; font-size: 20px;"></i>
                                                    </a>
                                                  <a href="#" onclick="dialogRemoverAvaliacao('.$avaliacao->id.');" class="no-style" style="margin-left: 6px;">
                                                      <i class="ion-android-delete" style="color: #ffffff; font-size: 20px;"></i>
                                                  </a>';
                        }

                        $opcoes_avaliacoes = ($opcoes_avaliacoes)??'';

                        if(in_array($avaliacao->id, $avaliacoes_status_array) && (($ownauthcontroller->PermissaoCheck(3) && !$ownauthcontroller->PermissaoCheck(1)) && (time()>strtotime($avaliacao->data.' '.$avaliacao->hora) && time()<strtotime("+".$avaliacao->prazo_nota." days", strtotime($avaliacao->data.' '.$avaliacao->hora))))){
                          $opcoes_avaliacao = ' <span id="pronto-faltasID_'.$avaliacao->id.'"><span class="badge badge-secondary">Pronto de faltas enviado</span></span>
                                                <a href="javascript: void(0);" class="badge badge-primary" style="margin-left: 6px;" onclick="dialogLancarGraus('.$avaliacao->id.');">Lançar grau</a>';
                        } else if(($ownauthcontroller->PermissaoCheck(3) && !$ownauthcontroller->PermissaoCheck(1)) && (time()>strtotime("+".$avaliacao->prazo_nota." days", strtotime($avaliacao->data.' '.$avaliacao->hora)) || time()<strtotime($avaliacao->data.' '.$avaliacao->hora))){
                          $opcoes_avaliacao = ' <span id="pronto-faltasID_'.$avaliacao->id.'"><span class="badge badge-secondary">Pronto de faltas</span></span>
                                                <span class="badge badge-secondary" style="margin-left: 6px;">Lançar grau</span>';
                        } else if(($ownauthcontroller->PermissaoCheck(3) && !$ownauthcontroller->PermissaoCheck(1)) && (time()>strtotime($avaliacao->data.' '.$avaliacao->hora) && time()<strtotime("+".$avaliacao->prazo_nota." days", strtotime($avaliacao->data.' '.$avaliacao->hora)))){                                                
                          $opcoes_avaliacao = ' <span id="pronto-faltasID_'.$avaliacao->id.'"><a href="javascript: void(0);" class="badge badge-primary" onclick="dialogProntoFaltas('.$avaliacao->id.');">Pronto de faltas</a></span>
                                                <a href="javascript: void(0);" class="badge badge-primary" style="margin-left: 6px;" onclick="dialogLancarGraus('.$avaliacao->id.');">Lançar grau</a>';
                        } else if($ownauthcontroller->PermissaoCheck(1) && (time()>strtotime($avaliacao->data.' '.$avaliacao->hora) && time()<strtotime("+".$avaliacao->prazo_nota." days", strtotime($avaliacao->data.' '.$avaliacao->hora)))){ 

                          
                          $opcoes_avaliacao = '<div id="removeProntoFaltas_'.$avaliacao->id.'">
                                                  <select class="custom-select" name="omcts_id" style="margin-top:5px;width:75%">
                                                    <option value="0" disabled selected hidden>UETE</option>
                                                    '.implode('', $combo_uete).'
                                                  </select>
                                                  <span id="deleta-pronto-faltasID_'.$avaliacao->id.'" "><a href="javascript: void(0);" class="badge badge-danger" onclick="dialogRemoveProntoFaltas(this, '.$avaliacao->id.', \''.csrf_token().'\');">Remover Pronto de Faltas</a></span>
                                              </div>';
                        }else {
                          $opcoes_avaliacao = '';  
                        }

                        $data_avaliacao[] = ' <div class="card text-white '.$style_color_chamada.' mb-3">
                                                <div class="card-header">
                                                  <div style="float: left; margin-top: 4px;">
                                                    '.$avaliacao->nome_completo.'
                                                  </div>
                                                  <div style="float: right;">
                                                    '.$opcoes_avaliacoes.'
                                                  </div>
                                                  <div class="clear;"></div>
                                                </div>
                                                <div class="card-body">
                                                  <h5 class="card-title" style="float: left; margin: -2px 8px 0 0;">
                                                    '.$avaliacao->nome_abrev.'
                                                  </h5>
                                                  <footer class="blockquote-footer" style="color: #fff;">Prevista para <cite title="Source Title">'.strftime('%A, %d de %B de %Y às %H:%M', strtotime($avaliacao->data.' '.$avaliacao->hora)).'</cite></footer>
                                                  <div class="clear"></div>
                                                  <p style="margin-top: -10px;"></p>
                                                    <b>GBM:</b> '.$avaliacao->gbm.' <br />
                                                    <b>Peso:</b> '.$avaliacao->peso.'<br />
                                                    <b>Limite para laçamento de grau:</b> <cite title="Source Title">'.strftime('%A, %d de %B de %Y às %H:%M', strtotime("+".$avaliacao->prazo_nota." days", strtotime($avaliacao->data.' '.$avaliacao->hora))).'</cite> 
                                                  </p>
                                                  <p class="card-text">'.$avaliacao->observacao.'</p>
                                                  </div>
                                                  <div class="card-footer '.$style_color_footer.'" style="background-color: #fff; color:black">
                                                    <div style="float: left;">'.$avaliacao_referencia.'</div>
                                                    <div style="float: right;">
                                                      '.$opcoes_avaliacao.'
                                                    </div>
                                                    <div class="clear"></div>
                                                    <div id="content-opcoes-avaliacaoID_'.$avaliacao->id.'"></div>
                                                  </div>
                                              </div>';
                      ?>
                    @endif
                  @endforeach
                  
                  <?php
                    if($qtde_avaliacoes==0) { 
                      $card_body = '<div style="color: #B40404; text-align:center">Não há avaliações para esta disciplina!</div>';
                      $qtde_titulo = '';
                    } else {
                      $card_body = implode('', $data_avaliacao);
                      $qtde_titulo = '<span style="font-style: italic;">('.$qtde_avaliacoes.')</span>';
                    }
                    unset($data_avaliacao); 
                  ?>
            
                    <div class="card">
                      <div class="card-header" id="heading{{$loop->index}}">
                        <h5 class="mb-0">
                          <button id="disciplina_{{$disciplina->id}}" class="btn btn-link accordion-style-button" data-toggle="collapse" data-target="#collapse{{$loop->index}}" aria-expanded="true" aria-controls="collapseOne">
                            {{$disciplina->nome_disciplina}} {!!$qtde_titulo!!}
                          </button>
                          <blockquote id="disciplina_{{$disciplina->id}}" class="blockquote" style="display: none;">
                            <footer class="blockquote-footer"></footer>
                          </blockquote>
                        </h5>
                      </div>
                  
                      <div id="collapse{{$loop->index}}" class="collapse" aria-labelledby="heading{{$loop->index}}" data-parent="#accordion">
                      <div class="card-body">
                        {!!$card_body!!}
                      </div>
                      </div>
                    </div>
                  @endforeach
                  
                  <div class="card">
                    <div class="card-header" id="heading99999">
                      <h5 class="mb-0">
                        <button id="disciplina_99999" class="btn btn-link accordion-style-button" data-toggle="collapse" data-target="#collapse99999" aria-expanded="true" aria-controls="collapseOne">
                          TESTE DE APTIDÃO FÍSICA
                        </button>
                        <blockquote id="disciplina_99999" class="blockquote" style="display: none;">
                          <footer class="blockquote-footer"></footer>
                        </blockquote>
                      </h5>
                    </div>
                
                    <div id="collapse99999" class="collapse" aria-labelledby="heading99999" data-parent="#accordion">
                    <div class="card-body">
                      <div style="margin: 18px; text-align: center;">
                        <div style="text-align: left">
                          <p><b>PERÍODO ABERTO PARA LANÇAMENTO:</b></p>
                          <div style="margin-top: 12px;">
                            <b>INÍCIO:</b> {{$confLancaTaf['data_inicio']}} às {{$confLancaTaf['hora_inicio']}}<br />
                            <b>TÉRMINO:</b> {{$confLancaTaf['data_fim']}} às {{$confLancaTaf['hora_fim']}}
                          </div>
                        </div>
                        @if($ownauthcontroller->PermissaoCheck(1))
                          <div style="margin-top: 18px;">
                            <button type="button" class="btn btn-success" onclick="dialogPeriodoLancamentoTAF();">
                                Editar período de lançamento
                            </button>
                          </div>
                        @endif
                    </div> 
                    </div>
                    </div>
                  </div>
                </div>
            @endif
        </p>
    </div>
    
</div>

<script>

    $("[data-toggle=popover]").popover({
            trigger: 'focus',
            html: true, 
            delay: { "show": 100, "hide": 400 },
            content: function() {
                return $('#popover-content').html();
            }
    }); 

</script>
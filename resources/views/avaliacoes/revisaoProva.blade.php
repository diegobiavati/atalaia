<div class="card bg-light mb-3">
    <div class="card-header">
        <i class="ion-ios-compose"></i><strong>Revisão de Prova</strong>
    </div>
    <div class="card-body">
        <div class="alert alert-danger errors-adicionar-avaliacoes2" role="alert"></div>
        <h4 style="text-align: center; margin: 18px 0 44px 0; color: #696969;">
          REVISÃO DE PROVAS PENDENTES NAS RESPECTIVAS DISCIPLINAS
        </h4>

        <p class="card-text">
        @if(sizeOf($avaliacoesMostra) > 0)
        
            @foreach($avaliacoesMostra as $avaliacoes)
            <?
                $disciplina = $avaliacoes->disciplinas;

                if($avaliacoes->chamada==0 && $avaliacoes->avaliacao_recuperacao==1){
                    $style_color_chamada = 'bg-secondary';
                    $style_color_footer = 'border-secondary';
                    $avaliacao_referencia = 'Única chamada';
                } else if($avaliacoes->chamada==1){
                    $style_color_chamada = 'bg-success';
                    $style_color_footer = 'border-success';
                    $avaliacao_referencia = $avaliacoes->chamada.'ª chamada';
                } else {
                    $style_color_chamada = 'bg-warning';                          
                    $style_color_footer = 'border-warning';
                    $avaliacao_referencia = $avaliacoes->chamada.'ª chamada da '.$avaliacao_nome_por_indice[$avaliacoes->chamada_refer_id];                          
                }

                $opcoes_mostra = null;
                $opcoes_resposta_mostra = null;

                $opcoes_mostra = '<div id="div_mostra_'.$avaliacoes->id.'" class="card text-white bg-info" style="margin: 10px;">
                                                <div class="card-header">
                                                  Pedido de Revisão de Prova
                                                </div>';

                $avaliacaoController = new App\Http\Controllers\Ajax\AjaxAvaliacoesController();

                $opcoes_mostra .= $avaliacaoController->ViewListaArquivoMostra($avaliacoes->avaliacoesMostra).'</div>';

                /*$opcoes_resposta_mostra = '<div id="div_resposta_mostra_'.$avaliacoes->id.'" class="card text-white bg-danger" style="margin: 10px;">
                                                <div class="card-header">
                                                  Solução de Pedido de Revisão de Prova
                                                </div>';*/

                /*if(in_array(8, session()->get('login')['perfil'])){
                    
                    foreach($avaliacoes->avaliacoesMostra as $avaliacoesMostra){
                      $omctsId[] = $avaliacoesMostra->omct_id;
                    }
                    
                    $uetes = App\Models\OMCT::whereIn('id', $omctsId)->get(); //Remove a ESA

                    $comboUete = null;
                    foreach ($uetes as $uete){
                        $comboUete .= "<option value=$uete->id>$uete->omct</option>";
                    }

                    $opcoes_resposta_mostra .= '<div class="card-body">
                                                    <form id="form_resposta_mostra_'. $avaliacoes->id .'">
                                                      <input type="hidden" name="_token" value="'.csrf_token().'">

                                                      <div class="alert alert-danger erro-upload" role="alert" style="display: none;"></div>
                                                      <div class="progress" style="margin-top: 36px; display: none;">
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                      </div>

                                                      <div class="row" style="display:-webkit-box;">
                                                        <div class="col" style="width:30%;float:right;">
                                                          <select name="omctID" class="custom-select">
                                                            <option value="0" disabled selected hidden>Selecione uma UETE</option>
                                                            <option value="todas_omct">TODAS AS UETE</option>
                                                            '.$comboUete.'
                                                          </select>
                                                        </div>
                                                        <div class="col custom-file" style="width:60%;float:right;">
                                                          <input type="file" class="custom-file-input" name="arquivo_mostra_'.$avaliacoes->id.'" id="arquivo_mostra_'.$avaliacoes->id.'" accept=".pdf">
                                                          <label class="custom-file-label" style="border-color: #007bff;" for="arquivo_mostra">Escolha o arquivo</label>
                                                        </div>
                                                        <div class="col" style="float:right;width:10%;">
                                                          <button type="button" class="btn btn-primary" onclick="enviarArquivo(\'form_resposta_mostra_'. $avaliacoes->id .'\', \'ajax/upload/resposta-arquivo-mostra/arquivo_mostra_'.$avaliacoes->id.'\');">Enviar</button>
                                                        </div>
                                                      </div>
                                                    </form>
                                                </div>';
                }*/
                
                $opcoes_resposta_mostra = '<div id="div_resposta_mostra_'.$avaliacoes->id.'" class="card text-white bg-danger" style="margin: 10px;">
                                                          <div class="card-header">
                                                              Solução de Pedido de Revisão de Prova
                                                          </div>';

                $opcoes_resposta_mostra .= $avaliacaoController->ViewListaArquivoRepostaMostra($avaliacoes).'</div>';

                $data_avaliacao = null;

                $dataMostra = (isset($avaliacoes->data_mostra) ? strftime('%A, %d de %B de %Y às %H:%M', strtotime($avaliacoes->data_mostra)) : null);
                $dataLimiteMostra = (isset($avaliacoes->limite_dias_pedido) ? strftime('%A, %d de %B de %Y às 23:59', strtotime($avaliacoes->data_mostra.' + '.$avaliacoes->limite_dias_pedido.' days')) : null);

                $data_avaliacao[] = ' <div class="card text-white '.$style_color_chamada.' mb-3">
                                                <div class="card-header">
                                                  <div style="float: left; margin-top: 4px;">
                                                    '.$avaliacoes->nome_completo.'
                                                  </div>
                                                  <div class="clear;"></div>
                                                </div>
                                                <div class="card-body">
                                                  <h5 class="card-title" style="float: left; margin: -2px 8px 0 0;">
                                                    '.$avaliacoes->nome_abrev.'
                                                  </h5>
                                                  <footer class="blockquote-footer" style="color: #fff;">Prevista para <cite title="Source Title">'.strftime('%A, %d de %B de %Y às %H:%M', strtotime($avaliacoes->data.' '.$avaliacoes->hora)).'</cite></footer>
                                                  <div class="clear"></div>
                                                  <p style="margin-top: -10px;"></p>
                                                    <b>GBM:</b> '.$avaliacoes->gbm.' <br />
                                                    <b>Peso:</b> '.$avaliacoes->peso.'<br />
                                                    <b>Limite para laçamento de grau:</b> <cite title="Source Title">'.strftime('%A, %d de %B de %Y às %H:%M', strtotime("+".$avaliacoes->prazo_nota." days", strtotime($avaliacoes->data.' '.$avaliacoes->hora))).'</cite> 
                                                    <b>Data da Mostra:</b> <cite title="Source Title">'.$dataMostra.'</cite><br /> 
                                                    <b>Limite do Pedido de Revisão da Prova:</b> <cite title="Source Title">'.$dataLimiteMostra.'</cite> 
                                                  </p>
                                                  <p class="card-text">'.$avaliacoes->observacao.'</p>
                                                  </div>
                                                  <div class="card-footer '.$style_color_footer.'" style="background-color: #fff; color:black">
                                                    <div style="float: left;">'.$avaliacao_referencia.'</div>

                                                    <div class="clear"></div>
                                                    '.$opcoes_mostra.'
                                                    <div class="clear"></div>

                                                    <div class="clear"></div>
                                                    '.$opcoes_resposta_mostra.'
                                                    <div class="clear"></div>

                                                    <div id="content-opcoes-avaliacaoID_'.$avaliacoes->id.'"></div>
                                                  </div>
                                              </div>';

                $card_body = implode('', $data_avaliacao);

            ?>
                <div id="accordion">
                    <div class="card">
                      <div class="card-header" id="heading{{$loop->index}}">
                        <h5 class="mb-0">
                          <button id="disciplina_{{$disciplina->id}}" class="btn btn-link accordion-style-button" data-toggle="collapse" data-target="#collapse{{$loop->index}}" aria-expanded="true" aria-controls="collapseOne">
                            {{$disciplina->nome_disciplina}} 
                          </button>
                          <b style="font-size: 15px;">Total : {{ $avaliacoes->avaliacoesMostra->count() }}</b>
                          <blockquote id="disciplina_{{$disciplina->id}}" class="blockquote" style="display: none;">
                            <footer class="blockquote-footer"></footer>
                          </blockquote>
                        </h5>
                      </div>
                  
                      <div id="collapse{{$loop->index}}" class="collapse" aria-labelledby="heading{{$loop->index}}" data-parent="#accordion">
                      <div class="card-body">
                        {!! $card_body !!}
                      </div>
                      </div>
                    </div>
                </div>

            @endforeach
            
        @else
          <div style="text-align: center;">
              <label class="custom-control-label">
                  <font style="font-size: 1.05rem;">Não Existem Solicitações de Revisão de Provas Pendentes.</font>
              </label>
          </div>
        @endif
        </p>
    </div>
</div>
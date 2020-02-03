<div class="card bg-light mb-3">
        <div class="card-header">
            <i class="ion-ios-calendar-outline"></i><strong>Anos de formação</strong>
            <div style="float: right">
                <!--div class="box-pesquisar-in-card-title" style="display: inline-block; padding:0; height: 36px;">
                    <input class="pesquisar-in-card-title busca-operador" type="text" placeholder="Busca" />
                    <a class="no-style" href="javascript: void(0);">
                            <i class="ion-android-search" style="color: #696969;"></i>
                    </a>
                </div-->                
                <a href="javascript: void(0);" data-toggle="popover" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
                    <i class="ion-android-more-vertical" style="color: #696969;"></i>
                </a>
                <div id="popover-content" style="display: none;">
                    <div class="menu_inside_popover">
                        <i class="ion-compose"></i><a href="javascript: void(0);" onclick="dialogAdicionarAnoFormacao();">Novo ano de formação</a><br />                 
                    </div>
                </div>    
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-danger errors-cadastro-ano-formacao" role="alert"></div>
            <h5 class="card-title" style="text-align: center; margin: 18px 0 44px 0;">
                RELAÇÃO DOS ANOS DE FORMAÇÃO
            </h5>           
            <p class="card-text">
                @forelse($anos_formacao as $ano)
                    <div id="ano_de_formacao_{{$ano->id}}" style="width: 360px; margin: 0 auto; border-bottom: 1px solid #ccc; padding: 4px;">
                        <li style="font-size: 18px; float: left;">
                            {{$ano->formacao}}
                            <br /><span id="ano_de_formacao_data_matricula_{{$ano->id}}" style="color: #696969; font-style: italic; font-size: 12px;">(Data matrícula: <?= strftime('%d/%m/%Y', strtotime($ano->data_matricula)) ?>)</span>
                        </li>
                        <div style="float: right; font-size: 18px; margin-top: 16px;">
                            <a href="#" onclick="dialogEditarAnoFormacao({{$ano->id}});" class="no-style">
                                <i class="ion-android-create"></i>
                            </a>                            
                            <a href="#" onclick="dialogRemoverAnoFormacao({{$ano->id}});" class="no-style" style="margin-left: 12px;">
                                <i class="ion-android-delete"></i>
                            </a>
                        </div>
                        <div class="clear"></div>
                    </div>
                    
                @empty
                    <div class="box-registro-not-found">
                        <i class="ion-social-snapchat-outline" style="font-size: 32px"></i><br />
                        <span style="color: brown">Não há registro de anos de formação</span>
                    </div>
                @endforelse                
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
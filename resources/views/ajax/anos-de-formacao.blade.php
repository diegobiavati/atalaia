<div class="card bg-light mb-3">
        <div class="card-header">
            <i class="ion-ios-calendar-outline"></i><strong>Anos de formação</strong>
            @if(!session()->has('login.qmsID'))
            <div style="float: right">           
                <a href="javascript: void(0);" data-toggle="popover" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
                    <i class="ion-android-more-vertical" style="color: #696969;"></i>
                </a>
                
                <div id="popover-content" style="display: none;">
                    <div class="menu_inside_popover">
                        <i class="ion-compose"></i><a href="javascript: void(0);" onclick="dialogAdicionarAnoFormacao();">Novo ano de formação</a><br />                 
                    </div>
                </div>    
            </div>
            @endif
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
                            @if(!session()->has('login.qmsID'))                                      
                            <a href="#" onclick="dialogRemoverAnoFormacao({{$ano->id}});" class="no-style" style="margin-left: 12px;">
                                <i class="ion-android-delete"></i>
                            </a>
                            @endif
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

        // DIALOGO EDITAR ANO FORMAÇÃO 
        function dialogEditarAnoFormacao(id){
             $.ajax({
            	type:'GET',
                dataType: 'json',
                url: '/ajax/dialog-editar-ano-formacao/' + id,
                beforeSend: function(){
                    loadingModalDinamica('show', 'sm');
                },
                success: function(data){
                    $('div#modalDinamica div.modal-header h5').html(data.header);
                    $('div#modalDinamica div.modal-body').html(data.body);
                    $('div#modalDinamica div.modal-footer').html(data.footer);
                    setTimeout(function(){
                        $('input[name="data_matricula"]').select();
                    }, 600);
                    loadingModalDinamica('hide', 'sm');
                }
            });
        }

        // ATUALIZAR ANO DE FORMAÇÃO 
        function AtualizarAnoFormacao(id) {         
            var dataForm = $('form#atualizar_ano_formacao').serialize();
            var style_bg_inicial = $('div#ano_de_formacao_'+id).css('background-color');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                url: '/ajax/atualizar-ano-formacao/' + id,
                beforeSend: function(){
                    $('div#modalDinamica').modal('hide');
                    $('div#ano_de_formacao_'+id).css('background-color', '#81F7BE');
                },
                success: function(data){
                    $('div#ano_de_formacao_'+id).css('background-color', style_bg_inicial);
                    if(data.status=='ok'){
                        $('span#ano_de_formacao_data_matricula_' + id).html(data.content)
                    } else {
                        $('div.errors-cadastro-ano-formacao').html(data.content).slideDown();
                        setTimeout(function(){
                            $('div.errors-cadastro-ano-formacao').slideUp(200, function(){
                                $(this).empty();    
                            });
                        }, 10000);
                    }
                },
                error: function(jqxhr){
                    $('div#ano_de_formacao_'+id).css('background-color', style_bg_inicial);
                    $('div.errors-cadastro-ano-formacao').html('Erro interno ao tentar atualizar a data inicial de matrícula.').slideDown();
                    setTimeout(function(){
                            $('div.errors-cadastro-ano-formacao').slideUp(200, function(){
                                $(this).empty();    
                            });
                        }, 10000);                    
                }                    
            });            
        }
    
    </script>
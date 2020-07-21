<style>
    a.nav-link{
        color: #A4A4A4;
    }
    tr:nth-child(even) {
        background: #F2F2F2;

    }
    
</style>
<div class="card bg-light mb-3">
        <div class="card-header">
            <i class="ion-plane"></i><strong>Voluntários para aviação</strong>
            <!--div style="float: right">
                <div class="box-pesquisar-in-card-title" style="display: inline-block; padding:0; height: 36px;">
                    <input class="pesquisar-in-card-title busca-operador" type="text" placeholder="Busca" />
                    <a class="no-style" href="javascript: void(0);">
                            <i class="ion-android-search" style="color: #696969;"></i>
                    </a>
                </div>
                @if($ano_corrente)
                    <a href="javascript: void(0);" data-toggle="popover" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
                        <i class="ion-android-more-vertical" style="color: #696969;"></i>
                    </a>
                    <div id="popover-content" style="display: none;">
                        <div class="menu_inside_popover">
                            <i class="ion-plus"></i><a href="javascript: void(0);" onclick="dialogAdicionarPortaria();">Criar novo período</a><br />                 
                        </div>
                        <div class="menu_inside_popover">
                            <i class="ion-android-arrow-down"></i><a href="javascript: void(0);" onclick="dialogImportarDisciplinas();">Importar disciplinas</a><br />                 
                        </div>                                            
                    </div>
                @endif  
            </div-->
        </div>
    
        <div class="card-body">
            <div class="alert alert-danger errors-liberar-escolha-aviacao" role="alert"></div>
            <p class="card-text" style="margin-top: 32px;">
                <nav class="nav-justified">
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        @if($ownauthcontroller->PermissaoCheck(1))
                        <a class="nav-item nav-link active" data-toggle="tab" href="#liberar-escolha-sistema" role="tab" aria-controls="nav-home" aria-selected="true">Liberar escolha no sistema</a>
                        <a class="nav-item nav-link" data-toggle="tab" href="#relacao-selecao-exame" role="tab" aria-controls="nav-home" aria-selected="true" onclick="dialogRelacaoAlunosExame()">Seleção de Voluntários para Exame</a>
                        <a class="nav-item nav-link" data-toggle="tab" href="#relacao-voluntatios" role="tab" aria-controls="nav-home" aria-selected="true" onclick="dialogRelacaoVoluntariosAviacao();">Seleção de aptos</a>
                        @endif
                        @if($ownauthcontroller->PermissaoCheck(25))
                        <a class="nav-item nav-link active" data-toggle="tab" href="#relacao-alunos" role="tab" aria-controls="nav-home" aria-selected="true" onclick="dialogRelacaoAlunos();"><b>Lançar Voluntários para Aviação</b></a>
                        @endif
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    @if($ownauthcontroller->PermissaoCheck(1))
                    <div class="tab-pane fade show active" id="liberar-escolha-sistema" role="tabpanel" aria-labelledby="nav-home-tab" style="margin-top: 80px;">                        
                        <h5 class="card-title" style="text-align: center; margin: 18px 0 44px 0;">
                            @if($ano_corrente)
                                OPÇÃO POR QMS AVIAÇÃO PARA O ANO DE FORMAÇÃO <span style="font-size: 24px;"><b>{{$ano_corrente->formacao}}</b></span>
                            @endif        
                        </h5>           
                            @if($ano_corrente)
                                <div style="text-align: center; width: 70%; max-width: 800px; margin: 32px auto;">
                                        <a href="javascript: void(0);" onclick="marcarVoluntarioAviacao(this);" class="no-style">
                                            @if($escolha_aviacao_status->status==0)
                                                <div style="border-radius: 50%; border: 1px solid #B40404; width: 40px; height: 40px; margin: 24px auto;"></div>
                                                <h6 style="color: #B40404;"><b>OPÇÃO POR QMS AVIAÇÃO INSDISPONÍVEL PARA ESCOLHA</b><br /><small>Clique aqui para tornar disponível</small></h6>
                                            @else
                                                <div style="border-radius: 50%; border: 1px solid #0B6121; width: 40px; height: 40px; margin: 24px auto;">
                                                        <i style="color: #0B6121; font-size: 26px;" class="ion-android-done"></i>
                                                </div>
                                                <h6 style="color: #0B6121;"><b>OPÇÃO POR QMS AVIAÇÃO DISPONÍVEL PARA ESCOLHA</b><br /><small>Clique aqui para tornar indisponível</small></h6>
                                            @endif
                                        </a>
                                </div>
                            @else
                                <div class="alert alert-danger" role="alert" style="width: 80%; margin: 0 auto; text-align: center;">
                                    <strong>ATENÇÃO</strong><br />
                                    <p>Para abrir o sistema para preenchimento da opção de <i>Voluntários para Aviação</i> é necessário adicionar ao sistema pelo menos um <i>Ano de Formação</i>.</p>
                                </div>                
                            @endif
                    </div>
                    <div class="tab-pane fade show active" id="relacao-selecao-exame" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 56px;">
                        <div id="content-selecao-exame"></div>
                    </div>
                    <div class="tab-pane fade show active" id="relacao-voluntatios" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 56px;">                        
                        <div id="content-voluntarios-aviacao"></div>   
                    </div> 
                    @endif                   
                    @if($ownauthcontroller->PermissaoCheck(25))
                    <div class="tab-pane fade show active" id="relacao-alunos" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 16px;">                        
                        <div id="content-alunos-aviacao"></div>   
                    </div> 
                    @endif
                </div>
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
    
        function dialogRelacaoAlunos(){
            $.ajax({
            	type:'GET',
                url: '/ajax/relacao-alunos-aviacao',
                beforeSend: function(){
                    $('div.alert').html(null).hide();
                    $('div#content-alunos-aviacao').html('<div id="temp" style="text-align: center; margin: 24px; padding: 24px;"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
                },
                success: function(data){
                    $('div#temp').fadeOut(300, function(){
                        $(this).remove();
                        $('div#content-alunos-aviacao').html(data);
                    });
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            });       
        }

        function dialogRelacaoAlunosExame(){
            $.ajax({
            	type:'GET',
                url: '/ajax/relacao-selecao-exame',
                beforeSend: function(){
                    $('div.alert').html(null).hide();
                    $('div#content-selecao-exame').html('<div id="temp" style="text-align: center; margin: 24px; padding: 24px;"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
                },
                success: function(data){
                    $('div#temp').fadeOut(300, function(){
                        $(this).remove();
                        $('div#content-selecao-exame').html(data);
                    });
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            });       
        }

        //Valida se existe o elemento
        if ($("a[href='#relacao-alunos']").length) { 
            dialogRelacaoAlunos(); 
        }
        
    </script>
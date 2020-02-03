<div class="card bg-light mb-3">
        <div class="card-header">
            <i class="ion-ios-bookmarks"></i><strong>Gerenciar disciplinas</strong>
            <div style="float: right">
                <!--div class="box-pesquisar-in-card-title" style="display: inline-block; padding:0; height: 36px;">
                    <input class="pesquisar-in-card-title busca-operador" type="text" placeholder="Busca" />
                    <a class="no-style" href="javascript: void(0);">
                            <i class="ion-android-search" style="color: #696969;"></i>
                    </a>
                </div-->
                @if(count($anos_formacao)>0)
                    <a href="javascript: void(0);" data-toggle="popover" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
                        <i class="ion-android-more-vertical" style="color: #696969;"></i>
                    </a>
                    <div id="popover-content" style="display: none;">
                        <div class="menu_inside_popover">
                            <i class="ion-ios-bookmarks"></i><a href="javascript: void(0);" onclick="dialogAdicionarDisciplina();">Adicionar disciplina no sistema</a><br />                 
                        </div>
                        <div class="menu_inside_popover">
                            <i class="ion-android-arrow-down"></i><a href="javascript: void(0);" onclick="dialogImportarDisciplinas();">Importar disciplinas</a><br />                 
                        </div>
                        <!--div class="menu_inside_popover">
                                <i class="ion-android-list"></i><a href="javascript: void(0);" onclick="dialogDisciplinasAnosAnteriores();">Disciplinas de anos anteriores</a><br />                 
                        </div-->                                              
                    </div>
                @endif    
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-danger errors-adicionar-disciplinas2" role="alert"></div>
            <h5 class="card-title" style="text-align: center; margin: 18px 0 44px 0;">
                @if(count($anos_formacao)>0)
                    RELAÇÃO DE DISCIPLINAS CADASTRADAS NO ANO DE FORMAÇÃO <span style="font-size: 24px;"><b>{{$ano_corrente->formacao}}</b></span>
                @endif
            </h5>           
            <p class="card-text">
                @if(count($anos_formacao)>0)

                    @forelse($disciplinas as $disciplina)

                        <div id="disciplina_{{$disciplina->id}}" style="width: 480px; margin: 0 auto; border-bottom: 1px solid #ccc; padding: 4px;">
                            <li style="font-size: 18px; float: left;">
                                <strong>{{$disciplina->nome_disciplina_abrev}}</strong><br />
                                <div style="font-size: 14px; color: #696969; margin-left: 26px;">{{$disciplina->nome_disciplina}}
                                <br /><span style="color: #696969; font-style: italic; font-size: 12px;">(Peso {{$disciplina->peso}})</span></div>
                            </li>
                            <div style="float: right; font-size: 18px; margin-top: 22px;">
                                <a href="javascript: void(0);" class="no-style" onclick="dialogEditarDisciplina({{$disciplina->id}});" title="Editar configurações da disciplina">
                                    <i class="ion-android-create"></i>
                                </a>
                                <a href="#" onclick="dialogRemoverDisciplina({{$disciplina->id}});" class="no-style" style="margin-left: 10px;" >
                                    <i class="ion-android-delete"></i>
                                </a>
                            </div>
                            <div class="clear"></div>
                        </div>
                        
                    @empty

                        <div class="box-registro-not-found">
                            <i class="ion-social-snapchat-outline" style="font-size: 32px"></i><br />
                            <span style="color: brown">Não há disciplinas configuradas para o ano de {{$ano_corrente->formacao}}</span>
                        </div>
                        
                    @endforelse

                @else
                    <div class="alert alert-danger" role="alert" style="width: 80%; margin: 0 auto; text-align: center;">
                        <strong>ATENÇÃO</strong><br />
                        <p>Para configurar novas disciplinas adicione primeiro um <i>Ano de Formação</i>.<br />As disciplina serão cadastrardas no <i>Ano de Formação</i> mais recente.</p>
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
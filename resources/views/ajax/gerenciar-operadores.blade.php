    <style>
        tr:nth-child(even) {
            background: #F2F2F2;
        }    
    </style>
    <div class="card bg-light mb-3">
            <div class="card-header">
                <i class="ion-ios-people"></i><strong>Gerenciar Operadores</strong>
                <div style="float: right">
                    <div class="box-pesquisar-in-card-title" style="display: inline-block; padding:0; height: 36px;">
                        <input class="pesquisar-in-card-title busca-operador" type="text" placeholder="Busca" />
                        <a class="no-style" href="javascript: void(0);">
                                <i class="ion-android-search" style="color: #696969;"></i>
                        </a>
                    </div>                
                    <!--a class="no-style" href="javascript: void(0);" onclick="" placement="bottom" style="margin-left: 6px;"-->
                    <a href="javascript: void(0);" data-toggle="popover" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
                        <i class="ion-android-more-vertical" style="color: #696969;"></i>
                    </a>
                    <div id="popover-content" style="display: none;">
                        <div class="menu_inside_popover">
                            <i class="ion-android-person-add"></i><a href="javascript: void(0);" onclick="dialogAdicionarOperador();">Adicionar operador</a><br />                 
                        </div>
                    </div>    
                </div>
            </div>
            <div class="card-body">
                <h5 class="card-title" style="text-align: center; margin: 18px 0 44px 0;">
                    RELAÇÃO DE USUÁRIOS OPERADORES DO SISTEMA ATALAIA
                    <!-- @if($ownauthcontroller->PermissaoCheck(1))
                        TESTANDO PERMISSÃO NA VIEW
                    @endif -->
                </h5>
                    @if(count($operadores)>0)           
                        <p class="card-text">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <!--th scope="col">POSTO/GRAD</th-->
                                        <th scope="col">MILITAR</th>
                                        <th scope="col">FUNÇÃO</th>
                                        <th scope="col">UETE</th>
                                        <th scope="col">CONTATOS</th>
                                        <th scope="col" style="width: 140px;">AÇÃO</th>
                                    </tr>
                                </thead>
                                <tbody>                    
                                @foreach($operadores as $op)
                                <tr id="operador_{{$op->id}}">
                                        <!--td>{{$op->postograd->postograd_abrev}}</td-->
                                        <td class="operador-nome">
                                            <b>{{$op->postograd->postograd_abrev}} {{$op->nome_guerra}}</b><br />
                                            <span style="font-size: 11px;"><i>{{$op->nome}}</i></span>

                                        </td>
                                        <td>
                                        <?php

                                            if(is_array(explode(',', $op->id_funcao_operador))){
                                                foreach(explode(',', $op->id_funcao_operador) as $funcao){
                                                    $funcao_extenso[] = (isset($data[$funcao]))?$data[$funcao]:'<span style="color: #DF0101;">NÃO CONFIGURADA</span>';
                                                }
                                            }
                                            echo implode(', ', $funcao_extenso);
                                            unset($funcao_extenso);
                                        ?>
                                        </td>
                                        <td>{{$op->omcts->sigla_omct}}</td>   
                                    <td>{{$op->tel_pronto_atendimento}}<br />{{$op->email}}</td>
                                        <td class="botao-action-table">
                                            <a href="javascript: void(0);" class="no-style" onclick="dialogInfoUser({{$op->usuario->id}}, 'operador');" title="Detalhes do operador"><i class="ion-information-circled"></i></a>
                                            <a href="javascript: void(0);" class="no-style" onclick="dialogEditarOperador({{$op->id}});" title="Editar configurações do operador"><i class="ion-android-create"></i></a>
                                            <!--<a href="javascript: void(0);" class="no-style" onclick="dialogRemoverOperador({{$op->id}}, '{{$op->nome_guerra}}');" title="Remover este Operador do Sistema"><i class="ion-android-delete" style="font-size: 22px;"></i></a>-->
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="box-pesquisa-not-found">
                                <i class="ion-social-snapchat-outline" style="font-size: 32px"></i><br />
                                <span style="color: brown">Sem ocorrências para palavra pesquisada!</span>
                            </div>    
                        </p>
                    @else
                        <div class="box-registro-not-found">
                            <i class="ion-social-snapchat-outline" style="font-size: 32px"></i><br />
                            <span style="color: brown">Não há operadores cadastrados</span>
                        </div>                        
                    @endif
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
    
    
    <style>
tr:nth-child(even) {
    background: #F2F2F2;
}
    </style>
    <?php
    $url_editar = ((session()->get('login.omctID')) ? '/ajax/dialog-editar-operador/' : '/gaviao/ajax/dialog-editar-operador-gaviao/');
    $url_adicionar = ((session()->get('login.omctID')) ? '/ajax/dialog-adicionar-operador/' : '/gaviao/ajax/dialog-adicionar-operador-gaviao');
    ?>
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

        @if(isset($ownauthcontroller) && $ownauthcontroller->PermissaoCheck(1))
        <div style="display: inline-block; margin-left: 15px; vertical-align: middle;">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="switchInativos" {{ (isset($mostrarInativos) && $mostrarInativos == 'S') ? 'checked' : '' }}>
                <label class="custom-control-label" for="switchInativos" style="font-size: 12px; cursor: pointer; color: #696969;"><strong>Mostrar Inativos</strong></label>
            </div>
        </div>
        @endif 
        <a href="javascript: void(0);" data-toggle="popover" data-container="body" data-placement="bottom"
           data-html="true" style="margin-left: 12px;">
            <i class="ion-android-more-vertical" style="color: #696969;"></i>
        </a>
        <div id="popover-content" style="display: none;">
                    <div class="menu_inside_popover">
                        <i class="ion-android-person-add"></i><a href="javascript: void(0);"
                            onclick="dialogAdicionarOperador();">Adicionar operador</a><br />
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <h5 class="card-title" style="text-align: center; margin: 18px 0 44px 0;">
                RELAÇÃO DE USUÁRIOS OPERADORES DO SISTEMA {{ ((session()->get('login.omctID')) ? 'ATALAIA': 'GAVIÃO') }}
            </h5>
            @if(count($operadores)>0)
            <p class="card-text">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <!--th scope="col">POSTO/GRAD</th-->
                        <th scope="col">MILITAR</th>
                        <th scope="col">FUNÇÃO</th>
                        <th scope="col">{{ (session()->get('login.qmsID') ? 'CURSO' : 'UETE') }}</th>
                        <th scope="col">CONTATOS</th>
                        <th scope="col" style="width: 140px;">AÇÃO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($operadores as $op)
                    @isset($op->id)
                    <tr id="operador_{{$op->id}}">
                        <!--td>{{$op->postograd->postograd_abrev}}</td-->
                        <td class="operador-nome">
                            <b>{{$op->postograd->postograd_abrev}} {{$op->nome_guerra}}</b><br />
                            <span style="font-size: 11px;"><i>{{$op->nome}}</i></span>
                        </td>
                        <td>
                            <?php

                            if (is_array(explode(',', $op->id_funcao_operador))) {
                                foreach (explode(',', $op->id_funcao_operador) as $funcao) {
                                    $funcao_extenso[] = (isset($data[$funcao])) ? $data[$funcao] : '<span style="color: #DF0101;">NÃO CONFIGURADA</span>';
                                }
                            }
                            echo implode(', ', $funcao_extenso);
                            unset($funcao_extenso);
                            ?>
                        </td>
                        <td>{{ (session()->get('login.qmsID') ? $op->qms->qms : $op->omcts->sigla_omct) }}</td>
                        <td>{{$op->tel_pronto_atendimento}}<br />{{$op->email}}</td>
                        <td class="botao-action-table">
                            <a href="javascript: void(0);" class="no-style"
                                onclick="dialogInfoUser({{$op->id}}, 'operador');"
                                title="Detalhes do operador"><i class="ion-information-circled"></i></a>
                            <a href="javascript: void(0);" class="no-style" onclick="dialogEditarOperador({{$op->id}});"
                                title="Editar configurações do operador"><i class="ion-android-create"></i></a>
                        </td>
                    </tr>
                    @endisset
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
    delay: {
        "show": 100,
        "hide": 400
    },
    content: function() {
        return $('#popover-content').html();
    }
});

// PESQUISA NA TABELA DE OPERADORES
$(document).on('keyup', 'input.busca-operador', function() {
    if ($('td.operador-nome').is(':visible')) {
        $('.box-pesquisa-not-found').hide();
        $("td.operador-nome:not(:contains('" + $(this).val() + "'))").parent().hide();
        $("td.operador-nome:contains('" + $(this).val() + "')").parent().show();
    } else {
        $("td.operador-nome:contains('" + $(this).val() + "')").parent().show();
        if ($('td.operador-nome').is(':visible')) {
            $('.box-pesquisa-not-found').hide();
        } else {
            $('.box-pesquisa-not-found').show();
        }
    }
});

// MOSTRA DIALOGO EDITAR OPERADORES 
function dialogEditarOperador(id) {

    $.ajax({
        type: 'GET',
        url: '{{ $url_editar }}' + id,
        beforeSend: function() {
            loadingModalDinamica('show', 'lg');
            $('div#modalDinamica div.modal-body').html(
                '<div id="temp"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>'
                );
        },
        success: function(data) {
            if (data.body) {
                $('div#modalDinamica div.modal-header h5').html(data.header);
                $('div#modalDinamica div.modal-body').html(data.body);
                $('div#modalDinamica div.modal-footer').html(data.footer);
            } else {
                $('div#modalDinamica div.modal-content').html(data);
            }
            loadingModalDinamica('hide', 'lg');
        },
        error: function(jqxhr) {
            $('div#modalDinamica div.modal-body').html('<strong>ATENÇÃO: </strong> Houve um erro interno')
                .slideDown();
        }
    });
}

$(document).on('change', '#switchInativos', function() {
    let status = $(this).is(':checked') ? 'S' : 'N';
    let urlListagem = '{{ session()->get("login.omctID") ? "/ajax/gerenciar-operadores" : "/gaviao/ajax/gerenciar-operadores-gaviao" }}';
    
    $.ajax({
        type: 'GET',
        url: urlListagem,
        data: { mostrar_inativos: status },
        success: function(data) {
            // Atualiza a div correta que achamos no F12
            $('.inside-content').html(data); 
        }
    });
});

// MOSTRA DIALOGO ADICIONAR OPERADORES /          
function dialogAdicionarOperador() {
    $.ajax({
        type: 'GET',
        url: '{{ $url_adicionar }}',
        beforeSend: function() {
            loadingModalDinamica('show', 'lg');
        },
        success: function(data) {
            if (data.body) {
                $('div#modalDinamica div.modal-header h5').html(data.header);
                $('div#modalDinamica div.modal-body').html(data.body);
                $('div#modalDinamica div.modal-footer').html(data.footer);
            } else {
                $('div#modalDinamica div.modal-content').html(data);
            }
            loadingModalDinamica('hide', 'lg');
        },
        error: function(jqxhr) {
            $('div#modalDinamica div.modal-body').html('<strong>ATENÇÃO: </strong> Houve um erro interno')
                .slideDown();
        }
    });
}
    </script>
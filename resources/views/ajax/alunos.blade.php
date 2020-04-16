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
        <i class="ion-ios-calendar-outline"></i><strong>Alunos</strong>
        <div style="float: right">
            <div class="box-pesquisar-in-card-title" style="display: inline-block; padding:0; height: 36px;">
                <input id="input_busca_rapida_aluno" class="pesquisar-in-card-title" type="text" placeholder="Busca rápida" list="alunos_list" style="width: 300px;" />
                <datalist id="alunos_list">
                    @foreach($alunos['list'] as $aluno)
                        @if(!$ownauthcontroller->PermissaoCheck(1))
                            @if($aluno->omcts_id == session()->get('login.omctID'))
                                <option value="{{$aluno->id.' - '.$aluno->nome_completo}}" style="background-color: #ccc;">
                                    <a href"#">Al {{$aluno->numero}} {{$aluno->nome_guerra}} {{$aluno->omct->sigla_omct or ''}}</a>
                                </option>
                            @endif
                        @else
                            <option value="{{$aluno->id.' - '.$aluno->nome_completo}}" style="background-color: #ccc;">
                                <a href"#">Al {{$aluno->numero}} {{$aluno->nome_completo}} {{$aluno->omct->sigla_omct or ''}}</a>
                            </option>
                        @endif
                    @endforeach
                </datalist>                
                <a class="no-style" href="javascript: void(0);">
                    <i class="ion-android-search" style="color: #696969;"></i>
                </a>
            </div>
            @if($ownauthcontroller->PermissaoCheck(1))                
                <a href="javascript: void(0);" data-toggle="popover" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
                    <i class="ion-android-more-vertical" style="color: #696969;"></i>
                </a>
                <div id="popover-content" style="display: none;">
                    <div class="menu_inside_popover">
                            <!--<i class="ion-person-add"></i><a href="javascript: void(0);" onclick="dialogImplantarAluno();">Implantar aluno no sistema</a><br />-->
                            <i class="ion-person-add"></i><a href="javascript: void(0);" onclick="loadAdminAjaxContent('admin/aluno');">Implantar aluno no sistema</a><br />
                    </div>
                </div>
            @endif    
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-alunos" role="alert"></div>
        <!--h5 class="card-title" style="text-align: center; margin: 18px 0 44px 0;"></h5-->           
        <p class="card-text">
            <div style="margin-top: 24px;">
                <nav class="nav-justified">
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" data-toggle="tab" href="#nav-alunos-visao-geral" role="tab" aria-controls="nav-home" aria-selected="true" onclick="$('a#alunos').trigger('click');">Visão geral do cadastro</a>
                        <a class="nav-item nav-link" data-toggle="tab" href="#nav-alunos-cadastro" role="tab" aria-controls="nav-home" aria-selected="true">Opções de listagem</a>
                        <a class="nav-item nav-link" data-toggle="tab" href="#nav-alunos-situacoes-diversas" role="tab" aria-controls="nav-home" aria-selected="true" onclick="loadAlunosSitDiv();">Alunos em situações diversas</a>
                        <!--a class="nav-item nav-link" data-toggle="tab" href="#nav-alunos-configs" role="tab" aria-controls="nav-profile" aria-selected="false"><i class="ion-gear-b"></i> Configurações</a-->
                    </div>
                </nav>
            
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-alunos-visao-geral" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 56px;">
                        <div style="margin: 24px 0 56px 0; text-align: center;">
                            <h4 style="color: #696969;">VISÃO GERAL DO CADASTRO DE ALUNOS NO ANO DE FORMAÇÃO {{$ano_corrente_formacao}}</h4>
                        </div>
                        @foreach($omcts as $omct)
                            @if($omct_data[$omct->id]['total']>0)
                                <div class="card text-center" style="margin-bottom: 36px;">
                                    <div class="card-header" style="background-color: #0B3861; color: #fff; padding: 16px; font-size: 16px;">
                                        <b>{{$omct->omct}}</b><br/>
                                        {{$omct->gu}}
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">
                                                <div style="text-align:left">
                                                    <b>QUANTITATIVO ALUNOS PRONTOS</b><br />
                                                    <div style="margin: 12px 20px;">                                                        
                                                        @if($omct_data[$omct->id]['total_masculino']==1)
                                                            <span style="color: #088A29;">{{$omct_data[$omct->id]['total_masculino']}} aluno do segmento masculino</span><br />    
                                                        @elseif($omct_data[$omct->id]['total_masculino']>1)
                                                            <span style="color: #088A29;">{{$omct_data[$omct->id]['total_masculino']}} alunos do segmento masculino</span><br />
                                                        @else
                                                            <span style="color: #088A29;">OMCT sem alunos do segmento masculino</span><br />
                                                        @endif

                                                        @if($omct_data[$omct->id]['total_feminino']==1)
                                                            <span style="color: #FA58AC;">{{$omct_data[$omct->id]['total_feminino']}} aluno do segmento feminino</span><br />    
                                                        @elseif($omct_data[$omct->id]['total_feminino']>1)
                                                            <span style="color: #FA58AC;">{{$omct_data[$omct->id]['total_feminino']}} alunos do segmento feminino</span><br />    
                                                        @else
                                                            <span style="color: #FA58AC;">OMCT sem alunas do segmento feminino</span><br />
                                                        @endif
                                                        <b>TOTAL:</b> <span style="font-size: 18px;">{{$omct_data[$omct->id]['total']}}</span>
                                                    </div>                                                        
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div style="text-align:left">
                                                    <b>QUANTITATIVO ALUNOS EM SITUAÇÕES DIVERSAS</b><br />
                                                    <div style="margin: 12px 20px;">                                                        
                                                        @if($omct_data[$omct->id]['total_sit_div']>0)
                                                            <b>TOTAL:</b> <span style="font-size: 18px;">{{$omct_data[$omct->id]['total_sit_div']}}</span>                                                        
                                                        @else
                                                        <span style="color: #696969;">OMCT sem alunos em situações diversas</span><br /> 
                                                        @endif
                                                    </div>                                                        
                                                </div>                                                
                                            </li>
                                            <li class="list-group-item">
                                                <div style="text-align:left">
                                                    <b>PENDÊNCIAS CADASTRAIS</b><br />
                                                    <div style="margin: 12px 20px;">
                                                        @if($omct_data[$omct->id]['total_cadastro_al_pendente']>0)
                                                            <div class="alert alert-danger" role="alert">
                                                                <i class="ion-minus-circled" style="font-size: 18px; margin-right: 4px; vertical-align: -1px;"></i>
                                                                Está OMCT possui {{$omct_data[$omct->id]['total_cadastro_al_pendente']}} pendência(s) de cadastro. Favor verificar junto ao Sgte da OMCT se os dados: <b>email, turma e área</b> estão preenchidos para todos os alunos.
                                                            </div>
                                                        @else
                                                            <div class="alert alert-success" role="alert">
                                                                <i class="ion-checkmark-circled"  style="font-size: 18px; margin-right: 4px; vertical-align: -1px;"></i>
                                                                Não há pendências de cadastro
                                                            </div>
                                                        @endif
                                                    </div>                                                        
                                                </div>                                                
                                            </li>
                                        </ul>                                       
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="tab-pane fade" id="nav-alunos-cadastro" role="tabpanel" aria-labelledby="nav-profile-tab" style="padding: 56px;">
                        <form id="opcoes-listagem-selecao-alunos">
                            @if($ownauthcontroller->PermissaoCheck([1,10]))
                                <div style="width: 90%; margin: 0 auto; text-align: center; border-bottom: 1px solid #ccc;">
                                    <h4 style="text-align: center;">ANO DE FORMAÇÃO</h4>
                                    <div style="text-align: center; margin: 22px auto; width: 70%;">
                                        <select class="custom-select" name="anos_de_formacao">
                                        <option>Selecione um ano de formação</option>
                                        @foreach($anos_formacao as $ano_formacao)
                                            @if($ano_formacao->formacao==$ano_corrente_formacao)
                                                <option value="{{$ano_formacao->id}}" selected>{{$ano_formacao->formacao}}</option>                                            
                                            @else 
                                                <option value="{{$ano_formacao->id}}">{{$ano_formacao->formacao}}</option>
                                            @endif
                                        @endforeach
                                        </select>
                                    </div>
                                </div>                            
                                <div style="width: 90%; margin: 16px auto; text-align: center; border-bottom: 1px solid #ccc;">
                                    <h4 style="text-align: center;">OMCT</h4>
                                    <div style="text-align: center; margin: 22px;">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="$('input.omcts').prop('checked', true);">Marcar todas</button>
                                        <button type="button" class="btn btn-warning btn-sm" style="margin-left: 6px;" onclick="$('input.omcts').prop('checked', false);">Desmarcar todas</button>
                                    </div>
                                    @foreach($omcts as $omct)
                                        <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                                            <input type="checkbox" class="custom-control-input omcts" id="omct_{{$omct->id}}" name="omcts[]" value="{{$omct->id}}" />
                                            <label class="custom-control-label" for="omct_{{$omct->id}}">{{$omct->sigla_omct}}</label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <div style="width: 90%; margin: 16px auto; text-align: center; border-bottom: 1px solid #ccc;">
                                <h4 style="text-align: center;">SEGMENTO</h4>
                                <div style="text-align: center; margin: 22px;">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="$('input.segmento').prop('checked', true);">Marcar todas</button>
                                    <button type="button" class="btn btn-warning btn-sm" style="margin-left: 6px;" onclick="$('input.segmento').prop('checked', false);">Desmarcar todas</button>
                                </div>
                                <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                                    <input type="checkbox" class="custom-control-input segmento" id="segmento_M" name="segmento[]" value="M" />
                                    <label class="custom-control-label" for="segmento_M">Masculino</label>
                                </div>
                                <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                                    <input type="checkbox" class="custom-control-input segmento" id="segmento_F" name="segmento[]" value="F" />
                                    <label class="custom-control-label" for="segmento_F">Feminino</label>
                                </div>
                            </div>
                            <div style="width: 90%; margin: 16px auto; text-align: center; border-bottom: 1px solid #ccc;">
                                    <h4 style="text-align: center;">ÁREA</h4>
                                    <div style="text-align: center; margin: 22px;">
                                        <button type="button" class="btn btn-primary btn-sm"  onclick="$('input.area').prop('checked', true);">Marcar todas</button>
                                        <button type="button" class="btn btn-warning btn-sm" style="margin-left: 6px;"  onclick="$('input.area').prop('checked', false);">Desmarcar todas</button>
                                    </div>
                                    @foreach($areas as $area)
                                        <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                                            <input type="checkbox" class="custom-control-input area" id="area_{{$area->id}}" name="areas[]" value="{{$area->id}}" />
                                            <label class="custom-control-label" for="area_{{$area->id}}">{{$area->area}}</label>
                                        </div>
                                    @endforeach
                            </div>
                            <div style="text-align: center; margin-top: 36px">
                                    <button id="submitFiltroListagem" type="button" class="btn btn-success btn-lg" onclick="OpcoesdeListagemSelecaoAlunos(this);">Listar seleção</button>
                            </div>
                            <div style="margin: 24px auto;">
                                <div class="alert alert-danger opcoes-de-listagem" role="alert"></div>                                
                            </div>
                        </form>                                                
                        <div id="response" style="margin-top: 84px;"></div>
                    </div>
                    <div class="tab-pane fade" id="nav-alunos-situacoes-diversas" role="tabpanel" aria-labelledby="nav-contact-tab">
                        <div style="width: 90%; margin: 94px auto; text-align: center;">
                            <h4 style="text-align: center; color: #696969;">RELAÇÃO DE ALUNOS EM SITUAÇÕES DIVERSAS</h4>     
                        </div>
                        <div id="response2"></div>                        
                    </div>
                </div>
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
        
</script>
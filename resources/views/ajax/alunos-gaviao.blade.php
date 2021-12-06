<?php
    $backgroundVisaoGeral = session()->get('backgroundVisaoGeral');
?>
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
                    @foreach($alunos as $aluno)
                        <option value="{{$aluno->id.' - '.$aluno->nome_completo}}" style="background-color: #ccc;">
                            <a href"#">Al {{$aluno->numero}} {{$aluno->nome_completo}} {{$aluno->omct->sigla_omct or ''}}</a>
                        </option>
                    @endforeach
                </datalist>                
                <a class="no-style" href="javascript: void(0);">
                    <i class="ion-android-search" style="color: #696969;"></i>
                </a>
            </div>
            @if($ownauthcontroller->PermissaoCheck(8))
                <a href="javascript: void(0);" data-toggle="popover" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
                    <i class="ion-android-more-vertical" style="color: #696969;"></i>
                </a>
                <div id="popover-content" style="display: none;">
                    <div class="menu_inside_popover">
                            <i class="ion-person-add"></i><a href="javascript: void(0);" onclick="loadAdminAjaxContent('admin/aluno');">Implantar aluno no sistema</a><br />
                    </div>
                </div>
            @endif    
        </div>
    </div>

    <div class="card-body">
        <div class="alert alert-alunos" role="alert"></div>
        <p class="card-text">
            <div style="margin-top: 24px;">
                <nav class="nav-justified">
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" data-toggle="tab" href="#nav-alunos-visao-geral" role="tab" aria-controls="nav-home" aria-selected="true" onclick="$('a#alunos').trigger('click');">Visão geral do cadastro</a>
                        <a class="nav-item nav-link" data-toggle="tab" href="#nav-alunos-cadastro" role="tab" aria-controls="nav-home" aria-selected="true">Opções de listagem</a>
                        @if($ownauthcontroller->PermissaoCheck([1,10]))
                            <a class="nav-item nav-link" data-toggle="tab" href="#nav-alunos-turma" role="tab" aria-controls="nav-home" aria-selected="true">Seleção de Turmas</a>
                        @endif
                        <!--a class="nav-item nav-link" data-toggle="tab" href="#nav-alunos-situacoes-diversas" role="tab" aria-controls="nav-home" aria-selected="true" onclick="loadAlunosSitDiv();">Alunos em situações diversas</a-->
                    </div>
                </nav>
            
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-alunos-visao-geral" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 56px;">
                        <div style="margin: 24px 0 56px 0; text-align: center;">
                            <h4 style="color: #696969;">VISÃO GERAL DO CADASTRO DE ALUNOS NO ANO DE FORMAÇÃO {{$ano_corrente_data->formacao}}</h4>
                        </div>
                        @foreach($qmss as $qms)
                            @if($qmss_data[$qms->id]['total']>0)
                                <div class="card text-center" style="margin-bottom: 36px;">
                                    <div class="card-header" style="background-color: {{$backgroundVisaoGeral}} color: #fff; padding: 16px; font-size: 16px;">
                                        <b>{{$qms->qms}}</b><br/>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">
                                                <div style="text-align:left">
                                                    <b>QUANTITATIVO ALUNOS PRONTOS</b><br />
                                                    <div style="margin: 12px 20px;">                                                        
                                                        @if($qmss_data[$qms->id]['total_masculino']==1)
                                                            <span style="color: #088A29;">{{$qmss_data[$qms->id]['total_masculino']}} aluno do segmento masculino</span><br />    
                                                        @elseif($qmss_data[$qms->id]['total_masculino']>1)
                                                            <span style="color: #088A29;">{{$qmss_data[$qms->id]['total_masculino']}} alunos do segmento masculino</span><br />
                                                        @else
                                                            <span style="color: #088A29;">QMS sem alunos do segmento masculino</span><br />
                                                        @endif
                                                        <b>TOTAL:</b> <span style="font-size: 18px;">{{$qmss_data[$qms->id]['total']}}</span>
                                                    </div>                                                        
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div style="text-align:left">
                                                    <b>QUANTITATIVO ALUNOS EM SITUAÇÕES DIVERSAS</b><br />
                                                    <div style="margin: 12px 20px;">                                                        
                                                        @if($qmss_data[$qms->id]['total_sit_div']>0)
                                                            <b>TOTAL:</b> <span style="font-size: 18px;">{{$qmss_data[$qms->id]['total_sit_div']}}</span>                                                        
                                                        @else
                                                        <span style="color: #696969;">QMS sem alunos em situações diversas</span><br /> 
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
                            @if($ownauthcontroller->PermissaoCheck([1,10,31]))
                                <div style="width: 90%; margin: 0 auto; text-align: center; border-bottom: 1px solid #ccc;">
                                    <h4 style="text-align: center;">ANO DE FORMAÇÃO</h4>
                                    <div style="text-align: center; margin: 22px auto; width: 70%;">
                                        <select class="custom-select" name="anos_de_formacao">
                                        <option>Selecione um ano de formação</option>
                                        @foreach($anos_formacao as $ano_formacao)
                                            @if($ano_formacao->formacao==$ano_corrente_data->formacao)
                                                <option value="{{$ano_formacao->id}}" selected>{{$ano_formacao->formacao}}</option>                                            
                                            @else 
                                                <option value="{{$ano_formacao->id}}">{{$ano_formacao->formacao}}</option>
                                            @endif
                                        @endforeach
                                        </select>
                                        
                                    </div>
                                </div>                            
                                <div style="width: 90%; margin: 16px auto; text-align: center; border-bottom: 1px solid #ccc;">
                                    <h4 style="text-align: center;">QMS</h4>
                                    <div style="text-align: center; margin: 22px;">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="$('input.omcts').prop('checked', true);">Marcar todas</button>
                                        <button type="button" class="btn btn-warning btn-sm" style="margin-left: 6px;" onclick="$('input.omcts').prop('checked', false);">Desmarcar todas</button>
                                    </div>
                                    <div class="chk_listagem_qms">
                                        
                                    </div>
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
                                <!--<div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                                    <input type="checkbox" class="custom-control-input segmento" id="segmento_F" name="segmento[]" value="F" />
                                    <label class="custom-control-label" for="segmento_F">Feminino</label>
                                </div>-->
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
                    <div class="tab-pane fade" id="nav-alunos-turma" role="tabpanel" aria-labelledby="nav-profile-tab" style="padding: 56px;">
                        <form id="opcoes-selecao-turma">
                            @if($ownauthcontroller->PermissaoCheck([1,10]))
                                <h4 style="text-align: center;">ANO DE FORMAÇÃO</h4>
                                {!! App\Http\Controllers\Utilitarios\FuncoesController::retornaBotaoAnoFormacao(null) !!}

                                <div style="width: 90%; margin: 16px auto; text-align: center; margin-top:50px;">
                                    <h4 style="text-align: center;">CURSO</h4>
                                    
                                    <div style="text-align: center; margin: 22px auto; width: 70%;">
                                        <select class="custom-select" name="curso">
                                            <option>Selecione um Curso</option>
                                            @foreach($cursos as $curso)
                                                <option value="{{$curso->qms_matriz_id}}">{{$curso->qms}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div style="text-align: center; margin-top: 36px; ">
                                <button id="submitTurmas" type="button" class="btn btn-success btn-lg" onclick="ListagemTurmas(this);">Listar seleção</button>
                            </div>
                            <div style="margin: 24px auto; border-bottom: 1px solid #ccc;">
                                
                                <div class="alert alert-danger errors-seleciona-turma" role="alert"></div>
                                <div class="alert alert-success success-seleciona-turma" role="alert"></div>                      
                            </div>
                        </form>                                                
                        <div id="response-selecao-turma" style="margin-top: 84px;"></div>
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

$(document).ready(function() {
   
    $('div.errors-seleciona-turma').empty().hide();
    $('div.success-seleciona-turma').empty().hide();

    $("[data-toggle=popover]").popover({
        trigger: 'focus',
        html: true, 
        delay: { "show": 100, "hide": 400 },
        content: function() {
            return $('#popover-content').html();
        }
    });

    // CHAMA EDIÇÃO DO ALUNO APOS PRESSIONAR ENTER NO INPUT 
    $(document).on('keypress', 'input#input_busca_rapida_aluno', function(e) {
        e.stopImmediatePropagation(); //Não deixa duplicar os eventos

        if (e.which==13) {
            var alunoId = $(this).val();
            loadAdminAjaxContent('admin/aluno/' + alunoId);          
        }
    });

    //Carrega os CheckBox dos Cursos
    $('select[name="anos_de_formacao"].custom-select').on('change', function() {
        CarregaCheckBoxAlunosGaviaoPeriodo(this.value);
    });

    CarregaCheckBoxAlunosGaviaoPeriodo({{$ano_corrente_data->id}});
});

function CarregaCheckBoxAlunosGaviaoPeriodo(idAnoFormacao){
    $.ajax({
        type:'GET',
        url: '/gaviao/ajax/show-checkbox-anoformacao-qms/' + idAnoFormacao,
        beforeSend: function(){
            $('div.chk_listagem_qms').empty();
        },
        success: function(data){
            $('div.chk_listagem_qms').html(data).slideDown();
        }
    }); 
}

function OpcoesdeListagemSelecaoAlunos(dataButton){
    var dataButtonContent = $(dataButton).html();
    var dataForm = $('form#opcoes-listagem-selecao-alunos').serialize();
    $('div.opcoes-de-listagem').empty().hide();
    $.ajax({
        type:'GET',
        url: '/gaviao/ajax/listagem-selecao-alunos-gaviao?' + dataForm,
        beforeSend: function(){
            $(dataButton).html('<img src="/images/loadings/loading_03.svg" style="margin-right: 3px; width: 32px;" />Buscando registros...').addClass('disabled');
            $('div#response').empty();
        },
        success: function(data){
            $(dataButton).html(dataButtonContent).removeClass('disabled');
            if(data.status=='err'){
                $('div.opcoes-de-listagem').html(data.response).slideDown();
            } else {                        
                $('html, body').animate({
                    scrollTop: ($('button#submitFiltroListagem').offset().top - 80)
                }, 1000);
                $('div#response').html(data);
            }
        }
    });                
}

function ListagemTurmas(dataButton){
    var dataButtonContent = $(dataButton).html();
    var dataForm = $('form#opcoes-selecao-turma').serialize();
    $('div.errors-seleciona-turma').empty().hide();
    $('div.success-seleciona-turma').empty().hide();
    $.ajax({
        type:'GET',
        url: '/gaviao/ajax/listagem-selecao-alunos-turma?' + dataForm,
        beforeSend: function(){
            $(dataButton).html('<img src="/images/loadings/loading_03.svg" style="margin-right: 3px; width: 32px;" />Buscando registros...').addClass('disabled');
            $('div#response-selecao-turma').empty();
        },
        success: function(data){
            $(dataButton).html(dataButtonContent).removeClass('disabled');
            if(data.status=='err'){
                $('div.errors-seleciona-turma').html(data.response).slideDown();
            } else {                        
                $('html, body').animate({
                    scrollTop: ($('button#submitTurmas').offset().top - 80)
                }, 1000);
                $('div#response-selecao-turma').html(data);
            }
        }
    });
}
        
</script>
<div class="card bg-light mb-3">
    <div class="card-header">
        <i class="ion-ios-keypad"></i><strong>Escolha de QMS</strong>
        <div style="float: right">
            <!--div class="box-pesquisar-in-card-title" style="display: inline-block; padding:0; height: 36px;">
                <input class="pesquisar-in-card-title busca-operador" type="text" placeholder="Busca" />
                <a class="no-style" href="javascript: void(0);">
                        <i class="ion-android-search" style="color: #696969;"></i>
                </a>
            </div-->
            @if($ano_corrente)
                <a href="javascript: void(0);" data-toggle="popover" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
                    <i class="ion-android-more-vertical" style="color: #696969;"></i>
                </a>
                <div id="popover-content" style="display: none;">
                    <div class="menu_inside_popover">
                        <i class="ion-plus"></i><a href="javascript: void(0);" onclick="dialogAdicionarPeriodoEscolhaQMS();">Criar novo período</a><br />                 
                    </div>
                    <!--div class="menu_inside_popover">
                        <i class="ion-android-arrow-down"></i><a href="javascript: void(0);" onclick="dialogImportarDisciplinas();">Importar disciplinas</a><br />                 
                    </div-->                                            
                </div>
            @endif  
        </div>
    </div>

    <div class="card-body">
        <div class="alert errors-adicionar-escolha-qms2" role="alert"></div>
        <h5 class="card-title" style="text-align: center; margin: 18px 0 44px 0;">
            @if($ano_corrente)
                ESCOLHAS DE QMS CADASTRADAS PARA O ANO DE FORMAÇÃO <span style="font-size: 24px;"><b>{{$ano_corrente->formacao}}</b></span>
            @endif        
        </h5>           
        <p class="card-text">
            @if(count($escolhas_qms)>0)
                <div id="accordion">
                    @foreach($escolhas_qms as $periodo)
                        <div id="escolhaQMSID_{{$periodo->id}}" class="card">
                            <div class="card-header">
                                    <div style="float: left;">                                
                                        <button class="btn btn-link accordion-style-button" data-toggle="collapse" data-target="#collapse{{$periodo->id}}" aria-expanded="true" aria-controls="collapseOne">
                                            {{$periodo->identificador}}
                                            @if($periodo->data_hora_inicio<date('Y-m-d H:i:s') && $periodo->data_hora_fim>date('Y-m-d H:i:s'))
                                                <span style="color: #0B3B0B;"> <b>(Ativo)</b></span>{{--strftime('%d de %b de %Y', strtotime($periodo->data_hora_inicio))}} até {{strftime('%d de %b de %Y', strtotime($periodo->data_hora_fim))--}}
                                            @endif
                                        </button>
                                    </div>
                                    <div style="float: right; margin-top: 2px;">
                                        <a href="javascript: void(0);" class="no-style" onclick="dialogEditarEscolhaQMS({{$periodo->id}});" title="Editar configurações da escolha de QMS">
                                            <i class="ion-android-create" style="color: #696969; font-size: 20px;"></i>
                                        </a>
                                        <a href="#" onclick="dialogRemoverEscolhaQMS({{$periodo->id}});" class="no-style" style="margin-left: 6px;" title="Excluir este período de escolha de QMS">
                                            <i class="ion-android-delete" style="color: #696969; font-size: 20px;"></i>
                                        </a>                                                
                                    </div>
                                    <div class="clear;"></div>                                    
                            </div>
                    
                            <div id="collapse{{$periodo->id}}" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                                <div class="card-body">
                                    <p>
                                        <b>Início:</b> {{strftime('%A, %d de %B de %Y às %H:%M', strtotime($periodo->data_hora_inicio))}}<br />
                                        <b>Término:</b> {{strftime('%A, %d de %B de %Y às %H:%M', strtotime($periodo->data_hora_fim))}}
                                    </p>
                                    @if($periodo->observacao!='')
                                        <p>{{$periodo->observacao}}</p>
                                    @endif
                                    <h4 style="text-align: center; margin-top: 44px; color: #696969;">DISTRIBUIÇÃO DE VAGAS</h4>
                                    <div style="margin-top: 44px; margin: 32px auto;">
                                        <h5 style="color: #696969; text-align: center;"><b>SEGMENTO MASCULINO</b></h5>
                                        <div style="background-color: #F2F2F2; width: 40%; margin: 32px auto;">
                                            @foreach($vagas as $vaga)
                                                @if($vaga->escolha_qms_id==$periodo->id && $vaga->segmento=='M')
                                                    <div style="padding: 12px; border-bottom: 1px solid #ccc;">
                                                        <div style="float: left;"><b>{{$vaga->qms}}</b></div><div style="float: right; margin-top: -4px;">{{$vaga->vagas}}</div><div class="clear"></div>
                                                    </div>
                                                    @php
                                                        $sum_vagas_masc[] = $vaga->vagas;
                                                    @endphp
                                                @endif
                                            @endforeach
                                            @php
                                                if(!isset($sum_vagas_masc)){
                                                    $sum_vagas_masc[] = 0;        
                                                }
                                            @endphp
                                            <div style="padding: 12px; border-bottom: 1px solid #ccc; background-color: #BCF5A9;">
                                                <div style="float: left;"><b>TOTAL DE ALUNOS COMB/LOG/AV:</b></div><div style="float: right; margin-top: -4px;">{{$total_geral_alunos}}</div><div class="clear"></div>
                                                <div style="float: left;"><b>TOTAL DE ALUNOS APTOS:</b></div><div style="float: right; margin-top: -4px;">{{$total_alunos_aptos}}</div><div class="clear"></div>
                                                <div style="float: left;"><b>TOTAL DE VAGAS:</b></div><div style="float: right; margin-top: -4px;">{{array_sum($sum_vagas_masc)}}</div><div class="clear"></div>
                                            </div>                                            
                                        </div>
                                        <h5 style="color: #696969; margin-top: 44px; text-align: center;"><b>SEGMENTO FEMININO</b></h5>
                                        <div style="background-color: #F2F2F2; width: 40%; margin: 32px auto;">
                                            @foreach($vagas as $vaga)
                                                @if($vaga->escolha_qms_id==$periodo->id && $vaga->segmento=='F')
                                                    <div style="padding: 12px; border-bottom: 1px solid #ccc;">
                                                        <div style="float: left;"><b>{{$vaga->qms}}</b></div><div style="float: right; margin-top: -4px;">{{$vaga->vagas}}</div><div class="clear"></div>
                                                    </div>
                                                    @php
                                                        $sum_vagas_fem[] = $vaga->vagas;
                                                    @endphp
                                                @endif
                                            @endforeach
                                            @php
                                                if(!isset($sum_vagas_fem)){
                                                    $sum_vagas_fem[] = 0;        
                                                }
                                            @endphp
                                            <div style="padding: 12px; border-bottom: 1px solid #ccc; background-color: #F6CEEC;">
                                                <div style="float: left;"><b>TOTAL DE ALUNAS COMB/LOG/AV:</b></div><div style="float: right; margin-top: -4px;">{{$total_geral_alunas}}</div><div class="clear"></div>
                                                <div style="float: left;"><b>TOTAL DE ALUNAS APTAS:</b></div><div style="float: right; margin-top: -4px;">{{$total_alunas_aptas}}</div><div class="clear"></div>
                                                <div style="float: left;"><b>TOTAL DE VAGAS:</b></div><div style="float: right; margin-top: -4px;">{{array_sum($sum_vagas_fem)}}</div><div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>            
            @else
                <div class="box-registro-not-found">
                    <i class="ion-social-snapchat-outline" style="font-size: 32px"></i><br />
                    <span style="color: brown">Não há períodos de escolha de QMS configurados.</span>
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
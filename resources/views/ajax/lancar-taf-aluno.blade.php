<div class="card bg-light mb-3">
        <div class="card-header">
            <i class="ion-android-walk"></i><strong>Lançar TFM Aluno {{ ((isset($recuperacao) && $recuperacao)  ? '(Recuperação)' : null) }}</strong>
            <div style="float: right">
                <!--div class="box-pesquisar-in-card-title" style="display: inline-block; padding:0; height: 36px;">
                    <input class="pesquisar-in-card-title busca-operador" type="text" placeholder="Busca" />
                    <a class="no-style" href="javascript: void(0);">
                            <i class="ion-android-search" style="color: #696969;"></i>
                    </a>
                </div>                
                <a href="javascript: void(0);" data-toggle="popover" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
                    <i class="ion-android-more-vertical" style="color: #696969;"></i>
                </a>
                <div id="popover-content" style="display: none;">
                    <div class="menu_inside_popover">
                        <i class="ion-compose"></i><a href="javascript: void(0);" onclick="dialogAdicionarAnoFormacao();">Novo ano de formação</a><br />                 
                    </div>
                </div-->    
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-danger errors-cadastro-ano-formacao" role="alert"></div>
            <h5 class="card-title" style="text-align: center; margin: 18px 0 44px 0;">
                INFORME A NOTA DO ALUNO
            </h5>
            <div class="content-panel">
                <table class="table">
                    <thead>
                        <tr>
                            <td style="width: 200px;"></td>
                            <td td colspan="6">
                                <div class="text-center" style="display: inline-block; width: 180px;">Corrida</div>
                                <!--@if(((isset($recuperacao) && $recuperacao)))
                                    <div style="display: inline-block; width: 180px;"></div>
                                @endif-->
                                <div class="text-center" style="display: inline-block; width: 180px;">Flexão braço</div>
                                <!--@if(((isset($recuperacao) && $recuperacao)))
                                    <div style="display: inline-block; width: 180px;"></div>
                                @endif-->
                                <div class="text-center" style="display: inline-block; width: 180px;">Flexão barra</div>
                                <!--@if(((isset($recuperacao) && $recuperacao)))
                                    <div style="display: inline-block; width: 180px;"></div>
                                @endif-->
                                <div class="text-center" style="display: inline-block; width: 120px;">Abdominal</div>
                                <!--@if(((isset($recuperacao) && $recuperacao)))
                                    <div style="display: inline-block; width: 180px;"></div>
                                @endif-->
                                <div class="text-center" style="display: inline-block; width: 120px;">Média</div>
                                <!--@if(((isset($recuperacao) && $recuperacao)))
                                 <div style="display: inline-block; width: 180px;"></div>
                                @endif-->
                                <div class="text-center" style="display: inline-block; width: 100px;"></div>

                            </td>
                        </tr>
                    </thead>
                    
                @foreach ($alunos as $aluno) 
                    <tr>
                        <td style="width: 200px;" class="align-middle">
                                {{$aluno->numero}} {{$aluno->nome_guerra}}
                                @if($aluno->atleta_marexaer=='S')
                                <br />
                                <span style="color: #DF7401;"><i>(Atleta Marexaer)</i>
                                @endif
                            </td>
                            @if(isset($dataAvalicaoTaf[$aluno->id]))
                                <td colspan="6" >
                                    <form id="Form_lancarTaf_{{$aluno->id}}">
                                    <div style="display: inline-block; width: 180px;" class="align-middle text-center">
                                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                                        <label style="text-align:center;">AC
                                             <input {{ ((isset($recuperacao) && $recuperacao) ? 'readonly' : null) }} placeholder="Corrida" name="corrida_nota" type="text" style="display: block;margin-top: 8px; width: 70px;text-align:center;" value="{{$dataAvalicaoTaf[$aluno->id]['corrida_nota']}}" autocomplete="off">
                                        </label>
                                        @if((($recuperacao) && ($dataAvalicaoTaf[$aluno->id]['media'] < 5) ))     
                                        <label style="text-align:center;">AR    
                                            <input placeholder="Corrida R." name="corrida_nota_recuperacao" type="text" style="display: block;margin-top: 8px; width: 70px;text-align:center;" value="{{$dataAvalicaoTaf[$aluno->id]['corrida_nota_recuperacao']}}" autocomplete="off">
                                        </label>
                                        @endif
                                    </div>
                                
                                    <div style="display: inline-block; width: 180px;" class="align-middle text-center">
                                        <label style="text-align:center;">AC
                                            <input {{ ((isset($recuperacao) && $recuperacao) ? 'readonly' : null) }} placeholder="Flex bra" name="flex_bra_nota" type="text" style="display: block;margin-top: 8px; width: 70px;text-align:center;" value="{{$dataAvalicaoTaf[$aluno->id]['flexao_braco_nota']}}" autocomplete="off">
                                        </label>    
                                        @if((($recuperacao) && ($dataAvalicaoTaf[$aluno->id]['media'] < 5) ))                                             
                                        <label style="text-align:center;">AR
                                            <input placeholder="Flex bra R." name="flexao_braco_nota_recuperacao" type="text" style="display: block;margin-top: 8px; width: 70px;text-align:center;" value="{{$dataAvalicaoTaf[$aluno->id]['flexao_braco_nota_recuperacao']}}" autocomplete="off">
                                        </label>
                                        @endif
                                    </div>
                            
                                    <!-- SE O CAMARADA FOR DA ÁRE MÚSICA NÃO DESABILITA O CAMPO BARRA -->
                                    <div style="display: inline-block; width: 180px;" class="align-middle text-center">
                                        <label style="text-align:center;">AC
                                        @if($aluno->area_id==3)
                                            <input {{ ((isset($recuperacao) && $recuperacao) ? 'readonly' : null) }} placeholder="Flex bar" name="flex_barr_nota" type="text" style="display: block;margin-top: 8px; width: 70px;text-align:center;" value="" autocomplete="off" readonly>
                                        @else
                                            <input {{ ((isset($recuperacao) && $recuperacao) ? 'readonly' : null) }} placeholder="Flex bar" name="flex_barr_nota" type="text" style="display: block;margin-top: 8px; width: 70px;text-align:center;" value="{{$dataAvalicaoTaf[$aluno->id]['flexao_barra_nota']}}" autocomplete="off" >
                                        @endif
                                        </label>

                                        @if((($recuperacao) && ($dataAvalicaoTaf[$aluno->id]['media'] < 5) ))     
                                        <label style="text-align:center;">AR                                       
                                            <input placeholder="Flex bar R." name="flexao_barra_nota_recuperacao" type="text" style="display: block;margin-top: 8px; width: 70px;text-align:center;" value="{{$dataAvalicaoTaf[$aluno->id]['flexao_barra_nota_recuperacao']}}" autocomplete="off">
                                        </label>    
                                        @endif
                                    </div>
                            
                                    <div style="display: inline-block; width: 120px;" class="align-middle text-center">
                                        <label style="text-align:center;">AC
                                            <select {{ ((isset($recuperacao) && $recuperacao) ? 'readonly' : null) }} class="custom-select" name="suficiencia_abdominal">
                                                <option value="S" @if($dataAvalicaoTaf[$aluno->id]['abdominal_suficiencia']=='S') selected @endif>Suficiente</option>
                                                <option value="NS" @if($dataAvalicaoTaf[$aluno->id]['abdominal_suficiencia']=='NS') selected @endif> Não suficiente</option>
                                            </select>
                                        </label>
                                            @if((($recuperacao) && ($dataAvalicaoTaf[$aluno->id]['abdominal_suficiencia'] != 'S') )) 
                                            <label style="text-align:center;">AR
                                                <select class="custom-select" name="suficiencia_abdominal_recuperacao">
                                                    <option value="S" @if($dataAvalicaoTaf[$aluno->id]['abdominal_suficiencia_recuperacao']=='S') selected @endif>Suficiente</option>
                                                    <option value="NS" @if($dataAvalicaoTaf[$aluno->id]['abdominal_suficiencia_recuperacao']=='NS') selected @endif> Não suficiente</option>
                                                </select>
                                            </label>
                                            @endif
                                    </div>   
                        
                                    <div style="display: inline-block; width: 120px;" class="align-middle text-center">
                                        <label style="text-align:center;display: block;">AC
                                            <div id="media_nota_taf_{{$aluno->id}}" style="height:33px;">{{$dataAvalicaoTaf[$aluno->id]['media']}}</div>
                                        </label>
                                        @if(((isset($recuperacao) && $recuperacao)))     
                                        <label style="text-align:center;">AR
                                            <div id="media_nota_taf_recuperacao_{{$aluno->id}}">{{$dataAvalicaoTaf[$aluno->id]['media_recuperacao']}}</div>
                                        </label>
                                        @endif
                                    </div>
                            
                                    <div style="display: inline-block; width: 100x;" class="align-middle text-center">
                                        @if(((isset($recuperacao) && $recuperacao))) 
                                            <a href="javascript: void(0);" class="btn btn-warning btn-sm" role="button" onclick="lancarRecuperacaoTafAluno({{$aluno->id}}, this);" aria-pressed="true">Atualizar(Recuperação)</a>
                                        @else
                                            <a href="javascript: void(0);" class="btn btn-warning btn-sm" role="button" onclick="lancarTafAluno({{$aluno->id}}, this);" aria-pressed="true">Atualizar</a>
                                        @endif
                                    </div>
                                    </form>
                                </td>
                            @else
                            <td colspan="6">
                                <form id="Form_lancarTaf_{{$aluno->id}}">
                                    <div style="display: inline-block; width: 180px;">
                                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                                        <input placeholder="Corrida" name="corrida_nota" type="text" style="margin-top: 8px; width: 70px;" autocomplete="off">
                                    </div>
                                    <div style="display: inline-block; width: 180px;">
                                        <input placeholder="Flex bra" name="flex_bra_nota" type="text" style="margin-top: 8px; width: 70px;" autocomplete="off">                                    
                                    </div>
                        
                                    <!-- SE O CAMARADA FOR DA ÁRE MÚSICA NÃO DESABILITA O CAMPO BARRA -->
                                    <div style="display: inline-block; width: 180px;">
                                        @if($aluno->area_id==3)
                                            <input placeholder="Flex bar" name="flex_barr_nota" type="text" style="margin-top: 8px; width: 70px;" autocomplete="off" readonly>
                                        @else
                                            <input placeholder="Flex bar" name="flex_barr_nota" type="text" style="margin-top: 8px; width: 70px;" autocomplete="off">
                                        @endif
                                    </div>

                                    <div style="display: inline-block; width: 120px;">
                                        <select class="custom-select" name="suficiencia_abdominal">
                                            <option value="S">Suficiente</option>
                                            <option value="NS"> Não suficiente</option>
                                        </select>
                                    </div>
                                    
                                    <div style="display: inline-block; width: 100px;">
                                        <div id="media_nota_taf_{{$aluno->id}}">0,000</div>
                                    </div>
                        
                                    <div style="display: inline-block; width: 100px;">
                                        <a href="javascript: void(0);" class="btn btn-success btn-sm" role="button" onclick="lancarTafAluno({{$aluno->id}}, this);" aria-pressed="true">Lançar</a>
                                    </div>

                                </form>
                            </td>
                        @endif
                </tr>
            @endforeach
        
    </table> 
            </div>
        </div>
        
    </div>
    {{-- <script>
    $(function(){
            jQuery("form.form-gestao").submit(function(){
                jQuery(".msg-war").hide();
                jQuery(".msg-suc").hide();
    
                var dadosForm = jQuery(this).serialize();
    
                jQuery.ajax({
                    url: host + '/operador/lancar-taf-aluno',
                    data: dadosForm,
                    type: "POST",
                    beforeSend: iniciaPreloader()
                }).done(function(data){
                    finalizaPreloader();
                    if(data == true){
                        jQuery(".msg-suc").html("Salvo com sucesso!");
                        jQuery(".msg-suc").show();
    
                        setTimeout("jQuery('.msg-suc').hide();JQuery('#myModal').modal('hide');location.reload();", 3500);
                    }else{
                        jQuery(".msg-war").html(data);
                        jQuery(".msg-war").show();
                        setTimeout("jQuery('.msg-war').hide();JQuery('#myModal').modal('hide');location.reload();", 3500);
    
                    }
                }).fail(function(){
                    finalizaPreloader();
                    alert("Falha inesperada");
                });
    
                return false;
            });
        });
    </script> --}}
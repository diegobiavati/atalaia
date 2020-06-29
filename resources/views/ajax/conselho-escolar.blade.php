<style>
    tr:nth-child(even) {
        background: #F2F2F2;
    }
    
    input.pesquisar-in-card-title2 {
        width: 260px;
        border: none;
        outline: none;
        background: none;
        padding-left: 8px;
    }
    
</style>
<div class="card bg-light mb-3">
        <div class="card-header">
            <i class="ion-wand"></i><strong>Conselho de Ensino</strong>
            <div style="float: right">              
                <!--a class="no-style" href="javascript: void(0);" onclick="" placement="bottom" style="margin-left: 6px;"-->
                <!--a href="javascript: void(0);" data-toggle="popover" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
                    <i class="ion-android-more-vertical" style="color: #696969;"></i>
                </a-->
                <div id="popover-content" style="display: none;">
                    <div class="menu_inside_popover">
                        <i class="ion-android-person-add"></i><a href="javascript: void(0);" onclick="">Adicionar operador</a><br />                 
                    </div>
                </div>    
            </div>
        </div>
        <div class="card-body">
            <h5 class="card-title" style="text-align: center; margin: 18px 0 44px 0;">
                <div class="" style="padding:0; height: 30px; border-bottom: 1px solid #ccc; width: 300px; margin: 0 auto;">
                    <input id="localizar_aluno_conselho" class="pesquisar-in-card-title2" type="text" placeholder="Busca aluno" autofocus autocomplete="off" />
                    <a class="no-style" href="javascript: void(0);" onclick="localizarAlunoConselho(document.getElementById('localizar_aluno_conselho').value);">
                            <i class="ion-android-search" style="color: #696969;"></i>
                    </a>
                </div>

                <div id="content_conselho" style="margin-top: 44px;"></div>
                <div id="info-nota-aluno" style="margin-top: 44px;"></div>

            </h5>
                
        </div>
        
    </div>

    <script>
        $('input#localizar_aluno_conselho').on("keypress", function(e){
            if(e.keyCode==13){
                localizarAlunoConselho($(this).val());   
            }
        });
        function localizarAlunoConselho(aluno){
            $('div#content_conselho').empty();
            $('div#info-nota-aluno').empty();
            if(aluno.length<3){
                $('div#content_conselho').html('Não localizado');
            } else {
                $.ajax({
                    type:'GET',
                    url: '/ajax/dialog-conselho-escolar/' + aluno,
                    beforeSend: function(){
                        $('div#content_conselho').html('<div id="temp" style="text-align: center; margin: 24px; padding: 24px;"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
                    },
                    success: function(data){
                        $('div#temp').fadeOut(300, function(){
                            $(this).remove();
                            $('div#content_conselho').html(data); 
                        });
                    },
                    error: function(jqxhr){
                        setTimeout(function(){
                            alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                        }, 1000);
                    }                    
                });                
            }
        }

        function loadDialogAlConselho(aElement, aluno_id){
            $('div.box-aluno-conselho').hide();
            $('div#box-aluno-conselho_' + aluno_id).show().removeClass('box-aluno-conselho');
            $('div#box2-aluno-conselho_' + aluno_id).show();
            $(aElement).remove();
            $('div.box-aluno-conselho').remove();
        }

        function carregaNotaAlunoDisciplina(selectElement){
            $('input#localizar_aluno_conselho').val('');
            var aluno_id = $('input[name="aluno_id"]').val();
            var discilina_id = $(selectElement).val();
            if(discilina_id!=0){
                $.ajax({
                        type:'GET',
                        url: '/ajax/carrega-nota-aluno-disciplina/' + aluno_id + '/' + discilina_id,
                        beforeSend: function(){
                            $('div#info-nota-aluno').html('<div id="temp" style="text-align: center; margin: 24px; padding: 24px;"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
                        },
                        success: function(data){
                            $('div#temp').fadeOut(300, function(){
                                $(this).remove();
                                $('div#info-nota-aluno').html(data); 
                            });
                        },
                        error: function(jqxhr){
                            setTimeout(function(){
                                alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                            }, 1000);
                        }                    
                });
            } else {
                $('div#info-nota-aluno').html('<div style="text-align: center;">Por favor, selecione uma disciplina</div>');    
            }
        }

        function AdicionarConcessaoConselho(aluno_id, disciplina_id, acrescimo){
            $.ajax({
                type:'GET',
                url: '/ajax/adicionar-concessao-conselho/' + aluno_id + '/' + disciplina_id + '/' + acrescimo,
                beforeSend: function(){
                    $('div#info-nota-aluno').html('<div id="temp" style="text-align: center; margin: 24px; padding: 24px;"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
                },
                success: function(data){
                    $('div#temp').fadeOut(300, function(){
                        $(this).remove();
                        if(data=='err'){
                            alert('Operação não autorizada para esse perfil de usuário');
                        } else {
                            carregaNotaAlunoDisciplina($('select[name="disciplinas_id"]')); 
                        }
                    });
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            }); 
        }

        function removerConcessaoConselho(aluno_id, disciplina_id){
            $.ajax({
                type:'GET',
                url: '/ajax/remover-concessao-conselho/' + aluno_id + '/' + disciplina_id,
                beforeSend: function(){
                    $('div#info-nota-aluno').html('<div id="temp" style="text-align: center; margin: 24px; padding: 24px;"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
                },
                success: function(data){
                    $('div#temp').fadeOut(300, function(){
                        $(this).remove();
                        if(data=='err'){
                            alert('Operação não autorizada para esse perfil de usuário');
                        } else {
                            carregaNotaAlunoDisciplina($('select[name="disciplinas_id"]')); 
                        }
                    });
                },
                error: function(jqxhr){
                    setTimeout(function(){
                        alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                    }, 1000);
                }                    
            }); 
        }

    </script>
    


<style>
    #col_alunos, #col_alunos, .box-mensagens{
        overflow: auto;
    }

    div.box-aluno-list{
        cursor: pointer;
    }

    div.box-aluno-list:hover{
        background-color: #000000;
    }    

    div.aluno_checked{
        display: none;
        position: absolute;
        margin-left: 280px;
        margin-top: -32px;
        font-size: 16px;
        color: #0B6121;
    }    
</style>
<div class="card bg-light mb-3">
        <div class="card-header">
            <i class="ion-chatbubbles"></i><strong>Fale com aluno</strong>
            <div style="float: right">
                
                <!--a href="javascript: void(0);" data-toggle="popover" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
                    <i class="ion-android-more-vertical" style="color: #696969;"></i>
                </a>
                <div id="popover-content" style="display: none;">
                    <div class="menu_inside_popover">
                        <i class="ion-ios-bookmarks"></i><a href="javascript: void(0);" onclick="dialogAdicionarDisciplina();">Adicionar disciplina no sistema</a><br />                 
                    </div>
                    <div class="menu_inside_popover">
                        <i class="ion-android-arrow-down"></i><a href="javascript: void(0);" onclick="dialogImportarDisciplinas();">Importar disciplinas</a><br />                 
                    </div>                                              
                </div-->    

            </div>
        </div>

        <div class="card-body">
            <div id="col_alunos" style="display: inline-block;">
                <div style="text-align: right; margin: 8px 12px;">
                    <a class="no-style" href="javascript: void(0);" onclick="selecionarTodosAlunosChat(this);"><span style="color: #0B6121;"><i class="ion-android-checkmark-circle"></i> Selecionar todos</span></a>
                    <a class="no-style" id="desmarcar-todas" href="javascript: void(0);" onclick="desmarcarTodosAlunosChat(this);" style="display: none;"><span style="color: #DF0101;"><i class="ion-android-radio-button-off"></i> Desmarcar todos</span></a>
                </div>
                @csrf
                @forelse($alunos as $aluno)
                    <div class="box-aluno-list" style="font-size: 13px; margin-bottom: 4px; background-color: #F2F2F2; padding: 2px 4px; position: relative;">
                        <b>{{$aluno->numero}} {{$aluno->nome_guerra}}</b><br />
                        <span style="color: ##6E6E6E; font-size: 10px;"><i>({{$aluno->nome_completo}})</i></span>
                        <div class="aluno_checked">
                            <i class="ion-android-checkmark-circle"></i>
                        </div>
                        <input type="checkbox" name="alunosCheck[]" value="{{$aluno->id}}" style="display: none;" />
                    </div>
                @empty
                    <div style="text-align: center; margin-top: 24px;">
                        <span style="color: #DF0101;">
                            <b>Não há alunos registrados no App</b>
                        </span>
                    </div>
                @endforelse
            </div><div id="col_conversas" style="position: relative; margin-left: 12px; display: inline-block; border-left: 1px solid #ccc; vertical-align: top;">
                <div class="lista_destinatarios" style="display: none; position: absolute; top: 0; margin-top: 1px; margin-left: 12px; width: 100%; border-bottom: 1px solid #ccc; padding: 8px 2px 8px 6px; background-color: #F2F2F2;">
                    <div class="lista"  style="float: left; width: 92%; margin-top: 4px;"></div>
                    <div style="float: right; font-size: 20px; margin: 0 8px 0 0;">
                        <a href="javascript: void(0);" class="no-style" style="color: #0B6138;" onclick="dialogMensagensEspeciais();" title="Mensagens especiais...">
                            <i class="ion-wand"></i>
                        </a>
                    </div>
                    <div class="clear"></div>
                </div>
                
                <div class="box-mensagens" style="text-align: center; font-size: 18px; margin-top: 40px; margin-left: 12px; width: 100%; height: 100%;">
                    <i class="ion-chatbubble-working" style=" font-size: 38px;"></i><br />
                    SELECIONE UM ALUNO PARA ENVIAR UMA MENSAGEM
                </div>

                <div class="dialog-envio-msg" style="display: none; position: absolute; top: 100%; margin-top: -60px; margin-left: 12px; width: 100%; border-top: 1px solid #ccc; padding: 8px 2px 0 2px;">
                    <div style="width: 84%;">
                        <textarea name="text_msg_fale_com_aluno" style="width: 100%; height: 60px; padding: 4px; outline: none;" placeholder="Escreva aqui sua mensagem..."></textarea>
                    </div>
                    <div style="float: right; width: 14%; text-align: center; margin-top: -44px;">
                        <button id="enviar_mensagem_fale_com_aluno" type="button" class="btn btn-success" onclick="enviarMsgAluno(this);" disabled>Enviar</button>
                    </div>
                    <div style="clear: both"></div>
                </div>
            </div>
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

        $(document).ready(function(){
            var card_width = $('div.card').width();
            var col_height = $(window).height() - 310;
            var col_alunos_width = 320;

            $('div#col_alunos').height(col_height).width(col_alunos_width);
            $('div#col_conversas').height(col_height).width(card_width - col_alunos_width - 66);

            var heightBoxMsg = $('div.box-mensagens').height();
            $('div.box-mensagens').height(heightBoxMsg - 100);

        });
        
        function selecionarTodosAlunosChat(aElement){
            $('div.aluno_checked').show();
            $('input[name="alunosCheck[]"]').prop('checked', true);
            loadColConversasOnSelectAluno();
            $(aElement).hide();
            $(aElement).next().show();
        }

        function desmarcarTodosAlunosChat(aElement){
            $('div.aluno_checked').hide();
            $('input[name="alunosCheck[]"]').prop('checked', false);
            loadColConversasOnSelectAluno();
            $(aElement).hide();
            $(aElement).prev().show();
        }

    
    </script>
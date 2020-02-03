<style>

.modal-full {
    min-width: 100%;
    margin: 0;
}

.modal-full .modal-content {
    min-height: 100vh;
}

</style>

<div style="text-align: center; margin-top: 22px; color:  #696969;">    
    <h4>DOCUMENTAÇÃO E RELATÓRIOS</h4>
</div>
<div class="alert documentacao-relatorios" role="alert" style="margin-top: 44px"></div>
<div style="margin: 44px auto; width: 530px; text-align: center; margin-top: 84px;">

        @if(($ownauthcontroller->PermissaoCheck(20) || $ownauthcontroller->PermissaoCheck(1)) && ($ownauthcontroller->PermissaoCheck(22)))
        <div class="box-option-menu-docs-rels">
            <a href="javascript: void(0);" class="no-style" onclick="carregaOpcoesRelatorio('mapa');">
                <div style="border-radius: 36px; width: 84px; height: 84px; background-color: #0A2A1B; margin: 0 auto; margin-bottom: 6px; line-height: 86px; box-shadow: 2px 2px 5px #888888; color: #ffffff;">
                    <i class="ion-android-clipboard" style="font-size: 44px;"></i> 
                </div>
                <span>Mapa</span>
            </a>

        </div>@endif @if(($ownauthcontroller->PermissaoCheck(20) || $ownauthcontroller->PermissaoCheck(1)) && ($ownauthcontroller->PermissaoCheck(22)))
        <div class="box-option-menu-docs-rels">
            <a href="javascript: void(0);" class="no-style" onclick="carregaOpcoesRelatorio('avaliacoes');">
                <div style="border-radius: 36px; width: 84px; height: 84px; background-color: #0A2A1B; margin: 0 auto; margin-bottom: 6px; line-height: 86px; box-shadow: 2px 2px 5px #888888; color: #ffffff;">
                    <i class="ion-ios-compose" style="font-size: 44px;"></i> 
                </div>
                <span>Avaliações</span>
            </a>

        </div>@endif @if(($ownauthcontroller->PermissaoCheck(20) || $ownauthcontroller->PermissaoCheck(1)) && ($ownauthcontroller->PermissaoCheck(22)))<div class="box-option-menu-docs-rels">

            <a href="javascript: void(0);" class="no-style" onclick="carregaOpcoesRelatorio('alunos');">
                <div style="border-radius: 36px; width: 84px; height: 84px; background-color: #0A2A1B; margin: 0 auto; margin-bottom: 6px; line-height: 86px; box-shadow: 2px 2px 5px #888888; color: #ffffff;">
                    <i class="ion-android-contacts" style="font-size: 44px;"></i> 
                </div>
                <span>Alunos</span>
            </a>

        </div>@endif @if(($ownauthcontroller->PermissaoCheck(20) || $ownauthcontroller->PermissaoCheck(1)) && ($ownauthcontroller->PermissaoCheck(22)))<div class="box-option-menu-docs-rels">

            <a href="javascript: void(0);" class="no-style" onclick="carregaOpcoesRelatorio('voluntarios-aviacao');">
                <div style="border-radius: 36px; width: 84px; height: 84px; background-color: #0A2A1B; margin: 0 auto; margin-bottom: 6px; line-height: 86px; box-shadow: 2px 2px 5px #888888; color: #ffffff;">
                    <i class="ion-plane" style="font-size: 44px;"></i> 
                </div>
                <span>Voluntários para Aviação</span>
            </a>            

        </div>@endif @if(($ownauthcontroller->PermissaoCheck(20) || $ownauthcontroller->PermissaoCheck(1)) && ($ownauthcontroller->PermissaoCheck(22)))<div class="box-option-menu-docs-rels">
            <a href="javascript: void(0);" class="no-style" onclick="carregaOpcoesRelatorio('escolha-qms');">
                <div style="border-radius: 36px; width: 84px; height: 84px; background-color: #0A2A1B; margin: 0 auto; margin-bottom: 6px; line-height: 86px; box-shadow: 2px 2px 5px #888888; color: #ffffff;">
                    <i class="ion-ios-keypad" style="font-size: 44px;"></i> 
                </div>
                <span>Escolha de QMS</span>
            </a>

        </div>@endif @if($ownauthcontroller->PermissaoCheck(20))<div class="box-option-menu-docs-rels">
            <a href="javascript: void(0);" class="no-style" onclick="carregaOpcoesRelatorio('avaliacoes-ssaa');">
                <div style="border-radius: 36px; width: 84px; height: 84px; background-color: #0A2A1B; margin: 0 auto; margin-bottom: 6px; line-height: 86px; box-shadow: 2px 2px 5px #888888; color: #ffffff;">
                    <i class="ion-pie-graph" style="font-size: 44px;"></i> 
                </div>
                <span>Dados e análise estatística</span>
            </a>

        </div>@endif  @if($ownauthcontroller->PermissaoCheck(1))<div class="box-option-menu-docs-rels">
                <a href="javascript: void(0);" class="no-style" onclick="dialogConfiguracoesRelatorio();">
                    <div style="border-radius: 36px; width: 84px; height: 84px; background-color: #045FB4; margin: 0 auto; margin-bottom: 6px; line-height: 86px; box-shadow: 2px 2px 5px #888888; color: #ffffff;">
                        <i class="ion-ios-gear" style="font-size: 44px;"></i> 
                    </div>
                    <span>Configurações</span>
                </a>    
            </div>

        @endif
    

</div>

<!--a href="#full-modal" role="button" class="btn btn-primary" data-toggle="modal">Launch modal</a-->

<div class="modal" id="full-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-full" role="document">
        <div class="modal-content" style="background-color: rgba(255,255,255,0.925); border-radius: 0;">
            <div class="modal-header">
                <div style="width: 100%;">
                    <div id="title-full-modal" style="margin-top: 10px; font-size: 16px; text-align: center;"></div>
                    <div style="position: absolute; left: 100%; font-size: 18px; margin: -32px 0 0 -38px;">
                        <a href="javascript: void(0);" class="no-style" data-dismiss="modal">
                            <i class="ion-close"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div id="body-full-modal"></div>
        </div>
    </div>
</div>

<script>

    $(document).on('change', 'select.required_to_show_button', function(){
        $('select.required_to_show_button').each(function(index, element){
            if($(element).val()==0){
                $('button#submit-relatorio').slideUp(100);
                $('button#submit-relatorio-excel').slideUp(100);
                return false;
            }
            $('button#submit-relatorio').slideDown(100);
            $('button#submit-relatorio-excel').slideDown(100);
        });
    });


</script>


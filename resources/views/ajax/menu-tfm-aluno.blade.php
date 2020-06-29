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
    <h4>TFM DO ALUNO</h4>
</div>
<div class="alert documentacao-relatorios" role="alert" style="margin-top: 44px"></div>
<div style="margin: 44px auto; width: 530px; text-align: center; margin-top: 84px;">

    @if(($ownauthcontroller->PermissaoCheck(3)))
    <div class="box-option-menu-docs-rels">
        <a href="javascript: void(0);" id="lancar-taf-aluno" class="no-style">
            <div style="border-radius: 36px; width: 84px; height: 84px; background-color: #0A2A1B; margin: 0 auto; margin-bottom: 6px; line-height: 86px; box-shadow: 2px 2px 5px #888888; color: #ffffff;">
                <i class="ion-android-clipboard" style="font-size: 44px;"></i>
            </div>
            <span>Lançar TFM</span>
        </a>
    </div>
    @endif
    @if(($ownauthcontroller->PermissaoCheck(3)))
    <div class="box-option-menu-docs-rels">
        <a href="javascript: void(0);" id="lancar-taf-aluno-recuperacao" class="no-style">
            <div style="border-radius: 36px; width: 84px; height: 84px; background-color: #0A2A1B; margin: 0 auto; margin-bottom: 6px; line-height: 86px; box-shadow: 2px 2px 5px #888888; color: #ffffff;">
                <i class="ion-ios-compose" style="font-size: 44px;"></i>
            </div>
            <span>Lançar TFM (Recuperação)</span>
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
    $(document).on('click', 'div.box-option-menu-docs-rels', function(evt) {
        evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
        
        var id = $(this).find('a').attr('id');
        loadAdminAjaxContent(id);
    });
</script>
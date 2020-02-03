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
    <h4>PARÂMETROS & IMPORTAÇÕES</h4>
</div>
<div class="alert documentacao-relatorios" role="alert" style="margin-top: 44px"></div>
<div style="margin: 44px auto; width: 530px; text-align: center; margin-top: 84px;">

    <div class="box-option-menu-docs-rels">
        <a href="javascript: void(0);" class="no-style" id="parametros">
            <div style="border-radius: 36px; width: 84px; height: 84px; background-color: #0A2A1B; margin: 0 auto; margin-bottom: 6px; line-height: 86px; box-shadow: 2px 2px 5px #888888; color: #ffffff;">
                <i class="ion-android-clipboard" style="font-size: 44px;"></i>
            </div>
            <span>Parâmetros</span>
        </a>
    </div>

    <div class="box-option-menu-docs-rels">
        <a href="javascript: void(0);" class="no-style" id="alunoConcurso">
            <div style="border-radius: 36px; width: 84px; height: 84px; background-color: #0A2A1B; margin: 0 auto; margin-bottom: 6px; line-height: 86px; box-shadow: 2px 2px 5px #888888; color: #ffffff;">
                <i class="ion-ios-people" style="font-size: 44px;"></i>
            </div>
            <span>Aluno (Concurso)</span>
        </a>
    </div>

    <div class="box-option-menu-docs-rels">
        <a href="javascript: void(0);" class="no-style" id="alunoCursoConcurso">
            <div style="border-radius: 36px; width: 84px; height: 84px; background-color: #0A2A1B; margin: 0 auto; margin-bottom: 6px; line-height: 86px; box-shadow: 2px 2px 5px #888888; color: #ffffff;">
                <i class="ion-android-archive" style="font-size: 44px;"></i>
            </div>
            <span>Aluno Curso (Concurso)</span>
        </a>
    </div>

</div>

<div class="modal" id="full-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="alert alert-success" role="alert" style="margin-bottom:0px;"></div>
    <div class="alert alert-danger" role="alert" style="margin-bottom:0px;"></div>
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

            <div class="progress" style="margin-top: 36px; display: none;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#parametros').click(function() {
            carregaOpcao('parametros', 'parametros');
        });

        $('#alunoConcurso').click(function() {
            carregaOpcao('importacoes', 'alunoConcurso');
        });

        $('#alunoCursoConcurso').click(function() {
            carregaOpcao('importacoes', 'alunoCursoConcurso');
        });
    });

    function carregaOpcao(tipo, item) {

        $('div.alert-success').html(null).slideUp();
        $('div.alert-danger').html(null).slideUp();

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: '/ajax/' + tipo + '/' + item,
            beforeSend: function() {
                $('#full-modal').modal('show');
                $('div#title-full-modal').empty();
                $('div#body-full-modal').empty();
                $('div#title-full-modal').html('<div id="temp"><img src="/images/loadings/loading_01.svg" style="width: 24px; margin-right: 8px;" /> Aguarde, carregando...</div>');
            },
            success: function(data) {
                $('div#temp').fadeOut(300, function() {
                    $(this).remove();
                    $('div#title-full-modal').html(data.modalTitle);
                    $('div#body-full-modal').html(data.response);
                });
            },
            error: function(jqxhr) {
                setTimeout(function() {
                    alert('ERRO INTERNO/Violação de acesso!/CARACTERE NÃO PERMITIDO PARA ESTA OPERAÇÃO');
                }, 1000);
            }
        });
    }

    function enviaArquivoExcel() {
        var fd = new FormData(document.getElementById('importar_concurso'));

        $.ajax({
            cache: false,
            dataType: 'json',
            url: '/ajax/importacoes',
            type: "POST",
            data: fd,
            enctype: 'multipart/form-data',
            xhr: function() {
                var xhr = $.ajaxSettings.xhr();
                xhr.upload.onprogress = function(e) {
                    $('div.progress div').css('width', (Math.floor(e.loaded / e.total * 100)) - (1) + '%');
                };
                return xhr;
            },
            beforeSend: function() {
                $('div.alert-success').html(null).slideUp();
                $('div.alert-danger').html(null).slideUp();
                $('div.progress').slideDown(100);
            },
            success: function(data) {

                $('div.progress div').css('width', '100%');
                setTimeout(function() {
                    $('div.progress').slideUp(100, function() {
                        $('div.progress div').css('width', '0%');
                    });
                }, 400);
                if (data.status == 'ok') {
                    $('div.alert-success').html(data.response).slideDown();
                } else {
                    $('div.alert-danger').html(data.error).slideDown();
                }

            },
            error: function(jqxhr) {
                $('div.alert-danger').html('Houve um erro ao tentar enviar o arquivo').slideDown();
                $('div.progress div').css('width', '0%');
            },
            processData: false, // tell jQuery not to process the data
            contentType: false // tell jQuery not to set contentType
        });

    }
</script>
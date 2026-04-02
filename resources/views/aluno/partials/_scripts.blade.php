<script>
    $(document).ready(function() {

        $('.data_mask').mask('00/00/0000');
        $('.cep_mask').mask('00000-000');
        $('.telefone_mask').mask('(00)-0000-0000');
        $('.celular_mask').mask('(00)-00000-0000');
        $('.cpf_mask').mask('000.000.000-00');

        $('#nav-tab a').click(function() {
            $('div.errors-implantar-aluno').empty().hide();
            $('div.success-implantar-aluno').empty().hide();
        });

        $('#add-dependente').click(function() {

            $("#toClone").clone().find("input:text").val("").end().appendTo("#nav-implantar-aluno7");

            //Refaz a mascára
            $('.data_mask').mask('00/00/0000');
        });

        $('select.custom-select[name="atleta_marexaer"]').change(function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
            
            if(evt.currentTarget.value == 'S'){
                $('.divImplantarAluno.bonificacao').css('display', 'block');
                $('input#modalidade').prop('disabled', false); 
                $('input#habilidades').prop('disabled', false); 
            
            }else{
                $('.divImplantarAluno.bonificacao').css('display', 'none');
                $('select.custom-select[name="bonificacao_atleta"]').val(0);

                $('input#modalidade').prop('disabled', true); 
                $('input#habilidades').prop('disabled', true); 
                $('input#modalidade option[value=0]').prop('selected', true); 
                $('input#habilidades').prop('value', null); 
                $('input#modalidade').prop('value', null);
            }
        });

        $('.btn.btn-secondary').click(function() {
            //Reseta os Campos
            loadAdminAjaxContent('admin/aluno');
        });

        //Desabilita edição dos campos se não tiver permissão
        @if(isset($aluno) && !$ownauthcontroller->PermissaoCheck(10))
            $('#implantar_aluno input, select, textarea').prop('disabled', true);
        @endif
        
        @if(isset($aluno) && $ownauthcontroller->PermissaoCheck(37))
            $('#implantar_aluno textarea#obs_psicopedagogia').prop('disabled', false);
            $('#implantar_aluno input[name="_token"]').prop('disabled', false);
        @endif
        
        @if(isset($aluno))
            $('select.custom-select[name="atleta_marexaer"]').change();
        @endif

        $('a.open_file').click(function(){

            $('div.erro-upload').slideUp(200, function(){
                $(this).empty();
            });
            $('input[type="file"][name="aluno_imagem"]').trigger('click');
        });

        //Carregar Imagem ao Selecionar
        $('#aluno_imagem').change(function() {
            const file = $(this)[0].files[0];

            var fileSize = (file.size / 1000);
            if (fileSize > 1024) {
                $('div.erro-upload').html('O arquivo a ser enviado não deve ser maior que 1024Kb').slideDown();
            } else {
                const fileReader = new FileReader();
                fileReader.onloadend = function() {
                    $('#aluno_img').attr('style', 'background: url(' + fileReader.result + ') no-repeat center center; background-size: contain;');
                }
                fileReader.readAsDataURL(file);
            }
        });

        $('button.btn.btn-primary').click(function() {
            
            var formData = $('form#implantar_aluno').serialize();
            var url = "/ajax/admin/aluno{{ ((isset($aluno->id)) ? '/'.$aluno->id : '') }}";
            
            $.ajax({
                dataType: 'json',
                url: url,
                type: '{{ (isset($aluno->id)) ? 'PUT': 'POST' }}',
                data: formData,
                beforeSend: function(){
                    $('div.errors-implantar-aluno').empty().hide();
                },
                success: function(data){
                    
                    if(data.status=='ok'){
                        $('div.success-implantar-aluno').html(data.response).slideDown();

                        if($('form#implantar_aluno input[type="file"]')[0].value != ""){
                            submitImageForm('implantar_aluno', '/ajax/upload/img-aluno/aluno-imagem/'+ data.id_aluno);
                        }
                        
                        setTimeout(function(){
                            $('div.success-implantar-aluno').slideUp(200, function(){
                                $(this).removeClass('alert-success').empty();
                            });

                            //Reseta os Campos
                            loadAdminAjaxContent('admin/aluno');
                        }, 3000);
                    } else {
                        $('div.errors-implantar-aluno').html('<strong>ATENÇÃO:</strong><br />').slideDown();
                        $.each(data.response, function(key, value){
                            $('div.errors-implantar-aluno').append('<li>' + value + '</li>');
                        });
                    }
                },
                error: function(jqxhr){
                    $('div.errors-implantar-aluno').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown(); 
                }         
            });
        });
    });

    function submitImageForm(formID, action) {
        
        var fd = new FormData(document.getElementById(formID));

        $.ajax({
            cache: false,
            dataType: 'json',
            url: action,
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
                $('div.progress').slideDown(100);
            },
            success: function(data) {
                if (data.uploadType == 'imgAluno') {
                    $('div.progress div').css('width', '100%');
                    setTimeout(function() {
                        $('div.progress').slideUp(100, function() {
                            $('div.progress div').css('width', '0%');
                        });
                    }, 400);
                    if (data.status == 'ok') {
                        $('div.success-implantar-aluno').html(data.response).slideDown();
                        /*if (!$('div#box-dialog-remover-imagem-perfil').is(':visible')) {
                            $('div#box-dialog-remover-imagem-perfil').slideDown(100);
                        }
                        $('div.imagem_perfil').css('background-image', 'url(\'' + data.src_image + '\')');
                        if (data.tipo == 'minha-imagem') {
                            $('div.data_user_imagem_perfil').css('background-image', 'url(\'' + data.src_image + '\')');
                        }*/
                    } else {
                        $('div.erro-upload').html(data.error).slideDown();
                    }
                }
            },
            error: function(jqxhr) {
                $('div.erro-upload').html('Houve um erro ao tentar enviar o arquivo').slideDown();
            },
            processData: false, // tell jQuery not to process the data
            contentType: false // tell jQuery not to set contentType
        });
    }
</script>
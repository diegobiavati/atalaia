<script src="/js/jquery/jquery-3.3.1.js"></script>
<div>

    <div>
        <div class="alert alert-success" role="alert" style="margin-bottom:10px;"></div>
        <div class="alert alert-danger erro-upload" role="alert" style="margin-bottom:10px;"></div>
        <form id="importar_sispbAluno">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="file" id="arquivo_excel" name="arquivo_excel"/>

            <br><br>

            <input type="radio" id="alunos" value="alunos" name="radio"/>
            <label for="alunos">Alunos</label>
            <input type="radio" id="dependentes" value="dependentes" name="radio"/>
            <label for="dependentes">Dependentes</label>
            
            <div class="progress" style="margin-top: 36px; display: none;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </form>
    </div>
</div>

<button type="button" class="btn btn-primary" style="margin:30px 0px 0px 0px;">
    Importar
</button>

<script>
    $(document).ready(function() {
        $('button.btn.btn-primary').click(function() {

            var fd = new FormData(document.getElementById('importar_sispbAluno'));

            $.ajax({
                cache: false,
                dataType: 'json',
                url: 'importar-excel-sispb-alunos',
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
                    $('div.erro-upload').html(null).slideUp();
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
                        $('div.erro-upload').html(data.error).slideDown();
                    }

                },
                error: function(jqxhr) {
                    $('div.erro-upload').html('Houve um erro ao tentar enviar o arquivo').slideDown();
                },
                processData: false, // tell jQuery not to process the data
                contentType: false // tell jQuery not to set contentType
            });

        });
    });
</script>
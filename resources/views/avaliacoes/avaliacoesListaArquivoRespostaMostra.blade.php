<?php
    if(in_array(8, session()->get('login')['perfil'])){
        
        $omctsId = Array();
        foreach($avaliacao->avaliacoesMostra as $avaliacoesMostra){
            $omctsId[] = $avaliacoesMostra->omct_id;
        }

        $omctsId = array_unique($omctsId);
        
        $uetes = App\Models\OMCT::whereIn('id', $omctsId)->get(); //Remove a ESA

        $comboUete = null;
        foreach ($uetes as $uete){
            $comboUete .= '<option value="'.$uete->id.'">'.$uete->omct.'</option>';
        }
    }
?>
        @if(in_array(8, session()->get('login')['perfil']))
        <div class="card-body">
            <form id="form_resposta_mostra_{{$avaliacao->id}}">
                <input type="hidden" name="_token" value="{{csrf_token()}}">

                <div class="alert alert-danger erro-upload" role="alert" style="display: none;"></div>
                <div class="progress" style="margin-top: 36px; display: none;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div class="row" style="display:-webkit-box;">
                <div class="col" style="width:30%;float:right;">
                    <select name="omctID" class="custom-select">
                    <option value="0" disabled selected hidden>Selecione uma UETE</option>
                    <option value="todas_omct">TODAS AS UETE</option>
                    {!!$comboUete!!}
                    </select>
                </div>
                <div class="col custom-file" style="width:60%;float:right;">
                    <input type="file" class="custom-file-input" name="arquivo_mostra_{{$avaliacao->id}}" id="arquivo_mostra_{{$avaliacao->id}}" accept=".pdf">
                    <label class="custom-file-label" style="border-color: #007bff;" for="arquivo_mostra">Escolha o arquivo</label>
                </div>
                <div class="col" style="float:right;width:10%;">
                    <button type="button" class="btn btn-primary" onclick="enviarArquivoReposta('form_resposta_mostra_{{$avaliacao->id}}', 'ajax/upload/resposta-arquivo-mostra/arquivo_mostra_{{$avaliacao->id}}');">Enviar</button>
                </div>
                </div>
            </form>
        </div>
        @endif

    <div class="card-footer" style="background-color: #fff; color:black">
        <form id="listaArquivoMostra">
            <div id="tableInfo">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 5%;text-align: center;">ID</th>
                            <th style="width: 50%;">Arquivo</th>
                            <th style="width: 20%;">UETE</th>
                            <th style="width: 15%;text-align: left;">Data de Envio</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($avaliacao->avaliacoesMostrasRespostas as $mostra)
                        <?php
                            
                            $permitido = false;
                            //Se o perfil for ESA ou SSAA ESA
                            if(in_array(1, session()->get('login')['perfil']) || in_array(8, session()->get('login')['perfil'])){
                                $permitido = true;
                            }else if(session()->get('login')['omctID'] == $mostra->omct_id){
                                $permitido = true;
                            }

                            $nome_arquivo = pathinfo($mostra->nome_arquivo, PATHINFO_FILENAME);

                        ?>
                            @if($permitido && isset($mostra->nome_arquivo))
                                <tr>
                                    <td style="text-align: center;">{{ $mostra->id }}</td>
                                    <td><a href="download/reposta-mostra/{{ $mostra->id }}/{{ $nome_arquivo }}" target="_blank">Solução de Pedido de Revisão de Prova  {{ $mostra->id }}</a></td>
                                    <td>{{ $mostra->omct->sigla_omct }}</td>
                                    <td style="text-align: left;">{{ date('d/m/Y H:i:s', strtotime($mostra->created_at)) }}</td>
                                </tr>
                            @endif
                            
                        @endforeach

                    </tbody>
                </table>
            </div>
        </form>
    </div>

    <script>
    /*Para Mostrar o Arquivo na Caixa de Seleção*/
    $('.custom-file-input').on('change',function(){
        var fileName = $(this).val();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    function enviarArquivoReposta(formID, action) {
            var input = $('form#' + formID + ' input[type="file"]')[0];
            var fileSize = (input.files[0].size/1000);
            if(fileSize>10240){
                $('div.erro-upload').html('O arquivo a ser enviado não deve ser maior que 10240Kb').slideDown();
            } else {
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
                            $('div.progress div').css('width', (Math.floor(e.loaded / e.total *100))-(1) + '%');
                        };
                        return xhr;
                    },            
                    beforeSend: function() {
                        $('div.erro-upload').slideUp(100);                            
                        $('div.progress').slideDown(100);                            
                    },
                    success: function(data) {
                        if(data.success=='ok'){
                            $('div.progress div').css('width', '100%');
                            setTimeout(function(){
                                $('div.progress').slideUp(100, function(){
                                    $('div.progress div').css('width', '0%');
                                });    
                            }, 400);

                            $('div#div_resposta_mostra_' + data.id).empty();
                            $('div#div_resposta_mostra_' + data.id).html(data.html);
                            
                        }else{
                            $('div.erro-upload').html(data.error).slideDown();    
                        }
                    },
                    error: function(jqxhr){
                        $('div.erro-upload').html('Houve um erro ao tentar enviar o arquivo').slideDown();
                        $('div.progress').slideUp(100, function(){
                            $('div.progress div').css('width', '0%');
                        });    
                    },                                                             
                    processData: false,  // tell jQuery not to process the data
                    contentType: false   // tell jQuery not to set contentType
                });
            }
        }
    </script>
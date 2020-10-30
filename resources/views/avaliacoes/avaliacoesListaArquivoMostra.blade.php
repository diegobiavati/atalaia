
<div class="card-footer" style="background-color: #fff; color:black">
    <form id="listaArquivoMostra">
        <input type="hidden" name="_token" value="{{csrf_token()}}">
        <div id="tableInfo">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 5%;text-align: center;">ID</th>
                        <th style="width: 50%;">Arquivo</th>
                        <th style="width: 20%;">UETE</th>
                        <th style="width: 15%;text-align: left;">Data de Envio</th>
                        <th style="width: 10%;text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($avaliacoesMostra as $mostra)
                    <?php
                        if($mostra->status == 'C'){
                            $iconClass = 'ion-android-cloud-done';
                            $iconColor = 'color:green;';
                        }else if($mostra->status == 'P'){
                            $iconClass = 'ion-android-upload';
                            $iconColor = 'color:red;';
                        }else{
                            $iconClass = 'ion-android-sync';
                            $iconColor = 'color:blue;';
                        }

                        $permitido = false;
                        //Se o perfil for ESA ou SSAA ESA
                        if(in_array(1, session()->get('login')['perfil']) || in_array(8, session()->get('login')['perfil'])){
                            $permitido = true;
                        }else if(session()->get('login')['omctID'] == $mostra->omct_id){
                            $permitido = true;
                        }

                        $nome_arquivo = pathinfo($mostra->nome_arquivo, PATHINFO_FILENAME);

                        if(in_array(8, session()->get('login')['perfil'])){
                            $onclick = "AprovaRevisaoUete(this,$mostra->id);";
                        }else{
                            $onclick = '';
                        }
                    ?>
                        @if($permitido)
                            <tr>
                                <td style="text-align: center;">{{ $mostra->id }}</td>
                                <td><a href="download/mostra/{{ $mostra->id }}/{{ $nome_arquivo }}" target="_blank">Pedido de Revisão de Prova {{ $mostra->id }}</a></td>
                                <td>{{ $mostra->omct->sigla_omct }}</td>
                                <td style="text-align: left;">{{ date('d/m/Y H:i:s', strtotime($mostra->created_at)) }}</td>
                                <td style="text-align: center;"><i class="{{ $iconClass }}" style="font-size:25px;{{ $iconColor }}" onclick={{ $onclick }} ></i></td>
                            </tr>
                        @endif
                        
                    @endforeach

                </tbody>
            </table>
        </div>
    </form>
</div>
<script>
  
    function AprovaRevisaoUete(elemento, id){

        var dataForm = $('form#listaArquivoMostra').serialize();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: dataForm,
            url: '/ajax/aprova-revisao-prova-uete/' + id,
            beforeSend: function(){
                $('div.erro-upload').slideUp(100);      
            },
            success: function(data){
                $(elemento).removeClass('ion-android-upload ion-android-sync');
                $(elemento).css({color: ''});
                
                $(elemento).addClass(data.retornoClass);
                $(elemento).css({color: data.retornoStyle});
            },
            error: function(jqxhr){
                $('tr#operador_' + id).css('background-color', style_bg_inicial);
                if(jqxhr.status==500){
                    setTimeout(function(){
                        $('div.progress').slideUp(100, function(){
                            $('div.erro-upload').html('Erro interno!').slideDown();    
                        });    
                    }, 400);
                    
                } else if(jqxhr.status==422){
                    $('div.erro-upload').slideDown(100);    
                    var errors = $.parseJSON(jqxhr.responseText);
                    $('div.erro-upload').prepend('<ul style="margin: 0 6px;"></ul>');                            
                    $.each(errors.errors, function (index, value) {
                        $('div.erro-upload ul').append('<li>' + value + '</li>');
                    });                           
                }
            }                
        });
    }
</script>
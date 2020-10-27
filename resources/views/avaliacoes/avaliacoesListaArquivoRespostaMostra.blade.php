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

                    @foreach($avaliacoesMostra as $mostra)
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
                                <td><a href="download/reposta-mostra/{{ $mostra->id }}/{{ $nome_arquivo }}" target="_blank">{{ $nome_arquivo }}</a></td>
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
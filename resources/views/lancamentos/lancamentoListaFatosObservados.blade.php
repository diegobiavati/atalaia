<form id="VisualizarLancamento">
    <div id="tableInfo" style="width: 100%; margin: 22px auto; text-align: center;">
        <table class="table table-striped" style="margin: 60px 0 90px 0;">
            <thead>
                <tr>
                    <th style="width: 5%;">Numero</th>
                    <th style="width: 15%;text-align: left;">Nome Guerra</th>
                    <th style="width: 10%;text-align: left;">Data Obs</th>
                    <th>Tipo</th>
                    <th style="width: 30%;text-align: left;">Observação</th>
                    <th>UETE</th>
                    <th>Opções2</th>
                </tr>
            </thead>
            <tbody>

                @foreach($lancamentoFO as $aluno)
                <tr>
                    <td>{{$aluno->numero}}</td>
                    <td style="text-align: left;{{ ((isset($aluno->situacoes_diversas_id)) ? 'color: red;' : null) }}">{{$aluno->nome_guerra}}</td>
                    <td style="text-align: left;">{{ App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($aluno->data_obs)}}</td>
                    <td style="text-align: center;">{{ (($aluno->tipo == 0) ? 'Negativo' : (($aluno->tipo == 1) ? 'Neutro' : (($aluno->tipo == 2) ? 'Positivo' : null)) ) }}</td>
                    <td style="text-align: left;">{{$aluno->observacao}}</td>
                    <td style="text-align: left;">{{$aluno->uete}}</td>
                    <td><a href="javascript: void(0);" class="no-style" title="Visualizar Fato Observado" onclick="abrirFichaIndividual('{{route($rota)}}', '{{$aluno}}');"><i class="ion-android-clipboard" style="font-size: 22px;"></i></a></td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</form>
<script>
    function abrirFichaIndividual(rota, aluno) {

        window.open(rota + '?params=' + aluno);

    }
</script>
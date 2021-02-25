<form id="VisualizarFATD">
    <div id="tableInfo" style="width: 100%; margin: 22px auto; text-align: center;">
        <table class="table table-striped" style="margin: 60px 0 90px 0;">
            <thead>
                <tr>
                    <th style="width: 5%;">N. Processo</th>
                    <th style="width: 5%;">Numero</th>
                    <th style="width: 15%;text-align: left;">Nome Guerra</th>
                    <th style="width: 10%;text-align: left;">Data Obs</th>
                    <th>Tipo</th>
                    <th style="width: 30%;text-align: left;">Observação</th>
                    <th>Opções</th>
                    <th>Situação</th>
                </tr>
            </thead>
            <tbody>
            
                @foreach($lancamentoFATD as $fatd)
                <tr>
                    <td>{{str_pad($fatd->fatdLancada->nr_processo, 3, 0, STR_PAD_LEFT)}}</td>
                    <td>{{$fatd->aluno->numero}}</td>
                    <td style="text-align: left;{{ ((isset($fatd->aluno->situacoes_diversas_id)) ? 'color: red;' : null) }}">{{$fatd->aluno->nome_guerra}}</td>
                    <td style="text-align: left;">{{ App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($fatd->data_obs)}}</td>
                    <td style="text-align: center;">{{ (($fatd->tipo == 0) ? 'Negativo' : (($fatd->tipo == 1) ? 'Neutro' : (($fatd->tipo == 2) ? 'Positivo' : null)) ) }}</td>
                    <td style="text-align: left;">{{$fatd->observacao}}</td>
                    <td>
                        <a href="javascript: void(0);" class="no-style" style="padding-right:10px;" title="Visualizar FATD" onclick="abrirFichaFATD('{{route($rota)}}', '{{$fatd->fatdLancada->lancamento_fo_id}}');"><i class="ion-android-clipboard" style="font-size: 22px;"></i></a>
                        <!--Perfil Sargenteante Pode Editar FATD-->
                        @if( $ownauthcontroller->PerfilCheck([4,9002]) )
                        <a href="javascript: void(0);" class="no-style" title="Lançar/Editar FATD" onclick="abrirFATD({{$fatd->fatdLancada->lancamento_fo_id}});"><i class="ion-android-create" style="font-size: 22px;"></i></a>
                        @endif
                    </td>
                    <td>
                        @if(isset($fatd->fatdLancada->justificado))
                        @if($fatd->fatd == 'S')
                        <font style="color:rgb(17, 65, 214)">Apurado</font>
                        @else
                        <font style="color:rgb(0,0,255)">Não Apurado</font>
                        @endif
                        @else
                        <font style="color:rgb(255,0,0)">Não Resolvido</font>
                        @endif
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</form>
<script>
    function abrirFichaFATD(rota, fatdID) {
        window.open(rota + '?fatdID=' + fatdID);
    }

    function abrirFATD(fatdID) {
        carregaOpcaoAjaxContent('fatd', fatdID, 'Modal');
    }
</script>
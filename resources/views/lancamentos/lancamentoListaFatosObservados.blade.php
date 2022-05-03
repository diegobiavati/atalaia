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
                    @if(session()->get('login.qmsID') !== null)
                    <th>Curso</th>
                    @endif
                    <th>UETE</th>
                    <th>Opções</th>
                    <th>Situação</th>
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
                    @if(session()->get('login.qmsID') !== null)
                    <td style="text-align: left;">{{$aluno->curso}}</td>
                    @endif
                    <td style="text-align: left;">{{$aluno->uete}}</td>
                    <td><a href="javascript: void(0);" class="no-style" title="Visualizar Fato Observado" onclick="abrirFichaIndividual({{$aluno->id}});"><i class="ion-android-clipboard" style="font-size: 22px;"></i></a></td>
                    <td>
                        @if($aluno->cancelado == 'S')
                            <font style="color:rgb(255,165,0)">Cancelado</font>
                        @elseif(isset($aluno->providencia))
                            @if($aluno->fatd == 'S')
                                <font style="color:rgb(16, 179, 16)">Resolvido com FATD</font>
                            @else
                                <font style="color:rgb(0,0,255)">Resolvido sem FATD</font>
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
    function abrirFichaIndividual(lancamento) {
        carregaOpcaoAjaxContent('lancamentos', lancamento + '/edit', 'Modal');
    }
</script>

@if(isset($relacao) && $relacao == 'excel')

{{ $fileName = bcrypt(date('Y-m-d H:i:s')).'.ods' }}

{{header("Content-Description: PHP Generated Data")}}
{{header("Content-Type: application/x-msexcel")}}
{{header("Content-Disposition: attachment; filename=\"{$fileName}\"")}}
{{header("Expires: 0")}}
{{header("Cache-Control: must-revalidate, post-check=0, pre-check=0")}}
{{header("Pragma: no-cache")}}

@endif
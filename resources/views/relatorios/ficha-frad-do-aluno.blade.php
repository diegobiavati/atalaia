<form id="ficha-relatorio">
    <input type="hidden" name="ano_formacao_id" value="{{$anoFormacao->id or old('ano_formacao_id')}}">

    <div id="tableInfo" style="width: 90%; margin: 22px auto; text-align: center;">
        <table class="table table-striped" style="margin: 60px 0 90px 0;">
            <thead>
                <tr>
                    <th style="width: 10%;">Numero</th>
                    <th style="width: 30%;text-align: left;">Nome Guerra</th>
                    <th style="text-align: left;">Nome Completo</th>
                    <th>Opções</th>
                </tr>
            </thead>
            <tbody>

                @foreach($alunos as $aluno)
                <tr>
                    <td>{{$aluno->numero}}</td>
                    <td style="text-align: left;">{{$aluno->nome_guerra}}</td>
                    <td style="text-align: left;">{{$aluno->nome_completo}}</td>
                    <td><a href="javascript: void(0);" class="no-style" title="Visualizar FRAD do Aluno" onclick="abrirFradIndividual('{{route($rota)}}', '{{$aluno->id}}');"><i class="ion-android-clipboard" style="font-size: 22px;"></i></a></td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
    <div style="margin-top: 24px;">
        <button id="btn-frad-geral" type="button" class="btn btn-primary" onclick="abrirFradGeral('{{route($rotaGeral)}}', '{{$idUeteCurso}}', '{{$anoFormacao->id}}');">Visualizar FRAD (Geral)</button>
    </div>
</form>
<script>
    function abrirFradIndividual(rota, aluno) {
        window.open(rota + '?aluno=' + aluno);
    }

    function abrirFradGeral(rota, idUeteCurso, idAnoFormacao) {
        window.open(rota + '?UeteCurso=' + idUeteCurso +'&idAnoFormacao=' + idAnoFormacao);
    }
</script>
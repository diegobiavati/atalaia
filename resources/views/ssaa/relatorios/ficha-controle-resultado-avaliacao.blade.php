<style>
    div.imagem-curso {
        background: url('{{asset($esaAvaliacoes->esadisciplinas->qms->qmsMatriz->img_ssaa)}}') no-repeat center center;
        background-size: 120px;
        background-position: left center;
    }

    div span.title {
        font-size:20px;
    }

    .center {
        text-align: center;
    }

    .uppercase{
        text-transform:uppercase;
    }

    .italic{
        text-decoration: underline;
    }

</style>

<div class="container" style="margin-top: 100px;">
    <div class="row" style="border: 1px solid;">
        <div class="col imagem-curso"></div>
        <div class="col" style="flex: 4;">
            <div><span class="title">Curso de {{$esaAvaliacoes->esadisciplinas->qms->qms}}</span></div>
            <div><span class="title">SSAA</span></div>
        </div>
        <div class="col" style="flex: 4;text-align:right;">
            <div><span class="title">CFGS: {{$esaAvaliacoes->esadisciplinas->qms->escolhaQms->anoFormacao->ano_cfs}}</span></div>
            <div><span class="title">Período: 2º Ano</span></div>
        </div>
    </div>

    <div class="row center uppercase border" style="margin-top: 10px;">
        <span class="title" style="margin:auto;">Ficha de Controle de Resultados de Avaliação</span>
    </div>

    <div class="row center italic" style="margin-top: 10px;">
        <span class="title" style="margin:auto;font-weight: bold;">Anexo F das NIAA-CFGS/ESA conforme o Artigo 52 das NIAA-CFGS/ESA</span>
    </div>

    <div class="row" style="margin-top: 10px;">
        <div class="col" style="max-width:10%;"><span>Disciplina:</span></div>
        <div class="col" style="min-width:70%;">{{$esaAvaliacoes->esadisciplinas->nome_disciplina}}</div>
        <div class="col" style="max-width:7%;"><span>GBM:</span></div>
        <div class="col">{{$gbm}}</div>
    </div>
    <div class="row" style="margin-top: 10px;">
        <div class="col" style="max-width:10%;"><span>Avaliação:</span></div>
        <div class="col">{{$esaAvaliacoes->getNomeProva()}}</div>
    </div>


    @foreach($esaAvaliacoes->esadisciplinas->qms->consultaTurmas() as $turma)
        <div class="row" style="margin-top: 30px;">
            <div class="col"><span>{{$esaAvaliacoes->getNomeProva()}}...</span></div>
            <div class="col"><span>Turma: {{$turma->turma}}</span></div>
        </div>

        <div class="table-responsive" style="margin-top:10px;">
            <table class="table columnTable" style="width:95%;padding: 0px 0px 0px 5px;margin-left:18px;">
                <tr>
                    <th><span>Ord</span></th>
                    <th><span>Nr</span></th>
                    <th><span>Nome de Guerra</span></th>
                    <th><span>GBO Errado</span></th>
                    <th><span>GBO Certo</span></th>
                    <th><span>Penalização</span></th>
                    <th><span>Nota</span></th>
                    <th><span>Menção</span></th>
                </tr>
                @foreach($lancamentosGbo as $lancamento)
                    @if($turma->id == $lancamento->aluno->turma_esa_id)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $lancamento->aluno->numero }}</td>
                            <td>{{ $lancamento->aluno->nome_guerra }}</td>
                            <td>{{ $lancamento->gbo_errado }}</td>
                            <td>{{ $lancamento->gbo_certo }}</td>
                            <td>-</td>
                            <td>{{ $lancamento->nota_aluno }}</td>
                            <td>{{ App\Models\Mencoes::getMencaoV2($lancamento->nota_aluno)->mencao }}</td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </div>   
    @endforeach
    
</div>
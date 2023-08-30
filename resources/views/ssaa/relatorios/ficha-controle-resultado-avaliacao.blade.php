<style>
        
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

<div class="container">

    @include('ssaa.relatorios.cabecalho')

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
                @foreach($avaliacoesResultados as $resultado)
                    @if($turma->id == $resultado->aluno->turma_esa_id)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $resultado->aluno->numero }}</td>
                            <td>{{ $resultado->aluno->nome_guerra }}</td>
                            <td>{{ ($resultado->gbo_aluno) ?? '-' }}</td>
                            <td>{{ $resultado->gbo_ssaa }}</td>
                            <td>-</td>
                            <td>{{ number_format($resultado->nota, 3, ',', '.') }}</td>
                            <td>{{ App\Models\Mencoes::getMencaoV2($resultado->nota)->mencao }}</td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </div>   
    @endforeach
    
    <div style="text-align: center;">
        <img src="{{ route('gaviao.ajax.relatorios.assinatura.mostrar', $assinatura->id) }}" alt="Image" width="auto" height="100"/>
    </div>
</div>


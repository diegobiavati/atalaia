<style>
    .center {
        text-align: center;
    }

    .uppercase {
        text-transform: uppercase;
    }

    .italic {
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


    <!-- Inicio do Formulario de Relação de TIROS menor que 5 -->
    <div style="margin-top: 10px;">
        <div class="row" style="border: 1px solid;">
            <div class="col imagem-curso"></div>
            <div class="col" style="flex: 10;">
                <div class="center"><span class="title">Relação de Aluno(s) com Nota(s) abaixo de 5,00 da {{$esaAvaliacoes->getChamadaAbreviada()}} da {{$esaAvaliacoes->nome_avaliacao}} de {{$esaAvaliacoes->esadisciplinas->nome_disciplina}} do Curso de {{ $esaAvaliacoes->esadisciplinas->qms->qms }}</span></div>
            </div>
        </div>

        <div>
            <table class="center" style="width:95%;padding: 0px 0px 0px 5px;margin-left:18px; margin-top:30px;">
                <tr>
                    <th><span>Ord</span></th>
                    <th><span>Nr</span></th>
                    <th><span>Nome de Guerra</span></th>
                    <th><span>Tu</span></th>
                    <th><span>GBO <div>Errado</div></span></th>
                    <th><span>GBO <div>Certo</div></span></th>
                    <th><span>Penalização</span></th>
                    <th><span>Nota</span></th>
                    <th><span>Menção</span></th>
                </tr>

                @foreach($avaliacoesResultados as $resultado)
                @if($resultado->nota < 5)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $resultado->aluno->numero }}</td>
                    <td>{{ $resultado->aluno->nome_guerra }}</td>
                    <td>{{ $resultado->aluno->turmaEsa->turma }}</td>
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

        <!-- Parti Escrita Como titulos-->
        <div class="center text-justify" style="margin-top: 22px;"><span class="h5 font-italic" style="font-weight: bold;">Cmt Cur: cumpra o previsto na "a" do n° "3." do anexo "N" das NIAA-CFGS/ESA - Estudo Obrigatório.</span></div>
        <div class="center text-justify" style="margin-top: 22px;"><span class="h5 font-italic" style="font-weight: bold;">Coord Avl: cumpra o previsto nos itens "XVII" e "XXII" do "Art 4º" do anexo "W" das NIAA-CFGS/ESA - Atribuições dos Coordenadores de Avaliação dos Cursos.</span></div>
        <div class="center text-justify" style="margin-top: 22px;"><span class="h5 font-italic" style="font-weight: bold;">Ch Spvs Es: cumpra o previsto nos itens "I", "V" e "VI" do "Art 33º" do RI/ESA - Do Chefe da Seção de Supervisão Escolar.</span></div>
        <div class="center text-justify" style="margin-top: 22px;"><span class="h5 font-italic" style="font-weight: bold;">Ch Seç PscPdg: cumpra o previsto no "Art 7º" do anexo "W" das NIAA-CFGS/ESA - Atribuições da Seção Psicopedagógica.</span></div>


        <!-- <div style="text-align: center;">
            <img src="{{ route('gaviao.ajax.relatorios.assinatura.mostrar', $assinatura->id) }}" alt="Image" width="auto" height="100" />
        </div> -->
    </div>
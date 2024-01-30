<style>

@media print {
    div .break {
        margin-top: 1.5cm;
    }
}

.tabela1{
    margin-top: 10px;
}

.tabela2{
    border-top-style: dotted;
    border-left-style: dotted ;
    border-bottom-style: dotted ;
    padding: 10px;
    margin-top: 10px;
}

.center {
    text-align: center;
    
}



</style>

<div class="container" style="margin-top: 100px;">

    @include('ssaa.relatorios.cabecalho')

    <div class="row border" style="margin-top: 10px;">
        <div class="col center" style="min-width:70%; font-weight: bold;">
            Demonstrativo Individual e Reservado de Nota de Prova <br>Referente à {{$esaAvaliacoes->nome_avaliacao}} {{$esaAvaliacoes->esadisciplinas->nome_disciplina}} - C {{$esaAvaliacoes->esadisciplinas->qms->qms_sigla}} ({{$esaAvaliacoes->getChamadaAbreviada()}})
        </div>
        <div class="col" style="border: 1px solid;padding: 0;">
            <div class="center" style="background-color:gray!important;text-align:center;" >
                Pasta nº
            </div>
            <div>
                <div class="col">SSAA: ___Arquivado </div>
                <div div class="col"> em: ___/___/{{$esaAvaliacoes->esadisciplinas->qms->escolhaQms->anoFormacao->ano_per_qualificacao}}</div>
            </div>
        </div>
    </div>
    
    <div class="row"><div class="col"><span>**** Este demonstrativo deve ser entregue individual e reservadamente pelo Coor Avl</span></div></div>
   <!-- TABELAS -->
   @foreach($avaliacoesResultados->get(0)->esaAvaliacoes->esadisciplinas->qms->consultaTurmas() as $turma)
    
    <div class="tabela1" >
        <div class="row"><div class="col"><span style="font-style:italic;">Turma: {{ $turma->turma }}</span></div></div>
        @php
            $i = 0;
        @endphp
        @foreach($avaliacoesResultados as $resultado)
            @if($turma->id == $resultado->aluno->turma_esa_id)
                <div class="row">
                    <div class="col pr-3 tabela1" style="padding-left: 0px;">
                        <table class="table" style="height: 100%;">
                            <tr>
                                <td colspan="7" class='h4 center'>Alu {{ $resultado->aluno->qms->qms_sigla }} nº <span style="font-size:18px;">{{ $resultado->aluno->numero }}</span> {{ $resultado->aluno->nome_guerra }} - Tu: {{$resultado->aluno->turmaEsa->turma}}</td>
                            </tr>
                            <tr><!-- linha 2 -->
                                <td rowspan="4">
                                    <div class="row" style="margin:auto">
                                        <div class="col imagem-curso"  style="padding: 30px;width: 70px;"></div>
                                        <div class="col numeracao">n°</div>
                                    </div>
                                    <div style="padding-left: 5px; padding-top: 20px">Data: ___/___/{{$resultado->esaAvaliacoes->esadisciplinas->qms->escolhaQms->anoFormacao->ano_per_qualificacao}}</div>
                                </td>
                                <td colspan="6" class='h5 center'>
                                    {{$resultado->esaAvaliacoes->getChamadaAbreviada()}} {{$resultado->esaAvaliacoes->nome_avaliacao}} {{ $resultado->esaAvaliacoes->esadisciplinas->nome_disciplina_abrev}}
                                </td>
                            </tr>
                            <tr><!-- Linha 3 -->
                                <td rowspan="2" class='h5 center'>GBM</td>
                                <td colspan="2" class='h5 center'>GBO</td>
                                <td rowspan="2" class='h5 center'>Penalização</td>
                                <td rowspan="2" class='h5 center'>NOTA</td>
                                <td rowspan="2" class='h5 center'>Menção</td>
                                    <tr>
                                        <td class='h5 center'>
                                            Errado
                                        </td>
                                        <td class='h5 center'>
                                            Correto
                                        </td>
                                    </tr>
                            </tr>
                            <tr><!-- linha 4 -->
                                <td class='h5 center'>{{$resultado->esaAvaliacoes->gbm}}</td>
                                <td class='h5 center'>{{ (($resultado->gbo_aluno) ? $resultado->gbo_aluno : '-' ) }}</td>
                                <td class='h5 center'>{{$resultado->gbo_ssaa}}</td>
                                <td class='h5 center'>-</td>
                                <td class='h5 center'>{{ number_format($resultado->nota, 3, ',', '.') }}</td>
                                <td class='h5 center'>{{ App\Models\Mencoes::getMencaoV2($resultado->nota)->mencao }}</td>
                            </tr>
                            <tr><!-- linha 5 -->
                                <td colspan="7" style="padding-left: 5px;"> Recibo:_________________________________________________________</td>
                            </tr>
                        </table>
                    </div>


                    <div class="col pl-4 tabela2" style="padding-right: 0px;">
                        <table  class="table">
                                <tr><!-- linha 1 -->
                                <td colspan="7" class='h5 center' > Dem Indv e Res de Nota Pv - Art 52 das NIAA-CFGS/ESA N6°</td> 
                                </tr>
                        
                                <tr><!-- linha 2 -->
                                    <td>
                                        <div class="imagem-curso " style="padding: 30px;"></div>
                                    </td>
                                    <td colspan="5">
                                        
                                        <div class="h4 col center " style="flex: 4;">Alu {{ $resultado->aluno->qms->qms_sigla }} nº {{ $resultado->aluno->numero }} {{ $resultado->aluno->nome_guerra }}</div>
                                    </td> 
                                </tr>
                        
                                <tr><!-- linha 3 -->
                                    <td colspan="5" class='h5 center'>1° Chm AC Armt, Mun e Tir II</td> 
                                </tr>
                        
                                </tr>
                                <tr><!-- Linha 3 -->
                                    <td rowspan="2" class='h5 center'>GBM</td>
                                    <td colspan="2" class='h5 center'>GBO</td>
                                    <td rowspan="2" class='h5 center'>Penalização</td>
                                    <td rowspan="2" class='h5 center'>NOTA</td>
                                    <td rowspan="2" class='h5 center'>Menção</td>
                                        <tr>
                                            <td class='h5 center'>
                                                Errado
                                            </td>
                                            <td class='h5 center'>
                                                Correto
                                            </td>
                                        </tr>
                                </tr>
                                <tr>
                                    <td class='h5 center'>{{$resultado->esaAvaliacoes->gbm}}</td>
                                    <td class='h5 center'>{{ (($resultado->gbo_aluno) ? $resultado->gbo_aluno : '-' ) }}</td>
                                    <td class='h5 center'>{{$resultado->gbo_ssaa}}</td>
                                    <td class='h5 center'>-</td>
                                    <td class='h5 center'>{{ number_format($resultado->nota, 3, ',', '.') }}</td>
                                    <td class='h5 center'>{{ App\Models\Mencoes::getMencaoV2($resultado->nota)->mencao }}</td>
                                </tr>
                        
                        </table>
                    </div>
                </div>
                @php
                    $i++;
                @endphp
            @endif
            
            @if($i == 5)
                @php
                    $i = 0;
                @endphp
                <!--<div style="text-align: center;">
                    <img src="{{ route('gaviao.ajax.relatorios.assinatura.mostrar', $assinatura->id) }}" alt="Image" width="auto" height="100"/>
                </div>-->
                <div class="break"></div>
            @endif
        @endforeach
    </div>
    @endforeach

    <div style="text-align: center;">
        <img src="{{ route('gaviao.ajax.relatorios.assinatura.mostrar', $assinatura->id) }}" alt="Image" width="auto" height="100"/>
    </div>
</div>
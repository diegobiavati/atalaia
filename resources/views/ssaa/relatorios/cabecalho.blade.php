<style>
    
    div.imagem-curso {
        background: url('{{asset($esaAvaliacoes->esadisciplinas->qms->qmsMatriz->img_ssaa)}}') no-repeat center center;
        background-size: contain;
        /*background-size: 120px;
        background-position: left center;*/
    }

    div span.title {
        font-size:20px;
    }

</style>
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
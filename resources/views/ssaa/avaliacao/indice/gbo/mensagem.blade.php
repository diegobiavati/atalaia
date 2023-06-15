<div class="row" style="background-color: #d33e3e;border-radius: 5px;">
    <div class="col-sm" style="height: 120px;text-align: center;line-height:5;">
        <span style="color:white; font-weight: bold;font-size: x-large;text-shadow: black 4px 4px 4px;">{{$mensagem}}</span>
    </div>
</div>

<script>
    $('.container-lancamento-gbo .quadro-soma-gbo #gbo').text('{{ $gbo }}');

    var meuIntervalo = setInterval(() => {
        //Ativa o gatilho para o primeiro item...
        $('div#lancamento-gbo .container-lancamento-gbo .container-header select[name="turmaID"]').change();
        clearInterval(meuIntervalo);
    }, 2000);
</script>
<form id="submit-relatorio" action="{{$rota}}" method="post" target="_blank">
    <input type="hidden" name="ano_formacao_id" value="{{$anoFormacao->id}}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div style="margin-top: 24px;">
        <button id="submit-relatorio" type="button" class="btn btn-primary" onclick="$('form#submit-relatorio').submit();">Visualizar</button>
    </div>
</form>
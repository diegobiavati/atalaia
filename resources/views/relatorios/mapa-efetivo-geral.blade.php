<form id="submit-relatorio" action="{{route($rota)}}" method="get" target="_blank">
    <input type="hidden" name="ano_formacao_id" value="{{$anoFormacao->id or old('ano_formacao_id')}}">
    
    <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
        <h4 style="text-align: center; margin-bottom: 12px;">TIPO DE RELAÇÃO</h4>
        <div class="custom-control custom-radio" style="display: inline-block; margin: 0 10px 12px 0;">
            <input type="radio" class="custom-control-input" id="relacaoHtml" name="relacao" value="html" checked />
            <label class="custom-control-label" for="relacaoHtml">HTML</label>
        </div>

        <div class="custom-control custom-radio" style="display: inline-block; margin: 0 10px 12px 0;">
            <input type="radio" class="custom-control-input" id="relacaoExcel" name="relacao" value="excel"/>
            <label class="custom-control-label" for="relacaoExcel">EXCEL</label>
        </div>
    </div>

    <div style="margin-top: 24px;">
        <button id="submit-relatorio" type="button" class="btn btn-primary" onclick="$('form#submit-relatorio').submit();">Gerar Relação</button>
    </div>
</form>
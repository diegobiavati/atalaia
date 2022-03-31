<form id="submit-relatorio" action="{{route('relatorios.analise_parcial_disciplinas')}}" method="get" target="_blank">
    <input type="hidden" name="ano_formacao_id" value="{{$ano_formacao_id}}">

    <div style="margin-top: 24px;"></div>
    @include('ajax.componenteSelectUeteCurso')

    <div style="margin-top: 24px;">';
        <select name="disciplinaID" class="custom-select required_to_show_button">
            <option value="0" selected>Selecione uma disciplina</option>
            @foreach($disciplinas as $disciplina)
                <option value="{{$disciplina->id}}">{{$disciplina->nome_disciplina}}</option>
            @endforeach
            
            <option value="taf">TESTE DE APTIDÃO FÍSICA</option>
        </select>
    </div>

    <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
        <h4 style="text-align: center; margin-bottom: 12px;">TIPO DE VISUALIZAÇÃO</h4>
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
        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$('form#submit-relatorio').submit();">Visualizar relação</button>
    </div>
</form>
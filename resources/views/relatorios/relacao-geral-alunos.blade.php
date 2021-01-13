
<form id="submit-relatorio" action="{{route('relatorios.relacao_alunos_prontos')}}" method="get" target="_blank">
    <input type="hidden" name="ano_formacao_id" value="{{$anoFormacao->id or old('ano_formacao_id')}}">

    @include('ajax.componenteSelectUeteCurso')
    
    <div style="text-align: right; margin-top: 12px;">
        <a class="marcar no-style" href="javascript: void(0);" onclick="$('input.custom-control-input').prop('checked', true); $('a.marcar').hide(); $('a.desmarcar').show();">
            Marcar todas
        </a>
        <a class="desmarcar no-style"" href=" javascript: void(0);" onclick="$('input.custom-control-input').prop('checked', false); $('a.marcar').show(); $('a.desmarcar').hide();" style="display: none;">
            Desmarcar todas
        </a>
    </div>

    <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
        <h4 style="text-align: center; margin-bottom: 12px;">SEGMENTO</h4>
        <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
            <input type="checkbox" class="custom-control-input segmento" id="segmento_M" name="segmento[]" value="M" />
            <label class="custom-control-label" for="segmento_M">Masculino</label>
        </div>
        @if(!session()->has('login.qmsID'))
        <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
            <input type="checkbox" class="custom-control-input segmento" id="segmento_F" name="segmento[]" value="F" />
            <label class="custom-control-label" for="segmento_F">Feminino</label>
        </div>
        @endif
    </div>

    @if(isset($areas))
        <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
            <h4 style="text-align: center; margin-bottom: 12px;">AREA</h4>

            @foreach ($areas as $area)
            <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
                <input type="checkbox" class="custom-control-input" id="{{'area_'.$area->id}}" name="areas[]" value={{$area->id}} />
                <label class="custom-control-label" for="{{'area_'.$area->id}}">{{$area->area}}</label>
            </div>
            @endforeach
        </div>
    @endif
    
    <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
        <h4 style="text-align: center; margin-bottom: 12px;">CONTENDO OS CAMPOS</h4>

        @foreach ($colunas as $coluna)
        <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
            <input type="checkbox" class="custom-control-input" id="{{$coluna->field}}" name="{{$coluna->field}}" value="1" />
            <label class="custom-control-label" for="{{$coluna->field}}">{{$coluna->description}}</label>
        </div>
        @endforeach
    </div>

    <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
        <h4 style="text-align: center; margin-bottom: 12px;">CONTENDO OS ADICIONAIS</h4>
        <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
            <input type="checkbox" class="custom-control-input" id="ordem" name="ordem" value="1" />
            <label class="custom-control-label" for="ordem">Ordem</label>
        </div>

        <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
            <input type="checkbox" class="custom-control-input" id="alteracao" name="alteracao" value="1" />
            <label class="custom-control-label" for="alteracao">Alteração</label>
        </div>

        <div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
            <input type="checkbox" class="custom-control-input" id="ciente" name="ciente" value="1" />
            <label class="custom-control-label" for="ciente">Ciente</label>
        </div>
    </div>

    <div style="width: 90%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
        <h4 style="text-align: center; margin-bottom: 12px;">TIPO DE RELAÇÃO</h4>
        <div class="custom-control custom-radio" style="display: inline-block; margin: 0 10px 12px 0;">
            <input type="radio" class="custom-control-input" id="relacaoHtml" name="relacao" value="html" checked/>
            <label class="custom-control-label" for="relacaoHtml">HTML</label>
        </div>

        <div class="custom-control custom-radio" style="display: inline-block; margin: 0 10px 12px 0;">
            <input type="radio" class="custom-control-input" id="relacaoExcel" name="relacao" value="excel"/>
            <label class="custom-control-label" for="relacaoExcel">EXCEL</label>
        </div>
    </div>

    <div style="margin-top: 24px;">
        <button id="submit-relatorio" type="button" class="btn btn-primary" style="display: none;" onclick="$('form#submit-relatorio').submit();">Gerar Relação</button>
    </div>
</form>
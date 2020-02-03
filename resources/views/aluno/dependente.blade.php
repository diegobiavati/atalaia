<div id="toClone" class="toClone">
    <input type="hidden" name="id_dependente[]" value="{{ $dependente->id or old('id') }}">
    <div class="divImplantarAluno" style="width: 15%;border-bottom:none;">
        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
        <label class="labelDescricao">Parentesco</label>
        <select class="custom-select" name="id_parentesco[]" style="margin-top:5px;">
            <option value="0" disabled selected hidden>Parentesco</option>
            @foreach ($situacaoParentescos as $situacaoParentesco)
            <option value={{$situacaoParentesco->id}} {{ (isset($dependente) && $situacaoParentesco->id == $dependente->id_parentesco) ? 'selected' : ''}}>{{$situacaoParentesco->parentesco}}</option>
            @endforeach
        </select>
    </div>
    <div class="divImplantarAluno" style="margin-left:20px;width:25%;">
        <i class="ion-ios-person" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
        <label class="labelDescricao">Nome Dependente</label>
        <input class="no-style" name="dep_nome_completo[]" type="text" value="{{$dependente->dep_nome_completo or old('dep_nome_completo')}}" maxlength="255" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;" />
    </div>
    <div class="divImplantarAluno" style="margin-left:20px;width:10%;">
        <i class="ion-android-calendar" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
        <label class="labelDescricao" style="font-size: 11px;">Data Nascimento</label>
        <input class="no-style data_mask" name="dep_data_nascimento[]" value="{{ isset($dependente->dep_data_nascimento) ? \Carbon\Carbon::parse($dependente->dep_data_nascimento)->format('d/m/Y') : null}}" type="text" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;" />
    </div>
    <div class="divImplantarAluno" style="margin-left:20px;width:20%;">
        <i class="ion-location" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
        <label class="labelDescricao">Naturalidade</label>
        <input class="no-style" name="dep_naturalidade[]" value="{{$dependente->dep_naturalidade or old('dep_naturalidade')}}" type="text" maxlength="100" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;" />
    </div>

    <div class="clear"></div>
    <div class="divImplantarAluno" style="width:40%;">
        <i class="ion-compose" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
        <label class="labelDescricao">Endereço</label>
        <input class="no-style" name="dep_endereco[]" value="{{$dependente->dep_endereco or old('dep_endereco')}}" type="text" maxlength="100" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;" />
    </div>
    <div class="divImplantarAluno" style="border-bottom:none;width: 17%;margin-left:20px;">
        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
        <label class="labelDescricao">Possui Emprego</label>
        <select class="custom-select" name="dep_trabalho_ativo[]" style="margin-top:5px;">
            <option value="0" disabled selected hidden>Possui Emprego</option>
            <option value="S" {{ (isset($dependente) && $dependente->dep_trabalho_ativo == 'S') ? 'selected' : ''}}>Sim</option>
            <option value="N" {{ (isset($dependente) && $dependente->dep_trabalho_ativo == 'N') ? 'selected' : ''}}>Não</option>
        </select>
    </div>
    <div class="divImplantarAluno" style="border-bottom:none;width: 25%; margin-left:20px;">
        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
        <label class="labelDescricao">Escolaridade</label>
        <select class="custom-select" name="dep_id_escolaridade[]" style="margin-top:5px;">
            <option value="0" disabled selected hidden>Escolaridade</option>
            @foreach ($situacaoEscolaridades as $situacaoEscolaridade)
            <option value={{$situacaoEscolaridade->id}} {{ (isset($dependente) && $situacaoEscolaridade->id == $dependente->dep_id_escolaridade) ? 'selected' : ''}}>{{$situacaoEscolaridade->escolaridade}}</option>
            @endforeach
        </select>
    </div>

    <div class="clear"></div>
    <div class="divImplantarAluno" style="width: 30%; border-bottom:none;">
        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
        <label class="labelDescricao">Profissão</label>
        <select class="custom-select" name="dep_id_profissao[]" style="margin-top:5px;">
            <option value="0" disabled selected hidden>Profissão</option>
            @foreach ($profissaos as $profissao)
            <option value={{$profissao->id}} {{ (isset($dependente) && $profissao->id == $dependente->dep_id_profissao) ? 'selected' : ''}}>{{$profissao->profissao . ((trim($profissao->militar_postograd) != '') ? ' - ' . $profissao->militar_postograd : '')}}</option>
            @endforeach
        </select>
    </div>
    <div class="divImplantarAluno" style="margin-left:20px;width:20%;">
        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
        <label class="labelDescricao">Função de Trabalho</label>
        <input class="no-style" name="dep_trabalho_funcao[]" value="{{$dependente->dep_trabalho_funcao or old('dep_trabalho_funcao')}}" type="text" maxlength="255" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;" />
    </div>
    <div class="divImplantarAluno" style="margin-left:20px;width:20%;">
        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
        <label class="labelDescricao">BI de Publicação</label>
        <input class="no-style" name="dep_bi_publicacao[]" value="{{$dependente->dep_bi_publicacao or old('dep_bi_publicacao')}}" type="text" maxlength="255" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;" />
    </div>
</div>
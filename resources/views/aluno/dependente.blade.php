<div id="toClone" class="toClone dependente-box">

    <input type="hidden" name="id_dependente[]" value="{{ $dependente->id ?? old('id') }}">

    <div class="divImplantarAluno">
        <label class="labelDescricao">Parentesco</label>
        <select class="custom-select" name="id_parentesco[]">
            <option value="" disabled selected>Parentesco</option>
            @foreach ($situacaoParentescos as $situacaoParentesco)
            <option value="{{ $situacaoParentesco->id }}"
                {{ (isset($dependente) && $situacaoParentesco->id == $dependente->id_parentesco) ? 'selected' : '' }}>
                {{ $situacaoParentesco->parentesco }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="divImplantarAluno">
        <label class="labelDescricao">Nome Dependente</label>
        <input class="form-control text-uppercase"
            name="dep_nome_completo[]"
            value="{{ $dependente->dep_nome_completo ?? old('dep_nome_completo') }}"
            type="text"
            maxlength="255">
    </div>

    <div class="divImplantarAluno">
        <label class="labelDescricao">Data Nascimento</label>
        <input class="form-control data_mask"
            name="dep_data_nascimento[]"
            value="{{ isset($dependente->dep_data_nascimento) ? \Carbon\Carbon::parse($dependente->dep_data_nascimento)->format('d/m/Y') : null }}"
            type="text">
    </div>

    <div class="divImplantarAluno">
        <label class="labelDescricao">Naturalidade</label>
        <input class="form-control"
            name="dep_naturalidade[]"
            value="{{ $dependente->dep_naturalidade ?? old('dep_naturalidade') }}"
            type="text">
    </div>

    <div class="divImplantarAluno">
        <label class="labelDescricao">Endereço</label>
        <input class="form-control"
            name="dep_endereco[]"
            value="{{ $dependente->dep_endereco ?? old('dep_endereco') }}"
            type="text">
    </div>

    <div class="divImplantarAluno">
        <label class="labelDescricao">Possui Emprego</label>
        <select class="custom-select" name="dep_trabalho_ativo[]">
            <option value="" disabled selected>Selecione</option>
            <option value="S" {{ (isset($dependente) && $dependente->dep_trabalho_ativo == 'S') ? 'selected' : '' }}>Sim</option>
            <option value="N" {{ (isset($dependente) && $dependente->dep_trabalho_ativo == 'N') ? 'selected' : '' }}>Não</option>
        </select>
    </div>

    <div class="divImplantarAluno">
        <label class="labelDescricao">Escolaridade</label>
        <select class="custom-select" name="dep_id_escolaridade[]">
            <option value="" disabled selected>Escolaridade</option>
            @foreach ($situacaoEscolaridades as $situacaoEscolaridade)
            <option value="{{ $situacaoEscolaridade->id }}"
                {{ (isset($dependente) && $situacaoEscolaridade->id == $dependente->dep_id_escolaridade) ? 'selected' : '' }}>
                {{ $situacaoEscolaridade->escolaridade }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="divImplantarAluno">
        <label class="labelDescricao">Profissão</label>
        <select class="custom-select" name="dep_id_profissao[]">
            <option value="" disabled selected>Profissão</option>
            @foreach ($profissaos as $profissao)
            <option value="{{ $profissao->id }}"
                {{ (isset($dependente) && $profissao->id == $dependente->dep_id_profissao) ? 'selected' : '' }}>
                {{ $profissao->profissao }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="divImplantarAluno">
        <label class="labelDescricao">Função de Trabalho</label>
        <input class="form-control"
            name="dep_trabalho_funcao[]"
            value="{{ $dependente->dep_trabalho_funcao ?? old('dep_trabalho_funcao') }}"
            type="text">
    </div>

    <div class="divImplantarAluno">
        <label class="labelDescricao">BI de Publicação</label>
        <input class="form-control"
            name="dep_bi_publicacao[]"
            value="{{ $dependente->dep_bi_publicacao ?? old('dep_bi_publicacao') }}"
            type="text">
    </div>

</div>
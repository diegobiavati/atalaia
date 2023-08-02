@if(isset($disciplinas))
    <select name="disciplinaID" class="selectpicker custom-select" {{ (isset($readOnly)) ? $readOnly : null }}>
        <option value="0" disabled selected hidden>Selecione uma Disciplina</option>
        @foreach ($disciplinas as $disciplina)
            @php
                $id = (isset($criptografia) && ($criptografia) ? encrypt('disciplina_'.$disciplina->id) : $disciplina->id);
            @endphp
        <option {{ ((isset($disciplinaSelecionada) && $disciplina->id == $disciplinaSelecionada->id) ? 'selected' : '') }}
                         value={{$id}}>{{ $disciplina->nome_disciplina_abrev }}</option>
        @endforeach
    </select>
@else
    <select name="disciplinaID" class="selectpicker custom-select">
        <option value="0" disabled selected hidden>Selecione uma Disciplina</option>
    </select>
@endif
<select name="turmaID" class="custom-select required_to_show_button" {{ (isset($readOnly)) ? $readOnly : null }}>
    <option value="0" disabled selected hidden>Selecione uma Turma</option>
    @if($ownauthcontroller->PermissaoCheck(1))
    <option value="todas_turmas">TODAS AS TURMAS</option>
    @endif
    @foreach ($turmas as $turma)
    @php
        $id = (($criptografia) ? encrypt('turma_'.$turma->id) : $turma->id);
    @endphp
    <option {{ ((isset($turmaSelecionada) && $turma->id == $turmaSelecionada->id) ? 'selected' : '') }}
                        value={{$id}}>{{ $turma->turma }}</option>
    @endforeach
</select>
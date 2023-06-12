<select name="alunoID" class="selectpicker custom-select required_to_show_button" {{ (isset($readOnly)) ? $readOnly : null }}>
    <option value="0" disabled selected hidden>Selecione um Aluno</option>
    @if($ownauthcontroller->PermissaoCheck(1))
    <option value="todos_alunos">TODOS OS ALUNOS</option>
    @endif
    @foreach ($alunos as $aluno)
    @php
        $id = (($criptografia) ? encrypt('aluno_'.$aluno->id) : $aluno->id);
    @endphp
    <option {{ ((isset($alunoSelecionado) && $aluno->id == $alunoSelecionado->id) ? 'selected' : '') }}
                        value={{$id}}>{{ $aluno->numero}} - <font style="font-weight:bold;">{{ $aluno->nome_guerra }}</font></option>
    @endforeach
</select>
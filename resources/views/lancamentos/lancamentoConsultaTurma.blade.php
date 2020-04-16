<option value="0" disabled selected hidden>Selecione uma Turma</option>
@foreach ($turmas as $turma)
    <option value={{$turma->id}} {{ (isset($lancamentoFo) && ($lancamentoFo->aluno->turma->id == $turma->id) ? 'selected': '') }}>{{ $turma->turma }}</option>
@endforeach
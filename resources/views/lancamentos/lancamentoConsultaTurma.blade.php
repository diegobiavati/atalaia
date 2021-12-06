<option value="0" disabled selected hidden>Selecione uma Turma</option>

    @foreach ($turmas as $turma)
        @if(isset($turma->qms_matriz_id))
            <option value={{$turma->id}} {{ (isset($lancamentoFo) && ($lancamentoFo->aluno->turmaEsa->id == $turma->id) ? 'selected': '') }}>{{ $turma->turma }}</option>
        @else
            <option value={{$turma->id}} {{ (isset($lancamentoFo) && ($lancamentoFo->aluno->turma->id == $turma->id) ? 'selected': '') }}>{{ $turma->turma }}</option>
        @endif
    @endforeach
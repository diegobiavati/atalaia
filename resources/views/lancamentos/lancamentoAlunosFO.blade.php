<div style="width: 100%; margin: 22px auto; text-align: center; border-bottom: 0px solid #ccc;">
    <div style="margin-bottom: 15px;">
        @if(count($alunosTurma) > 0)
        <div>
            <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(250, 157, 110);">Alunos</label>
        </div>
        <div>
            @foreach($alunosTurma as $aluno)
            <div class="custom-control custom-checkbox custom-control-inline" style="width: 30%;text-align:left;margin-right:0px;">
                <input type="checkbox" class="custom-control-input" id="aluno_{{$aluno->id}}">
                <label class="custom-control-label" for="aluno_{{$aluno->id}}" style='font-weight:bold;'>{{$aluno->nome_guerra}}</label>
            </div>
            @endforeach
        </div>
        @else
        <div>
            <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(255, 0, 0);">Não Existe Alunos Para a OMCT Especificada</label>
        </div>
        @endif
    </div>
</div>
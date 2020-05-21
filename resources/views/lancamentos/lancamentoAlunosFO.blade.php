<div style="width: 100%; margin: 22px auto; text-align: center; border-bottom: 0px solid #ccc;">
    <div style="margin-bottom: 15px;">
        @if(isset($alunosTurma) && count($alunosTurma) > 0)
        <div>
            <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(121, 161, 212);">
                <font style="color:rgb(255, 255, 255);">Alunos</font>
            </label>
        </div>
        <div>
            @foreach($alunosTurma as $aluno)
                @if(!isset($edit))
                    <div class="custom-control custom-checkbox custom-control-inline" style="width: 30%;text-align:left;margin-right:0px;">
                        <input type="checkbox" class="custom-control-input" id="aluno_{{$aluno->id}}" name="aluno_{{$aluno->id}}">
                        <label class="custom-control-label" for="aluno_{{$aluno->id}}" style='font-weight:bold;'>{{$aluno->numero.' - '.$aluno->nome_guerra}}</label>
                    </div>
                @else
                    <div>
                        <label class="custom-control-label" style='font-weight:bold;font-size: x-large;'>{{$aluno->numero.' - '.$aluno->nome_guerra}}</label>
                    </div>
                @endif
            @endforeach
        </div>
        @else
        <div>
            <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(255, 0, 0);">
                <font style="color:rgb(255, 255, 255);">Não Existe Alunos Para a UETE Especificada</font>
            </label>
        </div>
        @endif
    </div>
</div>
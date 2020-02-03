<div style="float: right">
    <div class="box-pesquisar-in-card-title" style="display: inline-block; padding:0; height: 36px;">
        <input id="input_busca_rapida_aluno" class="pesquisar-in-card-title" type="text" placeholder="Busca rápida" list="alunos_list" style="width: 300px;" />
        <datalist id="alunos_list">
            @foreach($alunos['list'] as $aluno)
            @if(!$ownauthcontroller->PermissaoCheck(1))
            @if($aluno->omcts_id == session()->get('login.omctID'))
            <option value="{{$aluno->id.' - '.$aluno->nome_completo}}" style="background-color: #ccc;">
                <a href"#">Al {{$aluno->numero}} {{$aluno->nome_completo}} {{$aluno->omct->sigla_omct or ''}}</a>
            </option>
            @endif
            @else
            <option value="{{$aluno->id.' - '.$aluno->nome_completo}}" style="background-color: #ccc;">
                <a href"#">Al {{$aluno->numero}} {{$aluno->nome_completo}} {{$aluno->omct->sigla_omct or ''}}</a>
            </option>
            @endif
            @endforeach
        </datalist>
        <a class="no-style" href="javascript: void(0);">
            <i class="ion-android-search" style="color: #696969;"></i>
        </a>
    </div>
</div>
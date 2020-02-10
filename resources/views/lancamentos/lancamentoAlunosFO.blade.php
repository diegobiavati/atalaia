<div style="width: 40%; margin: 22px auto; text-align: center; border-bottom: 0px solid #ccc;">
    <div style="margin-bottom: 15px;">
        <div>
            <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(250, 235, 215);">Uete</label>
        </div>
        <div>
            @foreach($conteudoAtitudinal as $conteudo)
            <div class="custom-control custom-checkbox custom-control-inline" style="width: 30%;text-align:left;margin-right:0px">
                <input type="checkbox" class="custom-control-input" id="atitudinal{{$conteudo->id}}">
                <label class="custom-control-label" for="atitudinal{{$conteudo->id}}">{{$conteudo->descricao}}</label>
            </div>
            @endforeach
        </div>
    </div>
</div>
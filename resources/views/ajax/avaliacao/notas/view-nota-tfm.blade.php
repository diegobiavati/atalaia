<div>
    <span>
        <span style="color: #3B913B;"><b>Nota</span></b><br />

        <span style="font-family: Tahoma; font-weight: bold;">
            {{ $nota[$aluno->id]['nota_tfm']}}
        </span>

    </span>
    </span><br />
</div>
<div>
    <a href="javascript: void(0);" class="no-style" onclick="editarTfm({{$avaliacao->id}}, {{$aluno->id}});">
        <i class="ion-android-create" style="color: #0B610B; font-size: 18px;"></i>
    </a>
</div>
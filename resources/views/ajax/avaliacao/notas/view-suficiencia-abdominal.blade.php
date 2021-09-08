@if($avaliacao->tfm_abdominal == 'S')
<div>
    <span>
        <span style="color: #3B913B;"><b>Abdominal</span></b><br />

        @switch($nota[$aluno->id]['suficiencia_abdominal'])
        @case('S')
        <span style="font-family: Tahoma; font-weight: bold;">
            Suficiente
        </span>
        @break
        @case('NS')
        <span style="font-family: Tahoma; color: #F00; font-weight: bold;">
            Não Suficiente
        </span>
        @break
        @endswitch

    </span>
    </span><br />
</div>
<div>
    <a href="javascript: void(0);" class="no-style" onclick="editarAbdominal({{$avaliacao->id}}, {{$aluno->id}});">
        <i class="ion-android-create" style="color: #0B610B; font-size: 18px;"></i>
    </a>
</div>

@else
<div style="float: left;">
    Desenvolver
</div>
@endif
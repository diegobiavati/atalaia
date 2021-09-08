<div style="display: inline-block; width: 120px;">
    <select class="custom-select" name="suficiencia_abdominal" onchange="lancarAbdominal({{$avaliacao->id}}, {{$aluno->id}}, this);">
        <option value="0" disabled selected hidden>Selecione</option>
        <option value="S">Suficiente</option>
        <option value="NS"> Não suficiente</option>
    </select>
</div>
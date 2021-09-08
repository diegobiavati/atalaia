<div style="display: inline-block; width: 120px;">
    <select class="custom-select" style="font-weight: bold;" name="suficiencia_abdominal" onchange="lancarTfm({{$avaliacao->id}}, {{$aluno->id}}, this);">
        <option value="0" disabled selected hidden>Selecione</option>
        <option value="0.0">0.0</option>
        <option value="0.5">0.5</option>
        <option value="1.0">1.0</option>
        <option value="1.5">1.5</option>
        <option value="2.0">2.0</option>
        <option value="2.5">2.5</option>
        <option value="3.0">3.0</option>
        <option value="3.5">3.5</option>
        <option value="4.0">4.0</option>
        <option value="4.5">4.5</option>
        <option value="5.0">5.0</option>
        <option value="5.5">5.5</option>
        <option value="6.0">6.0</option>
        <option value="6.5">6.5</option>
        <option value="7.0">7.0</option>
        <option value="7.5">7.5</option>
        <option value="8.0">8.0</option>
        <option value="8.5">8.5</option>
        <option value="9.0">9.0</option>
        <option value="9.5">9.5</option>
        <option value="10.0">10.0</option>
    </select>
</div>
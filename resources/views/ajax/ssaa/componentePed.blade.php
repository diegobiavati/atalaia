@if(isset($esaPedAno))
<select name="pedID" class="selectpicker custom-select" {{ (isset($readOnly)) ? $readOnly : null }}>
    <option value="0" disabled selected hidden>Selecione um PED</option>
    @foreach ($esaPedAno as $ped)
    @php
    $id = (isset($criptografia) && ($criptografia) ? encrypt('ped_'.$ped->id) : $ped->id);
    @endphp
    <option {{ ((isset($pedSelecionado) && $ped->id == $pedSelecionado->id) ? 'selected' : '') }} value={{$id}}>
        {{ $ped->ano }} / {{ $ped->tipo }}
    </option>
    @endforeach
</select>
@else
<select name="pedID" class="selectpicker custom-select">
    <option value="0" disabled selected hidden>Selecione um PED</option>
</select>
@endif
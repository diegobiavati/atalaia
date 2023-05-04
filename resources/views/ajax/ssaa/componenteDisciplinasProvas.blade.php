@if(isset($provas))
    <select name="provasID" class="selectpicker custom-select">
        <option value="0" disabled selected hidden>Selecione uma Prova</option>
        @foreach ($provas as $prova)
        <option value={{$prova->id}}>( {{ $prova->nome_avaliacao }} ) {{ $prova->getDescricao() }} - {{ $prova->getChamada() }}</option>
        @endforeach
    </select>
@else
    <select name="provasID" class="selectpicker custom-select">
        <option value="0" disabled selected hidden>Selecione uma Prova</option>
    </select>
@endif
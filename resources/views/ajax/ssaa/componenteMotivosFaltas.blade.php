<style>
    .flex-container {
        display: flex;
    }
</style>

<div id="aluno_{{$hash}}" style="margin: 14px auto;">
    <label><b>{{ $aluno->nome_guerra }}</b></label>
    <div class="flex-container">
        <div style="margin-right: 10px; width: 260px;">
            <select name="id_motivo_{{$hash}}" class="selectpicker custom-select">
                <option value="0" disabled selected hidden>Selecione um Motivo</option>
                @foreach ($motivos as $motivo)
                <option value={{$motivo->id}}> {{ $motivo->descricao }}</option>
                @endforeach
            </select>
        </div>
        <div style="width: 100%;">
            <input type="text" class="form-control" name="motivo_{{$hash}}" placeholder="Informe aqui o motivo da falta.">
        </div>
    </div>
    <div class="clear"></div>
</div>
<script>
    $('div[id^="aluno_"] select[name^="motivos_"]').on('change', function(evt){
        evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

        console.log(evt.target.name, evt.target.value);
    });
</script>
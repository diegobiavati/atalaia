
<!--Select2-->
<!--<script src="/js/select2/select2.min.js"></script>
<link href="/css/select2.min.css" rel="stylesheet"/>

<style>

    .select2-results__option{
        font-style: italic;
        font-weight:bold;
        padding: 6px;
        user-select: none;
        -webkit-user-select: none;
    }

</style>
<select name="napd" class="custom-select js-basic-single">
    <option value="0" disabled selected hidden>Selecione uma NASE</option>
    
    @foreach ($napds as $napd)
    <option value={{$napd->id}} {{ (isset($lancamentoFo->aluno) && $napd->id == $lancamentoFo->napd_id)  ? 'selected': ''}}>{{ $napd->getDescricao() }}</option>
    @endforeach
</select>

<script>

    $(document).ready(function() {

        $('.js-basic-single').select2({
            minimumInputLength: 2,
            maximumInputLength: 10,
            minimumResultsForSearch: 10,
            dropdownParent: $('#divEnquadramento')
        });

    });

</script>-->
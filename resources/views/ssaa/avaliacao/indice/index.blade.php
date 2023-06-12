
<!-- Editablegrid -->
<script src="{{asset('js/editablegrid/editablegrid.js')}}"></script>
<script src="{{asset('js/editablegrid/editablegrid_renderers.js')}}"></script>   
<script src="{{asset('js/editablegrid/editablegrid_editors.js')}}"></script>   
<script src="{{asset('js/editablegrid/editablegrid_validators.js')}}"></script>   
<script src="{{asset('js/editablegrid/editablegrid_utils.js')}}"></script>   

<!--<script src="{{asset('js/jquery/autocomplete.js')}}"></script>   -->

<link rel="stylesheet" href="{{asset('css/editablegrid/editablegrid.css')}}" type="text/css" media="screen">
<link rel="stylesheet" href="{{asset('css/editablegrid/indicesgrid.css')}}" type="text/css" media="screen">
<!--<link rel="stylesheet" href="{{asset('css/autocomplete.css')}}" type="text/css" media="screen">-->
<script>
    /**
     updateCellValue calls the PHP script that will update the database. 
    */
    function updateCellValue(editableGrid, rowIndex, columnIndex, oldValue, newValue, row, onResponse)
    {   
        var dataJson = editableGrid.getRowValues(rowIndex);
        dataJson._token = '{{ csrf_token() }}';
        dataJson.oldValue = editableGrid.getColumnType(columnIndex) == "boolean" ? (oldValue ? 1 : 0) : oldValue;
        dataJson.newvalue = editableGrid.getColumnType(columnIndex) == "boolean" ? (newValue ? 1 : 0) : newValue;
        dataJson.colname = editableGrid.getColumnName(columnIndex);

        $.ajax({
            url: '{{isset($urlIndice) ? $urlIndice : null}}/{{encrypt(date("H:i:s"))}}',
            type: 'PUT',
            dataType: "html",
            data: dataJson,
            success: function (response) 
            { 
                response = jQuery.parseJSON(response);
                // reset old value if failed then highlight row
                var success = onResponse ? onResponse(response.status) : (response.status == "success" || !isNaN(parseInt(response.status)));
                if (!success) editableGrid.setValueAt(rowIndex, columnIndex, oldValue);
                highlight(row.id, success ? "ok" : "error");

                if (success) editableGrid.fetchGrid('{{isset($urlIndice) ? $urlIndice."/carrega-indices" : null}}/'+ response.identificador);
            },
            error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + textStatus); },
            async: true
        });
        
    }

    function getGBM(){
        $.ajax({
            url: '{{isset($urlGBM) ? $urlGBM : null}}',
            type: 'GET',
            dataType: "html",
            success: function (response) { 
                response = jQuery.parseJSON(response);
                if (response.success) $('div .row .col-sm .quadro-gbm #gbm').text(response.resultado_gbm);
            },
            error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + textStatus); },
            async: true
        });  
    }

</script>
<script src="{{asset('js/editablegrid/indicesNotas/indicesgrid.js')}}"></script>

<!-- Fim Editablegrid -->

<div>
    <div id="indice_dificuldades">
        {!! App\Http\Controllers\Utilitarios\FuncoesController::retornaBotaoAnoFormacao((isset($ano_formacao) ? $ano_formacao : null)) !!}
        <div class="container_indice_dificuldades"></div>
    </div>
  
  @include('layouts.footer_ssaa')

</div>
<script>
    $(document).ready(function() {
        $('div#indice_dificuldades .btn.btn-secondary input[name="ano_formacao"]').change(function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

            url = '{{$urlIndice}}/' + evt.target.value;
            
            $('div.container_indice_dificuldades').empty();

            //Carrega a combo com Uetes ou Cursos
            carregaContainerIndice(url);
        });
    });

    function carregaContainerIndice(url) {
        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() {
                $('div.container_indice_dificuldades').empty();
            },
            success: function(data) {
                $('div.container_indice_dificuldades').html(data);
            },
            error: function(jqxhr) {
                $('div.container_indice_dificuldades').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
            }
        });
    }

    //carrega o container com data selecionada padrão
    carregaContainerIndice( '{{$urlIndice}}/' + $('div#indice_dificuldades .btn.btn-secondary.active input[name="ano_formacao"]').val());
</script>
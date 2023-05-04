<style>
    /*div .row .col-sm {
        border: solid black 1px;
        margin: auto;
    }*/

    div .row .col-sm .quadro-gbm{
        border: solid #dcfb05 1px;
        margin: auto;
        background-color: #e3e68e;
    }

    .message_feedback{
        background-color: {{ isset($cursoSelecionado) ? App\Http\Controllers\Utilitarios\FuncoesController::getQmsColor($cursoSelecionado->qms_matriz_id)->backgroundColor : '#f00' }};
        color: white;
        padding: 5px;
    }

    th.editablegrid-id_esa_avaliacoes, td.editablegrid-id_esa_avaliacoes{
        display: none;
    }

    div .toolbar{
        margin-bottom: 10px;
    }

    .col-sm {
        height: 40px;
    }

    .container {
        min-width: 16rem;
        max-width: 70rem;
        margin-right: auto;
        margin-left: auto;
        margin-top: 35px;
    }

    .container-header {
        padding: 20px;
        height: 10rem;
        margin-bottom: 0.5rem;
        background-color: #c2c0c0;
        border-radius: 0.25rem;
    }

    .container-side {
        float: right;
        height: 8rem;
        background-color: #80bdff;
        border-radius: 0.25rem;
    }

    .container-body {
        /*height: 8rem;*/
        /*margin-right: 4.5rem;*/
        background-color: #ffffff52;
        border-radius: 0.25rem;
    }

    div#imagem_curso {
        background: url("{{ isset($cursoSelecionado) ? asset($cursoSelecionado->qmsMatriz->img_ssaa) : asset('/images/logo_esa.png') }}") no-repeat center center;
        background-size: contain; 
        height: 100px;
    }

</style>
<div class="container">
    <div class="alert alertas-indices-dificuldades" role="alert"></div>
    
    <div class="container-header">
        <div class="row" style="height:105px;">
            <div class="col-sm" id='imagem_curso' style="height:105px;"></div>
            <div class="col-sm" style="flex: 5; height:105px;">
                <div class="row" style="padding: 5px;">
                    <div class="col-sm" style="max-width: 65px; line-height: 3;font-weight: bold;">Curso:</div>
                    <div class="col-sm" style="max-width: 220px;">
                        @include('ajax.componenteSelectUeteCurso')
                    </div>
                    <div class="col-sm" style="max-width: 90px; line-height: 3;font-weight: bold;">Disciplina:</div>
                    <div class="col-sm" style="max-width: 300px;">
                        @include('ajax.ssaa.componenteDisciplinas')
                    </div>
                </div>
                <div class="row" style="padding: 5px;">
                    <div class="col-sm" style="max-width: 65px; line-height: 3;font-weight: bold;">Prova:</div>
                    <div class="col-sm" id="container-prova">
                        @include('ajax.ssaa.componenteDisciplinasProvas')
                    </div>
                </div>
            </div>
            <div class="col-sm" style="text-align:center; height:150px;">
                <div class="col-sm" style="line-height: 3;font-weight: bold;margin-bottom: -16px;">Processo nº</div>
                <div class="col-sm" id="nr_processo" style="font-size: xx-large;margin-bottom: 5px;"></div>

                <div class="quadro-gbm">
                    <div class="col-sm" style="line-height: 3;font-weight: bold;margin-bottom: -16px;margin-top: -10px;">GBM</div>
                    <div class="col-sm" id="gbm" style="font-size: xx-large;">0</div>
                </div>
                
            </div>
        </div>
        <div class="row">
            <div class="col-sm" id="texto_processo" style="text-align: center;"></div>
        </div>
    </div>
    <div class="container-side">
        <button type="button" class="btn btn-success">Lançamento</button>
    </div>
    <div class="container-body" style="height:auto;">
        <div class="row">
            <div class="col-sm" style="height: auto;">
            
                <!-- Grid -->
                <div id="wrap">
                    <!-- Feedback message zone -->
                    <div id="message"></div>
                    <div id="toolbar" class="toolbar">
                        <!--<input type="text" id="filter" name="filter" placeholder="Filter :type any text here"  />-->
                        <button id="addRow" type="button" class="ion-plus btn btn-primary"> Adicionar item</button>
                    </div>
                    <!--  Number of rows per page and bars in chart -->
                    <!--<div id="pagecontrol">
                        <label for="pagecontrol">Rows per page: </label>
                        <select id="pagesize" name="pagesize">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="25">25</option>
                            <option value="30">30</option>
                            <option value="40">40</option>
                            <option value="50">50</option>
                        </select>
                    </div>-->
                
                    <!-- Grid filter -->
                    <!--<label for="filter">Filter :</label>
                    <input type="text" id="filter">-->
                
                    <!-- Grid contents -->
                    <div id="tablecontent"></div>
                    <!-- [DO NOT DEPLOY] --> 	
                
                    <!-- Paginator control -->
                    <!--<div id="paginator"></div>-->
                
                    <!-- Edition zone (to demonstrate the "fixed" editor mode) -->
                    <div id="edition"></div>
                    
                    <!-- Charts zone -->
                    <!--<div id="barchartcontent"></div>
                    <div id="piechartcontent"></div>-->
                    
                </div>  


            </div>
        </div>
    </div>
</div>
<script>
    
    $(document).ready(function() {

        $('div.alertas-indices-dificuldades').slideUp();

        $('div .container-body').css('display', 'none');
        $('div .container-side').css('display', 'none');

        $('div.container_indice_dificuldades select.custom-select[name="qmsID"]').change(function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

            url = '{{$urlIndiceDisciplinas}}' + evt.target.value;

            $('div.container_indice_dificuldades').empty();
            //Carrega a combo com Uetes ou Cursos
            carregaContainerIndice(url);
        });

        $('div.container_indice_dificuldades select.custom-select[name="disciplinaID"]').change(function(evt) {
            evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

            $.ajax({
                type: 'GET',
                url: '{{isset($urlIndiceDisciplinasProvas) ? $urlIndiceDisciplinasProvas : null}}' + evt.target.value,
                beforeSend: function() {
                    $('div.alertas-indices-dificuldades').slideUp();
                    $('div#container-prova').empty();
                    $('div#nr_processo').empty();
                    $('div#texto_processo').empty();
                    $('div .container-body').css('display', 'none');
                    $('div .container-side').css('display', 'none');
                    editableGrid.onloadJSON("{{asset('gaviao/ajax/indice-dificuldades/carrega-indices')}}/0");
                },
                success: function(data) {
                    
                    $('div#container-prova').html(data);

                    $('div.container_indice_dificuldades select.custom-select[name="provasID"]').change(function(evt) {
                        evt.stopImmediatePropagation(); //Não deixa duplicar os eventos                    

                        var ano = $('div#indice_dificuldades .btn.btn-secondary input[name="ano_formacao"]').filter(':checked').parent().text();
                        $('div#nr_processo').html('<span>'+ evt.target.value +'</span>');
                        $('div#texto_processo').html('<h4><span class="badge badge-info">Processo em Tela: '+ evt.target.value +' / ' + ano +' - ' + evt.target.options[evt.target.selectedIndex].text + '</span></h4>');
                        //Verifica se já existe um processo gerado para a prova...
                        editableGrid.onloadJSON("{{asset('gaviao/ajax/indice-dificuldades/carrega-indices')}}/"+ evt.target.value); 

                        $('div .container-body').css('display', 'block');
                        $('div .container-side').css('display', 'block');

                        $('.container .container-body #toolbar #addRow').click( function()  {
                            //Adiciona Nova Linha...
                            editableGrid.addRow(null, [0]);
                        });
                    });
                }
            });
        });

        EditableGrid.prototype.deleteRow = function(id) 
        {
            var self = this;
            
            if ( confirm('Você tem certeza que vai excluir o Índice ? ' + id )  ) {
                var dataJson = editableGrid.getRowValues(id);
                dataJson._token = '{{ csrf_token() }}';

                $.ajax({
                    url: '{{isset($urlIndice) ? $urlIndice : null}}/'+ id,
                    type: 'DELETE',
                    dataType: "html",
                    data: dataJson,
                    success: function (response) { 
                        response = jQuery.parseJSON(response);

                        var success = (response.status == "success" ? true : false);
                        
                        if (success) {
                            displayMessage(response.response.mensagem, 'message_feedback');
                            editableGrid.fetchGrid('{{isset($urlIndice) ? $urlIndice."/carrega-indices" : null}}/'+ dataJson.id_esa_avaliacoes);
                        }else{
                            highlight(id, success ? "ok" : "error");
                            displayMessage(response.response, 'message_feedback');
                            editableGrid.fetchGrid('{{isset($urlIndice) ? $urlIndice."/carrega-indices" : null}}/'+ dataJson.id_esa_avaliacoes);
                        }
                        if (success) getGBM();
                    },
                    error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + textStatus); },
                    async: true
                });       
            }       
        }; 

    });

</script>
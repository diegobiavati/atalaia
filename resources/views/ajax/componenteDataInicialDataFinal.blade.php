<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/bootstrap-datepicker.pt-BR.min.js"></script>
<link href="/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css"/>

<div style="float: right; width: 93%; margin-top: 4px; padding: 0 0 10px 6px; text-align: center;">
    <i class="ion-calendar" style="font-size: 24px; color: #696969;"></i>
    <input class="no-style data" style="margin-top:10px;margin-left: 10px;border-bottom: 1px solid #ccc;" name="data_inicial" type="text" maxlength="10" autocomplete="off" placeholder="Data Inicial" />
    <label class="labelDescricao"> á</label>
    <input class="no-style data" style="margin-top:10px;margin-left: 10px;border-bottom: 1px solid #ccc;" name="data_final" type="text" maxlength="10" autocomplete="off" placeholder="Data Final" />
</div>

<script>

    $('.data').mask('00/00/0000');

    $(document).ready(function() {

        $('[name="data_inicial"]')
        .datepicker({
            autoclose: true, // It is false, by default
            format: 'dd/mm/yyyy'
        });

        $('[name="data_final"]')
        .datepicker({
            autoclose: true, // It is false, by default
            format: 'dd/mm/yyyy'
        });

    });
</script>
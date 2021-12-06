<?php
$backgroundColor = session()->get('backgroundColor');
?>

<div class="modal-header" style="background-color: {{ $backgroundColor }}">
    <h5 class="modal-title">
        <i class="ion-android-create" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i>
        Cadastro de Período de Diplomação
    </h5>
</div>
<div class="modal-body">
    <div class="alert alert-danger info-diploma-periodo" role="alert"></div>
</div>
<div class="modal-footer" style="text-align: right;">
    <a class="btn btn-info" class="carousel-control-next" href="#carouselControls" role="button" data-slide="next" onclick="toogleContent(this, \'Configurar &raquo;\', \'&laquo; Voltar\');">Configurar vagas &raquo;</a>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        Cancelar
    </button>
    <button type="button" class="btn btn-primary" onclick="">
        Salvar
    </button>
</div>

<script>
  
  $('div.info-diploma-periodo').slideUp().empty().removeClass('alert-success').removeClass('alert-danger');

  
</script>
<div style="width: 100%; margin: 22px auto; text-align: center; border-bottom: 0px solid #ccc;">
    <div style="margin-bottom: 15px;">
        <div>
            <label class="custom-control-label" style="padding: 5px;width: 100%;background-color:rgb(255, 0, 0);">
                <font style="color:rgb(255, 255, 255);">Cancelamento do Fato Observado</font>
                <br>
                <font style="color:rgb(255, 255, 255);font-size: xx-small;">*Caso preenchido o campo abaixo será cancelado o Fato Observado do Aluno.</font>
            </label>
        </div>
        <div>
            <textarea class="form-control" name="textAreaCancelamento" rows="3" style="display: inline;" {{ (($lancamentoFo->cancelado == 'S') ? 'readOnly': '') }}>{{ ((isset($lancamentoFo)) ? $lancamentoFo->cancelado_motivo : null) }}</textarea>
        </div>
    </div>
</div>
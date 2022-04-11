<div class="modal-header" style="background-color: rgb(242, 242, 242); display: block;">
    <h5 class="modal-title" id="exampleModalLongTitle">
        <i class="ion-forward" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> {{ (isset($aluno->situacoes_diversas_id)) ? 'Editar': 'Incluir em'}} Situação Diversas
    </h5>
    <!--button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button-->
</div>

<div class="modal-body">
    <div class="alert alert-success success_aluno_situacao_diversa" role="alert" style="margin-bottom:10px;"></div>
    <div class="alert alert-danger error_aluno_situacao_diversa" role="alert"></div>
    <div style="width: 80%; margin: 18px auto; color: #696969; font-size: 16px;"><b>Selecione em qual situação estará {{ ($aluno->sexo == 'M') ? 'enquadrado o ALUNO' : 'enquadrada a ALUNA' }}
            <font style='color: rgb(255, 69, 0);'>{{ $aluno->numero.' - '.$aluno->nome_guerra}}</font>
        </b></div>


    <form id="incluir_aluno_situacao_diversa">
        <div style="margin: 6px auto;">
            <input type="hidden" name="_token" value={{ csrf_token() }} />
            <input type="hidden" name="alunoID" value={{ $aluno->id }} />
            <input type="hidden" name="tipoRequisicao" value={{ (isset($aluno->situacoes_diversas_id)) ? 'Editar': 'Salvar' }} />
            <div style="margin: 36px auto; width: 80%;">

                <div class="btn-group btn-group-toggle" data-toggle="buttons" style="padding-bottom:10px;left: 20%;">
                    @foreach($situacoesDiversas as $situacao)
                    <label class="btn btn-outline-info {{ (isset($aluno->situacoes_diversas_id) && $aluno->situacoes_diversas_id == $situacao->id)  ? 'active': ''}}" onclick=ocultarOpcaoDiversas()>
                        <input type="radio" name="sitDivID" value="{{ $situacao->id }}">
                        {{ $situacao->descricao }}
                    </label>
                    @endforeach
                </div>
                <div id="opcaoDiversas" style="width: 80%;">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons" style="padding-bottom:10px;left: 34%;">
                        <label class="btn btn-outline-info {{ (isset($aluno->solicitacao_situacao) && $aluno->solicitacao_situacao == 'AP')  ? 'active': ''}}" onclick="">
                            <input type="radio" name="opcao" value="AP">
                            A Pedido
                        </label>
                        <label class="btn btn-outline-info {{ (isset($aluno->solicitacao_situacao) && $aluno->solicitacao_situacao == 'EO')  ? 'active': ''}}" onclick="">
                            <input type="radio" name="opcao" value="EO">
                            Ex Oficio
                        </label>
                    </div>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons" style="left: 21%;">
                        <label class="btn btn-outline-info {{ (isset($aluno->adido) && $aluno->adido == 'S')  ? 'active': ''}}" onclick="">
                            <input type="radio" name="adido" value="S">
                            Adido
                        </label>
                        <label class="btn btn-outline-info {{ (isset($aluno->adido) && $aluno->adido == 'N')  ? 'active': ''}}" onclick="">
                            <input type="radio" name="adido" value="N">
                            Não Adido
                        </label>
                        <label class="btn btn-outline-info {{ (isset($aluno->adido) && $aluno->adido == 'E')  ? 'active': ''}}" onclick="">
                            <input type="radio" name="adido" value="E">
                            Encostado
                        </label>
                    </div>

                    <div style="float: left;margin-top: 10px;padding: 0 0 5px 5px; width: 125%;">
                        <label>Número BI</label>
                        <input class="no-style" style="display: block;border-bottom: 1px solid #ccc;margin-bottom:10px;width: 100%;" name="numero_bi" type="text" maxlength="50" autocomplete="off" placeholder="" value="{{ (isset($aluno->numero_bi) ? $aluno->numero_bi : '') }}"/>

                        <label>Data BI</label>
                        <input class="no-style data_mask" style="display: block;border-bottom: 1px solid #ccc;margin-bottom:10px;width: 30%;" name="data_bi" type="text" maxlength="10" autocomplete="off" placeholder="" value="{{ (isset($aluno->data_bi) ? \Carbon\Carbon::parse($aluno->data_bi)->format('d/m/Y') : '')}}"/>

                        <select class="custom-select" style="display: block;border-bottom: 1px solid #ccc;margin-bottom:10px;" name="motivo" style="margin-top:5px;">
                            <option value="0" disabled selected hidden>Motivo</option>
                            @foreach ($motivos as $motivo)
                            <option value={{$motivo->id}} {{ ((isset($aluno->id_motivo) && $aluno->id_motivo == $motivo->id) ? 'selected':'') }}>{{$motivo->motivo}}</option>
                            @endforeach
                        </select>

                        <label>Amparo</label>
                        <input class="no-style" style="display: block;border-bottom: 1px solid #ccc;margin-bottom:10px;width: 100%;" name="amparo" type="text" maxlength="255" autocomplete="off" placeholder="" value="{{ (isset($aluno->amparo) ? $aluno->amparo : '') }}"/>
                    </div>
                </div>
            </div>
            <div style="margin: 2px auto; width: 80%;">
                <div>
                    <textarea class="form-control" id="observacao" name="observacao" rows="3" placeholder="Observações pertinentes a alteração">{{ (isset($aluno->situacoes_diversas_obs) ? $aluno->situacoes_diversas_obs : '') }}</textarea>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </form>
</div>

<div class="modal-footer" style="text-align: right; display: block;">
    <button type="button" id="btnCancSituacaoDiversa" class="btn btn-secondary" data-dismiss="modal" title="Cancelar">
    Cancelar
    </button>
    <button type="button" class="btn btn-primary">
        {{ (isset($aluno->situacoes_diversas_id)) ? 'Editar': 'Salvar'}}
    </button>
</div>

<script>
    $(document).ready(function() {
        $('div.error_aluno_situacao_diversa').empty().hide();
        $('div.success_aluno_situacao_diversa').empty().hide();

        @if(!isset($aluno->situacoes_diversas_id))
            $('div#opcaoDiversas').hide();
        @endif

        $('.data_mask').mask('00/00/0000');

        $('button.btn.btn-primary').click(function() {

            var formData = $('form#incluir_aluno_situacao_diversa').serialize();

            $.ajax({
                dataType: 'json',
                url: '/ajax/admin/alunoSitDiversas',
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    $('div.error_aluno_situacao_diversa').empty().hide();
                    $('div.success_aluno_situacao_diversa').empty().hide();
                },
                success: function(data) {

                    if (data.status == 'ok') {
                        $('div.success_aluno_situacao_diversa').html(data.response).slideDown();
                        $('button.btn.btn-primary').hide();
                        $('#btnCancSituacaoDiversa').text('Fechar');

                        setTimeout(function() {
                            $('div.success_aluno_situacao_diversa').slideUp(200, function() {
                                $(this).removeClass('alert-success').empty();
                            });
                        }, 10000);
                    } else {
                        $('div.error_aluno_situacao_diversa').html('<strong>ATENÇÃO:</strong><br />').slideDown();

                        $.each(data.response, function(key, value) {
                            $('div.error_aluno_situacao_diversa').append('<li>' + value + '</li>');
                        });
                    }
                },
                error: function(jqxhr) {
                    $('div.error_aluno_situacao_diversa').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
                }
            });
        });
    });

    function ocultarOpcaoDiversas() {
        $('div#opcaoDiversas label').removeClass('active');
        $('div#opcaoDiversas').show();
    }
</script>
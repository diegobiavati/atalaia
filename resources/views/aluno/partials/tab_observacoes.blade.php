                <div class="tab-pane fade" id="nav-implantar-aluno11" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">
                    <div class="form-group">
                        <label for="anulacaoCancelamento">Anulação ou Cancelamento de Puniçoes Disciplinares</label>
                        <textarea class="form-control" id="anulacaoCancelamento" name="anulacaoCancelamento" rows="3">{{$aluno->anulacaoCancelamento or old('anulacaoCancelamento') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="recursosDisciplinares">Recursos Disciplinares</label>
                        <textarea class="form-control" id="recursosDisciplinares" name="recursosDisciplinares" rows="3">{{$aluno->recursosDisciplinares or old('recursosDisciplinares') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="recompensas">Recompensas</label>
                        <textarea class="form-control" id="recompensas" name="recompensas" rows="3">{{$aluno->recompensas or old('recompensas') }}</textarea>
                    </div>
                    @if($ownauthcontroller->PermissaoCheck(37))
                    <div class="form-group">
                        <label for="obs_psicopedagogia">Observações Seção Psicopedagogia</label>
                        <textarea class="form-control" id="obs_psicopedagogia" name="obs_psicopedagogia" rows="3">{{$aluno->obs_psicopedagogia or old('obs_psicopedagogia') }}</textarea>
                    </div>
                    @endif
                </div>
            </div>

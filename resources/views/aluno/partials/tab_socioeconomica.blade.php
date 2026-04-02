                <div class="tab-pane fade" id="nav-implantar-aluno8" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">

                    <div class="divImplantarAluno" style="border-bottom:none;width: 25%;">
                        <i class="ion-cash" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Renda Familiar</label>
                        <select class="custom-select" name="id_renda" style="margin-top:5px;">
                            <option value="0" disabled selected hidden>Renda Familiar</option>
                            @foreach ($situacaoRendas as $situacaoRenda)
                            <option value={{$situacaoRenda->id}} {{ (isset($aluno) && $situacaoRenda->id == $aluno->id_renda) ? 'selected' : ''}}>{{ $situacaoRenda->descricao_renda }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="clear"></div>
                    <div class="divImplantarAluno" style="border-bottom:none;width: 25%;">
                        <i class="ion-pricetag" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Banco</label>
                        <select class="custom-select" name="id_banco" style="margin-top:5px;">
                            <option value="0" disabled selected hidden>Banco</option>
                            @foreach ($situacaoBancos as $situacaoBanco)
                            <option value={{$situacaoBanco->id}} {{ (isset($aluno) && $situacaoBanco->id == $aluno->id_banco) ? 'selected' : ''}}>{{ $situacaoBanco->nome_banco }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="divImplantarAluno" style="width: 12%; margin-left:20px;">
                        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Agência</label>
                        <input class="no-style" name="num_agencia_banco" value="{{$aluno->num_agencia_banco or old('num_agencia_banco') }}" type="text" maxlength="50" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;" />
                    </div>
                    <div class="divImplantarAluno" style="width: 12%; margin-left:20px;">
                        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Nº Conta</label>
                        <input class="no-style" name="num_conta_bancaria" value="{{$aluno->num_conta_bancaria or old('num_conta_bancaria') }}" type="text" maxlength="50" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;" />
                    </div>

                </div>

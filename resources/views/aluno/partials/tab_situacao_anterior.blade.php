                <div class="tab-pane fade" id="nav-implantar-aluno3" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">

                    <div class="divImplantarAluno" style="border-bottom:none; width:25%">
                        <i class="ion-android-clipboard" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Situação Anterior (militar ou civil)</label>
                        <select class="custom-select" name="id_situacao_anterior" style="margin-top:5px;">
                            <option value="0" disabled selected hidden>Situação Anterior (militar ou civil)</option>
                            @foreach ($situacaoAnteriors as $situacaoAnterior)
                            <option value={{$situacaoAnterior->id}} {{ (isset($aluno) && $situacaoAnterior->id == $aluno->id_situacao_anterior) ? 'selected' : ''}}>{{ $situacaoAnterior->situacao_anterior }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px;width:17%;">
                        <i class="ion-android-calendar" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Data de Praça Anterior</label>
                        <input class="no-style data_mask" style="width: 100%;margin-top:10px;" name="primeira_data_praca" value="{{ isset($aluno->primeira_data_praca) ? \Carbon\Carbon::parse($aluno->primeira_data_praca)->format('d/m/Y') : null }}" type="text" maxlength="10" autocomplete="off" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px;">
                        <i class="ion-android-calendar" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Data de Promoção a Cabo</label>
                        <input class="no-style data_mask" style="width: 100%;margin-top:10px;" name="data_cb" value="{{ isset($aluno->data_cb) ? \Carbon\Carbon::parse($aluno->data_cb)->format('d/m/Y') : null }}" type="text" maxlength="10" autocomplete="off" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px;width:23%;">
                        <i class="ion-android-calendar" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Data de Promoção a Sgt Temp</label>
                        <input class="no-style data_mask" style="width: 100%;margin-top:10px;" name="data_sgttemp" value="{{ isset($aluno->data_sgttemp) ? \Carbon\Carbon::parse($aluno->data_sgttemp)->format('d/m/Y') : null }}" type="text" maxlength="10" autocomplete="off" placeholder="" />
                    </div>

                    <div class="clear"></div>
                    <div class="divImplantarAluno">
                        <i class="ion-speakerphone" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Última OM</label>
                        <input class="no-style" style="width: 100%;margin-top:10px;" name="nome_ultima_om" value="{{$aluno->nome_ultima_om or old('nome_ultima_om') }}" type="text" maxlength="150" autocomplete="off" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px;width:21%;">
                        <i class="ion-android-calendar" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Data de Baixa da Última OM</label>
                        <input class="no-style data_mask" style="width: 100%;margin-top:10px;" name="data_baixa_ultima_om" value="{{ isset($aluno->data_baixa_ultima_om) ? \Carbon\Carbon::parse($aluno->data_baixa_ultima_om)->format('d/m/Y') : null }}" type="text" maxlength="10" autocomplete="off" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px;">
                        <i class="ion-compose" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Endereço da Última OM</label>
                        <input class="no-style" style="width: 100%;margin-top:10px;" name="endereco_ultima_om" value="{{$aluno->endereco_ultima_om or old('endereco_ultima_om') }}" type="text" maxlength="255" autocomplete="off" placeholder="" />
                    </div>

                    <div class="clear"></div>
                    <div class="divImplantarAluno">
                        <i class="ion-compose" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Tempo SV Anterior</label>
                        <input class="no-style" style="width: 100%;margin-top:10px;" name="temposv_anterior" value="{{$aluno->temposv_anterior or old('temposv_anterior') }}" type="text" maxlength="100" autocomplete="off" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px;">
                        <i class="ion-compose" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">TSCMM Anterior</label>
                        <input class="no-style" style="width: 100%;margin-top:10px;" name="tscmm_anterior" value="{{$aluno->tscmm_anterior or old('tscmm_anterior') }}" type="text" maxlength="100" autocomplete="off" placeholder="" />
                    </div>

                </div>

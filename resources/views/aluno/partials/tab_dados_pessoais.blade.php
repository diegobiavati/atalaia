                <div class="tab-pane fade" id="nav-implantar-aluno6" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">

                    <div class="divImplantarAluno" style="border-bottom:none;width: 15%;">
                        <i class="ion-nuclear" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Estado Civil</label>
                        <select class="custom-select" name="id_estado_civil" style="margin-top:5px;">
                            <option value="0" disabled selected hidden>Estado Civil</option>
                            @foreach ($situacaoEstadoCivils as $situacaoEstadoCivil)
                            <option value={{$situacaoEstadoCivil->id}} {{ (isset($aluno) && $situacaoEstadoCivil->id == $aluno->id_estado_civil) ? 'selected' : ''}}>{{ $situacaoEstadoCivil->estado_civil }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="divImplantarAluno" style="width: 12%; margin-left:20px;">
                        <i class="ion-clipboard" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Tipo Sanguíneo</label>
                        <input class="no-style text-uppercase" name="tipo_sanguineo" value="{{$aluno->tipo_sanguineo or old('tipo_sanguineo') }}" type="text" maxlength="2" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;" />
                    </div>
                    <div class="divImplantarAluno" style="width: 8%; margin-left:20px;">
                        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Fator RH</label>
                        <input class="no-style" name="fator_rh" value="{{$aluno->fator_rh or old('fator_rh') }}" type="text" maxlength="15" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;" />
                    </div>
                    <div class="divImplantarAluno" style="width: 10%; margin-left:20px;">
                        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Cabelos</label>
                        <input class="no-style" name="cabelo" value="{{$aluno->cabelo or old('cabelo') }}" type="text" maxlength="50" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;" />
                    </div>
                    <div class="divImplantarAluno" style="width: 8%; margin-left:20px;">
                        <i class="ion-clipboard" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Altura</label>
                        <input class="no-style" name="altura" value="{{$aluno->altura or old('altura') }}" type="number" step="0.01" min="0" max="10" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;" />
                    </div>
                    <div class="divImplantarAluno" style="border-bottom:none;width: 25%; margin-left:20px;">
                        <i class="ion-help-buoy" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Tatuagem</label>
                        <select class="custom-select" name="tatuagem" style="margin-top:5px;">
                            <option value="0" disabled selected hidden>Tatuagem</option>
                            <option value="S" {{ (isset($aluno) && $aluno->tatuagem == 'S') ? 'selected' : ''}}>Sim</option>
                            <option value="N" {{ (isset($aluno) && $aluno->tatuagem == 'N') ? 'selected' : ''}}>Não</option>
                        </select>
                    </div>

                    <div class="clear"></div>
                    <div class="divImplantarAluno" style="border-bottom:none;width: 14%">
                        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Cor/Raça</label>
                        <select class="custom-select" name="id_raca" style="margin-top:5px;">
                            <option value="0" disabled selected hidden>Cor/Raça</option>
                            @foreach ($situacaoRacas as $situacaoRaca)
                            <option value={{$situacaoRaca->id}} {{ (isset($aluno) && $situacaoRaca->id == $aluno->id_raca) ? 'selected' : ''}}>{{ $situacaoRaca->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="divImplantarAluno" style="width: 15%; margin-left:20px;">
                        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Cutis</label>
                        <input class="no-style" name="cutis" value="{{$aluno->cutis or old('cutis') }}" type="text" maxlength="50" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;" />
                    </div>
                    <div class="divImplantarAluno" style="width: 12%; margin-left:20px;">
                        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Olhos</label>
                        <input class="no-style" name="olhos" value="{{$aluno->olhos or old('olhos') }}" type="text" maxlength="50" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;" />
                    </div>
                    <div class="divImplantarAluno" style="border-bottom:none;width: 29%; margin-left:20px;">
                        <i class="ion-android-globe" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Religião</label>
                        <select class="custom-select" name="id_religiao" style="margin-top:5px;">
                            <option value="0" disabled selected hidden>Religião</option>
                            @foreach ($situacaoReligiaos as $situacaoReligiao)
                            <option value={{$situacaoReligiao->id}} {{ (isset($aluno) && $situacaoReligiao->id == $aluno->id_religiao) ? 'selected' : ''}}>{{ $situacaoReligiao->religiao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="divImplantarAluno" style="border-bottom:none;width: 20%; margin-left:20px;">
                        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Escolaridade</label>
                        <select class="custom-select" name="id_escolaridade" style="margin-top:5px;">
                            <option value="0" disabled selected hidden>Escolaridade</option>
                            @foreach ($situacaoEscolaridades as $situacaoEscolaridade)
                            <option value={{$situacaoEscolaridade->id}} {{ (isset($aluno) && $situacaoEscolaridade->id == $aluno->id_escolaridade) ? 'selected' : ''}}>{{ $situacaoEscolaridade->escolaridade }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="clear"></div>
                    <div class="divImplantarAluno" style="width: 50%;">
                        <i class="ion-male" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Nome do Pai</label>
                        <input class="no-style text-uppercase" name="nome_pai" value="{{$aluno->nome_pai or old('nome_pai') }}" type="text" maxlength="255" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; width: 8%;">
                        <i class="ion-bookmark" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">CPF do Pai</label>
                        <input class="no-style cpf_mask" style="margin-top:10px;width:100%;" name="cpf_pai" value="{{$aluno->cpf_pai or old('cpf_pai') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="width: 30%; margin-left:20px; border-bottom:none;">
                        <i class="ion-settings" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Profissão Pai</label>
                        <select class="custom-select" name="id_profissao_pai" style="margin-top:5px;">
                            <option value="0" disabled selected hidden>Profissão Pai</option>
                            @foreach ($profissaos as $profissao)
                            <option value={{$profissao->id}} {{ (isset($aluno) && $profissao->id == $aluno->id_profissao_pai) ? 'selected' : ''}}>{{ $profissao->profissao . ((trim($profissao->militar_postograd) != '') ? ' - ' . $profissao->militar_postograd : '') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="clear"></div>
                    <div class="divImplantarAluno" style="width: 50%;">
                        <i class="ion-female" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Nome da Mãe</label>
                        <input class="no-style text-uppercase" name="nome_mae" value="{{$aluno->nome_mae or old('nome_mae') }}" type="text" maxlength="255" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; width: 8%;">
                        <i class="ion-bookmark" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">CPF da Mãe</label>
                        <input class="no-style cpf_mask" style="margin-top:10px;width:100%;" name="cpf_mae" value="{{$aluno->cpf_mae or old('cpf_mae') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="width: 30%; margin-left:20px; border-bottom:none;">
                        <i class="ion-settings" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Profissão Mãe</label>
                        <select class="custom-select" name="id_profissao_mae" style="margin-top:5px;">
                            <option value="0" disabled selected hidden>Profissão Mãe</option>
                            @foreach ($profissaos as $profissao)
                            <option value={{$profissao->id}} {{ (isset($aluno) && $profissao->id == $aluno->id_profissao_mae) ? 'selected' : ''}}>{{ $profissao->profissao . ((trim($profissao->militar_postograd) != '') ? ' - ' . $profissao->militar_postograd : '') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="clear"></div>
                    <div class="divImplantarAluno" style="width: 100%;">
                        <i class="ion-android-textsms" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Observações</label>
                        <input class="no-style" name="obs" value="{{$aluno->obs or old('obs') }}" type="text" maxlength="255" autocomplete="off" placeholder="" style="margin-top:10px;width: 100%;" />
                    </div>

                </div>

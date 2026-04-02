                <div class="tab-pane fade" id="nav-implantar-aluno4" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">

                    <div class="divImplantarAluno" style="width: 30%;">
                        <i class="ion-compose" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Endereço</label>
                        <input class="no-style" name="endereco" value="{{$aluno->endereco or old('endereco') }}" type="text" maxlength="255" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;" />
                    </div>
                    <div class="divImplantarAluno" style="width: 25%; margin-left:20px;">
                        <i class="ion-location" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Bairro</label>
                        <input class="no-style" name="bairro" value="{{$aluno->bairro or old('bairro') }}" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;" />
                    </div>
                    <div class="divImplantarAluno" style="width: 25%; margin-left:20px;">
                        <i class="ion-location" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Cidade</label>
                        <input class="no-style" name="cidade" value="{{$aluno->cidade or old('cidade') }}" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;" />
                    </div>
                    <div class="divImplantarAluno" style="border-bottom:none; margin-left:20px; width: 10%;">
                        <i class="ion-location" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">UF</label>
                        <select class="custom-select" name="id_uf" style="margin-top:5px;">
                            <option value="0" disabled selected hidden>UF</option>
                            @foreach ($ufs as $uf)
                            <option value={{$uf->id}} {{ (isset($aluno) && $uf->id == $aluno->id_uf) ? 'selected' : ''}}>{{ $uf->uf_sigla }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="clear"></div>
                    <div class="divImplantarAluno" style="width: 12%;">
                        <i class="ion-compose" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">CEP</label>
                        <input class="no-style cep_mask" name="cep" value="{{$aluno->cep or old('cep') }}" type="text" maxlength="255" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;" />
                    </div>
                    <div class="divImplantarAluno" style="width: 15%; margin-left:20px;">
                        <i class="ion-ios-telephone" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Telefone</label>
                        <input class="no-style telefone_mask" name="telefone" value="{{$aluno->telefone or old('telefone') }}" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;" />
                    </div>
                    <div class="divImplantarAluno" style="width: 17%; margin-left:20px;">
                        <i class="ion-ios-telephone-outline" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Celular 1</label>
                        <input class="no-style celular_mask" name="celular1" value="{{$aluno->celular1 or old('celular1') }}" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;" />
                    </div>
                    <div class="divImplantarAluno" style="width: 17%; margin-left:20px;">
                        <i class="ion-ios-telephone-outline" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Celular 2</label>
                        <input class="no-style celular_mask" name="celular2" value="{{$aluno->celular2 or old('celular2') }}" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;" />
                    </div>
                    <div class="divImplantarAluno" style="width: 17%; margin-left:20px;">
                        <i class="ion-ios-telephone-outline" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Celular 3</label>
                        <input class="no-style celular_mask" name="celular3" value="{{$aluno->celular3 or old('celular3') }}" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;" />
                    </div>

                </div>

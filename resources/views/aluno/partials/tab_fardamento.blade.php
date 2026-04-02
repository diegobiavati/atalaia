                <div class="tab-pane fade" id="nav-implantar-aluno10" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">

                    <div class="divImplantarAluno" style="width: 10%">
                        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Boina</label>
                        <input class="no-style" style="width: 100%;margin-top:10px;" name="farda_tam_boina" value="{{$aluno->farda_tam_boina or old('farda_tam_boina') }}" pattern="[0-9]+$" type="text" maxlength="11" autocomplete="off" />
                    </div>
                    <div class="divImplantarAluno" style="width: 10%;margin-left:20px;">
                        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Gorro</label>
                        <input class="no-style" style="width: 100%;margin-top:10px;" name="farda_tam_gorro" value="{{$aluno->farda_tam_gorro or old('farda_tam_gorro') }}" pattern="[0-9]+$" type="text" maxlength="11" autocomplete="off" />
                    </div>
                    <div class="divImplantarAluno" style="width: 10%;margin-left:20px;">
                        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Camiseta</label>
                        <input class="no-style" style="width: 100%;margin-top:10px;" name="farda_tam_camiseta" value="{{$aluno->farda_tam_camiseta or old('farda_tam_camiseta') }}" type="text" maxlength="3" autocomplete="off" />
                    </div>
                    <div class="divImplantarAluno" style="width: 10%;margin-left:20px;">
                        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Gandola</label>
                        <input class="no-style" style="width: 100%;margin-top:10px;" name="farda_tam_gandola" value="{{$aluno->farda_tam_gandola or old('farda_tam_gandola') }}" type="text" maxlength="3" autocomplete="off" />
                    </div>
                    <div class="divImplantarAluno" style="width: 10%;margin-left:20px">
                        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Calça</label>
                        <input class="no-style" style="width: 100%;margin-top:10px;" name="farda_tam_calca" value="{{$aluno->farda_tam_calca or old('farda_tam_calca') }}" type="text" maxlength="3" autocomplete="off" />
                    </div>
                    <div class="divImplantarAluno" style="width: 10%;margin-left:20px">
                        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Coturno</label>
                        <input class="no-style" style="width: 100%;margin-top:10px;" name="farda_tam_coturno" value="{{$aluno->farda_tam_coturno or old('farda_tam_coturno') }}" pattern="[0-9]+$" type="text" maxlength="11" autocomplete="off" />
                    </div>

                </div>

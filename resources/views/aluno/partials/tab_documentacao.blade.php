                <div class="tab-pane fade" id="nav-implantar-aluno5" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">

                    <div class="divImplantarAluno" style="width:15%">
                        <i class="ion-bookmark" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Nº Identidade Civil</label>
                        <input class="no-style" style="margin-top:10px;width:100%;" name="doc_idt_civil" value="{{$aluno->doc_idt_civil or old('doc_idt_civil') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="40" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; width: 15%;">
                        <i class="ion-bookmark" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Org Expd (Idt Civil)</label>
                        <input class="no-style" style="margin-top:10px;width:100%;" name="doc_idt_civil_o_exp" value="{{$aluno->doc_idt_civil_o_exp or old('doc_idt_civil_o_exp') }}" type="text" autocomplete="off" maxlength="40" placeholder="" />
                    </div>

                    <div class="clear"></div>
                    <div class="divImplantarAluno" style="width: 15%;">
                        <i class="ion-bookmark" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Nº Título Eleitoral</label>
                        <input class="no-style" style="margin-top:10px;width:100%;" name="doc_tit_eleitor" value="{{$aluno->doc_tit_eleitor or old('doc_tit_eleitor') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="40" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; width: 17%;">
                        <i class="ion-location" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Seção (Título Eleitoral)</label>
                        <input class="no-style" style="margin-top:10px;width:100%;" name="doc_tit_secao" value="{{$aluno->doc_tit_secao or old('doc_tit_secao') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="40" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; width: 17%;">
                        <i class="ion-location" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Zona (Título Eleitoral)</label>
                        <input class="no-style" style="margin-top:10px;width:100%;" name="doc_tit_zona" value="{{$aluno->doc_tit_zona or old('doc_tit_zona') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="40" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; width: 20%;">
                        <i class="ion-location" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Cidade/UF (Título Eleitoral)</label>
                        <input class="no-style" style="margin-top:10px;width:100%;" name="doc_tit_cidade_uf" value="{{$aluno->doc_tit_cidade_uf or old('doc_tit_cidade_uf') }}" type="text" autocomplete="off" maxlength="40" placeholder="" />
                    </div>

                    <div class="clear"></div>
                    <div class="divImplantarAluno" style="width: 18%;">
                        <i class="ion-bookmark" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Certidão de Nascimento</label>
                        <input class="no-style" style="margin-top:10px;" name="doc_cert_nascimento" value="{{$aluno->doc_cert_nascimento or old('doc_cert_nascimento') }}" type="text" autocomplete="off" maxlength="40" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; width: 10%;">
                        <i class="ion-bookmark" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">CPF</label>
                        <input class="no-style" style="margin-top:10px;width:100%;" name="doc_cpf" value="{{$aluno->doc_cpf or old('doc_cpf') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; width: 10%;">
                        <i class="ion-bookmark" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">CNH</label>
                        <input class="no-style" style="margin-top:10px;width:100%;" name="doc_cnh" value="{{$aluno->doc_cnh or old('doc_cnh') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; width: 10%;">
                        <i class="ion-bookmark" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">PIS</label>
                        <input class="no-style" style="margin-top:10px;width:100%;" name="doc_pis" value="{{$aluno->doc_pis or old('doc_pis') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; width: 10%;">
                        <i class="ion-bookmark" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">PASEP</label>
                        <input class="no-style" style="margin-top:10px;width:100%;" name="doc_pasep" value="{{$aluno->doc_pasep or old('doc_pasep') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder="" />
                    </div>

                    <div class="clear"></div>
                    <div class="divImplantarAluno" style="width: 16%;">
                        <i class="ion-bookmark" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Nº Identidade Militar</label>
                        <input class="no-style" style="margin-top:10px;width:100%;" name="doc_idt_militar" value="{{$aluno->doc_idt_militar or old('doc_idt_militar') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; width: 14%;">
                        <i class="ion-bookmark" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Org Expd (Idt Mil)</label>
                        <input class="no-style" style="margin-top:10px;width:100%;" name="doc_idt_militar_o_exp" value="{{$aluno->doc_idt_militar_o_exp or old('doc_idt_militar_o_exp') }}" type="text" autocomplete="off" maxlength="50" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; width: 14%;">
                        <i class="ion-android-calendar" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Data Expd (Idt Mil)</label>
                        <input class="no-style data_mask" style="margin-top:10px;width:100%;" name="doc_idt_militar_dt_exp" value="{{ isset($aluno->doc_idt_militar_dt_exp) ? \Carbon\Carbon::parse($aluno->doc_idt_militar_dt_exp)->format('d/m/Y') : null}}" type="text" autocomplete="off" placeholder="" />
                    </div>

                    <div class="clear"></div>
                    <div class="divImplantarAluno" style="width: 10%;">
                        <i class="ion-bookmark" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">FAM</label>
                        <input class="no-style" style="margin-top:10px;width:100%;" name="doc_fam" value="{{$aluno->doc_fam or old('doc_fam') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; width: 10%;">
                        <i class="ion-bookmark" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">CAPEMI</label>
                        <input class="no-style" style="margin-top:10px;width:100%;" name="doc_capemi" value="{{$aluno->doc_capemi or old('doc_capemi') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; width: 10%;">
                        <i class="ion-bookmark" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">GBOEx</label>
                        <input class="no-style" style="margin-top:10px;width:100%;" name="doc_gboex" value="{{$aluno->doc_gboex or old('doc_gboex') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; width: 10%;">
                        <i class="ion-bookmark" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">CP</label>
                        <input class="no-style" style="margin-top:10px;width:100%;" name="doc_cp" value="{{$aluno->doc_cp or old('doc_cp') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px;">
                        <i class="ion-bookmark" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">PREC CP</label>
                        <input class="no-style" style="margin-top:10px;width:100%;" name="doc_preccp" value="{{$aluno->doc_preccp or old('doc_preccp') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="30" placeholder="" />
                    </div>

                </div>

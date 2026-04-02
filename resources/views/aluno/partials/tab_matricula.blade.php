<div class="tab-pane fade show active" id="nav-implantar-aluno1" role="tabpanel"
    aria-labelledby="nav-home-tab" style="padding: 20px;">
    <div class="divImplantarAluno" style="border-bottom:none; width: 10%">
        <div style="width: 100px; margin: 0 auto;">
            <div id="aluno_img" class="imagem_aluno"
                style="background: url('/storage/imagens_aluno/{{  (isset($aluno) && strlen($aluno->imagem_aluno->nome_arquivo) > 12) ? ($aluno->ano_formacao->formacao.'/'.$aluno->imagem_aluno->nome_arquivo) : 'no-image.jpg' }}') no-repeat center center; background-size: contain;">
                <div style="background-color: none;">
                    @if(empty($readonly))
                        <a class="no-style open_file" href="javascript: void(0);">
                            <span style="color: #696969;"><i class="ion-ios-camera"></i></span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="progress" style="margin-top: 36px; display: none;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

    <div class="divImplantarAluno" style="margin-left:35px;">
        <i class="ion-pricetag" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
        <label class="labelDescricao">Número Inscrição</label>
        <input class="no-style" style="width: 100%;margin-top:10px;" id="al_inscricao"
            name="al_inscricao" value="{{ $aluno->al_inscricao or old('al_inscricao') }}" type="text"
            pattern="[0-9]+$" autocomplete="off" maxlength="8" placeholder="" />
    </div>
    <div class="divImplantarAluno" style="margin-left:35px; width: 18%;">
        <i class="ion-pricetag" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
        <label class="labelDescricao">Número</label>
        <input class="no-style" style="width: 100%;margin-top:10px;" id="numero" name="numero"
            value="{{ $aluno->numero or old('numero') }}" type="text" pattern="[0-9]+$"
            autocomplete="off" maxlength="5" placeholder="" />
    </div>
    <div class="divImplantarAluno" style="margin-left:35px; width: 25%;">
        <i class="ion-ios-person" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
        <label class="labelDescricao">Nome de Guerra</label>
        <input class="no-style text-uppercase" style="width: 100%;margin-top:10px;" name="nome_guerra"
            value="{{ $aluno->nome_guerra or old('nome_guerra') }}" type="text" autocomplete="off"
            placeholder="" />
    </div>
    <div class="divImplantarAluno" style="margin-left:35px; width: 55%;">
        <i class="ion-ios-person" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
        <label class="labelDescricao">Nome Completo</label>
        <input class="no-style text-uppercase" style="width: 100%;margin-top:10px;" name="nome_completo"
            value="{{ $aluno->nome_completo or old('nome_completo') }}" type="text" autocomplete="off"
            placeholder="" />
    </div>
    <div class="divImplantarAluno" style="border-bottom:none; margin-left:20px;">
        <i class="ion-person-stalker" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
        <label class="labelDescricao">Colégio Militar</label>
        <select class="custom-select" name="colegio_militar" style="margin-top:5px;">
            <option value="0" disabled selected hidden>Estudou?</option>
            <option value="N" {{ isset($aluno) && $aluno->colegio_militar == 'N' ? 'selected' : ''}}>Não
            </option>
            <option value="I" {{ isset($aluno) && $aluno->colegio_militar == 'I' ? 'selected' : ''}}>
                Integral</option>
            <option value="P" {{ isset($aluno) && $aluno->colegio_militar == 'P' ? 'selected' : ''}}>
                Parcialmente</option>
        </select>
    </div>

    <div class="clear"></div>

    <div class="divImplantarAluno" style="border-bottom:none; margin-left:20px;">
        <i class="ion-person-stalker" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
        <label class="labelDescricao">Segmento</label>
        <select class="custom-select" name="sexo" style="margin-top:5px;">
            <option value="0" disabled selected hidden>Segmento</option>
            <option value="M" {{ isset($aluno) && $aluno->sexo == 'M' ? 'selected' : ''}}>Masculino
            </option>
            <option value="F" {{ isset($aluno) && $aluno->sexo == 'F' ? 'selected' : ''}}>Feminino
            </option>
        </select>
    </div>
    <div class="divImplantarAluno" style="margin-left:20px;">
        <i class="ion-android-calendar" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
        <label class="labelDescricao">Data de Nascimento</label>
        <input class="no-style data_mask" style="width: 100%;margin-top:10px;" name="data_nascimento"
            value="{{ isset($aluno->data_nascimento) ? \Carbon\Carbon::parse($aluno->data_nascimento)->format('d/m/Y') : null}}"
            type="text" value="" maxlength="10" autocomplete="off" placeholder="" />
    </div>

    <div class="clear"></div>
    <div class="divImplantarAluno" style="width: 25%;">
        <i class="ion-location" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
        <label class="labelDescricao">Cidade (Naturalidade)</label>
        <input class="no-style text-uppercase" name="nasc_cidade" type="text"
            value="{{$aluno->nasc_cidade or old('nasc_cidade') }}" maxlength="100" autocomplete="off"
            placeholder="" style="width: 100%;margin-top:10px;" />
    </div>
    <div class="divImplantarAluno" style="margin-left:20px; border-bottom:none; width: 15%;">
        <i class="ion-location" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
        <label class="labelDescricao">UF (Naturalidade)</label>
        <select class="custom-select" name="nasc_id_uf" style="margin-top:5px;">
            <option value="0" disabled selected hidden>UF</option>
            @foreach ($ufs as $uf)
            <option value={{$uf->id}}
                {{ (isset($aluno) && $uf->id == $aluno->nasc_id_uf)  ? 'selected': ''}}>
                {{ $uf->uf_sigla }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="divImplantarAluno" style="margin-left:20px; width: 20%;">
        <i class="ion-location" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
        <label class="labelDescricao">País (Naturalidade)</label>
        <input class="no-style text-uppercase" name="nasc_pais" type="text"
            value="{{$aluno->nasc_pais or old('nasc_pais') }}" maxlength="50" autocomplete="off"
            placeholder="" style="width: 100%;margin-top:10px;" />
    </div>

    <div class="clear"></div>
    <div class="divImplantarAluno" style="border-bottom:none; width: 20%;">
        <i class="ion-android-calendar" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
        <label class="labelDescricao">Ano de Formação</label>
        <select {{ ($ownauthcontroller->PermissaoCheck(1) ? '': 'disabled') }} class="custom-select"
            id="ano_formacao" name="ano_formacao" style="margin-top:5px;"
            onchange="$('select#data_matricula').val( $(this).val() );"
            {{ ( (session()->get('login.qmsID') == null) ? '' : 'readonly') }}>
            <option value="0" disabled selected hidden>Ano de Formação</option>
            @foreach ($anoCorrente as $formacao)
            <option value={{$formacao->id}}
                {{ (isset($aluno) && $formacao->id == $aluno->ano_formacao->id) ? 'selected' : ''}}>
                {{ $formacao->formacao }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="divImplantarAluno" style="margin-left:20px; border-bottom:none; width: 20%;">
        <i class="ion-android-calendar" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
        <label class="labelDescricao">Data de Matrícula</label>
        <select class="custom-select" id="data_matricula" name="data_matricula" style="margin-top:5px;"
            readonly>
            <option value="0" disabled selected hidden>Data Matrícula</option>
            @foreach ($anoCorrente as $matricula)
            <option value={{$matricula->id}}
                {{ (isset($aluno) && $matricula->id == $aluno->data_matricula) ? 'selected' : ''}}>
                {{ $matricula->data_matricula }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="divImplantarAluno" style="margin-left:20px;width: 40%;">
        <i class="ion-at" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
        <label class="labelDescricao">E-mail</label>
        <input class="no-style text-lowercase" name="email" value="{{$aluno->email or old('email') }}"
            type="email" maxlength="60" autocomplete="off" placeholder=""
            style="width: 100%;margin-top:10px;" />
    </div>

    <div class="clear"></div>
    <div class="divImplantarAluno" style="border-bottom:none; width: 30%;">
        <i class="ion-ios-pulse" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
        <label class="labelDescricao">Situação no Ato da Matrícula</label>
        <select class="custom-select" id="id_situacao_matricula" name="id_situacao_matricula"
            style="margin-top:5px;">
            <option value="0" disabled selected hidden>Situação no Ato da Matrícula</option>
            @foreach ($situacaoMatriculas as $situMatricula)
            <option value={{$situMatricula->id}}
                {{ (isset($aluno) && $situMatricula->id == $aluno->id_situacao_matricula) ? 'selected' : ''}}>
                {{ $situMatricula->situacao_matricula }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="divImplantarAluno" style="margin-left:20px; border-bottom:none; width: 20%;">
        <i class="ion-ios-pulse" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
        <label class="labelDescricao">Situação Atual</label>
        <select class="custom-select" id="id_situacao_atual" name="id_situacao_atual"
            style="margin-top:5px;" readonly>
            <option value="" disabled selected hidden>{{ $situacaoAtuals }}</option>
        </select>
    </div>

    <div class="divImplantarAluno" style="margin-left:20px; border-bottom:none; width: 20%;">
        <label class="labelDescricao" style="color:red;">QMS</label>
        <select class="custom-select" id="id_qms" name="id_qms" style="margin-top:5px;"
            {{ ($ownauthcontroller->PermissaoCheck(36) ? '': 'disabled') }}>
            <option value="0" disabled selected hidden>Selecione a QMS</option>
            @foreach ($qms as $qm)
            <option value={{$qm->id}}
                {{ (isset($aluno) && $qm->id == $aluno->qms_id) ? 'selected' : ''}}>{{ $qm->qms }}
            </option>
            @endforeach
        </select>
    </div>

</div>
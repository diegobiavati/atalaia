
<div class="card bg-light mb-3">
    <div class="card-header">
        <i class="ion-person-add" style="vertical-align: middle; font-size: 24px; margin-right: 10px;"></i> Implantar Aluno
        @include('aluno.pesquisaAluno')
    </div>
    <div class="card-body"
    @if(isset($aluno))
        style='background-color: rgb(237,236,228);';
    @endif
    >
        <div class="alert alert-success success-implantar-aluno" role="alert" style="margin-bottom:10px;"></div>
        <div class="alert alert-danger errors-implantar-aluno erro-upload" role="alert" style="margin-bottom:10px;"></div>
        <form id="implantar_aluno">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="file" id="aluno_imagem" name="aluno_imagem" style="display: none;">
            <div class="nav new-nav-tabs" id="nav-tab" role="tablist">
                <a class="new-nav-item new-nav-link active" data-toggle="tab" href="#nav-implantar-aluno1" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Matrícula no CFGS</font>
                </a>
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno2" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Primeiro Ano CFGS</font>
                </a>
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno3" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Situação Militar ou Civil Anterior</font>
                </a>
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno4" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Endereço Residencial Atual</font>
                </a>
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno5" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Documentação</font>
                </a>
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno6" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Dados Pessoais</font>
                </a>
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno7" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Dependentes</font>
                </a>
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno8" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Situação Sócio-Econômica</font>
                </a>
                <!--<a class="new-nav-item new-nav-link"        data-toggle="tab" href="#nav-implantar-aluno9" role="tab" aria-controls="nav-home" aria-selected="true"><font style="color: rgb(0,175,123);">Dados Bancários</font></a>-->
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno10" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Fardamento</font>
                </a>
                <a class="new-nav-item new-nav-link" data-toggle="tab" href="#nav-implantar-aluno11" role="tab" aria-controls="nav-home" aria-selected="true">
                    <font style="color: rgb(0,175,123);">Observações</font>
                </a>
            </div>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-implantar-aluno1" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">
                    <div class="divImplantarAluno" style="border-bottom:none; width: 10%">
                        <div style="width: 100px; margin: 0 auto;">
                            <div id="aluno_img" class="imagem_aluno" style="background: url('/storage/imagens_aluno/{{  (isset($aluno) && strlen($aluno->imagem_aluno->nome_arquivo) > 12) ? ($aluno->ano_formacao->formacao.'/'.$aluno->imagem_aluno->nome_arquivo) : 'no-image.jpg' }}') no-repeat center center; background-size: contain;">
                                <div>
                                    <a class="no-style open_file" href="javascript: void(0);">
                                        <span style="color: #696969;"><i class="ion-ios-camera"></i></span>
                                    </a>
                                </div>
                            </div>
                            <!--<div id="box-dialog-remover-imagem-perfil" style="font-size: 12px; left: 64%; position: absolute; {{ (isset($aluno) && $aluno->imagem_aluno->id != null) ? 'display: block;' : 'display: none;' }}">
                                <a href="javascript: void(0);" class="no-style" onclick="removerImgPerfil(' . auth()->id() . ');">
                                    <i class="ion-android-delete" style="font-size: 16px;"></i>
                                    Remover imagem
                                </a>
                            </div>-->
                        </div>

                        <div class="progress" style="margin-top: 36px; display: none;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="divImplantarAluno" style="margin-left:35px;">
                        <i class="ion-pricetag" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Número Inscrição</label>
                        <input class="no-style" style="width: 100%;margin-top:10px;" id="al_inscricao" name="al_inscricao" value="{{ $aluno->al_inscricao or old('al_inscricao') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="8" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:35px; width: 18%;">
                        <i class="ion-pricetag" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Número</label>
                        <input class="no-style" style="width: 100%;margin-top:10px;" id="numero" name="numero" value="{{ $aluno->numero or old('numero') }}" type="text" pattern="[0-9]+$" autocomplete="off" maxlength="5" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:35px; width: 25%;">
                        <i class="ion-ios-person" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Nome de Guerra</label>
                        <input class="no-style text-uppercase" style="width: 100%;margin-top:10px;" name="nome_guerra" value="{{ $aluno->nome_guerra or old('nome_guerra') }}" type="text" autocomplete="off" placeholder="" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:35px; width: 55%;">
                        <i class="ion-ios-person" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Nome Completo</label>
                        <input class="no-style text-uppercase" style="width: 100%;margin-top:10px;" name="nome_completo" value="{{ $aluno->nome_completo or old('nome_completo') }}" type="text" autocomplete="off" placeholder="" />
                    </div>

                    <div class="clear"></div>

                    <div class="divImplantarAluno" style="border-bottom:none; margin-left:20px;">
                        <i class="ion-person-stalker" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Segmento</label>
                        <select class="custom-select" name="sexo" style="margin-top:5px;">
                            <option value="0" disabled selected hidden>Segmento</option>
                            <option value="M" {{ isset($aluno) && $aluno->sexo == 'M' ? 'selected' : ''}}>Masculino</option>
                            <option value="F" {{ isset($aluno) && $aluno->sexo == 'F' ? 'selected' : ''}}>Feminino</option>
                        </select>
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px;">
                        <i class="ion-android-calendar" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Data de Nascimento</label>
                        <input class="no-style data_mask" style="width: 100%;margin-top:10px;" name="data_nascimento" value="{{ isset($aluno->data_nascimento) ? \Carbon\Carbon::parse($aluno->data_nascimento)->format('d/m/Y') : null}}" type="text" value="" maxlength="10" autocomplete="off" placeholder="" />
                    </div>

                    <div class="clear"></div>
                    <div class="divImplantarAluno" style="width: 25%;">
                        <i class="ion-location" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Cidade (Naturalidade)</label>
                        <input class="no-style text-uppercase" name="nasc_cidade" type="text" value="{{$aluno->nasc_cidade or old('nasc_cidade') }}" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;" />
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; border-bottom:none; width: 15%;">
                        <i class="ion-location" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">UF (Naturalidade)</label>
                        <select class="custom-select" name="nasc_id_uf" style="margin-top:5px;">
                            <option value="0" disabled selected hidden>UF</option>
                            @foreach ($ufs as $uf)
                            <option value={{$uf->id}} {{ (isset($aluno) && $uf->id == $aluno->nasc_id_uf)  ? 'selected': ''}}>{{ $uf->uf_sigla }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; width: 20%;">
                        <i class="ion-location" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">País (Naturalidade)</label>
                        <input class="no-style text-uppercase" name="nasc_pais" type="text" value="{{$aluno->nasc_pais or old('nasc_pais') }}" maxlength="50" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;" />
                    </div>

                    <div class="clear"></div>
                    <div class="divImplantarAluno" style="border-bottom:none; width: 20%;">
                        <i class="ion-android-calendar" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Ano de Formação</label>
                        <select class="custom-select" id="ano_formacao" name="ano_formacao" style="margin-top:5px;" onchange="$('select#data_matricula').val( $(this).val() );">
                            <option value="0" disabled selected hidden>Ano de Formação</option>
                            @foreach ($anoCorrente as $formacao)
                            <option value={{$formacao->id}} {{ (isset($aluno) && $formacao->id == $aluno->ano_formacao->id) ? 'selected' : ''}}>{{ $formacao->formacao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; border-bottom:none; width: 20%;">
                        <i class="ion-android-calendar" style="font-size: 15px; color: #696969; margin-right: 5px;"></i>
                        <label class="labelDescricao">Data de Matrícula</label>
                        <select class="custom-select" id="data_matricula" name="data_matricula" style="margin-top:5px;" readonly>
                            <option value="0" disabled selected hidden>Data Matrícula</option>
                            @foreach ($anoCorrente as $matricula)
                            <option value={{$matricula->id}} {{ (isset($aluno) && $matricula->id == $aluno->data_matricula) ? 'selected' : ''}}>{{ $matricula->data_matricula }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px;width: 40%;">
                        <i class="ion-at" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">E-mail</label>
                        <input class="no-style text-lowercase" name="email" value="{{$aluno->email or old('email') }}" type="email" maxlength="60" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;" />
                    </div>

                    <div class="clear"></div>
                    <div class="divImplantarAluno" style="border-bottom:none; width: 30%;">
                        <i class="ion-ios-pulse" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Situação no Ato da Matrícula</label>
                        <select class="custom-select" id="id_situacao_matricula" name="id_situacao_matricula" style="margin-top:5px;">
                            <option value="0" disabled selected hidden>Situação no Ato da Matrícula</option>
                            @foreach ($situacaoMatriculas as $situMatricula)
                            <option value={{$situMatricula->id}} {{ (isset($aluno) && $situMatricula->id == $aluno->id_situacao_matricula) ? 'selected' : ''}}>{{ $situMatricula->situacao_matricula }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; border-bottom:none; width: 20%;">
                        <i class="ion-ios-pulse" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Situação Atual</label>
                        <select class="custom-select" id="id_situacao_atual" name="id_situacao_atual" style="margin-top:5px;" readonly>
                            <option value="" disabled selected hidden>{{ $situacaoAtuals }}</option>
                        </select>
                    </div>

                </div>

                <div class="tab-pane fade" id="nav-implantar-aluno2" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">

                    <div class="divImplantarAluno" style="border-bottom:none; width: 22%">
                        <i class="ion-pinpoint" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Área do Aluno</label>
                        <select class="custom-select" name="area_id" style="margin-top:5px;" onchange="if($(this).val()==3){ $('select#instrumento').prop('disabled', false); } else { $('select#instrumento').prop('disabled', true); $('select#instrumento option[value=0]').prop('selected', true);}">
                            <option value="0" disabled selected hidden>Área do Aluno</option>
                            @foreach ($areas as $area)
                            <option value={{$area->id}} {{ (isset($aluno) && $area->id == $aluno->area_id) ? 'selected' : ''}}>{{ $area->area }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="divImplantarAluno" style="margin-left:20px; border-bottom:none;" width: 25%">
                        <i class="ion-music-note" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Tipo de Instrumento</label>
                        <select class="custom-select" id="instrumento" name="instrumento_id" style="margin-top:5px;" disabled>
                            <option value="0" disabled selected hidden>Tipo de Instrumento</option>
                            @foreach ($instrumentos as $instrumento)
                            <option value={{$instrumento->id}} {{ (isset($aluno) && $instrumento->id == $aluno->instrumento_id) ? 'selected' : ''}}>{{ $instrumento->instrumento }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="clear"></div>
                    <div class="divImplantarAluno" style="border-bottom:none; width: 25%">
                        <i class="ion-home" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">UETE</label>
                        <select class="custom-select" name="omcts_id" style="margin-top:5px;">
                            <option value="0" disabled selected hidden>UETE</option>
                            @foreach ($options_omcts as $omct)
                            <option value={{$omct->id}} {{ (isset($aluno) && $omct->id == $aluno->omcts_id) ? 'selected' : ''}}>{{ $omct->omct }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="divImplantarAluno" style="border-bottom:none; margin-left:20px; width: 15%">
                        <i class="ion-university" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Turma</label>
                        <select class="custom-select" name="turma_id" style="margin-top:5px;">
                            <option value="0" disabled selected hidden>Turma</option>
                            @foreach ($turmas as $turma)
                            <option value={{$turma->id}} {{ (isset($aluno) && $turma->id == $aluno->turma_id) ? 'selected' : ''}}>{{ $turma->turma }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="divImplantarAluno" style="width: 18%; margin-left:20px;">
                        <i class="ion-ribbon-b" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Classificação no CACFS</label>
                        <input class="no-style" name="classif_cacfs" value="{{$aluno->classif_cacfs or old('classif_cacfs') }}" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;" />
                    </div>

                    <div class="clear"></div>
                    <div class="divImplantarAluno" style="border-bottom:none;">
                        <i class="ion-ios-football" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Atleta Marexaer</label>
                        <select {{ ($ownauthcontroller->PermissaoCheck(1)) ? '' : 'disabled' }} class="custom-select" name="atleta_marexaer" style="margin-top:5px;" onchange="if($(this).val()=='S'){ $('input#modalidade').prop('disabled', false); $('input#habilidades').prop('disabled', false); } else { $('input#modalidade').prop('disabled', true); $('input#habilidades').prop('disabled', true); $('input#modalidade option[value=0]').prop('selected', true); $('input#habilidades').prop('value', null); $('input#modalidade').prop('value', null);}">
                            <option value="0" disabled selected hidden>Atleta</option>
                            <option value="S" {{ (isset($aluno) && $aluno->atleta_marexaer) == 'S' ? 'selected' : ''}}>Sim</option>
                            <option value="N" {{ (isset($aluno) && $aluno->atleta_marexaer) == 'N' ? 'selected' : ''}}>Não</option>
                        </select>
                    </div>
                    <div class="divImplantarAluno" style="width: 22%; margin-left:20px;">
                        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Modalidade</label>
                        <input disabled class="no-style" name="modalidade" value="{{$aluno->modalidade or old('modalidade') }}" id="modalidade" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;" />
                    </div>
                    <div class="divImplantarAluno" style=" width: 22%; margin-left:20px;">
                        <i class="ion-radio-waves" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
                        <label class="labelDescricao">Habilidades</label>
                        <input disabled class="no-style" name="habilidades" value="{{$aluno->habilidades or old('habilidades') }}" id="habilidades" type="text" maxlength="100" autocomplete="off" placeholder="" style="width: 100%;margin-top:10px;" />
                    </div>

                </div>

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

                <div class="tab-pane fade" id="nav-implantar-aluno7" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 5px 25px 0px 5px;">

                    @if(isset($aluno) && sizeof($aluno->dependentes) > 0)
                    @foreach ($aluno->dependentes as $dependente)
                    @include('aluno.dependente')
                    @endforeach
                    @else
                    @include('aluno.dependente')
                    @endif

                    <div style="margin-left:15px;border-bottom:none;margin-top:100px;width: 100%;">
                        <button type="button" id="add-dependente"> + </button>
                    </div>

                </div>

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
                <!--<div class="tab-pane fade" id="nav-implantar-aluno9" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 20px;">Implantar 9</div>-->
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
                </div>
            </div>
        </form>
    </div>
</div>

<button type="button" class="btn btn-secondary">
    Limpar
</button>

<button type="button" class="btn btn-primary">
    {{ (isset($aluno)) ? 'Editar' : 'Salvar' }}
</button>

<script>
    $(document).ready(function() {

        $('.data_mask').mask('00/00/0000');
        $('.cep_mask').mask('00000-000');
        $('.telefone_mask').mask('(00)-0000-0000');
        $('.celular_mask').mask('(00)-00000-0000');

        $('#nav-tab a').click(function() {
            $('div.errors-implantar-aluno').empty().hide();
            $('div.success-implantar-aluno').empty().hide();
        });

        $('#add-dependente').click(function() {

            $("#toClone").clone().find("input:text").val("").end().appendTo("#nav-implantar-aluno7");

            //Refaz a mascára
            $('.data_mask').mask('00/00/0000');
        });

        $('.btn.btn-secondary').click(function() {
            //Reseta os Campos
            loadAdminAjaxContent('admin/aluno');
        });

        //Desabilita edição dos campos se não tiver permissão
        @if(isset($aluno) && !$ownauthcontroller->PermissaoCheck(10))
        $('#implantar_aluno input, select').prop('disabled', true);
        @endif

        $('a.open_file').click(function(){

            $('div.erro-upload').slideUp(200, function(){
                $(this).empty();
            });
            $('input[type="file"][name="aluno_imagem"]').trigger('click');
        });

        //Carregar Imagem ao Selecionar
        $('#aluno_imagem').change(function() {
            const file = $(this)[0].files[0];

            var fileSize = (file.size / 1000);
            if (fileSize > 500) {
                $('div.erro-upload').html('O arquivo a ser enviado não deve ser maior que 500Kb').slideDown();
            } else {
                const fileReader = new FileReader();
                fileReader.onloadend = function() {
                    $('#aluno_img').attr('style', 'background: url(' + fileReader.result + ') no-repeat center center; background-size: contain;');
                }
                fileReader.readAsDataURL(file);
            }
        });

        $('button.btn.btn-primary').click(function() {
            
            var formData = $('form#implantar_aluno').serialize();
            var url = "/ajax/admin/aluno{{ ((isset($aluno->id)) ? '/'.$aluno->id : '') }}";
            
            $.ajax({
                dataType: 'json',
                url: url,
                type: '{{ (isset($aluno->id)) ? 'PUT': 'POST' }}',
                data: formData,
                beforeSend: function(){
                    $('div.errors-implantar-aluno').empty().hide();
                },
                success: function(data){
                    
                    if(data.status=='ok'){
                        $('div.success-implantar-aluno').html(data.response).slideDown();

                        if($('form#implantar_aluno input[type="file"]')[0].value != ""){
                            submitImageForm('implantar_aluno', '/ajax/upload/img-aluno/aluno-imagem/'+ data.id_aluno);
                        }
                        
                        setTimeout(function(){
                            $('div.success-implantar-aluno').slideUp(200, function(){
                                $(this).removeClass('alert-success').empty();
                            });

                            //Reseta os Campos
                            loadAdminAjaxContent('admin/aluno');
                        }, 3000);
                    } else {
                        $('div.errors-implantar-aluno').html('<strong>ATENÇÃO:</strong><br />').slideDown();
                        $.each(data.response, function(key, value){
                            $('div.errors-implantar-aluno').append('<li>' + value + '</li>');
                        });
                    }
                },
                error: function(jqxhr){
                    $('div.errors-implantar-aluno').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown(); 
                }         
            });
        });
    });

    function submitImageForm(formID, action) {
        
        var fd = new FormData(document.getElementById(formID));

        $.ajax({
            cache: false,
            dataType: 'json',
            url: action,
            type: "POST",
            data: fd,
            enctype: 'multipart/form-data',
            xhr: function() {
                var xhr = $.ajaxSettings.xhr();
                xhr.upload.onprogress = function(e) {
                    $('div.progress div').css('width', (Math.floor(e.loaded / e.total * 100)) - (1) + '%');
                };
                return xhr;
            },
            beforeSend: function() {
                $('div.progress').slideDown(100);
            },
            success: function(data) {
                if (data.uploadType == 'imgAluno') {
                    $('div.progress div').css('width', '100%');
                    setTimeout(function() {
                        $('div.progress').slideUp(100, function() {
                            $('div.progress div').css('width', '0%');
                        });
                    }, 400);
                    if (data.status == 'ok') {
                        $('div.success-implantar-aluno').html(data.response).slideDown();
                        /*if (!$('div#box-dialog-remover-imagem-perfil').is(':visible')) {
                            $('div#box-dialog-remover-imagem-perfil').slideDown(100);
                        }
                        $('div.imagem_perfil').css('background-image', 'url(\'' + data.src_image + '\')');
                        if (data.tipo == 'minha-imagem') {
                            $('div.data_user_imagem_perfil').css('background-image', 'url(\'' + data.src_image + '\')');
                        }*/
                    } else {
                        $('div.erro-upload').html(data.error).slideDown();
                    }
                }
            },
            error: function(jqxhr) {
                $('div.erro-upload').html('Houve um erro ao tentar enviar o arquivo').slideDown();
            },
            processData: false, // tell jQuery not to process the data
            contentType: false // tell jQuery not to set contentType
        });
    }
</script>
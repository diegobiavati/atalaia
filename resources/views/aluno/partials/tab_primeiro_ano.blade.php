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
        <select {{ ($ownauthcontroller->PermissaoCheck(1)) ? '' : 'disabled' }} class="custom-select" name="atleta_marexaer" style="margin-top:5px;">
            <option value="0" disabled selected hidden>Atleta</option>
            @foreach(array('S' => 'Sim', 'N' => 'Não') as $key => $value)
            <option value="{{$key}}" {{ (isset($aluno) && $aluno->atleta_marexaer == $key) ? 'selected' : ''}}>{{$value}}</option>
            @endforeach
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

    <div class="clear"></div>
    <div class="divImplantarAluno bonificacao" style="border-bottom:none; display:none;">
        <i class="ion-ios-football" style="font-size: 15px; color: #696969; margin-right:5px;"></i>
        <label class="labelDescricao">Tipo Bonificação</label>
        <select {{ ($ownauthcontroller->PermissaoCheck(1)) ? '' : 'disabled' }} class="custom-select" name="bonificacao_atleta" style="margin-top:5px;">
            <option value="0" disabled selected hidden>Selecione o Tipo</option>
            @foreach(array('AA' => 'AA', 'AC' => 'AC', 'AAAC' => 'AA e AC') as $key => $value)
            <option value="{{$key}}" {{ (isset($aluno) && $aluno->bonificacao_atleta == $key) ? 'selected' : ''}}>{{$value}}</option>
            @endforeach
        </select>


    </div>

</div>
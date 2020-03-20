@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Mapa de Controle de Efetivo')

@section('content')

<h3 style="text-align: center; margin-top: -8px; font-size: 18px; font-family: Arial;">
    <u>MAPA DE CONTROLE DE EFETIVO / CFS {{$anoFormacao->ano_per_basico.'-'.$anoFormacao->ano_per_qualificacao}}</u><br />
    Situação Geral em {{ date('d/m/Y H:i:s') }}<br />
</h3>
<div style="margin: 14px auto; width: 100%; text-align: center;">
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 32px auto; width: 99%;font-size: x-small; font-family: Arial;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col" style="width:90px;">UETE</th>
                <th scope="col" style="width:200px;">Área</th>
                <th scope="col" style="width:50px;">Sexo</th>
                <th scope="col" style="width:50px;">Previsto</th>
                <th scope="col">Designado p/ Mtcl</th>
                <th scope="col">Adiamento de Mtcl</th>
                <th scope="col">1ª Mtcl</th>
                <th scope="col">2ª Mtcl</th>
                <th scope="col">Mtcl por Adiamento</th>
                <th scope="col">Mtcl por Ordem Judicial</th>
                <th scope="col">TOTAL do Efetivo Matriculado</th>
                <th scope="col">Desligamento a pedido</th>
                <th scope="col">Desligamento Ex-officio</th>
                <th scope="col">Trancamento de Mtcl a pedido</th>
                <th scope="col">Trancamento de Mtcl Ex-officio</th>
                <th scope="col">TOTAL do Efetivo Desligado</th>
                <th scope="col">Mtcl na EsSA</th>
                <th scope="col">Mtcl na EsSLog</th>
                <th scope="col">Mtcl no CIAvEx</th>
                <th scope="col">EFETIVO EXISTENTE no CFS</th>
            </tr>

            @foreach($mapaEfetivo as $informacao)
            @if(isset($informacao->pb_om_sigla))
            <tr>
                <td>{{$informacao->pb_om_sigla}}</td>
                <td>{{$informacao->area}}</td>
                <td>{{$informacao->sexo}}</td>
                <td>{{$informacao->qtdade_previstomtcl}}</td>
                <td>{{$informacao->qtdade_designadomtcl}}</td>
                <td>{{$informacao->qtdade_adiamentomtcl}}</td>
                <td {{ ($informacao->em_1mtcl <> $informacao->qtdade_em_1mtcl) ? 'bgcolor=#DAA520' : ''}}>{{$informacao->em_1mtcl}}</td>
                <td {{ ($informacao->em_2mtcl <> $informacao->qtdade_em_2mtcl) ? 'bgcolor=#DAA520' : ''}}>{{$informacao->em_2mtcl}}</td>
                <td {{ ($informacao->em_mtcl_adiam <> $informacao->qtdade_em_mtcladiamento) ? 'bgcolor=#DAA520' : ''}}>{{$informacao->em_mtcl_adiam}}</td>
                <td {{ ($informacao->em_mtcl_ord_jud <> $informacao->qtdade_em_mtclordjudicial) ? 'bgcolor=#DAA520' : ''}}>{{$informacao->em_mtcl_ord_jud}}</td>
                <td>{{$informacao->em_total}}</td>
                <td>{{$informacao->desl_apedido}}</td>
                <td>{{$informacao->desl_ExOf}}</td>
                <td>{{$informacao->tranc_apedido}}</td>
                <td>{{$informacao->tranc_ExOf}}</td>
                <td>{{$informacao->ed_total}}</td>
                <td>{{$informacao->qtdade_pqessa}}</td>
                <td>{{$informacao->qtdade_pqesslog}}</td>
                <td>{{$informacao->qtdade_pqciavex}}</td>
                <td>{{$informacao->ee_cfs}}</td>
            </tr>
            @else
            <tr style="font-weight: bold;">
                <td>{{$informacao->totOmct}}</td>
                <td>{{$informacao->totArea}}</td>
                <td>{{$informacao->totSexo}}</td>
                <td>{{$informacao->totPrevisto}}</td>
                <td>{{$informacao->totDesMtr}}</td>
                <td>{{$informacao->totAdiMtr}}</td>
                <td>{{$informacao->tot1Mtr}}</td>
                <td>{{$informacao->tot2Mtr}}</td>
                <td>{{$informacao->totMtrPorAdi}}</td>
                <td>{{$informacao->totMtrOrdJud}}</td>
                <td>{{$informacao->totEfeMtr}}</td>
                <td>{{$informacao->totDeslPed}}</td>
                <td>{{$informacao->totDeslExOf}}</td>
                <td>{{$informacao->totTrancMtrPed}}</td>
                <td>{{$informacao->totTrancMtrExOf}}</td>
                <td>{{$informacao->totEfetDesl}}</td>
                <td>{{$informacao->totMtrEsa}}</td>
                <td>{{$informacao->totMtrEsSLog}}</td>
                <td>{{$informacao->totMtrCIAvEx}}</td>
                <td>{{$informacao->totEECFS}}</td>
            </tr>
            @endif

            @endforeach
        </tbody>
    </table>
</div>

<div style="margin: 14px auto; width: 100%; text-align: center;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>1. Ex-Aluno(s) Adido(s) ou Agregado(s) ao CFS</b></h6>
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: auto auto 35px auto; width: 99%;font-size: x-small; font-family: Arial;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col" style="width:10%;">UETE</th>
                <th scope="col" style="width:30%;">Ex-Aluno CFS</th>
                <th scope="col" style="width:10%;">Situação</th>
                <th scope="col" style="width:50%;">Referência</th>
            </tr>

            @foreach($alunosAdidos as $alunosAdido)
            <tr>
                <td>{{$alunosAdido->omct->sigla_omct}}</td>
                <td style="text-align:left;">{{$alunosAdido->nome_completo}}</td>
                <td>Adido</td>
                <td style="text-align:left;">{{'BI Nº '.$alunosAdido->numero_bi.' '.$alunosAdido->omct->sigla_omct.', de '.App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($alunosAdido->data_bi) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div style="margin: 10px auto; width: 100%; text-align: center;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>2. Candidato Aguardando Matrícula</b></h6>
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: auto auto 35px auto; width: 99%;font-size: x-small; font-family: Arial;">
        <tbody>
            <!--<tr style="background-color: #dc3545;">-->
            <tr>
                @if(isset($parametros))
                <textarea class="form-control" name="textCandidatosAguardando" rows="3" style="display: inline;border: 1px solid #000;width: 99%;font-size: x-small; font-family: Arial;" readonly>{{$parametros->candidato_aguar_aprov}}</textarea>
                @else
                <th scope="col" style="width:10%;">Não há candidato aguardando matrícula para o ano atual.</th>
                @endif
            </tr>
        </tbody>
    </table>
</div>

<div style="margin: 14px auto; width: 100%; text-align: center;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>3. Comparativo de Evasão Escolar por UETE</b></h6>
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: auto auto 35px auto; width: 99%;font-size: x-small; font-family: Arial;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col" style="width:90px;">UETE</th>
                @foreach($evasaoUete as $evasao)
                <th scope="col" style="width:90px;">{{$evasao->sigla_omct}}</th>
                @endforeach
            </tr>

            <tr>
                <td>Evs Es</td>
                @foreach($evasaoUete as $evasao)
                <td scope="col" style="width:90px;">{{$evasao->quantidadeEvasao}}</td>
                @endforeach
            </tr>
            <tr>
                <td>Evs Es %</td>
                @foreach($evasaoUete as $evasao)
                <td scope="col" style="width:90px;">{{$evasao->quantidadeEvasaoPorCent}} %</td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>

<div style="margin: 14px 0 auto 7px; width: 20%; text-align: center;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>4. Taxa de Evasão Escolar do CFS</b></h6>
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: auto auto 45px auto; width: 99%;font-size: x-small; font-family: Arial;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col" style="width:90px;">Efetivo Matriculado</th>
                <th scope="col" style="width:90px;">Efetivo Desligado</th>
                <th scope="col" style="width:90px;">Taxa de Evasão</th>
            </tr>

            <tr>
                <td scope="col" style="width:90px;">{{ $mapaEfetivo->last()->totEfeMtr }}</td>
                <td scope="col" style="width:90px;">{{ $mapaEfetivo->last()->totEfetDesl }}</td>
                <td scope="col" style="width:90px;">{{ number_format(($mapaEfetivo->last()->totEfetDesl / $mapaEfetivo->last()->totEfeMtr) * 100, 2, ',', '') }} %</td>
            </tr>
        </tbody>
    </table>
</div>

<!--<div style="margin: 14px auto; width: 20%; text-align: center;">
    <table width="100%">
        <tbody>
            <tr><td style="font-family: 'Times New Roman', Times, serif;
                                            font-size: 12pt">_____________________________________</td></tr>
            <tr><td style="font-family: 'Times New Roman', Times, serif;
                                            font-size: 12pt">ALESSANDRO PEREIRA LOPES - TC</td></tr>
            <tr><td style="font-family: 'Times New Roman', Times, serif;
                                            font-size: 12pt">Ch Seç Coor Pdg P Bas</td></tr>
        </tbody>
    </table>
</div>-->

@if($relacao == 'excel')

{{ $fileName = bcrypt(date('Y-m-d H:i:s')).'.ods' }}

{{header("Content-Description: PHP Generated Data")}}
{{header("Content-Type: application/x-msexcel")}}
{{header("Content-Disposition: attachment; filename=\"{$fileName}\"")}}
{{header("Expires: 0")}}
{{header("Cache-Control: must-revalidate, post-check=0, pre-check=0")}}
{{header("Pragma: no-cache")}}

@endif
@stop
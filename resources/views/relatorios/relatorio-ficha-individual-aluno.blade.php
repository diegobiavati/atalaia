@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Mapa de Controle de Efetivo')

@section('content')

<h3 style="text-align: center; margin-top: -8px;margin-bottom: 18px; font-size: 18px; font-family: Arial;">
    <u>FICHA INDIVIDUAL DO ALUNO</u>
</h3>



<div style="display: flex; justify-content: center; align-items: center;">
    <table style="width: 70%;">
        <tr>
            <td style="width: 80%;">
                <table rules="all" style='border: 1px solid #000; font-family: initial;width: 95%;'>
                    <tbody>
                        <tr style="background-color: #CCCCCC;">
                            <th scope="col" style="text-align:right;width: 20%;padding-right: 10px;">Número do Aluno:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->numero }}</td>
                        </tr>
                        <tr style="background-color: #CCCCCC;">
                            <th scope="col" style="text-align:right;width: 20%;padding-right: 10px;font-weight: bold;">Nome Completo:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->nome_completo }}</td>
                        </tr>
                        <tr style="background-color: #CCCCCC;">
                            <th scope="col" style="text-align:right;width: 20%;padding-right: 10px;font-weight: bold;">Nome de Guerra:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->nome_guerra }}</td>
                        </tr>
                        <tr style="background-color: #CCCCCC;">
                            <th scope="col" style="text-align:right;width: 20%;padding-right: 10px;font-weight: bold;">OMCT:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->omct->omct }}</td>
                        </tr>
                        <tr style="background-color: #CCCCCC;">
                            <th scope="col" style="text-align:right;width: 20%;padding-right: 10px;font-weight: bold;">Área:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->area->area }}</td>
                        </tr>
                        <tr style="background-color: #CCCCCC;">
                            <th scope="col" style="text-align:right;width: 20%;padding-right: 10px;font-weight: bold;">QMS/Naipe:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->qms->qms }}</td>
                        </tr>
                        <tr style="background-color: #CCCCCC;">
                            <th scope="col" style="text-align:right;width: 20%;padding-right: 10px;font-weight: bold;">Turma:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->turma->turma }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td style="width: 40%;">

                <table rules="all" style='border: 1px solid #000; font-family: initial;width: 80%;'>
                    <tbody>
                        @if(isset($aluno->numero))
                        <tr style="background-color: #CCCCCC;">
                            <div id="aluno_img" class="imagem_aluno" style="background: url('/storage/imagens_aluno/{{  (isset($aluno) && strlen($aluno->imagem_aluno->nome_arquivo) > 12) ? ($aluno->ano_formacao->formacao.'/'.$aluno->imagem_aluno->nome_arquivo) : 'no-image.jpg' }}') no-repeat center center; background-size: contain;"></div>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div>

<div style="display: flex; justify-content: center; align-items: center;">
    <h6 style="margin-top: 40px;width: 70%;"><b>1. DADOS PESSOAIS</b></h6>
</div>

<div style="display: flex; justify-content: center; align-items: center;">
    <table style="width: 70%;">
        <tr>
            <td style="width: 40%;">
                <table rules="all" style='border: 1px solid #000; font-family: initial;width: 95%;'>
                    <tbody>
                        <tr>
                            <th scope="col" style="text-align:right;width: 40%;padding-right: 10px;">Data de Nascimento:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($aluno->data_nascimento) }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;font-weight: bold;">Naturalidade:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->nasc_cidade }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;font-weight: bold;">Nome do Pai:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->nome_pai }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;font-weight: bold;">Nome da Mãe:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->nome_mae }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;font-weight: bold;">Escolaridade:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->escolaridade->escolaridade }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;font-weight: bold;">Religião:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;"> {{ $aluno->religiao->religiao}}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;font-weight: bold;">Grupo Sanguíneo:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->tipo_sanguineo }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;font-weight: bold;">Fator RH:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->fator_rh }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td style="width: 40%;">
                <table rules="all" style='border: 1px solid #000; font-family: initial;width: 80%;'>
                    <tbody>
                        <tr>
                            <th scope="col" style="text-align:right;width: 40%;padding-right: 10px;">Cabelos:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->cabelo }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;">Cutis:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->cutis }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;">Olhos:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->olhos }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;">Altura:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->altura }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;">Tatuagem:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ ($aluno->tatuagem == 'S') ? 'Sim':'Não' }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;">Atleta:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ ($aluno->atleta_marexaer == 'S') ? 'Sim':'Não' }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;">Modalidade(s):</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->modalidade }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div>

<div style="display: flex; justify-content: center; align-items: center;">
    <h6 style="margin-top: 40px;width: 70%;"><b>2. DOCUMENTAÇÃO</b></h6>
</div>

<div style="display: flex; justify-content: center; align-items: center;">
    <table style="width: 70%;">
        <tr>
            <td style="width: 40%;">
                <table rules="all" style='border: 1px solid #000; font-family: initial;width: 95%;'>
                    <tbody>
                        <tr>
                            <th scope="col" style="text-align:right;width: 40%;padding-right: 10px;">Idt Militar:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->doc_idt_militar }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;font-weight: bold;">Órgão Expedidor (Idt Militar):</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->doc_idt_militar_o_exp }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;font-weight: bold;">Data de Expedição (Idt Militar):</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($aluno->doc_idt_militar_dt_exp) }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;font-weight: bold;">CPF:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->doc_cpf }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td style="width: 40%;">
                <table rules="all" style='border: 1px solid #000; font-family: initial;width: 80%;'>
                    <tbody>
                        <tr>
                            <th scope="col" style="text-align:right;width: 40%;padding-right: 10px;">CP:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->doc_cp }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;">Prec-CP:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->doc_preccp }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;">Idt Civil:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->doc_idt_civil }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;">Órgão Expedidor (Idt Civil):</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->doc_idt_civil_o_exp }}</td>
                        </tr>

                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div>

<div style="display: flex; justify-content: center; align-items: center;">
    <h6 style="margin-top: 40px;width: 70%;"><b>3. SERVIÇO MILITAR OU CIVIL ANTERIOR</b></h6>
</div>

<div style="display: flex; justify-content: center; align-items: center;">
    <table style="width: 70%;">
        <tr>
            <td style="width: 40%;">
                <table rules="all" style='border: 1px solid #000; font-family: initial;width: 95%;'>
                    <tbody>
                        <tr>
                            <th scope="col" style="text-align:right;width: 40%;padding-right: 10px;">Situação Atual:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $situacaoAtual }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;font-weight: bold;">Situação Anterior:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ $aluno->situacao_anterior->situacao_anterior }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;font-weight: bold;">Data de Praça no CFS:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($aluno->data_pracacfs) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td style="width: 40%;">
                <table rules="all" style='border: 1px solid #000; font-family: initial;width: 80%;'>
                    <tbody>
                        <tr>
                            <th scope="col" style="text-align:right;width: 40%;padding-right: 10px;">Data de Praça Anterior:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($aluno->primeira_data_praca) }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;">Data de Prom Cb:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($aluno->data_cb) }}</td>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:right;padding-right: 10px;">Data de Prom Sgt Temp:</th>
                            <td scope="col" style="text-align:left;padding-left: 10px;">{{ App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($aluno->data_sgttemp) }}</td>
                        </tr>

                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div>

<hr>

<div style="display: flex; justify-content: right; align-items: right;">
    <h6 style="margin-top: 20px;width: 30%;"> Consulta Realizada em {{ date('d/m/Y H:i:s') }} </h6>
</div>
<script>
    $(document).ready(function() {
        $("body").removeAttr("style"); //remove o display: table; para centralizar tudo
    });
</script>

@stop
@extends('relatorios.template-relatorios-sem-brasao')

@section('title', 'Gavião :: Relatório:: Demonstrativo de notas')

@section('titulo-relatorio', '')

@section('content')

@foreach ($alunos as $class)

<div style="width: 940px; margin:12px auto 56px auto; page-break-after: always;">
    <div style="border: 3px solid #000000; padding: 6px;">
        

        <div class="container" style="border: 0px solid #000000;">
            <div class="row" style="border: 1px solid #000000;">
                <div class="col-md-10" style="border: 1px solid #000000;">
                    <div style="font-size:12px;"><b>(SSAA/SCP)</b></div>
                    <div style="font-size:12px;float: right; margin-top: -20px;">
                        {{date('d/m/Y')}}
                    </div>

                    <div class="row">
                        <div class="col-md-2" style="border: 1px solid #000000;">Logo DE/ESA</div>
                        <div class="col-md-10" style="border: 1px solid #000000;">
                            <div class="form-row">
                                <div class="form-group col-md-6" style="margin-bottom: 0px;">
                                    <div>Aluno</div>
                                    <div>{{$class->aluno->numero}} <b>{{$class->aluno->nome_guerra}}</b> ({{$class->aluno->nome_completo}})</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2" style="border: 1px solid #000000;">LOGO CURSO</div>
            </div>
        </div>


        <!--<div class="col" style="border: 1px solid #000000;">
                    <div style="font-size:12px;"><b>(SSAA/SCP)</b></div>

                    <div style="font-size:12px;float: right; margin-top: -20px; margin-right: 80px;">
                        {{date('d/m/Y')}}
                    </div>
                </div>

                <div class="row" style="border: 1px solid #000000;">
                    <div class="col">IMAGEM DE/ESA</div>
                    <div class="col" style="border: 1px solid #000000;">
                        <div class="col-sm" style="width: 80%;border: 1px solid #000000; padding: 4px;">
                            <b>ALUNO(A):</b> {{$class->aluno->numero}} <b>{{$class->aluno->nome_guerra}}</b> ({{$class->aluno->nome_completo}})<br />
                            <b>CURSO:</b> {{$class->aluno->qms->qms}}<br />
                            <b>TURMA:</b> {{$class->aluno->turmaESA->turma or 'NÃO INFORMADA'}}
                        </div>
                    </div>
                </div>
                <div class="col">IMAGEM CURSO</div>-->

        <div style="clear: both;"></div>
    </div>

        <div style="margin: 8px;">
            <b>SITUAÇÃO GERAL</b>
        </div>
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 0 auto; width: 100%;">
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>N1</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>MENÇÃO</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>CLASSIFICAÇÃO GERAL</b></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{number_format($class->nota_final, '3', '.', '')}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    @php
                    $mencao = json_decode($class->data_demonstrativo)->qualfi_mencao;
                    @endphp
                    {{ $mencao or 'Não calculada'}}
                </td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                    {{ $class->classificacao }}
                </td>
            </tr>
        </table>

        <div style="margin-top: 10px;">
            <b><u>Observações :</u></b>
            <div class="clean"></div>
            - Classificação por Área e Segmento: classificação válida para o Processo de Escolha de QMS dentro de cada segmento (Masc e Fem) na Área Cmb/Log-Tec/Aviação.
            <div class="clean" style="margin-top: 10px;"></div>
            - Classificação Geral: Classificação na Área contendo os segmentos masculino e feminino.
            <div class="clean" style="margin-top: 10px;"></div>
            - De acordo com os Art 59 e 67 das NIAA, o grau dos alunos aprovados por decisão do Diretor de Ensino, após o julgamento do Conselho de Ensino,
            não será computado para o cálculo da Nota do 1º Ano (N1) e da Nota Final de Curso (NFC), sendo utilizados os graus obtidos na disciplina antes da recuperação.
            <div class="clean" style="margin-top: 10px;"></div>
            <b><u>Cálculo Notas :</u></b>
            <div class="clean"></div>
            Avaliação de Acompanhamento (AA): peso 1
            <div class="clean"></div>
            Avaliação de Controle (AC): peso 2
            <div class="clean" style="margin-top: 10px;"></div>
            <table>
                <tr>
                    <td style="padding-right: 6px;">Nota da Disciplina (ND) :</td>
                    <td style="border: 1px solid #000; padding-right: 6px;" rowspan="1"> ND = <u>AA + (2 x AC)</u> <br>
                        <center style="padding-left: 30px;">3</center>
                    </td>
                    <td style="padding-right: 6px;"></td>
                    <td style="border: 1px solid #000; padding-right: 6px;" rowspan="1"> ND = <u>AA1 + AA2 + (2 x AC)</u> <br>
                        <center style="padding-left: 30px;">4</center>
                    </td>
                </tr>
            </table>
            <div class="clean" style="margin-top: 10px;"></div>
            <table>
                <tr>
                    <td style="padding-right: 6px;">Nota do 1º ano CFGS (N1) :</td>
                    <td style="border: 1px solid #000; padding-right: 6px;" rowspan="1"> N1 = <u>ND1 + ND2 + ... + NDm</u>, onde m é a quantidade de disciplinas <br>
                        <center style="padding-right: 230px;">m</center>
                    </td>
                </tr>
            </table>
            <div class="clean" style="margin-top: 10px;"></div>
            <b><u>Menções :</u></b>
            <div class="clean" style="margin-top: 10px;"></div>
            <table>
                <tr>
                    <td style="border: 1px solid #000; text-align:center; width:80px;">Menção</td>
                    <td style="border: 1px solid #000; text-align:center; width:120px;">Faixa de Notas</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; text-align:center;">I</td>
                    <td style="border: 1px solid #000; text-align:center;">0,000 - 4,999</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; text-align:center;">R</td>
                    <td style="border: 1px solid #000; text-align:center;">5,000 - 6,999</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; text-align:center;">B</td>
                    <td style="border: 1px solid #000; text-align:center;">7,000 - 7,9999</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; text-align:center;">MB</td>
                    <td style="border: 1px solid #000; text-align:center;">8,000 - 9,499</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; text-align:center;">E</td>
                    <td style="border: 1px solid #000; text-align:center;">9,500 - 10,0000</td>
                </tr>
            </table>
        </div>

</div>

@endforeach
<script>
    $(document).ready(function() {
        $("body").removeAttr("style"); //remove o display: table; para centralizar tudo
    });
</script>
@stop
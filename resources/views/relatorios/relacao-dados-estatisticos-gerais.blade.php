@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Dados Estatísticos Gerais')

@section('content')

<h3 style="text-align: center; margin-top: -8px; font-size: 18px; font-family: Arial;">
    <u>Dados Estatísticos Gerais / CFS {{$anoFormacao->ano_per_basico.'-'.$anoFormacao->ano_per_qualificacao}}</u><br />
    {{$siglaUete}} em {{ date('d/m/Y H:i:s') }}<br />
</h3>
<div style="margin: 40px auto 40px auto; text-align: center; width: 40%;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>a. Efetivo total (no ato da matrícula)</b></h6>
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 10px auto;font-size: x-small; font-family: Arial; width: 99%;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col">Descrição da Situação</th>
                <th scope="col">Qnt</th>
            </tr>

            @foreach($efetivoTotal as $efetivo)
            @if(isset($efetivo->quantidade))
            <tr>
                <td>{{ $efetivo->situacao_matricula }}</td>
                <td>{{ $efetivo->quantidade }}</td>
            </tr>
            @else
            <tr style="background-color: #CCCCCC;font-weight: bold;">
                <td>{{$efetivo->descricao}}</td>
                <td>{{$efetivo->totQuantidade}}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
</div>

<div style="margin: 10px auto 40px auto; text-align: center; width: 40%;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>b. Situação Anterior (no ato da matrícula)</b></h6>
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 10px auto;font-size: x-small; font-family: Arial; width: 99%;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col">Descrição da Situação</th>
                <th scope="col">Situação Anterior</th>
                <th scope="col">Qnt</th>
            </tr>

            @foreach($efetivoSituacaoAnterior as $efetivo)
            @if(isset($efetivo->quantidade))
            <tr>
                <td>{{ $efetivo->situacao_matricula }}</td>
                <td>{{ $efetivo->situacao_anterior }}</td>
                <td>{{ $efetivo->quantidade }}</td>
            </tr>
            @else
            <tr style="background-color: #CCCCCC;font-weight: bold;">
                <td>{{$efetivo->descricao}}</td>
                <td>{{$efetivo->descricao}}</td>
                <td>{{$efetivo->totQuantidade}}</td>
            </tr>
            @endif
            @endforeach

        </tbody>
    </table>
</div>

<div style="margin: 10px auto 40px auto; text-align: center; width: 40%;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>c. Grau de escolaridade dos alunos (no ato da matrícula)</b></h6>
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 10px auto;font-size: x-small; font-family: Arial; width: 99%;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col">Grau de Escolaridade</th>
                <th scope="col">Qnt</th>
            </tr>

            @foreach($efetivoEscolaridade as $efetivo)
            @if(isset($efetivo->quantidade))
            <tr>
                <td>{{ $efetivo->grau_escolaridade }}</td>
                <td>{{ $efetivo->quantidade }}</td>
            </tr>
            @else
            <tr style="background-color: #CCCCCC;font-weight: bold;">
                <td>{{$efetivo->descricao}}</td>
                <td>{{$efetivo->totQuantidade}}</td>
            </tr>
            @endif
            @endforeach

        </tbody>
    </table>
</div>


<div style="margin: 10px auto 40px auto; text-align: center; width: 40%;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>d. Naturalidade de alunos (no ato da matrícula)</b></h6>
    <table style="width: 100%;">
        <tr>
            <td style="width: 45%;">
                <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 10px auto;font-size: x-small; font-family: Arial; width: 99%;">
                    <tbody>
                        <tr style="background-color: #CCCCCC;">
                            <th scope="col">Estado</th>
                            <th scope="col">Qnt</th>
                        </tr>

                        @foreach($efetivoNaturalidade as $efetivo)
                        @if(isset($efetivo->quantidade))
                        <tr>
                            <td>{{ $efetivo->estado }}</td>
                            <td>{{ $efetivo->quantidade }}</td>
                        </tr>
                        @else
                        <tr style="background-color: #CCCCCC;font-weight: bold;">
                            <td>{{$efetivo->descricao}}</td>
                            <td>{{$efetivo->totQuantidade}}</td>
                        </tr>
                        @endif
                        @endforeach

                    </tbody>
                </table>
            </td>
            <td style="width: 45%;display: inline-block;">
                <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 10px auto;font-size: x-small; font-family: Arial; width: 99%;">
                    <tbody>
                        <tr style="background-color: #CCCCCC;">
                            <th scope="col">Região</th>
                            <th scope="col">Qnt</th>
                        </tr>

                        @foreach($efetivoNaturalRegiao as $efetivo)
                        @if(isset($efetivo->quantidade))
                        <tr>
                            <td>{{ $efetivo->regiao }}</td>
                            <td>{{ $efetivo->quantidade }}</td>
                        </tr>
                        @else
                        <tr style="background-color: #CCCCCC;font-weight: bold;">
                            <td>{{$efetivo->descricao}}</td>
                            <td>{{$efetivo->totQuantidade}}</td>
                        </tr>
                        @endif
                        @endforeach

                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div>

<div style="margin: 10px auto 40px auto; text-align: center; width: 40%;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>e. Faixa etária de alunos (na data de matrícula)</b></h6>
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 10px auto;font-size: x-small; font-family: Arial; width: 99%;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col">Idade (anos)</th>
                <th scope="col">Qnt</th>
            </tr>

            @foreach($efetivoFaixaEtaria as $efetivo)
            @if(isset($efetivo->quantidade))
            <tr>
                <td>{{ $efetivo->idade }}</td>
                <td>{{ $efetivo->quantidade }}</td>
            </tr>
            @else
            <tr style="background-color: #CCCCCC;font-weight: bold;">
                <td>{{$efetivo->descricao}}</td>
                <td>{{$efetivo->totQuantidade}}</td>
            </tr>
            @endif
            @endforeach

        </tbody>
    </table>
</div>

<div style="margin: 10px auto 40px auto; text-align: center; width: 40%;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>f. Religião de alunos (no ato da matrícula)</b></h6>
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 10px auto;font-size: x-small; font-family: Arial; width: 99%;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col">Religião</th>
                <th scope="col">Qnt</th>
            </tr>

            @foreach($efetivoReligiao as $efetivo)
            @if(isset($efetivo->quantidade))
            <tr>
                <td>{{ $efetivo->religiao }}</td>
                <td>{{ $efetivo->quantidade }}</td>
            </tr>
            @else
            <tr style="background-color: #CCCCCC;font-weight: bold;">
                <td>{{$efetivo->descricao}}</td>
                <td>{{$efetivo->totQuantidade}}</td>
            </tr>
            @endif
            @endforeach

        </tbody>
    </table>
</div>

<div style="margin: 10px auto 40px auto; text-align: center; width: 40%;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>g. Renda familiar de alunos (no ato da matrícula)</b></h6>
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 10px auto;font-size: x-small; font-family: Arial; width: 99%;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col">Renda Familiar</th>
                <th scope="col">Qnt</th>
            </tr>

            @foreach($efetivoRenda as $efetivo)
            @if(isset($efetivo->quantidade))
            <tr>
                <td>{{ $efetivo->renda }}</td>
                <td>{{ $efetivo->quantidade }}</td>
            </tr>
            @else
            <tr style="background-color: #CCCCCC;font-weight: bold;">
                <td>{{$efetivo->descricao}}</td>
                <td>{{$efetivo->totQuantidade}}</td>
            </tr>
            @endif
            @endforeach

        </tbody>
    </table>
</div>

<div style="margin: 10px auto 40px auto; text-align: center; width: 40%;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>h. Profissão paterna de alunos (no ato da matrícula)</b></h6>
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 10px auto;font-size: x-small; font-family: Arial; width: 99%;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col">Profissão do Pai</th>
                <th scope="col">Situação Militar do Pai</th>
                <th scope="col">Qnt</th>
            </tr>

            @foreach($efetivoProfissaoPai as $efetivo)
            @if(isset($efetivo->quantidade))
            <tr>
                <td>{{ $efetivo->profissao }}</td>
                <td>{{ $efetivo->situacaoMilitar }}</td>
                <td>{{ $efetivo->quantidade }}</td>
            </tr>
            @else
            <tr style="background-color: #CCCCCC;font-weight: bold;">
                <td>{{$efetivo->descricao}}</td>
                <td>{{$efetivo->descricao}}</td>
                <td>{{$efetivo->totQuantidade}}</td>
            </tr>
            @endif
            @endforeach

        </tbody>
    </table>
</div>

<div style="margin: 10px auto 40px auto; text-align: center; width: 40%;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>i. Profissão materna de alunos (no ato da matrícula)</b></h6>
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 10px auto;font-size: x-small; font-family: Arial; width: 99%;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col">Profissão da Mãe</th>
                <th scope="col">Situação Militar da Mãe</th>
                <th scope="col">Qnt</th>
            </tr>

            @foreach($efetivoProfissaoMae as $efetivo)
            @if(isset($efetivo->quantidade))
            <tr>
                <td>{{ $efetivo->profissao }}</td>
                <td>{{ $efetivo->situacaoMilitar }}</td>
                <td>{{ $efetivo->quantidade }}</td>
            </tr>
            @else
            <tr style="background-color: #CCCCCC;font-weight: bold;">
                <td>{{$efetivo->descricao}}</td>
                <td>{{$efetivo->descricao}}</td>
                <td>{{$efetivo->totQuantidade}}</td>
            </tr>
            @endif
            @endforeach

        </tbody>
    </table>
</div>

<div style="margin: 10px auto 40px auto; text-align: center; width: 40%;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>j. Estado civil de alunos (no ato da matrícula)</b></h6>
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 10px auto;font-size: x-small; font-family: Arial; width: 99%;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col">Estado Civil</th>
                <th scope="col">Qnt</th>
            </tr>

            @foreach($efetivoEstadoCivil as $efetivo)
            @if(isset($efetivo->quantidade))
            <tr>
                <td>{{ $efetivo->estadoCivil }}</td>
                <td>{{ $efetivo->quantidade }}</td>
            </tr>
            @else
            <tr style="background-color: #CCCCCC;font-weight: bold;">
                <td>{{$efetivo->descricao}}</td>
                <td>{{$efetivo->totQuantidade}}</td>
            </tr>
            @endif
            @endforeach

        </tbody>
    </table>
</div>

<div style="margin: 10px auto 40px auto; text-align: center; width: 40%;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>k. Dependentes de alunos (no ato da matrícula)</b></h6>
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 10px auto;font-size: x-small; font-family: Arial; width: 99%;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col">Parentesco</th>
                <th scope="col">Qnt</th>
            </tr>

            @foreach($efetivoDependentes as $efetivo)
            @if(isset($efetivo->quantidade))
            <tr>
                <td>{{ $efetivo->parentesco }}</td>
                <td>{{ $efetivo->quantidade }}</td>
            </tr>
            @else
            <tr style="background-color: #CCCCCC;font-weight: bold;">
                <td>{{$efetivo->descricao}}</td>
                <td>{{$efetivo->totQuantidade}}</td>
            </tr>
            @endif
            @endforeach

        </tbody>
    </table>
</div>

<div style="margin: 10px auto 40px auto; text-align: center; width: 40%;">
    <h6 style="text-align: left; margin-top: -8px; font-size: 13px; font-family: Arial;margin-left: 1%;"><b>l. Cor/Raça (no ato da matrícula)</b></h6>
    <table rules="all" class="table-striped" style="border: 1px solid #000; margin: 10px auto;font-size: x-small; font-family: Arial; width: 99%;">
        <tbody>
            <tr style="background-color: #CCCCCC;">
                <th scope="col">Cor/Raça</th>
                <th scope="col">Qnt</th>
            </tr>

            @foreach($efetivoSegmento as $efetivo)
            @if(isset($efetivo->quantidade))
            <tr>
                <td>{{ $efetivo->segmento }}</td>
                <td>{{ $efetivo->quantidade }}</td>
            </tr>
            @else
            <tr style="background-color: #CCCCCC;font-weight: bold;">
                <td>{{$efetivo->descricao}}</td>
                <td>{{$efetivo->totQuantidade}}</td>
            </tr>
            @endif
            @endforeach

        </tbody>
    </table>
</div>

<div style="margin: 10px auto 40px auto; text-align: center; width: 40%;color:#FF0000;">
    <h6><b>OBS: Os relatórios quantitativos compreendem apenas os dados informados. Se o respectivo campo
        estiver vazio, o dado não será contabilizado.</b></h6>
</div>

<script>
    $(document).ready(function() {
        $("body").removeAttr("style"); //remove o display: table; para centralizar tudo
    });
</script>

@stop
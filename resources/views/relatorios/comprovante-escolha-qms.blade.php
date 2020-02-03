@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Relação de alunos sem preenchimento de QMS')

@section('content')

    <h3 style="text-align: center; margin-top: -8px; font-size: 18px;">
        CURSO DE FORMAÇÃO DE SARGENTOS - ANO DE FORMAÇÃO {{$ano_formacao->formacao}}<br />
        <br />CONFERÊNCIA DA ESCOLHA DE QMS<br />
        {{$omct_nome}} 
    </h3>
    <div style="margin: 14px auto; width: 100%;">
        @forelse($alunos as $aluno)
            <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 90%;">
                <tr>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 1.5cm;"><b>NÚMERO</b></td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME DE GUERRA</b></td>
                    @if(isset($opcao_aluno[$aluno->id]))
                        @php
                            $opcoes = unserialize($opcao_aluno[$aluno->id]['opcoes']);
                        @endphp
                        @foreach ($opcoes as $key => $opcao)
                            <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 140px;"><b>OPÇÃO {{$loop->index + 1}}</b></td>
                        @endforeach
                    @else
                        <td rowspan="2" style="text-align: center;">ALUNO NÃO CADASTROU OPÇÕES</td>
                    @endif
                    <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 220px;"><b>CIENTE</b></td>
                </tr>

                <tr>                  
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->numero}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nome_guerra}}</td>               
                    @if(isset($opcao_aluno[$aluno->id]))
                        @php
                            $opcoes = unserialize($opcao_aluno[$aluno->id]['opcoes']);
                        @endphp
                        @foreach ($opcoes as $key => $opcao)
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$qms_data[$opcao]}}</td>
                        @endforeach
                    @endif
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"></td>
                </tr>
            </table>
        @empty
            <div style="text-align: center;">SEM ALUNOS NO UNIVERSO DE ESCOLHA</div>
        @endforelse
    </div>  


@stop
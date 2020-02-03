@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Relação de Alunos Prontos')

@section('content')

    <h3 style="text-align: center; margin-top: -8px; font-size: 18px;">
        CURSO DE FORMAÇÃO DE SARGENTOS - ANO DE FORMAÇÃO {{$ano_formacao->formacao}}<br />
        {{$omct->omct}}<br />EFETIVO EM SITUAÇÕES DIVERSAS
    </h3>
    <div style="margin: 14px auto; width: 100%;">
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 90%;">
            <tr>
                @if(isset($_GET['ordem']))
                    <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 1cm;"><b>ORDEM</b></td>
                @endif
                @if(isset($_GET['numero']))
                    <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 1.5cm;"><b>NÚMERO</b></td>
                @endif
                @if(isset($_GET['nome_comleto']))
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME COMPLETO</b></td>
                @endif
                @if(isset($_GET['nome_guerra']))
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME DE GUERRA</b></td>
                @endif
                @if(isset($_GET['data_nascimento']))
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>DATA DE NASCIMENTO</b></td>
                @endif
                @if(isset($_GET['primeira_data_praca']))
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>DATA DE PRAÇA INICIAL</b></td>
                @endif
                @if(isset($_GET['turma']))
                    <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 2.35cm;"><b>TURMA</b></td>
                @endif
                @if(isset($_GET['area']))
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>AREA</b></td>
                @endif
                @if(isset($_GET['sexo']))
                    <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 2.35cm;"><b>SEGMENTO</b></td>
                @endif
                @if(isset($_GET['email']))
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>E-MAIL</b></td>
                @endif
                @if(isset($_GET['situacoes_diversas']))
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>SITUAÇÃO</b></td>
                @endif
                @if(isset($_GET['observacoes']))
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>OBSERVAÇÃO</b></td>
                @endif
            </tr>

            @forelse($alunos as $aluno)

                <tr>
                    @if(isset($_GET['ordem']))
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$loop->index +1}}</td>
                    @endif                    
                    @if(isset($_GET['numero']))
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->numero}}</td>
                    @endif
                    @if(isset($_GET['nome_comleto']))
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nome_completo}}</td>
                    @endif
                    @if(isset($_GET['nome_guerra']))
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nome_guerra}}</td>
                    @endif
                    @if(isset($_GET['data_nascimento']))
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nascimento()}}</td>
                    @endif
                    @if(isset($_GET['primeira_data_praca']))
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->PrimeiraDataPraca()}}</td>
                    @endif
                    @if(isset($_GET['turma']))
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->turma->turma or 'Não informada'}}</td>
                    @endif
                    @if(isset($_GET['area']))
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->area->area or 'Não informada'}}</td>
                    @endif
                    @if(isset($_GET['sexo']))
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->segmento()}}</td>
                    @endif
                    @if(isset($_GET['email']))
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->email}}</td>
                    @endif
                    @if(isset($_GET['situacoes_diversas']))
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->situacao->situacao}}</td>
                    @endif
                    @if(isset($_GET['observacoes']))
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->situacoes_diversas_obs}}</td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="100" style="text-align: center; padding: 12px;">SEM OCORRÊNCIAS</td></tr>
            @endforelse

        </table>
    </div>  


@stop
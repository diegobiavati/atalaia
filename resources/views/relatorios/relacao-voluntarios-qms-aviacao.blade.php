@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Relação de Voluntários Para QMS Aviação')

@section('content')

    <h3 style="text-align: center; margin-top: -8px; font-size: 18px;">
        CURSO DE FORMAÇÃO DE SARGENTOS - ANO DE FORMAÇÃO {{$ano_formacao->formacao}}<br />
        @if(isset($_GET['omctID']) && $_GET['omctID']=='todas_omct')
            TODAS UETE
        @else
            {{$omct->omct}}
        @endif
        <br />RELAÇÃO DE {{$filtro_voluntario}} - QMS AVIAÇÃO
    </h3>
    <div style="margin: 14px auto; width: 100%;">
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 90%;">
            <tr>
                @if(isset($_GET['ordem']))
                    <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 1cm;"><b>ORDEM</b></td>
                @endif
                <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 1.5cm;"><b>NÚMERO</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME COMPLETO</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME DE GUERRA</b></td>
                @if(isset($_GET['omctID']) && $_GET['omctID']=='todas_omct')
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>UETE</b></td>                
                @endif
            </tr>

            @forelse($alunos as $aluno)
                <tr>
                    @if(isset($_GET['ordem']))
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$loop->index +1}}</td>
                    @endif                    
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->numero}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nome_completo}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nome_guerra}}</td>
                    @if(isset($_GET['omctID']) && $_GET['omctID']=='todas_omct')
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->omct->sigla_omct}}</td>                
                    @endif                    
                </tr>
            @empty
                <tr><td colspan="100" style="text-align: center; padding: 12px;">SEM OCORRÊNCIAS</td></tr>
            @endforelse
        </table>
    </div>  


@stop
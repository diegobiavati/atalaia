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
        <br /><br>RELAÇÃO DE {{$filtro_voluntario}} - QMS AVIAÇÃO
    </h3>
    <div style="margin: 14px auto; width: 60%;">
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 90%;">
            <tr>
                @if(isset($_GET['ordem']))
                    <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 1cm;"><b>Ordem</b></td>
                @endif
                <td style="border: 1px solid #000; padding: 6px; text-align: center; width: 1.5cm;"><b>Número</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Nome Completo</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Nome de Guerra</b></td>
                @if(isset($_GET['omctID']) && $_GET['omctID']=='todas_omct')
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>UETE</b></td>                
                @endif
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Segmento</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Dt Nascimento</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Idt Militar</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>CPF</b></td>
            </tr>

            @forelse($alunos_voluntarios_aviacao as $voluntario)
                <tr>
                    @if(isset($_GET['ordem']))
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$loop->index +1}}</td>
                    @endif                    
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$voluntario->aluno->numero}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$voluntario->aluno->nome_completo}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$voluntario->aluno->nome_guerra}}</td>
                    @if(isset($_GET['omctID']) && $_GET['omctID']=='todas_omct')
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$voluntario->aluno->omct->sigla_omct}}</td>                
                    @endif                    
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$voluntario->aluno->sexo}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ App\Http\Controllers\Utilitarios\FuncoesController::formatDateEntoBr($voluntario->aluno->data_nascimento) }}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$voluntario->aluno->doc_idt_militar}}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$voluntario->aluno->doc_cpf}}</td>
                </tr>
            @empty
                <tr><td colspan="100" style="text-align: center; padding: 12px;">SEM OCORRÊNCIAS</td></tr>
            @endforelse
        </table>
    </div>  

    <script>
        $(document).ready(function() {
            $("body").removeAttr("style"); //remove o display: table; para centralizar tudo
        });
    </script>
@stop
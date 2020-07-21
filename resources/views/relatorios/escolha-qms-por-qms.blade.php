@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Escolha de QMS')

@section('titulo-relatorio', 'ESCOLHA DE QMS - CFS - FORMAÇÃO '. $ano_selecionado)

@section('content')
@php

@endphp

<div style="width: 1600px; margin: 4px auto; page-break-after: always; text-align: center;">

    @if($_GET['tipo_relatorio']==5 || $_GET['tipo_relatorio']==9)
        <h4 style="margin-top: -18px;">RELAÇÃO DE ALUNOS POR QMS - SEGMENTO MASCULINO</h4>
    @endif

    @if($_GET['tipo_relatorio']==6 || $_GET['tipo_relatorio']==10)
        <h4 style="margin-top: -18px;">RELAÇÃO DE ALUNAS POR QMS - SEGMENTO FEMININO</h4>
    @endif

    @if($alunos)

        @foreach ($qms as $item)

            <h4 style="margin-top: 56px;">{{mb_strtoupper($item->qms)}}</h4>

            @if(isset($alunos[$item->id]))

                <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 80%;">
                    <tr style="background-color: #E6E6E6;">
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Class QMS</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>UETE</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>N1</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Classificação<br />Perd Bas</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Classificação<br />Por area</b></td>
                    </tr>

                    @forelse ($alunos[$item->id] as $item_aluno)

                        
                            <tr>
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$loop->index + 1}}</td>
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$item_aluno['numero']}}</td>
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$item_aluno['nome_guerra']}}</td>
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$item_aluno['omct']}}</td>
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{number_format($item_aluno['NPB'], 3, ',', '')}}</td>
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$item_aluno['classificacao']}}</td>
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$item_aluno['classificacao_por_area']}}</td>
                            </tr>
                    
                    @empty

                        <tr><td style="colspan=100">SEM ALUNOS DESIGNADOS</td></tr> 
                    
                    @endforelse

                </table>

            @else

                <div style="text-align: center;">SEM ALUNOS DESIGNADOS</div> 

            @endif
  

        @endforeach
    

    @else

        <div style="text-align: center;">DISTRIBUIÇÃO DENTRO DAS QMS INDISPONÍVEL</div>

    @endif

    </div>

    <script>
        $(document).ready(function() {
            $("body").removeAttr("style"); //remove o display: table; para centralizar tudo
        });
    </script>
@stop
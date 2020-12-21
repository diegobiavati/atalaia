@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Classificação Geral de Alunos')

@section('titulo-relatorio', 'CLASSIFICAÇÃO CFGS - FORMAÇÃO '. $ano_selecionado)

@section('content')
@php

@endphp

@if(count($alunos_classificacao)>0)

<div style="width: 1600px; margin: 4px auto; page-break-after: always; text-align: center;">
    
    @if($_GET['options_class_geral']==1 || $_GET['options_class_geral']==2)
    
    @if($_GET['options_class_geral']==2)
        <h4 style="text-align: center; margin-top: -18px;">CLASSIFICAÇÃO GERAL REPROVADOS INCLUSEIVE</h4>
        <div style="background-color: #FFFFFF; width: 24px; height: 24px; border: 1px solid #000; display: inline-block; vertical-align: middle;"></div> Aprovados  
        <div style="background-color: #F6CECE; width: 24px; height: 24px; border: 1px solid #000; display: inline-block; vertical-align: middle; margin-left: 12px;"></div> Reprovados 
    @elseif($_GET['options_class_geral']==1)
        <h4 style="text-align: center; margin-top: -18px;">CLASSIFICAÇÃO GERAL APROVADOS</h4>
        <div style="background-color: #B40404; width: 24px; height: 24px; border: 1px solid #000; display: inline-block; vertical-align: middle; margin-left: 12px;"></div> Empate 
    @endif
    
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 80%;">
            <tr style="background-color: #E6E6E6;">
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME COMPLETO</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>OMCT</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ÁREA</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOTA FINAL</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>N1</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>CLASSIFICAÇÃO</b></td>
            </tr>
            @php
                $i =0;
            @endphp
            @foreach($alunos_classificacao as $aluno)
                
                @if($_GET['options_class_geral']==1 && $aluno->reprovado=='N')
                    @php
                        $i++;
                        if(isset($nota_final_empatada[str_replace('.', ',', $aluno->nota_final)])){
                            $style_empate = 'background-color: #B40404; color: #ffffff';
                        } else {
                            $style_empate = '';
                        }
                    @endphp
                    <tr>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->numero }}</td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->nome_completo }}</td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->omct->sigla_omct }}</td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->area->area }}</td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center; {!!$style_empate!!}">{{ $aluno->nota_final }}</td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center; {!!$style_empate!!}">{{ number_format($aluno->nota_final_arredondada, '3', ',', '') }}</td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $i }}</td>
                    </tr>
                @endif

                @if($_GET['options_class_geral']==2)
                    @php
                        $i++;
                        if($aluno->reprovado=='S'){
                            $style_bg_reprovado = 'style="background-color: #F8E0E0;"';
                        } else {
                            $style_bg_reprovado = '';
                        }

                        if(isset($nota_final_empatada[str_replace('.', ',', $aluno->nota_final)])){
                            $style_empate = 'color: #B40404;';
                        } else {
                            $style_empate = '';
                        }

                    @endphp
                    <tr {!!$style_bg_reprovado!!}>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->numero }}</td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->nome_completo }}</td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->omct->sigla_omct }}</td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->area->area }}</td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center; {!!$style_empate!!}">{{ $aluno->nota_final }}</td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center; {!!$style_empate!!}">{{ number_format($aluno->nota_final_arredondada, '3', ',', '') }}</td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $i }}</td>
                    </tr>
                @endif

            @endforeach

            </table>
        
        @endif

        @if($_GET['options_class_geral']==3)

            @foreach($areas as $area)
                <h4 style="text-align: center; margin-top: -18px;">CLASSIFICAÇÃO SEGMENTO MASCULINO POR ÁREA (APROVADOS)</h4>
                <h4 style="text-align: center;">{{$area->area}}</h4>
                <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 80%;">
                    <tr style="background-color: #E6E6E6;">
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME COMPLETO</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>OMCT</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOTA FINAL</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>N1</b></td>
                        @if($area->id==3)
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Instrumento</b></td>
                        @endif                        
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>CLASSIFICAÇÃO</b></td>
                    </tr>
                @php
                    $i = 0;
                @endphp
                @foreach($alunos_classificacao as $aluno)
                    @php
                        if($aluno->aluno->area_id==$area->id && $aluno->aluno->sexo=='M' && $aluno->reprovado=='N'){
                            $i++;
                        }
                    @endphp
                    
                    @if($aluno->aluno->area_id==$area->id && $aluno->aluno->sexo=='M' && $aluno->reprovado=='N')
                        <tr>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->numero }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->nome_completo }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->omct->sigla_omct }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->nota_final }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ number_format($aluno->nota_final_arredondada, '3', ',', '') }}</td>
                            @if($aluno->aluno->area_id==3)
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->aluno->instrumento->instrumento or 'Não informado'}}</td>
                            @endif
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $i }}</td>
                        </tr>
                    @endif

                    
                @endforeach

                </table>
                
            @endforeach
        
        @endif

        @if($_GET['options_class_geral']==6)

            @foreach($areas as $area)
                <h4 style="text-align: center; margin-top: -8px;">CLASSIFICAÇÃO SEGMENTO MASCULINO POR ÁREA (REPROVADOS INCLUSIVE)</h4>
                <h4 style="text-align: center;">{{$area->area}}</h4>
                <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 80%;">
                    <tr style="background-color: #E6E6E6;">
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME COMPLETO</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>OMCT</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOTA FINAL</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>N1</b></td>
                        @if($area->id==3)
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">Instrumento</td>
                        @endif                        
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>CLASSIFICAÇÃO</b></td>
                    </tr>
                @php
                    $i = 0;
                @endphp
                @foreach($alunos_classificacao as $aluno)
                    @php
                        if($aluno->aluno->area_id==$area->id && $aluno->aluno->sexo=='M'){
                            $i++;
                        }
                    @endphp
                    
                    @if($aluno->aluno->area_id==$area->id && $aluno->aluno->sexo=='M')
                        <tr>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->numero }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->nome_completo }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->omct->sigla_omct }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->nota_final }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ number_format($aluno->nota_final_arredondada, '3', ',', '') }}</td>
                            @if($aluno->aluno->area_id==3)
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->aluno->instrumento->instrumento or 'Não informado'}}</td>
                            @endif
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $i }}</td>
                        </tr>
                    @endif

                    
                @endforeach

                </table>
                
            @endforeach
        
        @endif

        @if($_GET['options_class_geral']==4)

            @foreach($areas as $area)
                <h4 style="text-align: center; margin-top: -18px;">CLASSIFICAÇÃO SEGMENTO FEMININO POR ÁREA (APROVADAS)</h4>
                <h4 style="text-align: center;">{{$area->area}}</h4>
                <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 80%;">
                    <tr style="background-color: #E6E6E6;">
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME COMPLETO</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>OMCT</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOTA FINAL</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>N1</b></td>
                        @if($area->id==3)
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Instrumento</b></td>
                        @endif                        
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>CLASSIFICAÇÃO</b></td>
                    </tr>
                @php
                    $i = 0;
                @endphp
                @foreach($alunos_classificacao as $aluno)
                    @php
                        if($aluno->aluno->area_id==$area->id && $aluno->aluno->sexo=='F' && $aluno->reprovado=='N'){
                            $i++;
                        }
                    @endphp
                    
                    @if($aluno->aluno->area_id==$area->id && $aluno->aluno->sexo=='F' && $aluno->reprovado=='N')
                        <tr>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->numero }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->nome_completo }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->omct->sigla_omct }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->nota_final }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ number_format($aluno->nota_final_arredondada, '3', ',', '') }}</td>
                            @if($aluno->aluno->area_id==3)
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->aluno->instrumento->instrumento or 'Não informado'}}</td>
                            @endif
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $i }}</td>
                        </tr>
                    @endif

                    
                @endforeach

                </table>
                
            @endforeach
        
        @endif

        @if($_GET['options_class_geral']==7)

            @foreach($areas as $area)
                <h4 style="text-align: center; margin-top: -8px;">CLASSIFICAÇÃO SEGMENTO FEMININO POR ÁREA (REPROVADAS INCLUSIVE)</h4>
                <h4 style="text-align: center;">{{$area->area}}</h4>
                <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 80%;">
                    <tr style="background-color: #E6E6E6;">
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME COMPLETO</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>OMCT</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOTA FINAL</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>N1</b></td>
                        @if($area->id==3)
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Instrumento</b></td>
                        @endif                        
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>CLASSIFICAÇÃO</b></td>
                    </tr>
                @php
                    $i = 0;
                @endphp
                @foreach($alunos_classificacao as $aluno)
                    @php
                        if($aluno->aluno->area_id==$area->id && $aluno->aluno->sexo=='F'){
                            $i++;
                        }
                    @endphp
                    
                    @if($aluno->aluno->area_id==$area->id && $aluno->aluno->sexo=='F')
                        <tr>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->numero }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->nome_completo }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->omct->sigla_omct }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->nota_final }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ number_format($aluno->nota_final_arredondada, '3', ',', '') }}</td>
                            @if($aluno->aluno->area_id==3)
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->aluno->instrumento->instrumento or 'Não informado'}}</td>
                            @endif        
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $i }}</td>
                        </tr>
                    @endif

                    
                @endforeach

                </table>
                
            @endforeach
        
        @endif
        
        @if($_GET['options_class_geral']==5)

            @foreach($areas as $area)
                <h4 style="text-align: center; margin-top: -8px;">CLASSIFICAÇÃO POR ÁREA MASCULINO E FEMININO (APROVADOS)</h4>
                <h4 style="text-align: center;">{{$area->area}}</h4>
                <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 80%;">
                    <tr style="background-color: #E6E6E6;">
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME COMPLETO</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>OMCT</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOTA FINAL</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>N1</b></td>
                        @if($area->id==3)
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Instrumento</b></td>
                        @endif
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>CLASSIFICAÇÃO</b></td>
                    </tr>
                @php
                    $i = 0;
                @endphp
                @foreach($alunos_classificacao as $aluno)
                    @php
                        if($aluno->aluno->area_id==$area->id && $aluno->reprovado=='N'){
                            $i++;
                        }
                    @endphp
                    
                    @if($aluno->aluno->area_id==$area->id && $aluno->reprovado=='N')
                        <tr>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->numero }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->nome_completo }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->omct->sigla_omct }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->nota_final }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ number_format($aluno->nota_final_arredondada, '3', ',', '') }}</td>
                            @if($aluno->aluno->area_id==3)
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->aluno->instrumento->instrumento or 'Não informado'}}</td>
                            @endif
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $i }}</td>
                        </tr>
                    @endif

                    
                @endforeach

                </table>
                
            @endforeach
        
        @endif

        @if($_GET['options_class_geral']==8)

            @foreach($areas as $area)
                <h4 style="text-align: center; margin-top: -8px;">CLASSIFICAÇÃO POR ÁREA MASCULINO E FEMININO (REPROVADAS INCLUSIVE)</h4>
                <h4 style="text-align: center;">{{$area->area}}</h4>
                <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 80%;">
                    <tr style="background-color: #E6E6E6;">
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME COMPLETO</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>OMCT</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOTA FINAL</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>N1</b></td>
                        @if($area->id==3)
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>Instrumento</b></td>
                        @endif
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>CLASSIFICAÇÃO</b></td>
                    </tr>
                @php
                    $i = 0;
                @endphp
                @foreach($alunos_classificacao as $aluno)
                    @php
                        if($aluno->aluno->area_id==$area->id){
                            $i++;
                        }
                    @endphp
                    
                    @if($aluno->aluno->area_id==$area->id)
                        <tr>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->numero }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->nome_completo }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->omct->sigla_omct }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->nota_final }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ number_format($aluno->nota_final_arredondada, '3', ',', '') }}</td>
                            @if($aluno->aluno->area_id==3)
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->aluno->instrumento->instrumento or 'Não informado'}}</td>
                            @endif
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $i }}</td>
                        </tr>
                    @endif

                    
                @endforeach

                </table>
                
            @endforeach
        
        @endif

        @if($_GET['options_class_geral']==9)

            @foreach($areas as $area)
                <h4 style="text-align: center; margin-top: 18px;">CLASSIFICAÇÃO POR ÁREA</h4>
                <h4 style="text-align: center;">{{$area->area}}</h4>
                <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 90%; text-align: center;">
                    <tr style="background-color: #E6E6E6;">
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME COMPLETO</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>SEGMENTO</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>OMCT</b></td>
                        @foreach ($disciplinas as $disciplina)
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ND<br />{{$disciplina->nome_disciplina_abrev}}</b></td>                            
                        @endforeach
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ND<br />TFM</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>N1</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>MENÇÃO</b></td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>CLASS</b></td>
                    </tr>
                @php
                    $i = 0;
                @endphp
                @foreach($alunos_classificacao as $aluno)
                    @php
                    
                        if($aluno->aluno->area_id==$area->id){
                            $i++;
                        }
                    
                    @endphp
                    
                    @if($aluno->aluno->area_id==$area->id)
                        <tr>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->numero }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->nome_completo }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->sexo }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $aluno->aluno->omct->sigla_omct }}</td>
                            @foreach ($disciplinas as $disciplina)
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                                    @if(isset($notas_data_array[$aluno->aluno_id]['data_demonstrativo']))
                                    
                                        @foreach ($notas_data_array[$aluno->aluno_id]['data_demonstrativo'] as $item)
                                            @if(isset($item['disciplina_id']) && $item['disciplina_id']==$disciplina->id)
                                                
                                                @if(isset($item['media_sem_peso']))
                                                    {{ number_format($item['media_sem_peso'], 3, ',', '') }}
                                                @else
                                                    {{ (is_numeric($item['media']) ? number_format($item['media'], '3', ',', '') : $item['media']) }}
                                                @endif
                                                

                                                @if(isset($item['avaliacoes']['CE']) && $item['avaliacoes']['CE'] == 'APROVADO')
                                                    {!! '<br><b>APROV CE</b></br>' !!}
                                                @endif
                                            @endif
                                            @if(isset($item['disciplina_id']) && $item['disciplina_id']==99999)
                                                @php    
                                                  $media_tfm=number_format($item['media'], '3', ',', '');
                                                @endphp
                                                @if(isset($item['avaliacoes']['CE']) && $item['avaliacoes']['CE'] == 'APROVADO')
                                                    @php
                                                        $media_tfm.='<br><b>APROV CE</b></br>';
                                                    @endphp
                                                @endif
                                              
                                            @endif
                                        @endforeach
                                    @endif
                                </td>                            
                            @endforeach
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{!!$media_tfm or '--'!!}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ number_format($aluno->nota_final_arredondada, '3', ',', '') }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                                @php

                                foreach($mencoes as $mencao){
                                    if($aluno->nota_final_arredondada>=$mencao->inicio && $aluno->nota_final_arredondada<=$mencao->fim){
                                        $mencao_aluno = $mencao->mencao;
                                        break;
                                    }
                                }
    
                                @endphp
                                {{$mencao_aluno or 'Não calculada'}}                            
                            </td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $i }}</td>
                        </tr>
                    @endif

                    
                @endforeach

                </table>
                
            @endforeach
        
        @endif 
        
    </div>

@else

    <div style="text-align: center;">CLASSIFICAÇÃO INDISPONÍVEL PARA ESSE UNIVERSO</div>

@endif
<script>
    $(document).ready(function() {
        $("body").removeAttr("style"); //remove o display: table; para centralizar tudo
    });
</script>
@stop
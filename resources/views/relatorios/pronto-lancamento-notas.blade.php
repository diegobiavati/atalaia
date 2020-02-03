@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Pronto do lançamento de notas na '.$avaliacao->nome_completo.'('.$avaliacao->nome_abrev.')')

{{-- @section('titulo-relatorio', 'PRONTO DO LANÇAMENTO DE NOTAS RELATIVO À '.$avaliacao->nome_completo.'('.$avaliacao->nome_abrev.') DA DISCIPLINA '.$avaliacao->disciplinas->nome_disciplina) --}}

@section('content')
    <h3 style="text-align: center;">PRONTO DO LANÇAMENTO DE NOTAS RELATIVO À<br /> {{$avaliacao->nome_completo}}({{$avaliacao->nome_abrev}}) DA DISCIPLINA {{$avaliacao->disciplinas->nome_disciplina}}</h3>
    @foreach($omcts as $omct)

        @if($omct->id!=1)

            <div style="width: 940px; margin: 4px auto; page-break-after: always;">

                <div style="text-align: center; margin-top: 32px;">
                    <h4>
                        {{$omct->omct}}<br />
                        ALUNOS SEM NOTA
                    </h4>
                </div>
                
                @foreach($alunos as $aluno)
                    @if($aluno->omcts_id==$omct->id)
                        @php
                            $faltas = true;
                            break;
                        @endphp
                    @endif
                @endforeach
                        
                @if(isset($faltas))
                    @unset($faltas)
                        <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 100%;">
                            <tr>
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NR</b></td>
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME COMPLETO</b></td>
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>NOME DE GUERRA</b></td>
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>TURMA</b></td>
                            </tr>
                        @foreach($alunos as $aluno)

                            @if($aluno->omcts_id==$omct->id)

                                <tr>
                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->numero}}</td>
                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nome_completo}}</td>
                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->nome_guerra}}</td>
                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$aluno->turma->turma or 'Não informada'}}</td>
                                </tr>

                            @endif

                        @endforeach
                    
                    </table>   

                @else
                    <div style="text-align: center; color: #0B6121;">SEM LANÇAMENTO PENDENTES</div>
                @endif

            </div>

        @endif

    @endforeach

@stop
@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Pronto de Faltas na '.$avaliacao->nome_completo.'('.$avaliacao->nome_abrev.')')

@section('titulo-relatorio', 'PRONTO DE FALTAS RELATIVO À '.$avaliacao->chamada.'ª CHAMADA DA '.$avaliacao->nome_completo.'('.$avaliacao->nome_abrev.') DA DISCIPLINA '.$avaliacao->disciplinas->nome_disciplina.'')

@section('content')

    @foreach($omcts as $omct)

        @if($omct->id!=1)

            <div style="text-align: center;">
                <h4>{{$omct->omct}}</h4>
            </div>
            
            @php
                $lancado['lancado'] = false;
                $lancado['valida_loop'] = false;
            @endphp
                    @foreach($status_pronto_faltas as $status_pronto)

                        @if($status_pronto->omcts_id==$omct->id)

                            @php
                                $pronto_enviado = true;
                            @endphp

                            @if($status_pronto->status==1)
                                <div style="text-align: center; color: #0B6121;">SEM REGISTRO DE FALTAS</div>                
                            @else    
                                <table style="border: 1px solid #000; border-collapse: collapse; margin: 0 auto; width: 600px;">
                                    @foreach($pronto_faltas as $prontos)
                                        @if($prontos->omcts_id == $omct->id)
                                            @if($prontos->avaliacao_id==$status_pronto->avaliacao_id)
                                            @php
                                            $lancado['lancado'] = true;
                                            $lancado['valida_loop'] = false;

                                            if(is_object($prontos->aluno)){
                                                $numero = $prontos->aluno->numero;
                                                $nome_guerra = $prontos->aluno->nome_guerra;
                                                $nome_completo = $prontos->aluno->nome_completo;
                                            }else{
                                                $numero = $prontos->alunoSitDiv->numero;
                                                $nome_guerra = $prontos->alunoSitDiv->nome_guerra;
                                                $nome_completo = $prontos->alunoSitDiv->nome_completo;
                                            }
                                            @endphp
                                                <tr>
                                                    <td style="border: 1px solid #000; padding: 6px;">{{$loop->index +1}}</td>
                                                    <td style="border: 1px solid #000; padding: 6px;">
                                                        AL {{$numero}} <b>{{$nome_guerra}}</b><br />
                                                        <span style="color: #363636;"><i>{{$nome_completo}}</i></span>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endif
                                        
                                    @endforeach

                                    @if(!$lancado['lancado'] && !$lancado['valida_loop'])
                                        <div style="text-align: center; color: #DF0101;">COM FALTAS - PRONTO DE FALTAS NÃO ENVIADO</div>
                                    @endif
                                </table>
                            @endif

                            @php
                                $lancado['valida_loop'] = true;
                            @endphp
                        @endif

                    @endforeach

                    @if(isset($pronto_enviado))
                        @unset($pronto_enviado)
                    @else
                        
                        @if($avaliacao->chamada==2)

                        @php
                            $valida = false;
                        @endphp
                            @foreach($status_pronto_faltas as $status)
                                @if($status->omcts_id==$omct->id)
                                    @php
                                        $valida = true;
                                    @endphp
                                    @if($status->status==1)
                                        <div style="text-align: center; color: #0104DF;">SEM REGISTRO DE FALTAS</div>
                                    @elseif($status->status==0)
                                        <div style="text-align: center; color: #DF0101;">COM FALTAS - PRONTO NÃO ENVIADO</div>
                                    @endif
                                @endif
                                
                            @endforeach
                            @if(!$valida)
                                
                            
    @php
        $valida_aluno = false;
    @endphp
                            
    <table style="border: 1px solid #000; border-collapse: collapse; margin: 0 auto; width: 600px;">
        @foreach($pronto_faltas_av_ref as $prontos)
        
            @if($prontos->omcts_id == $omct->id)
                    @php
                        $valida = true;
                        if(is_object($prontos->aluno)){
                            $numero = $prontos->aluno->numero;
                            $nome_guerra = $prontos->aluno->nome_guerra;
                            $nome_completo = $prontos->aluno->nome_completo;
                        }else{
                            $numero = $prontos->alunoSitDiv->numero;
                            $nome_guerra = $prontos->alunoSitDiv->nome_guerra;
                            $nome_completo = $prontos->alunoSitDiv->nome_completo;
                        }
                    @endphp
                    <tr>
                        <td style="border: 1px solid #000; padding: 6px;">{{$loop->index +1}}</td>
                        <td style="border: 1px solid #000; padding: 6px;">
                            AL {{$numero}} <b>{{$nome_guerra}}</b><br />
                            <span style="color: #363636;"><i>{{$nome_completo}}</i></span>
                        </td>
                    </tr>
            @endif
            
        @endforeach
    </table>

                                @if(!$valida)
                                    <div style="text-align: center; color: #0104DF;">UETE SEM ALUNOS NESSE UNIVERSO</div>   
                                @endif
                                
                            @endif
                        @else
                            <div style="text-align: center; color: #DF0101;">PRONTO NÃO ENVIADO</div>
                        @endif

                    @endif

        @endif

    @endforeach
    <script>
        $(document).ready(function() {
            $("body").removeAttr("style"); //remove o display: table; para centralizar tudo
        });
    </script>
@stop
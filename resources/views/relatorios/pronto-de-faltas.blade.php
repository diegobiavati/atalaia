@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Pronto de Faltas na '.$avaliacao->nome_completo.'('.$avaliacao->nome_abrev.')')

@section('titulo-relatorio', 'PRONTO DE FALTAS RELATIVO À '.$avaliacao->chamada.'ª CHAMADA DA '.$avaliacao->nome_completo.'('.$avaliacao->nome_abrev.') DA DISCIPLINA '.$avaliacao->disciplinas->nome_disciplina.'')

@section('content')

    @foreach($omcts as $omct)

        @if($omct->id!=1)

        
            <div style="text-align: center;">
                <h4>{{$omct->omct}}</h4>
            </div>

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
                                @if($prontos->omcts_id==$omct->id && $prontos->avaliacao_id==$status_pronto->avaliacao_id)
                                    <tr>
                                        <td style="border: 1px solid #000; padding: 6px;">{{$loop->index +1}}</td>
                                        <td style="border: 1px solid #000; padding: 6px;">
                                            AL {{$prontos->aluno->numero}} <b>{{$prontos->aluno->nome_guerra}}</b><br />
                                            <span style="color: #363636;"><i>{{$prontos->aluno->nome_completo}}</i></span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </table>
                    @endif
                @endif

            @endforeach

            @if(isset($pronto_enviado))
                @unset($pronto_enviado)
            @else
                @if($avaliacao->chamada==2)

                    @foreach($status_pronto_faltas_av_ref as $status)
                        @if($status->omcts_id==$omct->id && $status->status==1)
                            <div style="text-align: center; color: #DF0101;">UETE SEM ALUNOS NESSE UNIVERSO</div>
                        @elseif($status->omcts_id==$omct->id && $status->status==0)
                            <div style="text-align: center; color: #DF0101;">UETE SEM ALUNOS NESSE UNIVERSO</div>
                        @endif
                    @endforeach

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
@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Histórico Escolar')

@section('titulo-relatorio', '')

@section('content')

@foreach ($alunos as $aluno)
    @php
        $data = unserialize($aluno->data);
    @endphp
    <div style="text-align: center; font-size: 18px;">
        <b>HISTÓRICO ESCOLAR</b>
    </div>  
    <div style="width: 940px; margin: 32px auto 56px auto; page-break-after: always;">
        <div style="border: 1px solid #000000; padding: 6px;">
            <b>ALUNO(A):</b> {{$data['cadastro']['numero']}} {{$data['cadastro']['nome_guerra']}} ({{$data['cadastro']['nome_completo']}})<br />
            <b>UETE:</b> {{$data['cadastro']['omct']}}<br />
            <b>TURMA:</b> {{$data['cadastro']['turma']}}<br />
            <b>DATA MATRÍCULA:</b> {{$data['cadastro']['data_matricula']}}<br />
            <b>ANO FORMAÇÃO:</b> {{$data['cadastro']['ano_formacao']}}
            <div style="float: right; margin-top: -74px;">
                <b>SEGMENTO:</b> {{$data['cadastro']['sexo']}}<br />
                <b>AREA:</b> {{$data['cadastro']['area']}}<br />
                <b>VOLUNT AVI:</b> {{$data['cadastro']['voluntario_aviacao']}}<br />
                <b>ATLETA:</b> {{$data['cadastro']['atleta']}}
            </div>
            <div style="clear: both;"></div>
            <div style="border-top: 1px solid #ccc; padding: 12px 0; margin-top: 12px;">
                <b>EMAIL:</b> {{$data['cadastro']['email']}}<br />
                <b>DATA NASCIMENTO:</b> {{$data['cadastro']['data_nascimento']}}<br />
            </div>
            <div style="border-top: 1px solid #ccc; padding: 12px 0; margin-top: 12px;">
                <b>DATA INCLUSÃO:</b> {{$aluno->data_hora_inclusao}}<br />
                <b>
                    <p style="margin-top: 14px;">
                        {{$data['observacoes']['titulo']}}
                    </p>
                </b>
                <p>
                    {{$data['observacoes']['content']}} 
                </p>
            </div>
        </div>

        
        <div style="border: 1px solid #000000; padding: 6px; margin-top: 12px;">
            <div style="margin: 8px; text-align: center;">
                <b>AVALIAÇÕES REALIZADAS</b>
            </div>     
                     
        @foreach($data['avaliacoes'] as $key => $item)
            @if($key!=='taf')
                <div style="margin: 8px;">
                    <b>{{$loop->index+1}}. {{ $item['disciplinaNome']}}</b>
                    @php
                        $i = $loop->index+1;
                    @endphp
                </div>                
                <table style="border: 1px solid #000; border-collapse: collapse; margin: 0 auto; width: 100%;">
                    @if(isset($item['avaliacoes']))
                        <tr>
                            @foreach($item['avaliacoes'] as $key => $avaliacoes)
                                @if(!is_numeric($key))
                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>{{$key}}</b></td>
                                @else
                                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>---</b></td>
                                @endif
                            @endforeach
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ND</b></td>
                        </tr>                          
                        <tr>
                            @foreach($item['avaliacoes'] as $key => $avaliacoes)
                            @if(!is_numeric($key))
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$avaliacoes}}</td>
                            @else
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">---</td>
                            @endif
                            @endforeach
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$item['ND']}}</td>
                        </tr>                          
 
                    @else
                        <tr>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center;">NÃO HÁ AVALIAÇÕES LANÇADAS</td>
                        </td>
                    @endif                        
                </table>   
            @endif
        @endforeach
        <div style="margin: 8px;">
                <b>{{$i+1}}. TESTE DE APTIDÃO FÍSICA</b>
            </div> 
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 0 auto; width: 100%;">
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>CORRIDA</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>FLEXÃO DE BRAÇO</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>FLEXÃO NA BARRA</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ABDOMINAL</b></td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>ND</b></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$data['avaliacoes']['taf']['corrida']}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$data['avaliacoes']['taf']['flex_bra']}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$data['avaliacoes']['taf']['flex_bar']}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$data['avaliacoes']['taf']['abdom']}}</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$data['avaliacoes']['taf']['ND']}}</td>
            </tr>
        </table>
    </div>
    </div>    
@endforeach

@stop
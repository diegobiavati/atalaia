@extends('relatorios.template-relatorios')

@section('title', 'Atalaia :: Relatório::Dados estatísicos')

@section('content')

    <h3 style="text-align: center; margin-top: -8px; font-size: 18px;">
        CURSO DE FORMAÇÃO DE SARGENTOS - ANO DE FORMAÇÃO {{$ano_selecionado->formacao}}<br />
        <br />DADOS ESTATÍSTICOS <p> {{$ava->nome_completo}} ({{$ava->nome_abrev}}) DE {{$ava->disciplinas->nome_disciplina}}</p>
    </h3>
    <div style="margin: 14px auto; width: 100%;">
        <table style="border: 1px solid #000; border-collapse: collapse; margin: 32px auto; width: 90%;">
            <!--tr style="background-color: #E6E6E6;"-->
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center; background-color: #E6E6E6; width: 180px;"><b>MENÇÃO</b></td>
                @foreach($mencoes as $mencao)
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;"><b>{{$mencao->mencao}}</b></td>
                @endforeach
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center; background-color: #E6E6E6;"><b>QUANTITATIVO</b></td>
                @foreach($mencoes as $mencao)
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{$mencao_qtde[$mencao->mencao]}}</td>
                @endforeach
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 6px; text-align: center; background-color: #E6E6E6;"><b>PORCENTAGEM</b></td>
                @foreach($mencoes as $mencao)      
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{number_format($mencao_qtde[$mencao->mencao]*100/$total_notas, 1, ',', '')}}</td>
                @endforeach
            </tr>

        </table>
    </div>  


@stop
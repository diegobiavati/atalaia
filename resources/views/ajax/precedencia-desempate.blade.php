<style>
        tr:nth-child(even) {
            background: #F2F2F2;
        }    
    </style>
<div class="card bg-light mb-3">
    <div class="card-header">
        <i class="ion-social-yahoo"></i><strong>Precedência para desempate de notas</strong>
        <div style="float: right">
            <!--div class="box-pesquisar-in-card-title" style="display: inline-block; padding:0; height: 36px;">
                <input class="pesquisar-in-card-title busca-operador" type="text" placeholder="Busca" />
                <a class="no-style" href="javascript: void(0);">
                        <i class="ion-android-search" style="color: #696969;"></i>
                </a>
            </div-->                
            <!--a href="javascript: void(0);" data-toggle="popover" data-container="body" data-placement="bottom" data-html="true" style="margin-left: 12px;">
                <i class="ion-android-more-vertical" style="color: #696969;"></i>
            </a>
            <div id="popover-content" style="display: none;">
                <div class="menu_inside_popover">
                    <i class="ion-compose"></i><a href="javascript: void(0);" onclick="dialogAdicionarAnoFormacao();">Novo ano de formação</a><br />                 
                </div>
            </div-->    
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-danger errors-cadastro-ano-formacao" role="alert"></div>
        <h5 class="card-title" style="text-align: center; margin: 18px 0 44px 0;">
            RELAÇÃO DE ALUNOS COM MÉDIA FINAL EMPATADA
            @if(count($classificacao)>0)
                <span style="color: #585858; font-size: 14px;">
                    <p style="margin-top: 12px;">
                        Caso seja verificada uma inconcistência de classificação, tendo em vista o empate de notas,
                        atribua uma precedência aos alunos em questão.<br />
                        Quanto MAIOR a precedência, melhor será a classificação do aluno em relação ao de MENOR precedência (respeitando o empate).<br />
                        <b>Importante:</b> Para que sejam atualizados os valores, será necessário recarregar a classificação geral dos alunos na Seção de Relatórios. <br />
                        A precedência deve obedecer a prescrtita no ESTATUTO DOS MILITARES.

                    </p>
                </span>
            @endif 
        </h5>           
        <p class="card-text">
            @if(count($classificacao)>0)

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">AL NR.</th>
                            <th scope="col">NOME</th>
                            <th scope="col">UETE</th>
                            <th scope="col">NFc</th>
                            <th scope="col">NF</th>
                            <th scope="col" style="text-align: center;">CLASSIFICAÇÃO</th>
                            <th scope="col" style="text-align: center;">PRECEDÊNCIA</th>
                        </tr>
                    </thead>
                    <tbody>

                @foreach($classificacao as $aluno)
                        <tr>
                            <td>{{$aluno->aluno->numero}}</td>
                            <td>{{$aluno->aluno->nome_guerra}}</td>
                            <td>{{$aluno->aluno->omct->sigla_omct}}</td>
                            <td>{{$aluno->nota_final}}</td>
                            <td>{{number_format($aluno->nota_final_arredondada, 3, ',', '')}}</td>
                            <td style="text-align: center;">{{$aluno->classificacao}}</td>
                            <td style="text-align: center;">
                                    <a href="javascript: void(0);" class="no-style" style="font-size: 18px; color: #B40404;" onclick="alterarPrecedencia('decrement', {{$aluno->aluno_id}});">
                                        <i class="ion-minus-circled"></i>
                                    </a>
                                    <div id="precedencia_{{$aluno->aluno_id}}" style="display: inline-block; width: 20px; vertical-align: top;" >{{$aluno->aluno->precedencia}}</div>
                                    <a href="javascript: void(0);" class="no-style" style="font-size: 18px; color: #0B6121;" onclick="alterarPrecedencia('increment', {{$aluno->aluno_id}});">
                                        <i class="ion-plus-circled"></i>
                                    </a>
                            </td>
                        </tr>
                @endforeach

                    </tbody>
                </table>
                @else
                    <div class="box-registro-not-found">
                        <i class="ion-social-snapchat-outline" style="font-size: 32px"></i><br />
                        <span style="color: brown">Não há cálculo de classificação para este ano de formação</span>
                    </div>
                            
            @endif
        </p>
    </div>
    
</div>

<script>

    $("[data-toggle=popover]").popover({
            trigger: 'focus',
            html: true, 
            delay: { "show": 100, "hide": 400 },
            content: function() {
                return $('#popover-content').html();
            }
    }); 

</script>
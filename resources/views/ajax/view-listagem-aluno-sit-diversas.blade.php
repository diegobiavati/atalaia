<table class="table table-striped" style="margin: 60px 0 90px 0;">
    
        @if(count($alunos) > 0)
        <thead>
            <tr>
                <th>NÚMERO</th>
                <th>NOME DE GUERRA</th>
                <th>NOME COMPLETO</th>
                <th>OMCT</th>
                <th>CURSO</th>
                <th style="text-align: center;">ANO DE FORMAÇÃO</th>
                <th style="width: 360px;">SITUAÇÃO</th>
                <th style="text-align: center;">{{ (($ownauthcontroller->PermissaoCheck(11)) ? 'AÇÃO' : '') }}</th>
            </tr>
        </thead>
            @foreach ($alunos as $aluno)
                @php
                    $observacao = ($aluno->situacoes_diversas_obs != '') ? ': ' . $aluno->situacoes_diversas_obs : '';
                @endphp
                
                <tr id="alunoID_{{$aluno->id}}">
                <td>{{$aluno->numero}}</td>
                <td>{{$aluno->nome_guerra}}</td>
                <td>{{$aluno->nome_completo}}</td>
                
                <td>{{ $aluno->omct->sigla_omct }}</td>
                <td>{{ $aluno->qms->qms }}</td>

                <td style="text-align: center;">{{$aluno->ano_formacao->formacao}}</td>
                <td id="situacao_alunoID_{{$aluno->id}}"><span style="color: #B40404;"><b>{{$aluno->situacao->situacao}}</b></span>{{$observacao}}</td>
                <td style="text-align: center; font-size: 18px;"><a href="javascript: void(0);" class="no-style" onclick="dialogEditarCadastroAlunoSitDivNovo({{ (($ownauthcontroller->PermissaoCheck(11)) ? $aluno->id : '' )}} );" title="Alterar situação do aluno"> <i class="ion-android-create"></i> </a></td>
                </tr>
            @endforeach
        @else
            <div class="box-registro-not-found">
                <i class="ion-social-snapchat-outline" style="font-size: 32px"></i><br />
                <span style="color: brown">Não há registro de alunos em situações diversas</span>
            </div>
        @endif

</table>
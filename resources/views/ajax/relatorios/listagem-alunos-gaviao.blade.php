@foreach($qmss as $qms)
    <div style="text-align: center; margin-top: -32px">
        <h4 style="color: #696969;">{{ $qms->qms }}</h4>
    </div>
    <table class="table table-striped" style="margin: 60px 0 90px 0;">
        <thead>
            <tr>
                <th>NR</th>
                <th>NOME DE GUERRA</th>
                <th>NOME COMPLETO</th>
                <th>SEGMENTO</th>
                <th style="text-align: center;">PENDÊNCIAS</th>
                <th style="text-align: center; width: 150px;">AÇÃO</th>
            </tr>

    @foreach($alunos as $aluno)
        @if($aluno->qms_id == $qms->id)
            
            <?php
                unset($pendencias);
                if($aluno->email == null){
                    $pendencias[] = 'Email não informado';
                }
            ?>
            
            <tr id="alunoID_{{$aluno->id}}">
                <td>{{ $aluno->numero }}</td>
                <td>{{ $aluno->nome_guerra }}</td>
                <td>{{ $aluno->nome_completo }}</td>
                <td>{{ $sexo[$aluno->sexo] }}</td>
                
                @if(isset($pendencias))
                    <td data-toggle="tooltip" data-placement="top" title="{{implode(', ', $pendencias)}}" style="color: #DF0101; text-align: center; font-size: 18px;"><i class="ion-ios-minus-outline"></i></td>
                @else
                    <td style="color: #0B610B; text-align: center; font-size: 18px;"><i class="ion-ios-checkmark-outline"></i></td>
                @endif

                <td class="botao-action-table" style="text-align: center;">
                    @if(isset($aluno->user))
                        <a href="javascript: void(0);" class="no-style" onclick="dialogInfoUser({{ $aluno->user->id }}, 'aluno');" title="Detalhes do aluno"> <i class="ion-information-circled"></i> </a>
                        <a href="javascript: void(0);" class="no-style" onclick="loadAdminAjaxContent('admin/aluno/{{ $aluno->id }}');" title="Editar cadastro do aluno"> <i class="ion-android-create"></i> </a>
                    @endif
                    
                    @if($ownauthcontroller->PermissaoCheck(11))
                        <a href="javascript: void(0);" class="no-style" onclick="DialogAdicionarAluSitDivNovo({{ $aluno->id }});" title="Incluir aluno na lista de alunos em situações diversas."> <i class="ion-forward" style="font-size: 24px;"></i> </a>
                    @endif

                    <!--@if($ownauthcontroller->PermissaoCheck(9))
                        <a href="javascript: void(0);" class="no-style" onclick="dialogRemoverAluno({{ $aluno->id }});" title="Remover aluno definitivamente do sistema"> <i class="ion-android-delete" style="font-size: 22px;"></i> </a>
                    @endif-->
                </td>
            </tr>
               
        @endif
    @endforeach
        </thead>
    </table>
@endforeach


<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<table class="table table-striped" style="margin: 60px 0 90px 0;">
    <thead>
        <tr>
            <th>NR</th>
            <th>NOME DE GUERRA</th>
            <th>NOME COMPLETO</th>
            <th style="text-align: center;">CURSO</th>
            <th style="text-align: center; width: 150px;">TURMA</th>
        </tr>

        @foreach($alunos as $aluno)

        <tr id="alunoID_{{$aluno->id}}">
            <td>{{ $aluno->numero }}</td>
            <td>{{ $aluno->nome_guerra }}</td>
            <td>{{ $aluno->nome_completo }}</td>
            <td style="width: 150px;text-align: center;">{{ $aluno->qms->qms }}</td>
            
            <td class="botao-action-table" style="text-align: center;">
                <select class="custom-select select_curso" name="curso" onchange="if (this.options[this.selectedIndex].value) selecionaTurma(this.options[this.selectedIndex].value, {{$aluno->id}});">
                    <option>Turma</option>
                    
                    @foreach($turmas as $turma)
                    <option {{( ($aluno->turma_esa_id == $turma->id) ? 'selected': '')}} value="{{$turma->id}}">{{$turma->turma}}</option>
                    @endforeach
                </select>
            </td>
        </tr>

        @endforeach
    </thead>
</table>
<script>
    function selecionaTurma(idTurma, idAluno) {

        $.ajax({
            dataType: 'json',
            url: '/gaviao/ajax/seleciona-turma/'+ idTurma+'/'+idAluno,
            type: 'POST',
            data: '_token={{csrf_token()}}',
            beforeSend: function() {
                $('div.errors-seleciona-turma').empty().hide();
                $('div.success-seleciona-turma').empty().hide();
            },
            success: function(data) {

                if (data.status == 'err') {
                    $('div.errors-seleciona-turma').html(data.response).slideDown();
                } else {
                    $('div.success-seleciona-turma').html(data.response).slideDown();

                    setTimeout(function() {
                        $('div.success-seleciona-turma').slideUp(200, function() {
                            $('div#full-modal').modal('hide');
                        });
                    }, 3000);
                }

            },
            error: function(jqxhr) {
                $('div.errors-seleciona-turma').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
            }
        });
    }
</script>
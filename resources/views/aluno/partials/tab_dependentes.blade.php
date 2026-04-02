                <div class="tab-pane fade" id="nav-implantar-aluno7" role="tabpanel" aria-labelledby="nav-home-tab" style="padding: 5px 25px 0px 5px;">

                    @if(isset($aluno) && sizeof($aluno->dependentes) > 0)
                    @foreach ($aluno->dependentes as $dependente)
                    @include('aluno.dependente')
                    @endforeach
                    @else
                    @include('aluno.dependente')
                    @endif

                    <div style="margin-left:15px;border-bottom:none;margin-top:100px;width: 100%;">
                        <button type="button" id="add-dependente"> + </button>
                    </div>

                </div>

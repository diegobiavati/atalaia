
<div style="height:50px"></div>
<h1 style="margin:0 0 0 30px;">Calendário de Avaliações - <small><b>CFGS {{ $anoFormacao->ano_cfs }}</b></small></h1>

@if($ownauthcontroller->PermissaoCheck([33]))
  <button id="cria_avaliacao" type="button" style="margin:10px 0 0 30px;" class="btn btn-primary" href="javascript: void(0);">Adicionar Avaliação</button>
@endif

<hr>
<div class="calendario">
  <div class="row header-calendar">

    <div class="col" style="display: flex; justify-content: space-between; padding: 10px;">
      <a href="javascript:void(0);" style="margin:10px;">
        <i class="fas fa-chevron-circle-left" style="font-size:30px;color:white;"></i>
      </a>
      <h2 style="font-weight:bold;margin:10px;"><?= $mesportuguese; ?> <small><?= $data['year']; ?></small></h2>
      <a href="javascript:void(0);" style="margin:10px;">
        <i class="fas fa-chevron-circle-right" style="font-size:30px;color:white;"></i>
      </a>
    </div>

  </div>
  <div class="row">
    <div class="col header-col">Segunda</div>
    <div class="col header-col">Terça</div>
    <div class="col header-col">Quarta</div>
    <div class="col header-col">Quinta</div>
    <div class="col header-col">Sexta</div>
    <div class="col header-col">Sábado</div>
    <div class="col header-col">Domingo</div>
  </div>
  <!-- inicio de semana -->
  @foreach ($data['calendar'] as $weekdata)
  <div class="row">
    <!-- ciclo de dia por semana -->
    @foreach ($weekdata['datos'] as $dayweek)

    @if ($dayweek['mes']==$mes)
    <div class="col box-day">
      {{ $dayweek['dia']  }}
      <!-- evento -->
      @foreach ($dayweek['evento'] as $event)
      <a class="badge badge-primary" style="white-space:normal;background-color: {{App\Http\Controllers\Utilitarios\FuncoesController::getQmsColor($event->esadisciplinas->qms->qms_matriz_id)->backgroundColor}};" href="javascript:void(0);" data-id="{{$event->id}}">
        {{ '('.$event->chamada.'ª chamada)'}}<br>{{$event->nome_avaliacao.'-'.$event->esadisciplinas->nome_disciplina_abrev }}
      </a>
      @endforeach
    </div>
    @else
    <div class="col box-dayoff">
    </div>
    @endif


    @endforeach
  </div>
  @endforeach
</div>

<script>
  $('div.calendario .fas.fa-chevron-circle-left').click(function(evt) {
    evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
    //Carrega a combo com Uetes ou Cursos
    carregaContainerCalendario("{{ asset('/gaviao/ajax/calendario/mes/') }}/{{$anoFormacao->id}}/{{$data['last']}}");
  });

  $('div.calendario .fas.fa-chevron-circle-right').click(function(evt) {
    evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
    //Carrega a combo com Uetes ou Cursos
    carregaContainerCalendario("{{ asset('/gaviao/ajax/calendario/mes/') }}/{{$anoFormacao->id}}/{{$data['next']}}");
  });

  $('div.calendario .badge.badge-primary').click(function(evt) {
    evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
    carregaContainerCalendario("{{ asset('/gaviao/ajax/gerenciar-avaliacao') }}/"+$(this).data("id"));
  });

  $('#cria_avaliacao').click(function(evt) {
    evt.stopImmediatePropagation(); //Não deixa duplicar os eventos
    carregaContainerCalendario("{{asset('/gaviao/ajax/gerenciar-avaliacao')}}");
  });

</script>
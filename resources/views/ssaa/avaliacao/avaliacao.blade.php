<div class="container">
  <div style="height:50px"></div>
  <h1>
    < Arqui /> <small>Vai o Nome da Avaliação</small>
  </h1>
  <p class="lead">
  <h3>Avaliação</h3>
  <p>Detalhes da Avaliação</p>
  <a class="btn btn-default" href="{{ asset('/gaviao/ajax/Evento/index') }}">Atras</a>
  <hr>

  <div class="col-md-6">
    <form action="{{ asset('/gaviao/ajax/Evento/create/') }}" method="post">
      <div class="fomr-group">
        <h4>Titulo</h4>
        {{ $event->titulo }}
      </div>
      <div class="fomr-group">
        <h4>Descrição da Avaliação</h4>
        {{ $event->descripcion }}
      </div>
      <div class="fomr-group">
        <h4>Teste3</h4>
        {{ $event->fecha }}
      </div>
      <br>
      <input type="submit" class="btn btn-info" value="Salvar">
    </form>
  </div>


  <!-- inicio de semana -->


</div> <!-- /container -->
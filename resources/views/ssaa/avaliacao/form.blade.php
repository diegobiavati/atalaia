<style>
  div#form-avaliacao label {
    margin-bottom: 0.3rem;
    margin-top: 0.5rem;
  }

  form#form-avaliacoes select.selectpicker.custom-select option {
    font-weight: normal;
  }

  form#form-avaliacoes select.selectpicker.custom-select {
    font-weight: bold;
  }

  .nav-link h4{
    color: rgba(255, 0, 0, 0.9);
  }
</style>
<div class="container" id="form-avaliacao">
  <div style="height:50px"></div>
  <p class="lead">
  <h3>Cadastro de Avaliação</h3>

  <hr>
  <div class="alert alertas-avaliacoes" role="alert" style="margin:10px;width:100%;"></div>
  @isset($rapLancadas)
  <div style="background-color: #e3f2fd;">
      <nav class="navbar flex-row navbar-expand-lg navbar-light" style="padding: .5rem 1rem 0rem 1rem;">
        <span class="navbar-brand text-muted" href="#">Turma(s) com lançamento do RAP: </span>
        <!--<div class="collapse navbar-collapse">-->
          <ul class="navbar-nav mr-auto">
            @foreach($rapLancadas as $rap)
            <li class="nav-item active">
              <a class="nav-link h4" target="_blank" href="{{asset('gaviao/ajax/relatorio-rap/'.encrypt(session('_token').'-'.$rap->id_esa_avaliacoes.'-'.$rap->id_turmas_esa) )}}">{{ $rap->esaTurma->turma }}</a>
            </li>
            @endforeach
          </ul>
        <!--</div>-->
      </nav>
  </div>
  <span class="badge badge-pill badge-info" style="padding: .5rem 1rem; color: #d2fd00;">* Clique na Turma para visualizar o RAP</span>
  @endisset

  <div class="col-md-6" style="max-width: 100%;">
    <form id="form-avaliacoes">
      @csrf
      <div style="width: 193px;">
        <div class="fomr-group">
          <label>Curso</label>
          @include('ajax.componenteSelectUeteCurso')
        </div>
      </div>

      <div class="fomr-group components">
        @isset($esaAvaliacoes)
        @include('ssaa.avaliacao.componenteAjax')
        @endisset
      </div>

    </form>
  </div>

</div> <!-- /container -->

<script>
  $('div.alertas-avaliacoes').empty().hide();

  $('div#form-avaliacao select[name="qmsID"]').change(function(evt) {
    evt.stopImmediatePropagation(); //Não deixa duplicar os eventos

    $.ajax({
      url: '/gaviao/ajax/gerenciar-avaliacao/get-components/' + evt.currentTarget.value,
      type: 'GET',
      beforeSend: function() {
        $('div.components').empty();

        $('div.alertas-avaliacoes').empty().hide();
        $('div.alertas-avaliacoes').removeClass('alert-success').empty();
        $('div.alertas-avaliacoes').removeClass('alert-danger').empty();
      },
      success: function(data) {
        $('div.components').html(data);
      },
      error: function(jqxhr) {
        $('div.alertas-avaliacoes').addClass('alert-danger').empty();
        $('div.alertas-avaliacoes').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
      }
    });
  });
</script>
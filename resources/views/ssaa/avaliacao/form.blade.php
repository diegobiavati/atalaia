<style>
  select[readonly] {
    background: initial !important;
    font-size: initial !important;
    text-align: initial !important;
  }

  div#form-avaliacao {
    position: relative;
    z-index: 20;
    margin-bottom: 80px;
  }

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

  .nav-link h4 {
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
        <li class="nav-item active mr-2">
          <div class="dropdown d-inline-block">
            <a class="btn btn-info btn-sm dropdown-toggle"
              href="#"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
              style="border-radius: 20px; color: #d2fd00; font-weight: bold;">
              {{ $rap->esaTurma->turma }}
            </a>

            <div class="dropdown-menu">
              <a class="dropdown-item"
                target="_blank"
                href="{{ asset('gaviao/ajax/relatorio-rap/'.encrypt(session('_token').'-'.$rap->id_esa_avaliacoes.'-'.$rap->id_turmas_esa)) }}">
                Abrir relatório
              </a>

              <div class="dropdown-divider"></div>

              @if($ownauthcontroller->PerfilCheck([9005]))
              <a class="dropdown-item text-danger"
                href="javascript:void(0);"
                onclick="removerRap('{{ encrypt(session('_token').'-'.$rap->id_esa_avaliacoes.'-'.$rap->id_turmas_esa) }}')">
                Remover RAP
              </a>
              @endif
            </div>
          </div>
        </li>
        @endforeach
      </ul>
      <!--</div>-->
    </nav>
  </div>
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
        $('div.alertas-avaliacoes').html('<strong>ATENÇÃO: </strong> Houve um erro interno')
          .slideDown();
      }
    });
  });

  function removerRap(id) {
    $(document).confirmAcao('Deseja realmente remover este <strong>RAP</strong>?', function() {
      $.ajax({
        type: 'DELETE',
        dataType: 'json',
        url: "{{ asset('/gaviao/ajax/gerenciar-avaliacao/rap') }}",
        data: {
          '_token': '{{ csrf_token() }}',
          'hash_rap': id
        },
        beforeSend: function() {
          $('div.alertas-avaliacoes').empty().hide();
          $('div.alertas-avaliacoes').removeClass('alert-success').removeClass('alert-danger');
        },
        success: function(data) {
          if (data.status == 'success') {
            $('div.alertas-avaliacoes').addClass('alert-success').html('<li>' + data.response + '</li>').slideDown();

            setTimeout(function() {
              $('div.alertas-avaliacoes').slideUp(200, function() {
                $('div.container_calendario').empty();
                carregaContainerCalendario("{{ asset('gaviao/ajax/calendario/index/'.$cursoSelecionado->escolhaQms->anoFormacao->id) }}");
              });
            }, 2000);
          } else {
            $('div.alertas-avaliacoes').addClass('alert-danger').slideDown();
            $.each(data.response, function(key, value) {
              $('div.alertas-avaliacoes').append('<li>' + value + '</li>');
            });
          }
        },
        error: function() {
          $('div.alertas-avaliacoes').addClass('alert-danger').html('<strong>ATENÇÃO: </strong> Houve um erro interno').slideDown();
        }
      });
    });
  }
</script>

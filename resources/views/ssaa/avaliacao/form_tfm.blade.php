<style>
  
  select[readonly] {
    /*
    background: initial!important;
    font-size: initial!important;
    text-align: initial!important;
    */
    all:initial!important;
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

  .nav-link h4{
    color: rgba(255, 0, 0, 0.9);
  }
</style>
<div class="container" id="form-avaliacao">
  <div style="height:50px"></div>
  <p class="lead">
  <h3>Cadastro de Avaliação TFM</h3>

  <hr>
  <div class="alert alertas-avaliacoes" role="alert" style="margin:10px;width:100%;"></div>
  @isset($rapLancadas)
  <div style="background-color: #e3f2fd;">
      <nav class="navbar flex-row navbar-expand-lg navbar-light" style="padding: .5rem 1rem 0rem 1rem;">
        <span class="navbar-brand text-muted" href="#">Lançamento do RAP: </span>
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
                  RAP/TFM {{ $rap->esaAvaliacoes->esaDisciplinas->qms->qms }}
                </a>

                <div class="dropdown-menu">
                  <a class="dropdown-item"
                    target="_blank"
                    href="{{asset('gaviao/ajax/relatorio-rap-tfm/'.encrypt(session('_token').'-'.$rap->id_esa_avaliacoes) )}}">
                    Abrir relatÃ³rio
                  </a>

                  <div class="dropdown-divider"></div>

                  @if($ownauthcontroller->PerfilCheck([9005]))
                  <a class="dropdown-item text-danger"
                    href="javascript:void(0);"
                    onclick="removerRapTfm('{{ encrypt(session('_token').'-'.$rap->id_esa_avaliacoes) }}')">
                    Remover RAP/TFM
                  </a>
                  @endif
                </div>
              </div>
            </li>
            @endforeach
          </ul>
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
  (function($) {
    $.fn.confirmAcao = function(content, callback) {
      return this.each(function() {
        if ($('#modalConfirmAcao').length == 0) {
          $('body').append(
            '<div class="modal fade bd-example-modal-sm" id="modalConfirmAcao" style="background-color: rgb(119, 119, 119);" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">' +
              '<div class="modal-dialog modal-sm modal-dialog-centered">' +
                '<div class="modal-content" style="border-radius: 0; padding: 10px;min-height: 15vh;">' +
                  '<div style="border-bottom: 1px solid #ccc;"><i class="ion-android-hand" style="font-size: 22px; vertical-align: middle;"></i><span style="margin-left: 10px;"><b>ATENÃ‡ÃƒO!</b></span></div>' +
                  '<div class="content-confirmAcao" style="margin: 6px 0;"></div>' +
                  '<div style="text-align: right;">' +
                    '<div style="display: inline-block;">' +
                      '<a id="confirm-buttom" class="no-style" href="javascript: void(0);"><span style="color: #363636;"><strong>SIM</strong></span></a>' +
                    '</div>' +
                    '<div style="display: inline-block; margin-left: 8px;">' +
                      '<a class="no-style" href="javascript: void(0);" onclick="$(\'#modalConfirmAcao\').modal(\'hide\');"><span style="color: #2E64FE;"><strong>NÃƒO</strong></span></a>' +
                    '</div>' +
                  '</div>' +
                '</div>' +
              '</div>' +
            '</div>'
          );
        }

        $('div.content-confirmAcao').html(content);
        $('#modalConfirmAcao').modal('show');
        $('#confirm-buttom').off('click').on('click', function() {
          callback();
          $('#modalConfirmAcao').modal('hide');
        });
      });
    };
  })(jQuery);

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

  function removerRapTfm(id) {
    $(document).confirmAcao('Deseja realmente remover este <strong>RAP/TFM</strong>?', function() {
      $.ajax({
        type: 'DELETE',
        dataType: 'json',
        url: "{{ asset('/gaviao/ajax/gerenciar-avaliacao/rap') }}",
        data: {
          '_token': '{{ csrf_token() }}',
          'hash_rap': id,
          'tipo': 'tfm'
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
          $('div.alertas-avaliacoes').addClass('alert-danger').html('<strong>ATENÃ‡ÃƒO: </strong> Houve um erro interno').slideDown();
        }
      });
    });
  }
</script>

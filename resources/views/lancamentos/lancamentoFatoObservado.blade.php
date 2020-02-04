<form id="lancamentoFatoObservado">
    <input type="hidden" name="_token" value="{{csrf_token()}}" />

    {!! App\Http\Controllers\Utilitarios\FuncoesController::retornaBotaoAnoFormacao() !!}

    <div style="width: 50%; margin: 22px auto; text-align: center; border-bottom: 1px solid #ccc;">
        <select name="omctID" class="custom-select required_to_show_button">
            <option value="0" disabled selected hidden>Selecione uma UETE</option>
            @if($ownauthcontroller->PermissaoCheck(1))
            <option value="todas_omct">TODAS AS UETE</option>
            @endif
            @foreach ($uetes as $uete)
            <option value={{$uete->id}}>{{ $uete->omct }}</option>
            @endforeach
        </select>

        <label class="custom-control-label" style="padding: 5px;width:50%;">Observador
            <input class="form-control" style="display:block;" name="previsto" autocomplete="off" readonly />
        </label>
    </div>

    <script>
        $(document).ready(function() {
            $('.btn.btn-secondary').click(function() {

            });
        });
    </script>
</form>
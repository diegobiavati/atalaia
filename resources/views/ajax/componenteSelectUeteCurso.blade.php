@if(isset($cursos))
    <select name="qmsID" class="custom-select required_to_show_button">
        <option value="0" disabled selected hidden>Selecione um Curso</option>
        @if($ownauthcontroller->PermissaoCheck(1))
        <option value="todas_qmss">TODOS OS CURSOS</option>
        @endif
        @foreach ($cursos as $curso)
        <option value={{$curso->id}}>{{ $curso->qms }}</option>
        @endforeach
    </select>
@else
    <select name="omctID" class="custom-select required_to_show_button">
        <option value="0" disabled selected hidden>Selecione uma UETE</option>
        @if($ownauthcontroller->PermissaoCheck(1))
        <option value="todas_omct">TODAS AS UETE</option>
        @endif
        @foreach ($uetes as $uete)
        <option value={{$uete->id}}>{{ $uete->omct }}</option>
        @endforeach
    </select>
@endif
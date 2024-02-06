@isset($qmss)
@foreach($qmss as $qms)
<div class="custom-control custom-checkbox" style="display: inline-block; margin: 0 10px 12px 0;">
    <input type="checkbox" class="custom-control-input omcts" id="qms_{{$qms->id}}" name="qmss[]" value="{{$qms->id}}" />
    <label class="custom-control-label" for="qms_{{$qms->id}}">{{$qms->qms}}</label>
</div>
@endforeach
@endisset
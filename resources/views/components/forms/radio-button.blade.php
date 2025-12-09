<div class="form-check">
    <input class="form-check-input" type="radio" id="{{ $name }}-{{ $value }}" name="{{ $name }}" value="{{ $value }}" @if (old($name) == $value) checked @endif>
    <label class="form-check-label" for="{{ $name }}-{{ $value }}">{!! $display !!}</label>
</div>

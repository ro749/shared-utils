<textarea
    class="form-control"
    name="{{ $name }}"
    x-model="form.{{ $name }}"
    placeholder="{{ $field->placeholder }}"
    @if($field->max_length!=0)
    maxlength="{{ $field->max_length }}"
    @endif
    @if($field->autosave)
    @input.debounce.500ms="submit()"
    @endif
    rows="{{ $field->rows }}",
    cols="{{ $field->cols }}"
>
</textarea>
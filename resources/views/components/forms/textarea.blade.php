@props(['element' => null, 'name' => ''])

<textarea
    class="form-control"
    name="{{ $name }}"
    x-model="form.{{ $name }}"
    placeholder="{{ $element->placeholder }}"
    @if($element->max!=0)
    maxlength="{{ $element->max }}"
    @endif
    @if($element->autosave)
    @input.debounce.500ms="submit()"
    @endif
    rows="{{ $element->rows }}",
    cols="{{ $element->cols }}"
>
</textarea>
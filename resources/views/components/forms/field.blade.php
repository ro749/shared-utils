<input
    type="{{ $field->get_type() }}"
    class="form-control"
    name="{{ $name }}"
    x-model="form.{{ $name }}"
    placeholder="{{ $field->placeholder }}{{ $field->is_required() ? '*' : '' }}"
    @if($field->max_length!=0)
    maxlength="{{ $field->max_length }}"
    @endif
>
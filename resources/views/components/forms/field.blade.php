<input
    type="{{ $field->get_type() }}"
    class="form-control"
    name="{{ $name }}"
    x-model="form.{{ $name }}"
    placeholder="{{ $field->placeholder }}{{ $field->is_required() ? '*' : '' }}"
>
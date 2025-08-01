<input
    type="{{ $field->get_type() }}"
    class="form-control"
    name="{{ $name }}"
    x-model="form.{{ $name }}"
    placeholder="{{ $field->placeholder }}{{ $field->is_required() ? '*' : '' }}"
    @if($field->max_length!=0)
    maxlength="{{ $field->max_length }}"
    @endif
    @if(
        $field->type === \Ro749\SharedUtils\FormRequests\InputType::ID_NUMBER ||
        $field->type === \Ro749\SharedUtils\FormRequests\InputType::PHONE
        )
    oninput="this.value = this.value.replace(/\D/g, '')"
    @endif
>
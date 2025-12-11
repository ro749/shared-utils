<input
    type="{{ $field->get_type() }}"
    class="form-control
    @if($field->type === \Ro749\SharedUtils\Forms\InputType::PERCENTAGE)
    input-percent
    @elseif($field->type === \Ro749\SharedUtils\Forms\InputType::MONEY)
    input-money
    @endif
    "
    id="{{ $name }}"
    name="{{ $name }}"
    @if($field->type !== \Ro749\SharedUtils\Forms\InputType::PERCENTAGE && $field->type !== \Ro749\SharedUtils\Forms\InputType::MONEY)
    x-model="form.{{ $name }}"
    @endif
    placeholder="{{ $field->placeholder }}"
    @if($field->max_length!=0)
    maxlength="{{ $field->max_length }}"
    @endif
    @if(
        $field->type === \Ro749\SharedUtils\Forms\InputType::ID_NUMBER ||
        $field->type === \Ro749\SharedUtils\Forms\InputType::PHONE
        )
    oninput="this.value = this.value.replace(/\D/g, '')"
    @endif
    @if($field->type === \Ro749\SharedUtils\Forms\InputType::EMAIL)
    oninput="this.value = this.value.toLowerCase()"
    @endif
    @if($field->autosave)
    @input.debounce.500ms="submit()"
    @endif
>


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
    @if(
        $field->type === \Ro749\SharedUtils\Forms\InputType::TEXT ||
        $field->type === \Ro749\SharedUtils\Forms\InputType::PASSWORD
        )
        @if(!empty($field->max))
    maxlength="{{ $field->max }}"
        @endif
    @endif

    @if($field->type === \Ro749\SharedUtils\Forms\InputType::NUMBER)
        @if($field->max!==null)
    max="{{ $field->max }}"
        @endif
        @if($field->min!==null)
    min="{{ $field->min }}"
        @endif
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
        @if($field->type === \Ro749\SharedUtils\Forms\InputType::CHECKBOX)
        @change="submit()"
        @else
        @input.debounce.500ms="submit()"
        @endif
    @endif
>

